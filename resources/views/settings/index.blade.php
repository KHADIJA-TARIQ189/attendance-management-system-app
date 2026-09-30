@extends('layouts.app')
@section('title', 'Settings')
@section('content')
<h3>Settings</h3>
<div class="card col-md-7 col-lg-6">
  <div class="card-body">
    <h5>Change password</h5>
    <form method="POST" action="{{ route('settings.password') }}">
      @csrf @method('PUT')
      <div class="mb-3">
        <label class="form-label">Current password</label>
        <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
      </div>
      <div class="mb-3">
        <label class="form-label">New password</label>
        <input type="password" name="password" class="form-control" required minlength="6" autocomplete="new-password">
      </div>
      <div class="mb-3">
        <label class="form-label">Confirm new password</label>
        <input type="password" name="password_confirmation" class="form-control" required minlength="6" autocomplete="new-password">
      </div>
      <button class="btn btn-primary">Update password</button>
    </form>
  </div>
</div>
@endsection
