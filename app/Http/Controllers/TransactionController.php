<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Trader;
use App\Models\Company;
use App\Models\Status;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class TransactionController extends Controller
{
    /**
     * عرض قائمة المعاملات الجمركية مع دعم تتبع المراحل والفلاتر والبحث
     */
    public function index(Request $request)
    {
        $this->checkPermission('مشاهدة العمليات');

        // 1. جلب البيانات الأساسية للفلترة
        $traders   = Trader::all();
        $companies = Company::all();
        $statuses  = Status::all();
        $employees = User::all();
        $drivers   = Driver::all();

        // 2. بناء استعلام العمليات مع الفلاتر
        $query = Transaction::with(['trader', 'company', 'statusModel', 'driver', 'user', 'employee', 'creator']);

        // فلترة حسب التبويب (نشطة / مكتملة / الكل)
        $tab = $request->get('tab', 'active');
        if ($tab === 'active') {
            $query->active();
        } elseif ($tab === 'completed') {
            $query->completed();
        }

        // فلترة حسب المرحلة (1 إلى 6)
        if ($request->filled('stage')) {
            $query->where('current_stage', $request->stage);
        }

        // فلترة حسب التاجر
        if ($request->filled('trader_id')) {
            $query->where('trader_id', $request->trader_id);
        }

        // فلترة حسب الشركة
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // فلترة حسب الوكيل الملاحي
        if ($request->filled('shipping_agent')) {
            $query->where('shipping_agent', 'like', "%{$request->shipping_agent}%");
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } elseif ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        // فلترة حسب الموظف
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // فلترة حسب السائق
        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        // البحث الشامل
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhere('policy_number', 'like', "%{$search}%")
                    ->orWhere('declaration_number', 'like', "%{$search}%")
                    ->orWhere('trader_name', 'like', "%{$search}%")
                    ->orWhere('shipping_agent', 'like', "%{$search}%")
                    ->orWhere('driver_name', 'like', "%{$search}%");
            });
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();

        // إحصائيات سريعة للرأس
        $activeCount    = Transaction::active()->count();
        $completedCount = Transaction::completed()->count();
        $totalCount     = Transaction::count();

        // إحصائيات توزيع المراحل النشطة
        $stageCounts = [];
        for ($i = 1; $i <= 6; $i++) {
            $stageCounts[$i] = Transaction::active()->where('current_stage', $i)->count();
        }

        $stagesConfig = Transaction::STAGES;

        return view('transactions.index', compact(
            'transactions',
            'traders',
            'companies',
            'statuses',
            'employees',
            'drivers',
            'tab',
            'activeCount',
            'completedCount',
            'totalCount',
            'stageCounts',
            'stagesConfig'
        ));
    }

    /**
     * صفحة تتبع سير العمل والمراحل الست التفاعلية (Workflow Board)
     */
    public function workflow(Request $request)
    {
        $this->checkPermission(['مشاهدة العمليات', 'سير العمل والمراحل']);

        $stagesConfig = Transaction::STAGES;

        // جلب العمليات النشطة مقسمة حسب المراحل
        $stagesData = [];
        for ($i = 1; $i <= 6; $i++) {
            $stageQuery = Transaction::with(['trader', 'company', 'driver', 'employee'])
                ->active()
                ->where('current_stage', $i);

            if ($request->filled('search')) {
                $search = $request->search;
                $stageQuery->where(function ($q) use ($search) {
                    $q->where('transaction_number', 'like', "%{$search}%")
                        ->orWhere('policy_number', 'like', "%{$search}%")
                        ->orWhere('declaration_number', 'like', "%{$search}%")
                        ->orWhere('trader_name', 'like', "%{$search}%");
                });
            }

            if ($request->filled('trader_id')) {
                $stageQuery->where('trader_id', $request->trader_id);
            }

            $stagesData[$i] = $stageQuery->latest()->get();
        }

        $traders = Trader::all();
        $totalActive = Transaction::active()->count();
        $totalCompleted = Transaction::completed()->count();

        return view('transactions.workflow', compact('stagesData', 'stagesConfig', 'traders', 'totalActive', 'totalCompleted'));
    }

    /**
     * تقرير وصفحة العمليات المكتملة والأرشيف
     */
    public function completed(Request $request)
    {
        $this->checkPermission(['مشاهدة العمليات', 'مشاهدة العمليات المكتملة', 'مشاهدة التقارير']);

        $traders   = Trader::all();
        $companies = Company::all();

        $query = Transaction::with(['trader', 'company', 'driver', 'employee', 'stage1User', 'stage2User', 'stage3User', 'stage4User', 'stage5User', 'stage6User'])
            ->completed();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhere('policy_number', 'like', "%{$search}%")
                    ->orWhere('declaration_number', 'like', "%{$search}%")
                    ->orWhere('trader_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('trader_id')) {
            $query->where('trader_id', $request->trader_id);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('completed_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('completed_at', '<=', $request->to_date);
        }

        $completedTransactions = $query->latest('completed_at')->paginate(15)->withQueryString();

        // إحصائيات مالية للعمليات المكتملة
        $totalRevenue = Transaction::completed()->sum('client_invoice') ?? 0;
        $totalPorts   = Transaction::completed()->sum('ports_invoice') ?? 0;
        $totalCount   = Transaction::completed()->count();

        $stagesConfig = Transaction::STAGES;

        return view('transactions.completed', compact(
            'completedTransactions',
            'traders',
            'companies',
            'totalRevenue',
            'totalPorts',
            'totalCount',
            'stagesConfig'
        ));
    }

    /**
     * عرض صفحة إنشاء عملية جديدة
     */
    public function create()
    {
        $this->checkPermission(['مشاهدة العمليات', 'إضافة', 'مرحلة 1: الاستلام والترقيم']);

        $traders   = Trader::all();
        $companies = Company::all();
        $statuses  = Status::all();
        $employees = User::all();
        $drivers   = Driver::all();
        $autoNumber = Transaction::generateTransactionNumber();
        $stagesConfig = Transaction::STAGES;

        return view('transactions.create', compact('traders', 'companies', 'statuses', 'employees', 'drivers', 'autoNumber', 'stagesConfig'));
    }

    /**
     * حفظ عملية جمركية جديدة
     */
    public function store(Request $request)
    {
        $this->checkPermission(['مشاهدة العمليات', 'إضافة']);

        $request->validate([
            'transaction_number' => 'required|string|max:255|unique:transactions,transaction_number',
            'trader_id'          => 'required',
        ], [
            'transaction_number.required' => 'الرجاء إدخال رقم العملية.',
            'transaction_number.unique'   => 'رقم العملية مسجل مسبقاً، يرجى استخدام رقم آخر.',
            'trader_id.required'          => 'الرجاء اختيار التاجر.',
        ]);

        $newTransaction = null;

        DB::transaction(function () use ($request, &$newTransaction) {
            $data = $request->except(['_token', '_method']);

            // معالجة المرفق
            if ($request->hasFile('attachment')) {
                $data['attachment'] = $request->file('attachment')->store('attachments', 'public');
            } else {
                unset($data['attachment']);
            }

            // إسناد المستخدم الحالي
            if (empty($data['user_id']) && Auth::check()) {
                $data['user_id'] = Auth::id();
            }
            if (empty($data['created_by']) && Auth::check()) {
                $data['created_by'] = Auth::id();
            }

            // التعبئة التلقائية لاسم التاجر
            if (!empty($data['trader_id'])) {
                $trader = Trader::find($data['trader_id']);
                if ($trader) {
                    $data['trader_name'] = $trader->name ?? $trader->full_name ?? null;
                }
            }

            // معالجة السائق
            $driverName = $data['driver_name'] ?? $data['driver'] ?? null;
            if (!empty($driverName)) {
                $data['driver_name'] = $driverName;
                $data['driver'] = $driverName;

                $driverModel = null;
                if (Schema::hasColumn('drivers', 'full_name')) {
                    $driverModel = Driver::where('full_name', $driverName)->first();
                } elseif (Schema::hasColumn('drivers', 'driver_name')) {
                    $driverModel = Driver::where('driver_name', $driverName)->first();
                } elseif (Schema::hasColumn('drivers', 'name')) {
                    $driverModel = Driver::where('name', $driverName)->first();
                } else {
                    $driverModel = Driver::where('id', $driverName)->first();
                }

                if ($driverModel) {
                    $data['driver_id'] = $driverModel->id;
                }
            }

            // تعيين المرحلة الافتراضية
            $data['current_stage'] = $data['current_stage'] ?? 1;
            $data['status']        = $data['status'] ?? 'جديدة';

            // إذا تم تعليم المرحلة 1 كمكتملة
            if (!empty($request->stage_1_completed)) {
                $data['stage_1_completed']    = true;
                $data['stage_1_completed_at'] = now();
                $data['stage_1_user_id']      = Auth::id();
                $data['current_stage']        = 2;
                $data['progress_percentage']  = 16;
            }

            $newTransaction = Transaction::create($data);
            $newTransaction->recalculateProgress();
            $newTransaction->save();

            // تسجيل الإضافة في سجل العمليات
            DB::table('audit_logs')->insert([
                'user_id'        => Auth::id() ?? 1,
                'transaction_id' => $newTransaction->id,
                'action'         => 'إضافة سجل جديد',
                'description'    => 'تم إضافة معاملة جمركية جديدة برقم: ' . $newTransaction->transaction_number,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        });

        return redirect()->route('transactions.show', $newTransaction->id)
            ->with('success', 'تم إنشاء المعاملة الجمركية رقم (' . $newTransaction->transaction_number . ') بنجاح والبدء في المرحلة الأولى.');
    }

    /**
     * عرض تفاصيل المعاملة الجمركية مع سجل المراحل الكامل
     */
    public function show($id)
    {
        $this->checkPermission('مشاهدة العمليات');

        $transaction = Transaction::with([
            'trader', 'company', 'statusModel', 'driver', 'user', 'creator', 'employee',
            'stage1User', 'stage2User', 'stage3User', 'stage4User', 'stage5User', 'stage6User',
            'auditLogs.user'
        ])->findOrFail($id);

        $stagesConfig = Transaction::STAGES;

        return view('transactions.show', compact('transaction', 'stagesConfig'));
    }

    /**
     * عرض صفحة تعديل المعاملة الجمركية مقسمة حسب المراحل مع قيود الصلاحيات
     */
    public function edit($id)
    {
        $this->checkPermission(['مشاهدة العمليات', 'تعديل']);

        $transaction = Transaction::with([
            'stage1User', 'stage2User', 'stage3User', 'stage4User', 'stage5User', 'stage6User'
        ])->findOrFail($id);

        $traders   = Trader::all();
        $companies = Company::all();
        $statuses  = Status::all();
        $employees = User::all();
        $drivers   = Driver::all();
        $stagesConfig = Transaction::STAGES;

        // مصفوفة الصلاحيات المتاحة للمستخدم الحالي لكل مرحلة
        $userStagePermissions = [];
        $user = auth()->user();
        for ($i = 1; $i <= 6; $i++) {
            $userStagePermissions[$i] = $transaction->canUserEditStage($user, $i);
        }

        return view('transactions.edit', compact(
            'transaction',
            'traders',
            'companies',
            'statuses',
            'employees',
            'drivers',
            'stagesConfig',
            'userStagePermissions'
        ));
    }

    /**
     * تحديث بيانات المعاملة الجمركية مع تطبيق قيود الصلاحيات على كل مرحلة
     */
    public function update(Request $request, $id)
    {
        $this->checkPermission(['مشاهدة العمليات', 'تعديل']);

        $transaction = Transaction::findOrFail($id);

        $request->validate([
            'transaction_number' => 'required|string|max:255|unique:transactions,transaction_number,' . $transaction->id,
            'trader_id'          => 'required',
        ], [
            'transaction_number.required' => 'الرجاء إدخال رقم العملية.',
            'trader_id.required'          => 'الرجاء اختيار التاجر.',
        ]);

        $user = auth()->user();
        $isAdmin = $user->hasAnyRole(['admin', 'Admin', 'super-admin', 'Super Admin', 'مسؤول النظام']) || $user->can('تعديل كافة المراحل');

        DB::transaction(function () use ($request, $transaction, $user, $isAdmin) {
            $data = $request->except(['_token', '_method']);

            // معالجة المرفق
            if ($request->hasFile('attachment')) {
                $data['attachment'] = $request->file('attachment')->store('attachments', 'public');
            } else {
                unset($data['attachment']);
            }

            // التعبئة التلقائية لاسم التاجر
            if (!empty($data['trader_id'])) {
                $trader = Trader::find($data['trader_id']);
                if ($trader) {
                    $data['trader_name'] = $trader->name ?? $trader->full_name ?? null;
                }
            }

            // معالجة السائق
            $driverName = $data['driver_name'] ?? $data['driver'] ?? null;
            if (!empty($driverName)) {
                $data['driver_name'] = $driverName;
                $data['driver'] = $driverName;

                $driverModel = null;
                if (Schema::hasColumn('drivers', 'full_name')) {
                    $driverModel = Driver::where('full_name', $driverName)->first();
                } elseif (Schema::hasColumn('drivers', 'driver_name')) {
                    $driverModel = Driver::where('driver_name', $driverName)->first();
                } elseif (Schema::hasColumn('drivers', 'name')) {
                    $driverModel = Driver::where('name', $driverName)->first();
                } else {
                    $driverModel = Driver::where('id', $driverName)->first();
                }

                if ($driverModel) {
                    $data['driver_id'] = $driverModel->id;
                }
            }

            // معالجة حالة اكتمال المراحل وتاريخها والمستخدم المنفذ
            for ($i = 1; $i <= 6; $i++) {
                $canEditThisStage = $isAdmin || $transaction->canUserEditStage($user, $i);

                if ($canEditThisStage) {
                    $isCompletedNow = !empty($request->{"stage_{$i}_completed"});
                    $wasCompletedBefore = $transaction->{"stage_{$i}_completed"};

                    if ($isCompletedNow && !$wasCompletedBefore) {
                        $data["stage_{$i}_completed"] = true;
                        $data["stage_{$i}_completed_at"] = now();
                        $data["stage_{$i}_user_id"] = $user->id;
                    } elseif (!$isCompletedNow) {
                        $data["stage_{$i}_completed"] = false;
                        $data["stage_{$i}_completed_at"] = null;
                        $data["stage_{$i}_user_id"] = null;
                    }
                } else {
                    // إذا لم يكن للمستخدم صلاحية تعديل هذه المرحلة، نمنع تعديل حقولها
                    unset(
                        $data["stage_{$i}_completed"],
                        $data["stage_{$i}_notes"],
                        $data["stage_{$i}_delay_reason"]
                    );
                }
            }

            $transaction->fill($data);
            $transaction->recalculateProgress();
            $transaction->save();

            // تسجيل الحركة
            DB::table('audit_logs')->insert([
                'user_id'        => $user->id ?? 1,
                'transaction_id' => $transaction->id,
                'action'         => 'تعديل سجل',
                'description'    => 'تم تحديث بيانات المعاملة الجمركية رقم: ' . $transaction->transaction_number . ' (المرحلة الحالية: ' . $transaction->current_stage . ')',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        });

        return redirect()->route('transactions.show', $transaction->id)
            ->with('success', 'تم حفظ التحديثات بنجاح للمرحلة والمعاملة الجمركية.');
    }

    /**
     * تحديث مرحلة فردية (إكمال أو تعديل الملاحظات وسبب التأخير)
     */
    public function updateStage(Request $request, $id, $stage)
    {
        $stage = (int)$stage;
        if ($stage < 1 || $stage > 6) {
            abort(400, 'رقم المرحلة غير صحيح.');
        }

        $transaction = Transaction::findOrFail($id);
        $user = auth()->user();

        // التحقق الصارم من الصلاحيات حسب Spatie Permissions
        if (!$transaction->canUserEditStage($user, $stage)) {
            $stageConfig = Transaction::STAGES[$stage];
            abort(403, 'عذراً، لا تمتلك الصلاحية المطلوبة لتحديث (' . $stageConfig['name'] . '). مطلوب صلاحية: ' . $stageConfig['permission']);
        }

        $notes       = $request->input('notes');
        $delayReason = $request->input('delay_reason');
        $action      = $request->input('action', 'save_notes'); // save_notes or complete_stage or uncomplete_stage

        if ($action === 'complete_stage') {
            $transaction->markStageCompleted($stage, $notes, $delayReason, $user->id);
            $msg = 'تم اعتماد وإكمال ' . Transaction::STAGES[$stage]['name'] . ' بنجاح والانتقال للمرحلة التالية.';
        } elseif ($action === 'uncomplete_stage') {
            $transaction->{"stage_{$stage}_completed"} = false;
            $transaction->{"stage_{$stage}_completed_at"} = null;
            $transaction->{"stage_{$stage}_user_id"} = null;
            if ($notes !== null) $transaction->{"stage_{$stage}_notes"} = $notes;
            if ($delayReason !== null) $transaction->{"stage_{$stage}_delay_reason"} = $delayReason;
            $transaction->recalculateProgress();
            $transaction->save();
            $msg = 'تم إعادة فتح ' . Transaction::STAGES[$stage]['name'] . ' بنجاح.';
        } else {
            // حفظ الملاحظات وسبب التأخير فقط
            if ($notes !== null) $transaction->{"stage_{$stage}_notes"} = $notes;
            if ($delayReason !== null) $transaction->{"stage_{$stage}_delay_reason"} = $delayReason;
            $transaction->save();
            $msg = 'تم حفظ الملاحظات وسبب التأخير للمرحلة بنجاح.';
        }

        // تسجيل في سجل العمليات
        DB::table('audit_logs')->insert([
            'user_id'        => $user->id ?? 1,
            'transaction_id' => $transaction->id,
            'action'         => 'تحديث مرحلة',
            'description'    => 'تم إجراء تعديل على ' . Transaction::STAGES[$stage]['name'] . ' للمعاملة رقم: ' . $transaction->transaction_number . ' الإجراء: ' . $msg,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'current_stage' => $transaction->current_stage,
                'progress_percentage' => $transaction->progress_percentage,
                'status' => $transaction->status,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * حذف عملية جمركية
     */
    public function destroy($id)
    {
        $this->checkPermission(['مشاهدة العمليات', 'حذف']);

        $transaction = Transaction::findOrFail($id);
        $transNumber = $transaction->transaction_number;

        DB::transaction(function () use ($transaction, $transNumber) {
            $transId = $transaction->id;
            $transaction->delete();

            DB::table('audit_logs')->insert([
                'user_id'        => Auth::id() ?? 1,
                'transaction_id' => $transId,
                'action'         => 'حذف سجل',
                'description'    => 'تم حذف المعاملة الجمركية رقم: ' . $transNumber,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        });

        return redirect()->route('transactions.index')
            ->with('success', 'تم حذف العملية الجمركية بنجاح!');
    }

    /**
     * تصدير بيانات العمليات إلى CSV
     */
    public function export(Request $request)
    {
        $this->checkPermission(['مشاهدة العمليات', 'تصدير Excel']);

        $tab = $request->get('tab', 'all');
        $fileName = 'transactions_' . $tab . '_' . date('Y-m-d') . '.csv';

        $query = Transaction::with(['trader', 'company', 'statusModel', 'employee', 'driver']);
        if ($tab === 'active') {
            $query->active();
        } elseif ($tab === 'completed') {
            $query->completed();
        }

        $transactions = $query->get();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'رقم المعاملة',
            'التاجر',
            'الشركة الناقلة',
            'الوكيل الملاحي',
            'رقم البوليصة',
            'رقم البيان الجمركي',
            'المرحلة الحالية',
            'نسبة الإنجاز',
            'الحالة العامة',
            'السائق',
            'فاتورة العميل',
            'تاريخ الإنشاء',
            'تاريخ الإنجاز'
        ];

        $callback = function () use ($transactions, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns);

            foreach ($transactions as $tx) {
                $stageName = Transaction::STAGES[$tx->current_stage]['name'] ?? 'مرحلة ' . $tx->current_stage;
                fputcsv($file, [
                    $tx->transaction_number ?? '',
                    $tx->trader->name ?? $tx->trader_name ?? '',
                    $tx->company->name ?? $tx->company_name ?? '',
                    $tx->shipping_agent ?? '',
                    $tx->policy_number ?? '',
                    $tx->declaration_number ?? '',
                    $stageName,
                    ($tx->progress_percentage ?? 0) . '%',
                    $tx->status ?? '',
                    $tx->driver_name ?? $tx->driver ?? '',
                    $tx->client_invoice ?? 0,
                    $tx->created_at ? $tx->created_at->format('Y-m-d H:i') : '',
                    $tx->completed_at ? (is_object($tx->completed_at) ? $tx->completed_at->format('Y-m-d H:i') : $tx->completed_at) : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
