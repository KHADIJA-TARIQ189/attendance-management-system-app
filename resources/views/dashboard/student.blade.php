@extends('layouts.app')
@section('title', 'Student Dashboard')
@section('content')
<h3>My Courses</h3>
<table class="table bg-white">
  <thead><tr><th>Code</th><th>Title</th><th>My Attendance %</th><th>Attendance</th><th>Gradebook</th></tr></thead>
  <tbody>
  @forelse ($courses as $c)
    <tr>
      <td>{{ $c->code }}</td>
      <td>{{ $c->title }}</td>
      <td>
        <span class="badge {{ $c->my_percent >= 75 ? 'bg-success' : 'bg-danger' }}">{{ $c->my_percent }}%</span>
      </td>
      <td><a class="btn btn-sm btn-outline-primary" href="{{ route('student.attendance', $c) }}">View</a></td>
      <td><a class="btn btn-sm btn-outline-primary" href="{{ route('student.gradebook', $c) }}">View</a></td>
    </tr>
  @empty
    <tr><td colspan="5" class="text-muted">You're not enrolled in any course yet.</td></tr>
  @endforelse
  </tbody>
</table>
@endsection
