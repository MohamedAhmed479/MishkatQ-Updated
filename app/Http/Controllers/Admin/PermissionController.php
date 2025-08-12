<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;

class PermissionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            new ControllerMiddleware('permission:permissions.view', only: ['index']),
            new ControllerMiddleware('permission:permissions.create', only: ['store']),
            new ControllerMiddleware('permission:permissions.edit', only: ['update']),
            new ControllerMiddleware('permission:permissions.delete', only: ['destroy']),
        ];
    }

    public function index(): View
    {
        $permissions = Permission::where('guard_name', 'admin')->paginate(30);
        return view('admin/permissions/index', compact('permissions'));
    }

    public function store(): RedirectResponse
    {
        request()->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
        ]);

        Permission::create([
            'name' => request('name'),
            'guard_name' => 'admin',
        ]);

        return back()->with('success', 'تم إنشاء الصلاحية');
    }

    public function update(Permission $permission): RedirectResponse
    {
        request()->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
        ]);
        $permission->update(['name' => request('name')]);
        return back()->with('success', 'تم تحديث الصلاحية');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();
        return back()->with('success', 'تم حذف الصلاحية');
    }
}


