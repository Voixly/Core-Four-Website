<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $keys = [
            'office_phone' => config('app.office_phone'),
            'office_address' => config('app.office_address'),
            'hours' => Setting::get('hours', 'Mon–Sat 7am–7pm · Emergency 24/7'),
            'notify_emails' => Setting::get('notify_emails', ''),
            'notify_phones' => Setting::get('notify_phones', ''),
            'chat_offline' => Setting::get('chat_offline', 'We are offline — leave a number and we will call you.'),
            'mail_from' => config('mail.from.address'),
        ];

        return view('admin.settings', compact('keys'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'office_phone' => ['nullable', 'string', 'max:40'],
            'office_address' => ['nullable', 'string', 'max:255'],
            'hours' => ['nullable', 'string', 'max:190'],
            'notify_emails' => ['nullable', 'string', 'max:255'],
            'notify_phones' => ['nullable', 'string', 'max:255'],
            'chat_offline' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($data as $key => $value) {
            Setting::put($key, $value);
        }

        return back()->with('success', 'Settings saved.');
    }
}
