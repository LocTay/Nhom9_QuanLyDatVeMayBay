<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // Hiển thị thông tin tài khoản
    public function index()
    {
        $user = auth()->user();

        return view('profile.index', compact('user'));
    }

    // Hiển thị form chỉnh sửa hồ sơ
    public function edit()
    {
        $user = auth()->user();

        return view('profile.edit', compact('user'));
    }

    // Lưu thông tin hồ sơ
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:Nam,Nữ,Khác',
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được tài khoản khác sử dụng.',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'date_of_birth.date' => 'Ngày sinh không hợp lệ.',
            'date_of_birth.before' => 'Ngày sinh phải trước ngày hôm nay.',
            'gender.in' => 'Vui lòng chọn giới tính hợp lệ.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->date_of_birth = $validated['date_of_birth'] ?? null;
        $user->gender = $validated['gender'] ?? null;

        $user->save();

        return redirect()
            ->route('profile.index')
            ->with('success', 'Cập nhật thông tin cá nhân thành công!');
    }
}
