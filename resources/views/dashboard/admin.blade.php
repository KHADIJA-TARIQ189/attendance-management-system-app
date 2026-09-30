@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<h3>Admin Dashboard</h3>
<p class="text-muted">Full control: manage users, courses, enrollments, and oversee attendance & gradebooks.</p>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card"><div class="card-body">
      <h5>Users</h5>
      <p class="text-muted">Create/remove admins, teachers, students. Assign LoRaWAN tag IDs.</p>
      <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary">Manage Users</a>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card"><div class="card-body">
      <h5>Courses</h5>
      <p class="text-muted">Create courses, assign a teacher, enroll students.</p>
      <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-primary">Manage Courses</a>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card"><div class="card-body">
      <h5>Attendance & Gradebook</h5>
      <p class="text-muted">Open any course below to view/edit attendance and marks, same as a teacher.</p>
      <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-outline-primary">Go to Courses</a>
    </div></div>
  </div>
</div>
@endsection
