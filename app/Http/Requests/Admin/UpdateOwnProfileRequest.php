<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOwnProfileRequest extends FormRequest
{
	public function authorize(): bool
	{
		return auth('admin')->check();
	}

	public function rules(): array
	{
		$adminId = auth('admin')->id();
		return [
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'email', 'max:255', 'unique:admins,email,' . $adminId],
			'password' => ['nullable', 'string', 'min:8', 'confirmed'],
		];
	}
}


?>

