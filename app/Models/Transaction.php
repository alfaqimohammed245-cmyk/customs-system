<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'receipt_date'         => 'date',
            'policy_receipt_date'  => 'date',
            'declaration_date'     => 'date',
            'unloading_date'       => 'date',
            'empty_return_date'    => 'date',
            'started_at'           => 'datetime',
            'completed_at'         => 'datetime',
            'stage_1_completed_at' => 'datetime',
            'stage_2_completed_at' => 'datetime',
            'stage_3_completed_at' => 'datetime',
            'stage_4_completed_at' => 'datetime',
            'stage_5_completed_at' => 'datetime',
            'stage_6_completed_at' => 'datetime',
            'received_policy'      => 'boolean',
            'stage_1_completed'    => 'boolean',
            'stage_2_completed'    => 'boolean',
            'stage_3_completed'    => 'boolean',
            'stage_4_completed'    => 'boolean',
            'stage_5_completed'    => 'boolean',
            'stage_6_completed'    => 'boolean',
        ];
    }

    /**
     * تعريف المراحل الست لسير العمل الجمركي
     */
    public const STAGES = [
        1 => [
            'id'          => 1,
            'name'        => 'الاستلام والترقيم',
            'title_en'    => 'Receipt & Numbering',
            'subtitle'    => 'تسجيل البيانات الأولية، التاجر، الطرود، سابر، ومستندات الاستلام',
            'permission'  => 'مرحلة 1: الاستلام والترقيم',
            'icon'        => 'fa-clipboard-list',
            'color'       => 'blue',
            'badge_bg'    => 'bg-blue-100 text-blue-800 border-blue-200',
        ],
        2 => [
            'id'          => 2,
            'name'        => 'ربط البوليصة بالبيان',
            'title_en'    => 'Shipping Agent & Policy Linking',
            'subtitle'    => 'الوكيل الملاحي، بوليصة الشحن، استلام البوليصة، وإذن التسليم',
            'permission'  => 'مرحلة 2: ربط البوليصة بالبيان',
            'icon'        => 'fa-link',
            'color'       => 'indigo',
            'badge_bg'    => 'bg-indigo-100 text-indigo-800 border-indigo-200',
        ],
        3 => [
            'id'          => 3,
            'name'        => 'التخليص والإجراءات الجمركية',
            'title_en'    => 'Customs Clearance & Inspection',
            'subtitle'    => 'البيان الجمركي، الفحص والمعاينة بالميناء، فواتير الموانئ والمشغل',
            'permission'  => 'مرحلة 3: التخليص والإجراءات الجمركية',
            'icon'        => 'fa-building-columns',
            'color'       => 'amber',
            'badge_bg'    => 'bg-amber-100 text-amber-800 border-amber-200',
        ],
        4 => [
            'id'          => 4,
            'name'        => 'خروج الحاويات والنقل (الفسح)',
            'title_en'    => 'Container Exit & Transport',
            'subtitle'    => 'فسح وخروج الحاوية، تعيين وسيلة التحميل والسائق ونقل البضاعة',
            'permission'  => 'مرحلة 4: خروج الحاويات والنقل',
            'icon'        => 'fa-truck-fast',
            'color'       => 'purple',
            'badge_bg'    => 'bg-purple-100 text-purple-800 border-purple-200',
        ],
        5 => [
            'id'          => 5,
            'name'        => 'إعادة الحاويات الفارغة',
            'title_en'    => 'Returning Empty Containers',
            'subtitle'    => 'متابعة وتأكيد ترجيع الحاوية الفارغة لساحة الإرجاع',
            'permission'  => 'مرحلة 5: إعادة الحاويات الفارغة',
            'icon'        => 'fa-rotate-left',
            'color'       => 'teal',
            'badge_bg'    => 'bg-teal-100 text-teal-800 border-teal-200',
        ],
        6 => [
            'id'          => 6,
            'name'        => 'الإنجاز والفاتورة والمبيعات',
            'title_en'    => 'Completion & Sales Invoice',
            'subtitle'    => 'إصدار فاتورة العميل الإجمالية، الأرشفة، والإغلاق النهائي للمعاملة',
            'permission'  => 'مرحلة 6: الإنجاز والفاتورة والمبيعات',
            'icon'        => 'fa-circle-check',
            'color'       => 'emerald',
            'badge_bg'    => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        ],
    ];

    protected static function booted()
    {
        // توليد رقم عملية تسلسلي تلقائي إذا لم يتم إدخاله
        static::creating(function ($transaction) {
            if (empty($transaction->transaction_number)) {
                $transaction->transaction_number = static::generateTransactionNumber();
            }
            if (empty($transaction->current_stage)) {
                $transaction->current_stage = 1;
            }
            if (empty($transaction->status)) {
                $transaction->status = 'جديدة';
            }
            if (!isset($transaction->progress_percentage) || $transaction->progress_percentage === null) {
                $transaction->progress_percentage = 10;
            }
        });

        // التعبئة التلقائية لاسم التاجر لمنع أخطاء قاعدة البيانات
        static::saving(function ($transaction) {
            if (!empty($transaction->trader_id) && empty($transaction->trader_name)) {
                $trader = Trader::find($transaction->trader_id);
                if ($trader) {
                    $transaction->trader_name = $trader->name ?? $trader->full_name ?? null;
                }
            }
        });

        // تسجيل حركة الإنشاء تلقائياً في سجل العمليات
        static::created(function ($transaction) {
            if (class_exists(\App\Models\ActivityLog::class)) {
                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id() ?? 1,
                    'action' => 'إضافة عملية جمركية',
                    'description' => 'تم إضافة العملية رقم: ' . ($transaction->transaction_number ?? $transaction->id),
                ]);
            }
        });

        // تسجيل حركة التحديث تلقائياً في سجل العمليات
        static::updated(function ($transaction) {
            if (class_exists(\App\Models\ActivityLog::class)) {
                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id() ?? 1,
                    'action' => 'تعديل عملية جمركية',
                    'description' => 'تم تحديث العملية رقم: ' . ($transaction->transaction_number ?? $transaction->id),
                ]);
            }
        });

        // تسجيل حركة الحذف تلقائياً في سجل العمليات
        static::deleted(function ($transaction) {
            if (class_exists(\App\Models\ActivityLog::class)) {
                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id() ?? 1,
                    'action' => 'حذف عملية جمركية',
                    'description' => 'تم حذف العملية رقم: ' . ($transaction->transaction_number ?? $transaction->id),
                ]);
            }
        });
    }

    /**
     * توليد رقم عملية فريد بتنسيق رسمي
     */
    public static function generateTransactionNumber(): string
    {
        $prefix = 'TRX-' . date('Ym') . '-';
        $last = static::where('transaction_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($last && preg_match('/-(\d+)$/', $last->transaction_number, $matches)) {
            $nextNum = str_pad((int)$matches[1] + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return $prefix . $nextNum;
    }

    /**
     * التحقق مما إذا كان المستخدم يمتلك صلاحية تعديل مرحلة معينة
     */
    public function canUserEditStage($user = null, int $stage = 1): bool
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return false;
        }

        // المدراء والمشرفون يمتلكون حق تعديل كل المراحل
        if ($user->hasAnyRole(['admin', 'Admin', 'super-admin', 'Super Admin', 'مسؤول النظام'])) {
            return true;
        }

        if ($user->can('تعديل كافة المراحل')) {
            return true;
        }

        $stageConfig = self::STAGES[$stage] ?? null;
        if (!$stageConfig) {
            return false;
        }

        return $user->can($stageConfig['permission']);
    }

    /**
     * التحقق من اكتمال مرحلة محددة
     */
    public function isStageCompleted(int $stage): bool
    {
        $field = "stage_{$stage}_completed";
        return (bool)($this->{$field} ?? false);
    }

    /**
     * حساب المرحلة الحالية ونسبة الإنجاز التلقائية بناءً على اكتمال المراحل
     */
    public function recalculateProgress(): void
    {
        $completedCount = 0;
        $activeStage = 1;

        for ($i = 1; $i <= 6; $i++) {
            if ($this->isStageCompleted($i)) {
                $completedCount++;
                if ($i < 6) {
                    $activeStage = $i + 1;
                } else {
                    $activeStage = 6;
                }
            } else {
                if ($activeStage === 1 && $completedCount === 0) {
                    $activeStage = $i;
                } elseif ($completedCount < $i && !isset($firstIncomplete)) {
                    $firstIncomplete = $i;
                    $activeStage = $i;
                }
            }
        }

        $percentage = (int) round(($completedCount / 6) * 100);
        $this->progress_percentage = $percentage;
        $this->current_stage = $activeStage;

        if ($completedCount === 6 || $this->stage_6_completed) {
            $this->status = 'مكتملة';
            $this->progress_percentage = 100;
            if (!$this->completed_at) {
                $this->completed_at = now();
            }
        } elseif ($this->status === 'مكتملة' && $completedCount < 6) {
            $this->status = 'قيد التنفيذ';
            $this->completed_at = null;
        } elseif ($this->status === 'جديدة' && $completedCount > 0) {
            $this->status = 'قيد التنفيذ';
        }
    }

    /**
     * إكمال مرحلة معينة وتحديث ملاحظاتها وسبب التأخير والموظف المنفذ
     */
    public function markStageCompleted(int $stage, ?string $notes = null, ?string $delayReason = null, ?int $userId = null): void
    {
        $userId = $userId ?? auth()->id() ?? 1;
        $now = now();

        $this->{"stage_{$stage}_completed"} = true;
        $this->{"stage_{$stage}_completed_at"} = $now;
        $this->{"stage_{$stage}_user_id"} = $userId;

        if ($notes !== null) {
            $this->{"stage_{$stage}_notes"} = $notes;
        }
        if ($delayReason !== null) {
            $this->{"stage_{$stage}_delay_reason"} = $delayReason;
        }

        $this->recalculateProgress();
        $this->save();
    }

    /**
     * معلومات المرحلة الحالية باللغة العربية
     */
    public function getCurrentStageInfoAttribute(): array
    {
        return self::STAGES[$this->current_stage ?? 1] ?? self::STAGES[1];
    }

    public function getCurrentStageNameAttribute(): string
    {
        return $this->current_stage_info['name'] ?? 'مرحلة غير معروفة';
    }

    // --- النطاقات (Query Scopes) ---

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'مكتملة')
                     ->where('status', '!=', 'ملغاة')
                     ->where('status', '!=', 'مؤرشفة')
                     ->where(function ($q) {
                         $q->where('stage_6_completed', false)
                           ->orWhereNull('stage_6_completed');
                     });
    }

    public function scopeCompleted($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'مكتملة')
              ->orWhere('stage_6_completed', true);
        });
    }

    public function scopeInStage($query, int $stage)
    {
        return $query->where('current_stage', $stage);
    }

    // --- العلاقات الأساسية ---

    public function trader() 
    { 
        return $this->belongsTo(Trader::class, 'trader_id'); 
    }

    public function company() 
    { 
        return $this->belongsTo(Company::class, 'company_id'); 
    }

    public function statusModel() 
    { 
        return $this->belongsTo(Status::class, 'status_id'); 
    }

    public function driver() 
    { 
        return $this->belongsTo(Driver::class, 'driver_id'); 
    }

    public function user() 
    { 
        return $this->belongsTo(User::class, 'user_id'); 
    }

    public function creator() 
    { 
        return $this->belongsTo(User::class, 'created_by'); 
    }

    public function employee() 
    { 
        return $this->belongsTo(User::class, 'employee_id'); 
    }

    public function auditLogs() 
    { 
        return $this->hasMany(AuditLog::class, 'transaction_id'); 
    }

    // --- علاقات مسؤولي المراحل ---

    public function stage1User() { return $this->belongsTo(User::class, 'stage_1_user_id'); }
    public function stage2User() { return $this->belongsTo(User::class, 'stage_2_user_id'); }
    public function stage3User() { return $this->belongsTo(User::class, 'stage_3_user_id'); }
    public function stage4User() { return $this->belongsTo(User::class, 'stage_4_user_id'); }
    public function stage5User() { return $this->belongsTo(User::class, 'stage_5_user_id'); }
    public function stage6User() { return $this->belongsTo(User::class, 'stage_6_user_id'); }
}