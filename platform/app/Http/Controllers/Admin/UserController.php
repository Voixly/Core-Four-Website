<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StaffInviteMail;
use App\Models\User;
use App\Support\SiteSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $actor = $request->user();
        $roles = $this->assignableRoles($actor);
        $users = User::query()
            ->whereIn('role', $this->manageableRoles($actor))
            ->orderByRaw("case role when 'admin' then 0 when 'agency' then 1 when 'owner' then 2 else 3 end")
            ->orderBy('name')
            ->get();

        $customers = $actor->isAdmin()
            ? User::query()->where('role', 'customer')->orderBy('name')->get()
            : collect();

        return view('admin.users.index', compact('users', 'roles', 'customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $roles = $this->assignableRoles($request->user());
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'role' => ['required', Rule::in($roles)],
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'password' => Str::password(40),
            'is_active' => true,
        ]);

        $sent = $this->sendInvite($user);

        return back()->with('success', $sent
            ? 'Invite sent to '.$user->email.'. They choose their own password from that email.'
            : $user->name.' was added, but the invite email did not send. Use Resend invite.');
    }

    public function invite(Request $request, User $user): RedirectResponse
    {
        $this->authorizeManage($request, $user);

        if (! $this->sendInvite($user)) {
            return back()->withErrors(['user' => 'The invite email did not send. Check the mail settings and try again.']);
        }

        return back()->with('success', 'Invite sent to '.$user->email.'.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        if (! $user->isCustomer()) {
            $this->authorizeManage($request, $user);
        }

        $name = $user->name;

        try {
            $user->delete();
        } catch (\Throwable) {
            return back()->withErrors(['user' => $name.' could not be deleted.']);
        }

        return back()->with('success', $name.' was deleted.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeManage($request, $user);
        $roles = $this->assignableRoles($request->user());

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'role' => ['required', Rule::in($roles)],
        ]);

        if ($user->id === $request->user()->id && $data['role'] !== $user->role) {
            return back()->withErrors(['role' => 'You cannot change your own role.']);
        }

        $user->update($data);

        return back()->with('success', 'Saved '.$user->name.'.');
    }

    public function password(Request $request, User $user): RedirectResponse
    {
        $this->authorizeManage($request, $user);

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->forceFill([
            'password' => $data['password'],
            'remember_token' => Str::random(60),
        ])->save();

        if ($user->id === $request->user()->id) {
            $request->session()->regenerate();
        }

        return back()->with('success', 'Password updated for '.$user->name.'.');
    }

    public function toggle(Request $request, User $user): RedirectResponse
    {
        $this->authorizeManage($request, $user);

        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'You cannot disable your own account.']);
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->name.' is '.($user->is_active ? 'active' : 'disabled').'.');
    }

    public function editOwn(): View
    {
        return view('admin.password');
    }

    public function updateOwn(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->forceFill([
            'password' => $data['password'],
            'remember_token' => Str::random(60),
        ])->save();

        $request->session()->regenerate();

        return back()->with('success', 'Your password is updated.');
    }

    private function sendInvite(User $user): bool
    {
        try {
            $token = Password::broker()->createToken($user);
            $path = route('password.reset', [
                'token' => $token,
                'email' => $user->email,
            ], false);
            $url = SiteSeo::origin().$path;

            Mail::to($user->email)->send(new StaffInviteMail($user, $url));
        } catch (\Throwable) {
            return false;
        }

        return true;
    }

    /**
     * @return list<string>
     */
    private function assignableRoles(User $actor): array
    {
        if ($actor->isAdmin()) {
            return ['admin', 'agency', 'owner', 'staff'];
        }

        return $actor->isAgency()
            ? ['agency', 'owner', 'staff']
            : ['owner', 'staff'];
    }

    /**
     * @return list<string>
     */
    private function manageableRoles(User $actor): array
    {
        return $this->assignableRoles($actor);
    }

    private function authorizeManage(Request $request, User $user): void
    {
        abort_unless($request->user()->canManageUsers(), 403);
        abort_unless(in_array($user->role, $this->manageableRoles($request->user()), true), 404);
    }
}
