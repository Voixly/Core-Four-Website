<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\CustomerInvite;
use App\Services\JobService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InviteController extends Controller
{
    public function show(string $token): View
    {
        $invite = CustomerInvite::query()->where('token', $token)->with('job')->firstOrFail();
        abort_unless($invite->isOpen(), 410);

        return view('account.invite', compact('invite'));
    }

    public function store(Request $request, string $token, JobService $jobs): RedirectResponse
    {
        $invite = CustomerInvite::query()->where('token', $token)->with('job')->firstOrFail();
        abort_unless($invite->isOpen(), 410);

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $user = $jobs->acceptInvite($invite, $data['password']);
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['password' => $e->getMessage()]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->away('/account/jobs/'.$invite->job_id.'/');
    }
}
