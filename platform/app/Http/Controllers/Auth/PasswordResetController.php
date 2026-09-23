<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();

        if ($user && $user->is_active && $user->isStaffUser()) {
            try {
                $status = Password::sendResetLink(['email' => $data['email']]);
            } catch (\Throwable) {
                return back()->withErrors([
                    'email' => 'The reset email could not be sent. Ask an owner to set a new password.',
                ])->onlyInput('email');
            }

            if ($status !== Password::RESET_LINK_SENT) {
                return back()->withErrors(['email' => __($status)])->onlyInput('email');
            }
        }

        return back()->with('status', 'If that email is an active staff account, a reset link is on its way.');
    }

    public function edit(Request $request, string $token): View
    {
        return view('auth.reset', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                if (! $user->is_active || ! $user->isStaffUser()) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'email' => 'This account is disabled.',
                    ]);
                }

                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => __($status)])->onlyInput('email');
        }

        return redirect()->route('login')->with('status', 'Password updated. Log in with the new one.');
    }
}
