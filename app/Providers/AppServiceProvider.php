<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator; // أضفنا هذه المكتبة لدعم الـ Pagination عبر Tailwind

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
        // 0. استخدام تصميم Tailwind كافتراضي لأزرار التنقل (Pagination) في كل المشروع
        Paginator::useTailwind();

        // 1. تجاوز الصلاحيات تلقائياً لدور الأدمن — يجعل @can و $user->can() و authorize() تمرر للمسؤولين
        Gate::before(function ($user, $ability) {
            if ($user->hasAnyRole(['admin', 'Admin', 'super-admin', 'Super Admin', 'مسؤول النظام'])) {
                return true;
            }
        });

        // 2. التقاط عمليات الإنشاء (Created) للـ Audit Logs
        \Illuminate\Database\Eloquent\Model::created(function ($model) {
            $className = class_basename($model);
            if ($className === 'AuditLog' || $className === 'ActivityLog') return;

            $identifier = $model->name ?? $model->username ?? $model->transaction_number ?? $model->id ?? 'غير متوفر';

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

        // 3. التقاط عمليات التحديث (Updated) للـ Audit Logs
        \Illuminate\Database\Eloquent\Model::updated(function ($model) {
            $className = class_basename($model);
            if ($className === 'AuditLog' || $className === 'ActivityLog') return;

            $identifier = $model->name ?? $model->username ?? $model->transaction_number ?? $model->id ?? 'غير متوفر';

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

        // 4. التقاط عمليات الحذف (Deleted) للـ Audit Logs
        \Illuminate\Database\Eloquent\Model::deleted(function ($model) {
            $className = class_basename($model);
            if ($className === 'AuditLog' || $className === 'ActivityLog') return;

            $identifier = $model->name ?? $model->username ?? $model->transaction_number ?? $model->id ?? 'غير متوفر';

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