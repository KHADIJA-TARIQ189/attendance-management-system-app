@extends('layouts.app')
@section('title', 'Profile')
@section('content')
<h3>Profile</h3>
<div class="card col-md-7 col-lg-6">
  <div class="card-body">
    <form method="POST" action="{{ route('profile.update') }}">
      @csrf @method('PUT')
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email (Gmail)</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="input-group">
          <input type="password" class="form-control" value="••••••••" disabled>
          <a class="btn btn-outline-secondary" href="{{ route('settings') }}">Change</a>
        </div>
        <div class="form-text">Passwords are stored encrypted, so the real one can't be shown.</div>
      </div>
      <div class="mb-3">
        <label class="form-label">Role</label>
        <input class="form-control" value="{{ ucfirst($user->role) }}" disabled>
      </div>
      @if ($user->roll_no)
      <div class="mb-3">
        <label class="form-label">Roll No</label>
        <input class="form-control" value="{{ $user->roll_no }}" disabled>
      </div>
      @endif
      <button class="btn btn-primary">Save changes</button>
    </form>
  </div>
</div>
@endsection
