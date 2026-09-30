@extends('layouts.app')
@section('title', ucfirst($type))
@section('content')
@php $isStudent = auth()->user()->isStudent(); @endphp
<h3>{{ $type === 'attendance' ? 'Attendance' : 'Gradebook' }}</h3>
<p class="text-muted">Choose a course to open its {{ $type }}.</p>
<div class="table-responsive">
<table class="table bg-white align-middle">
  <thead><tr><th>Code</th><th>Title</th><th></th></tr></thead>
  <tbody>
  @forelse ($courses as $c)
    <tr>
      <td>{{ $c->code }}</td>
      <td>{{ $c->title }}</td>
      <td>
        @if ($isStudent)
          <a class="btn btn-sm btn-primary" href="{{ route('student.' . $type, $c) }}">Open</a>
        @elseif ($type === 'attendance')
          <a class="btn btn-sm btn-primary" href="{{ route('teacher.attendance.show', $c) }}">Open</a>
        @else
          <a class="btn btn-sm btn-primary" href="{{ route('teacher.gradebook', $c) }}">Open</a>
        @endif
      </td>
    </tr>
  @empty
    <tr><td colspan="3" class="text-muted">No courses yet.</td></tr>
  @endforelse
  </tbody>
</table>
</div>
@endsection
