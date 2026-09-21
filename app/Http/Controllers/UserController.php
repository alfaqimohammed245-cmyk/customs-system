<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class UserController extends Controller
{
    public function index()
    {
        $this->checkPermission('إدارة المستخدمين');

        // جلب المستخدمين مع الأدوار والصلاحيات وتقسيمهم إلى صفحات (10 لكل صفحة)
        $users = User::with(['roles', 'permissions'])->latest()->paginate(10);
        
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->checkPermission('إدارة المستخدمين');
        $roles = Role::all();
        $permissions = Permission::all();
        return view('users.create', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $this->checkPermission('إدارة المستخدمين');

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'username'    => 'required|string|max:255|unique:users,username',
            'email'       => 'nullable|string|email|max:255|unique:users,email',
            'password'    => 'required|string|min:6',
            'phone'       => 'nullable|string|max:255',
            'department'  => 'nullable|string|max:255',
            'job_title'   => 'nullable|string|max:255',
            'color'       => 'nullable|string|max:255',
            'role'        => 'nullable|string|exists:roles,name',
            'permissions' => 'array',
        ]);

        $validated['password'] = Hash::make($request->password);
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $roleName = $request->input('role');
        unset($validated['role']);

        $permissionsInput = $request->input('permissions', []);
        unset($validated['permissions']);

        $user = User::create($validated);

        if ($roleName) {
            $user->syncRoles([$roleName]);
        }

        $user->syncPermissions($permissionsInput);

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        AuditLog::create([
            'user_id'     => Auth::id() ?? 1,
            'action'      => 'إضافة مستخدم',
            'description' => 'تم إضافة مستخدم جديد وتحديد صلاحياته: ' . $user->name,
        ]);

        return redirect()->route('users.index')->with('success', 'تم إضافة المستخدم وتحديد صلاحياته بنجاح');
    }

    public function edit(User $user)
    {
        $this->checkPermission('إدارة المستخدمين');
        $roles = Role::all();
        $permissions = Permission::all();
        return view('users.edit', compact('user', 'roles', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        $this->checkPermission('إدارة المستخدمين');

        $rules = [
            'name'        => 'required|string|max:255',
            'username'    => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'       => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'phone'       => 'nullable|string|max:255',
            'department'  => 'nullable|string|max:255',
            'job_title'   => 'nullable|string|max:255',
            'color'       => 'nullable|string|max:255',
            'role'        => 'nullable|string|exists:roles,name',
            'permissions' => 'array',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6';
        }

        $validated = $request->validate($rules);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $roleName = $request->input('role');
        unset($validated['role']);

        $permissionsInput = $request->input('permissions', []);
        unset($validated['permissions']);

        $user->update($validated);

        if ($roleName) {
            $user->syncRoles([$roleName]);
        } else {
            $user->syncRoles([]);
        }

        $user->syncPermissions($permissionsInput);

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        AuditLog::create([
            'user_id'     => Auth::id() ?? 1,
            'action'      => 'تعديل مستخدم',
            'description' => 'تم تحديث بيانات وصلاحيات المستخدم: ' . $user->name,
        ]);

        return redirect()->route('users.index')->with('success', 'تم تحديث صلاحيات المستخدم بنجاح');
    }

    public function destroy(User $user)
    {
        $this->checkPermission('إدارة المستخدمين');

        if (auth()->id() === $user->id) {
            return back()->with('error', 'لا يمكنك حذف حسابك الحالي.');
        }

        AuditLog::create([
            'user_id'     => Auth::id() ?? 1,
            'action'      => 'حذف مستخدم',
            'description' => 'تم حذف حساب المستخدم: ' . $user->name,
        ]);

        $user->delete();
        
        return redirect()->route('users.index')->with('status', 'تم حذف المستخدم بنجاح');
    }
}
