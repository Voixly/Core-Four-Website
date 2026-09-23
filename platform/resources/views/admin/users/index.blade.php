@extends('layouts.admin')
@section('title', 'Users')
@section('meta', 'Admin, agency, owner, and staff logins')
@section('content')
<div class="panel">
    <h3>Add a login</h3>
    <p>Set a password and tell them. They can change it after login, or use Forgot password on the staff login page.</p>
    <form method="post" action="{{ route('admin.users.store') }}">
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
        <label>Password <input type="password" name="password" required minlength="8" autocomplete="new-password"></label>
        <label>Confirm password <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"></label>
        <button class="btn" type="submit">Add user</button>
    </form>
</div>

<div class="panel">
    <h3>People who can log in</h3>
    @foreach($users as $user)
        @php $form = 'user-'.$user->id; @endphp
        <article class="user-card">
            <div class="user-head">
                <div>
                    <strong>{{ $user->name }}</strong>
                    <p>{{ $user->email }}</p>
                </div>
                <div>
                    <span class="tag">{{ $user->role }}</span>
                    <span class="tag {{ $user->is_active ? 'tag-active' : 'tag-closed' }}">{{ $user->is_active ? 'Active' : 'Disabled' }}</span>
                </div>
            </div>
            <div class="user-forms">
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
            @if($user->id !== auth()->id())
                <form method="post" action="{{ route('admin.users.toggle', $user) }}" style="margin-top:0.75rem">
                    @csrf
                    <button class="btn btn-ghost" type="submit">{{ $user->is_active ? 'Disable' : 'Enable' }}</button>
                </form>
            @endif
        </article>
    @endforeach
</div>
@endsection
