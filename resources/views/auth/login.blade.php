@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h4 class="card-title mb-3 text-center">Sign in</h4>
        <form method="POST" action="{{ route('login') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required autofocus value="{{ old('email') }}">
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="form-check mb-3">
            <input type="checkbox" name="remember" class="form-check-input" id="remember">
            <label class="form-check-label" for="remember">Remember me</label>
          </div>
          <button class="btn btn-primary w-100" type="submit">Login</button>
        </form>
        <p class="text-muted small mt-3 mb-0">
          Seeded demo accounts (password: <code>password</code>):<br>
          admin@school.test · teacher@school.test · student@school.test
        </p>
      </div>
    </div>
  </div>
</div>
@endsection
