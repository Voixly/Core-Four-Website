@extends('layouts.admin')
@section('title', 'Users')
@section('content')
<div class="panel">
    <table>
        <tr><th>Name</th><th>Email</th><th>Role</th><th>Active</th></tr>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td>
                <td>
                    <form method="post" action="{{ route('admin.users.toggle', $user) }}">@csrf<button class="btn" type="submit">{{ $user->is_active ? 'Disable' : 'Enable' }}</button></form>
                </td>
            </tr>
        @endforeach
    </table>
</div>
<div class="panel">
    <h3>Invite staff</h3>
    <form method="post" action="{{ route('admin.users.store') }}">
        @csrf
        <label>Name <input name="name" required></label>
        <label>Email <input type="email" name="email" required></label>
        <label>Phone <input name="phone"></label>
        <label>Role
            <select name="role">
                <option value="staff">Staff</option>
                <option value="owner">Owner</option>
                <option value="agency">Agency</option>
            </select>
        </label>
        <label>Temp password <input name="password" required minlength="8"></label>
        <button class="btn" type="submit">Create user</button>
    </form>
</div>
@endsection
