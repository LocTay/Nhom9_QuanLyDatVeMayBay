<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    // Hiển thị trang liên hệ
    public function index()
    {
        return view('contact');
    }

    // Xử lý gửi thông tin liên hệ
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:5000',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'phone.regex' => 'Số điện thoại không hợp lệ.',
            'subject.required' => 'Vui lòng nhập chủ đề.',
            'message.required' => 'Vui lòng nhập nội dung liên hệ.',
        ]);

        $adminEmail = config('mail.from.address');

        if (!$adminEmail) {
            return back()
                ->withInput()
                ->with('error', 'Hệ thống chưa cấu hình email nhận liên hệ.');
        }

        $emailContent =
            "THÔNG TIN LIÊN HỆ SKYBOOKING\n\n" .
            "Họ và tên: {$validated['name']}\n" .
            "Email: {$validated['email']}\n" .
            "Số điện thoại: " . ($validated['phone'] ?? 'Không cung cấp') . "\n" .
            "Chủ đề: {$validated['subject']}\n\n" .
            "Nội dung:\n{$validated['message']}";

        try {
            Mail::raw($emailContent, function ($mail) use ($adminEmail, $validated) {
                $mail->to($adminEmail)
                    ->replyTo($validated['email'], $validated['name'])
                    ->subject('Liên hệ SkyBooking: ' . $validated['subject']);
            });

            return redirect()
                ->route('contact')
                ->with('success', 'Cảm ơn bạn! Thông tin liên hệ đã được gửi thành công.');

        } catch (\Throwable $e) {
            Log::error('Lỗi gửi email liên hệ SkyBooking: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Không thể gửi liên hệ lúc này. Vui lòng thử lại sau.');
        }
    }
}