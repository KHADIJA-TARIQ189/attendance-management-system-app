@extends('layouts.app')
@section('title', 'New User')
@section('content')
<h3>New User</h3>
<form method="POST" action="{{ route('admin.users.store') }}" class="col-md-6">
  @csrf
  <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
  <div class="mb-3">
    <label class="form-label">Role</label>
    <select name="role" class="form-select" required>
      <option value="student">Student</option>
      <option value="teacher">Teacher</option>
      <option value="admin">Admin</option>
    </select>
  </div>
  <div class="mb-3"><label class="form-label">Roll No (students)</label><input name="roll_no" class="form-control"></div>
  <div class="mb-3"><label class="form-label">LoRaWAN Tag ID (students, for auto attendance)</label><input name="lora_tag_id" class="form-control"></div>
  <button class="btn btn-primary">Create</button>
</form>
@endsection
