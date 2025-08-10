<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->string('q');
        $userFilter = (string) $request->string('user_id');
        $deviceType = (string) $request->string('device_type');
        $platform = (string) $request->string('platform');

        $devices = Device::query()
            ->with(['user', 'token'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('device_name', 'like', "%{$search}%")
                        ->orWhere('device_type', 'like', "%{$search}%")
                        ->orWhere('platform', 'like', "%{$search}%")
                        ->orWhere('browser', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhereHas('user', function($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($userFilter !== '', function ($q) use ($userFilter) {
                $q->where('user_id', $userFilter);
            })
            ->when($deviceType !== '', function ($q) use ($deviceType) {
                $q->where('device_type', $deviceType);
            })
            ->when($platform !== '', function ($q) use ($platform) {
                $q->where('platform', $platform);
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        // Get filter options
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        $deviceTypes = Device::distinct()->pluck('device_type')->filter();
        $platforms = Device::distinct()->pluck('platform')->filter();

        return view('admin.devices.index', compact(
            'devices', 'search', 'userFilter', 'deviceType', 'platform',
            'users', 'deviceTypes', 'platforms'
        ));
    }

    public function create(): View
    {
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();

        $deviceTypes = ['mobile', 'tablet', 'desktop', 'other'];
        $platforms = ['iOS', 'Android', 'Windows', 'macOS', 'Linux', 'Web'];
        $browsers = ['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera', 'Other'];

        return view('admin.devices.create', compact('users', 'deviceTypes', 'platforms', 'browsers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'device_type' => ['required', 'string', 'max:255'],
            'device_name' => ['required', 'string', 'max:255'],
            'platform' => ['required', 'string', 'max:255'],
            'browser' => ['nullable', 'string', 'max:255'],
            'ip_address' => ['nullable', 'ip'],
        ]);

        // Check for duplicate device
        $existingDevice = Device::where('user_id', $data['user_id'])
            ->where('device_type', $data['device_type'])
            ->where('device_name', $data['device_name'])
            ->where('platform', $data['platform'])
            ->where('browser', $data['browser'] ?? '')
            ->first();

        if ($existingDevice) {
            return back()
                ->withInput()
                ->with('error', 'هذا الجهاز مسجل مسبقاً لهذا المستخدم');
        }

        Device::create($data);

        return redirect()->route('admin.devices.index')
            ->with('success', 'تم إضافة الجهاز بنجاح');
    }

    public function show(Device $device): View
    {
        $device->load(['user', 'token']);

        // Get device usage statistics
        $tokenInfo = null;
        if ($device->token) {
            $tokenInfo = [
                'created_at' => $device->token->created_at,
                'last_used_at' => $device->token->last_used_at,
                'expires_at' => $device->token->expires_at,
                'is_active' => $device->token->expires_at ? $device->token->expires_at->isFuture() : true,
            ];
        }

        return view('admin.devices.show', compact('device', 'tokenInfo'));
    }

    public function edit(Device $device): View
    {
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();

        $deviceTypes = ['mobile', 'tablet', 'desktop', 'other'];
        $platforms = ['iOS', 'Android', 'Windows', 'macOS', 'Linux', 'Web'];
        $browsers = ['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera', 'Other'];

        return view('admin.devices.edit', compact('device', 'users', 'deviceTypes', 'platforms', 'browsers'));
    }

    public function update(Request $request, Device $device): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'device_type' => ['required', 'string', 'max:255'],
            'device_name' => ['required', 'string', 'max:255'],
            'platform' => ['required', 'string', 'max:255'],
            'browser' => ['nullable', 'string', 'max:255'],
            'ip_address' => ['nullable', 'ip'],
        ]);

        // Check for duplicate device (excluding current device)
        $existingDevice = Device::where('user_id', $data['user_id'])
            ->where('device_type', $data['device_type'])
            ->where('device_name', $data['device_name'])
            ->where('platform', $data['platform'])
            ->where('browser', $data['browser'] ?? '')
            ->where('id', '!=', $device->id)
            ->first();

        if ($existingDevice) {
            return back()
                ->withInput()
                ->with('error', 'هذا الجهاز مسجل مسبقاً لهذا المستخدم');
        }

        $device->update($data);

        return redirect()->route('admin.devices.index')
            ->with('success', 'تم تحديث بيانات الجهاز بنجاح');
    }

    public function destroy(Device $device): RedirectResponse
    {
        // Revoke associated token if exists
        if ($device->token) {
            $device->token->delete();
        }

        $deviceName = $device->device_name;
        $userName = $device->user->name;

        $device->delete();

        return redirect()->route('admin.devices.index')
            ->with('success', "تم حذف جهاز '{$deviceName}' للمستخدم '{$userName}' بنجاح");
    }

    public function revokeToken(Device $device): RedirectResponse
    {
        if (!$device->token) {
            return back()->with('error', 'لا يوجد رمز مميز مرتبط بهذا الجهاز');
        }

        $device->token->delete();

        // Update device to remove token reference
        $device->update(['token_id' => null]);

        return back()->with('success', 'تم إلغاء الرمز المميز للجهاز بنجاح');
    }

    public function userDevices(User $user): View
    {
        $devices = $user->devices()
            ->with('token')
            ->latest('id')
            ->paginate(20);

        return view('admin.devices.user-devices', compact('user', 'devices'));
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $deviceIds = $request->validate([
            'device_ids' => ['required', 'array'],
            'device_ids.*' => ['exists:devices,id'],
        ])['device_ids'];

        $devices = Device::whereIn('id', $deviceIds)->with('token')->get();

        $deletedCount = 0;
        foreach ($devices as $device) {
            // Revoke token if exists
            if ($device->token) {
                $device->token->delete();
            }

            $device->delete();
            $deletedCount++;
        }

        return redirect()->route('admin.devices.index')
            ->with('success', "تم حذف {$deletedCount} جهاز بنجاح");
    }
}
