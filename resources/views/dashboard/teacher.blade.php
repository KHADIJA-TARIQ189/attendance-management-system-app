@extends('layouts.app')
@section('title', 'Teacher Dashboard')
@section('content')
<h3>My Courses</h3>
<table class="table bg-white">
  <thead><tr><th>Code</th><th>Title</th><th>Students</th><th>Roster</th><th>Attendance</th><th>Gradebook</th></tr></thead>
  <tbody>
  @forelse ($courses as $c)
    <tr>
      <td>{{ $c->code }}</td>
      <td>{{ $c->title }}</td>
      <td>{{ $c->students_count }}</td>
      <td><a class="btn btn-sm btn-outline-secondary" href="{{ route('teacher.roster.show', $c) }}">Manage</a></td>
      <td><a class="btn btn-sm btn-primary" href="{{ route('teacher.attendance.show', $c) }}">Mark / Edit</a></td>
      <td><a class="btn btn-sm btn-outline-primary" href="{{ route('teacher.gradebook', $c) }}">Open</a></td>
    </tr>
  @empty
    <tr><td colspan="6" class="text-muted">No courses assigned to you yet — ask the admin to assign one.</td></tr>
  @endforelse
  </tbody>
</table>
@endsection
