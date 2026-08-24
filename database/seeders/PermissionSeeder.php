<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // إعادة ضبط تخزين الصلاحيات لضمان المزامنة
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'إضافة', 'تعديل', 'حذف', 'مشاهدة', 'طباعة', 
            'تصدير Excel', 'استيراد Excel', 'رفع مرفقات', 
            'حذف مرفقات', 'اعتماد', 'تغيير الحالة', 
            'إدارة المستخدمين', 'إدارة الإعدادات',
            'مشاهدة العمليات', 'مشاهدة التقارير', 'مشاهدة التجار',
            'مشاهدة الشركات', 'مشاهدة الحالات', 'مشاهدة سجل العمليات', 'إدارة الصلاحيات',
            'مرحلة 1: الاستلام والترقيم',
            'مرحلة 2: ربط البوليصة بالبيان',
            'مرحلة 3: التخليص والإجراءات الجمركية',
            'مرحلة 4: خروج الحاويات والنقل',
            'مرحلة 5: إعادة الحاويات الفارغة',
            'مرحلة 6: الإنجاز والفاتورة والمبيعات',
            'مشاهدة العمليات المكتملة',
            'تعديل كافة المراحل',
            'سير العمل والمراحل'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
        
        // إنشاء دور مدير النظام وإعطاؤه كل الصلاحيات
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // تعيين دور Admin لجميع المستخدمين الذين ليس لديهم دور بعد أو للمستخدم الرئيسي
        $users = User::all();
        foreach ($users as $user) {
            if ($user->roles->isEmpty()) {
                $user->assignRole($adminRole);
            }
        }
    }
}
