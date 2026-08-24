<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // تجاوز الصلاحيات تلقائياً لدور الأدمن — يجعل @can و $user->can() و authorize() تمرر مباشرة للمسؤولين
        Gate::before(function ($user, $ability) {
            if ($user->hasAnyRole(['admin', 'Admin', 'super-admin', 'Super Admin', 'مسؤول النظام'])) {
                return true;
            }
        });
        // 1. التقاط عمليات الإنشاء (Created)
        \Illuminate\Database\Eloquent\Model::created(function ($model) {
            $className = class_basename($model);
            if ($className === 'AuditLog' || $className === 'ActivityLog') return;

            $identifier = $model->name ?? $model->transaction_number ?? $model->id ?? 'غير متوفر';

            DB::table('audit_logs')->insert([
                'user_id' => Auth::id() ?? 1,
                'action' => 'إضافة سجل جديد (' . $className . ')',
                'entity' => $className,
                'transaction_id' => $model->transaction_id ?? ($className === 'Transaction' ? $model->id : null),
                'description' => 'تم إضافة سجل جديد في ' . $className . ' (المعرف: ' . $identifier . ')',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        // 2. التقاط عمليات التحديث (Updated)
        \Illuminate\Database\Eloquent\Model::updated(function ($model) {
            $className = class_basename($model);
            if ($className === 'AuditLog' || $className === 'ActivityLog') return;

            $identifier = $model->name ?? $model->transaction_number ?? $model->id ?? 'غير متوفر';

            DB::table('audit_logs')->insert([
                'user_id' => Auth::id() ?? 1,
                'action' => 'تعديل سجل (' . $className . ')',
                'entity' => $className,
                'transaction_id' => $model->transaction_id ?? ($className === 'Transaction' ? $model->id : null),
                'description' => 'تم تحديث بيانات ' . $className . ' (المعرف: ' . $identifier . ')',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        // 3. التقاط عمليات الحذف (Deleted)
        \Illuminate\Database\Eloquent\Model::deleted(function ($model) {
            $className = class_basename($model);
            if ($className === 'AuditLog' || $className === 'ActivityLog') return;

            $identifier = $model->name ?? $model->transaction_number ?? $model->id ?? 'غير متوفر';

            DB::table('audit_logs')->insert([
                'user_id' => Auth::id() ?? 1,
                'action' => 'حذف سجل (' . $className . ')',
                'entity' => $className,
                'transaction_id' => $model->transaction_id ?? null,
                'description' => 'تم حذف عنصر من جدول ' . $className . ' (المعرف: ' . $identifier . ')',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}