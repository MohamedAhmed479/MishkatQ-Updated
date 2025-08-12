<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Models\Admin;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;

class AdminManagementController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            'auth:admin',
            new ControllerMiddleware('permission:admins.view', only: ['index']),
            new ControllerMiddleware('permission:admins.create', only: ['create', 'store']),
            new ControllerMiddleware('permission:admins.edit', only: ['edit', 'update']),
            new ControllerMiddleware('permission:admins.delete', only: ['destroy']),
        ];
    }

    public function index(): View
    {
        $query = Admin::query()->with(['roles', 'assignedBy']);
        $search = request('q');
        if ($search) {
            $term = '%' . trim($search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('email', 'like', $term);
            });
        }
        $admins = $query->latest()->paginate(15)->withQueryString();
        return view('admin/admins/index', compact('admins', 'search'));
    }

    public function create(): View
    {
        $roles = Role::where('guard_name', 'admin')->pluck('name', 'id');
        return view('admin/admins/create', compact('roles'));
    }

    public function store(StoreAdminRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['assigned_by'] = auth('admin')->id();
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $admin = Admin::create($data);

        if ($request->filled('roles')) {
            $admin->syncRoles(Role::whereIn('id', $request->input('roles', []))->get());
        }

        return redirect()->route('admin.admins.index')->with('success', 'تم إنشاء المشرف بنجاح');
    }

    public function edit(Admin $admin): View
    {
        $roles = Role::where('guard_name', 'admin')->pluck('name', 'id');
        $adminRoleIds = $admin->roles->pluck('id')->toArray();
        return view('admin/admins/edit', compact('admin', 'roles', 'adminRoleIds'));
    }

    public function update(UpdateAdminRequest $request, Admin $admin): RedirectResponse
    {
        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $admin->update($data);

        if ($request->filled('roles')) {
            $admin->syncRoles(Role::whereIn('id', $request->input('roles', []))->get());
        } else {
            $admin->syncRoles([]);
        }

        return redirect()->route('admin.admins.index')->with('success', 'تم تحديث بيانات المشرف');
    }

    public function destroy(Admin $admin): RedirectResponse
    {
        // Prevent self delete
        if ($admin->id === auth('admin')->id()) {
            return back()->withErrors(['لا يمكن حذف حسابك الشخصي']);
        }
        $admin->delete();
        return redirect()->route('admin.admins.index')->with('success', 'تم حذف المشرف');
    }
}


