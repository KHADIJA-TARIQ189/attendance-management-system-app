@extends('layouts.app')
@section('title', 'Users')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Users</h3>
  <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">+ New User</a>
</div>
<table class="table bg-white">
  <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Roll No</th><th>LoRa Tag</th><th></th></tr></thead>
  <tbody>
  @foreach ($users as $u)
    <tr>
      <td>{{ $u->name }}</td><td>{{ $u->email }}</td><td>{{ ucfirst($u->role) }}</td>
      <td>{{ $u->roll_no }}</td><td>{{ $u->lora_tag_id }}</td>
      <td>
        <form action="{{ route('admin.users.destroy', $u) }}" method="POST" onsubmit="return confirm('Remove this user?')">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-outline-danger">Remove</button>
        </form>
      </td>
    </tr>
  @endforeach
  </tbody>
</table>
{{ $users->links() }}
@endsection
