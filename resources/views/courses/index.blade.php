@extends('layouts.app')
@section('title', 'Courses')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Courses</h3>
  <a href="{{ route('admin.courses.create') }}" class="btn btn-primary btn-sm">+ New Course</a>
</div>
@if ($teachers->isEmpty())
  <div class="alert alert-warning">No teachers yet. <a href="{{ route('admin.users.create') }}">Create a teacher user</a> first, then assign them to a course here.</div>
@endif
<div class="table-responsive">
<table class="table bg-white align-middle">
  <thead><tr><th>Code</th><th>Title</th><th style="min-width:260px">Teacher</th><th>Students</th><th></th></tr></thead>
  <tbody>
  @forelse ($courses as $c)
    <tr>
      <td>{{ $c->code }}</td><td>{{ $c->title }}</td>
      <td>
        <form action="{{ route('admin.courses.assign', $c) }}" method="POST" class="d-flex gap-1">
          @csrf @method('PATCH')
          <select name="teacher_id" class="form-select form-select-sm">
            <option value="">— unassigned —</option>
            @foreach ($teachers as $t)
              <option value="{{ $t->id }}" @selected($c->teacher_id == $t->id)>{{ $t->name }}</option>
            @endforeach
          </select>
          <button class="btn btn-sm btn-success">Assign</button>
        </form>
      </td>
      <td>{{ $c->students_count }}</td>
      <td class="text-nowrap">
        <a class="btn btn-sm btn-primary" href="{{ route('teacher.roster.show', $c) }}">Enroll Students</a>
        <a class="btn btn-sm btn-outline-primary" href="{{ route('teacher.attendance.show', $c) }}">Attendance</a>
        <a class="btn btn-sm btn-outline-primary" href="{{ route('teacher.gradebook', $c) }}">Gradebook</a>
        <form action="{{ route('admin.courses.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete course?')">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
      </td>
    </tr>
  @empty
    <tr><td colspan="5" class="text-muted">No courses yet. Click "+ New Course".</td></tr>
  @endforelse
  </tbody>
</table>
</div>
@endsection
