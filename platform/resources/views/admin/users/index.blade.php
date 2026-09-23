@extends('layouts.admin')
@section('title', 'Users')
@section('meta', 'Admin, agency, owner, and staff logins')
@section('content')
<div class="panel">
    <h3>Invite someone</h3>
    <p>They get an email with a link to set their own password. The link lasts 60 minutes, and you can send it again from their row.</p>
    <form class="invite-grid" method="post" action="{{ route('admin.users.store') }}">
        @csrf
        <input type="hidden" name="form" value="create">
        <label>Name <input name="name" value="{{ old('form') === 'create' ? old('name') : '' }}" required></label>
        <label>Email <input type="email" name="email" value="{{ old('form') === 'create' ? old('email') : '' }}" required autocomplete="off"></label>
        <label>Phone <input name="phone" value="{{ old('form') === 'create' ? old('phone') : '' }}"></label>
        <label>Role
            <select name="role">
                @foreach($roles as $role)
                    <option value="{{ $role }}" @selected((old('form') === 'create' ? old('role') : 'staff') === $role)>{{ ucfirst($role) }}</option>
                @endforeach
            </select>
        </label>
        <button class="btn" type="submit">Send invite</button>
    </form>
</div>

<div class="panel">
    <h3>People who can log in</h3>
    @forelse($users as $user)
        @php $form = 'user-'.$user->id; $passwordForm = $form.'-password'; @endphp
        <article class="user-row">
            <div class="user-id">
                <strong>{{ $user->name }}</strong>
                <span>{{ $user->email }}@if($user->phone) · {{ $user->phone }}@endif</span>
            </div>
            <div class="user-actions">
                <span class="tag">{{ $user->role }}</span>
                <span class="tag {{ $user->is_active ? 'tag-active' : 'tag-closed' }}">{{ $user->is_active ? 'Active' : 'Disabled' }}</span>
                <form method="post" action="{{ route('admin.users.invite', $user) }}">
                    @csrf
                    <button class="btn btn-ghost" type="submit">Resend invite</button>
                </form>
                @if($user->id !== auth()->id())
                    <form method="post" action="{{ route('admin.users.toggle', $user) }}">
                        @csrf
                        <button class="btn btn-ghost" type="submit">{{ $user->is_active ? 'Disable' : 'Enable' }}</button>
                    </form>
                    @if(auth()->user()->isAdmin())
                        <form method="post" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm({{ json_encode('Delete '.$user->name.'? This cannot be undone.', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_THROW_ON_ERROR) }})">
                            @csrf
                            @method('delete')
                            <button class="btn btn-ghost" type="submit">Delete</button>
                        </form>
                    @endif
                @endif
            </div>
            <details class="user-edit" @if(old('form') === $form || old('form') === $passwordForm) open @endif>
                <summary class="btn btn-ghost">Edit</summary>
                <div class="user-edit-grid">
                    <form method="post" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('patch')
                        <input type="hidden" name="form" value="{{ $form }}">
                        <label>Name <input name="name" value="{{ old('form') === $form ? old('name') : $user->name }}" required></label>
                        <label>Email <input type="email" name="email" value="{{ old('form') === $form ? old('email') : $user->email }}" required></label>
                        <label>Phone <input name="phone" value="{{ old('form') === $form ? old('phone') : $user->phone }}"></label>
                        <label>Role
                            <select name="role" @disabled($user->id === auth()->id())>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" @selected((old('form') === $form ? old('role') : $user->role) === $role)>{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                        </label>
                        @if($user->id === auth()->id())
                            <input type="hidden" name="role" value="{{ $user->role }}">
                        @endif
                        <button class="btn" type="submit">Save</button>
                    </form>
                    <form method="post" action="{{ route('admin.users.password', $user) }}">
                        @csrf
                        <input type="hidden" name="form" value="{{ $form }}-password">
                        <label>New password <input type="password" name="password" required minlength="8" autocomplete="new-password"></label>
                        <label>Confirm password <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"></label>
                        <button class="btn" type="submit">Set password</button>
                    </form>
                </div>
            </details>
        </article>
    @empty
        <p>No logins yet.</p>
    @endforelse
</div>

@if(auth()->user()->isAdmin() && $customers->isNotEmpty())
    <div class="panel">
        <h3>Customer portal</h3>
        @foreach($customers as $user)
            <article class="user-row">
                <div class="user-id">
                    <strong>{{ $user->name }}</strong>
                    <span>{{ $user->email }}</span>
                </div>
                <div class="user-actions">
                    <span class="tag">Customer</span>
                    <form method="post" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm({{ json_encode('Delete '.$user->name.'? This cannot be undone.', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_THROW_ON_ERROR) }})">
                        @csrf
                        @method('delete')
                        <button class="btn btn-ghost" type="submit">Delete</button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>
@endif
@endsection
