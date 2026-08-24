<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index()
    {
        $this->checkPermission('إدارة الصلاحيات');

        $roles = Role::with('permissions')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $this->checkPermission('إدارة الصلاحيات');

        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->checkPermission('إدارة الصلاحيات');

        $request->validate([
            'name' => 'required|string|unique:roles,name|max:255'
        ], [
            'name.required' => 'اسم الدور مطلوب',
            'name.unique' => 'هذا الدور موجود مسبقاً'
        ]);

        Role::create(['name' => $request->name]);

        // مسح كاش الصلاحيات فوراً حتى تُطبق التغييرات مباشرة
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->back()->with('success', 'تم إضافة الدور بنجاح');
    }

    public function storeAjax(Request $request)
    {
        $this->checkPermission('إدارة الصلاحيات');

        $request->validate(['name' => 'required|string|unique:roles,name']);

        $role = Role::create(['name' => $request->name]);

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'role' => $role
        ]);
    }

    public function show(Role $role)
    {
        $this->checkPermission('إدارة الصلاحيات');

        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $this->checkPermission('إدارة الصلاحيات');

        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $this->checkPermission('إدارة الصلاحيات');

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ]);

        $role->update(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        // مسح كاش الصلاحيات فوراً حتى تُطبق التغييرات مباشرة على جميع المستخدمين
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', 'تم تحديث الدور وصلاحياته بنجاح');
    }

    public function destroy(Role $role)
    {
        $this->checkPermission('إدارة الصلاحيات');

        $role->delete();

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', 'تم حذف الدور بنجاح');
    }
}
