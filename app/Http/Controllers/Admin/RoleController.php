<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;

class RoleController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            'auth:admin',
            new ControllerMiddleware('permission:roles.view', only: ['index']),
            new ControllerMiddleware('permission:roles.create', only: ['store']),
            new ControllerMiddleware('permission:roles.edit', only: ['update']),
            new ControllerMiddleware('permission:roles.delete', only: ['destroy']),
        ];
    }

    public function index(): View
    {
        $roles = Role::where('guard_name', 'admin')->with('permissions')->paginate(20);
        $permissions = Permission::where('guard_name', 'admin')->get();
        return view('admin/roles/index', compact('roles', 'permissions'));
    }

    public function store(): RedirectResponse
    {
        request()->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => request('name'),
            'guard_name' => 'admin',
        ]);

        $role->syncPermissions(Permission::whereIn('id', request('permissions', []))->get());

        return back()->with('success', 'تم إنشاء الدور');
    }

    public function update(Role $role): RedirectResponse
    {
        request()->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->update(['name' => request('name')]);
        $role->syncPermissions(Permission::whereIn('id', request('permissions', []))->get());

        return back()->with('success', 'تم تحديث الدور');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name === 'Super Admin') {
            return back()->withErrors(['لا يمكن حذف دور المشرف العام']);
        }
        $role->delete();
        return back()->with('success', 'تم حذف الدور');
    }
}


