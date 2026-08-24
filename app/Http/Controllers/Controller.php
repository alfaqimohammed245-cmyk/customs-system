<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * التحقق من امتلاك المستخدم لدور إداري أو صلاحية محددة
     */
    protected function checkPermission($permissions)
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(403, 'غير مسجل دخول');
        }

        // إذا كان المشرف أو الأدمن
        if ($user->hasAnyRole(['admin', 'Admin', 'super-admin', 'Super Admin'])) {
            return true;
        }

        $permissions = is_array($permissions) ? $permissions : [$permissions];
        foreach ($permissions as $perm) {
            if ($user->can($perm)) {
                return true;
            }
        }

        abort(403, 'عذراً، لا تمتلك الصلاحية الكافية للقيام بهذا الإجراء.');
    }
}
