<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOwnProfileRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller implements HasMiddleware
{
	public static function middleware(): array
	{
		return [
			'auth:admin',
		];
	}

	public function edit(): View
	{
		$admin = auth('admin')->user();
		return view('admin.profile.edit', compact('admin'));
	}

	public function update(UpdateOwnProfileRequest $request): RedirectResponse
	{
		$admin = auth('admin')->user();
		$data = $request->validated();
		if (!empty($data['password'])) {
			$data['password'] = Hash::make($data['password']);
		} else {
			unset($data['password']);
		}
		$admin->update($data);

		return redirect()->route('admin.profile.edit')->with('success', 'تم تحديث ملفك الشخصي بنجاح');
	}
}


?>

