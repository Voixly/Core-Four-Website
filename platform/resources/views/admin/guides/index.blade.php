@extends('layouts.admin')
@section('title', 'Guides')
@section('content')
<div class="panel">
    <table>
        <tr><th>Title</th><th>Audience</th><th>Downloads</th><th>Active</th></tr>
        @foreach($guides as $guide)
            <tr>
                <td>{{ $guide->title }}</td>
                <td>{{ $guide->audience }}</td>
                <td>{{ $guide->downloads_count }}</td>
                <td>
                    <form method="post" action="{{ route('admin.guides.toggle', $guide) }}">@csrf<button class="btn" type="submit">{{ $guide->is_active ? 'On' : 'Off' }}</button></form>
                </td>
            </tr>
        @endforeach
    </table>
</div>
<div class="panel">
    <h3>Upload a guide</h3>
    <form method="post" action="{{ route('admin.guides.store') }}" enctype="multipart/form-data">
        @csrf
        <label>Title <input name="title" required></label>
        <label>Excerpt <textarea name="excerpt"></textarea></label>
        <label>Audience
            <select name="audience">
                <option value="residential">Residential</option>
                <option value="commercial">Commercial</option>
            </select>
        </label>
        <label>PDF <input type="file" name="file"></label>
        <button class="btn" type="submit">Add guide</button>
    </form>
</div>
@endsection
