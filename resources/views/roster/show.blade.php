@extends('layouts.app')
@section('title', 'Roster')
@section('content')
<h3>{{ $course->code }} — {{ $course->title }}: Roster</h3>
<p class="text-muted">Teacher: {{ $course->teacher->name ?? 'not assigned yet' }}</p>

<div class="row">
  <div class="col-md-6">
    <h5>Enrolled Students ({{ $course->students->count() }})</h5>
    <table class="table bg-white">
      <thead><tr><th>Name</th><th>Roll No</th><th></th></tr></thead>
      <tbody>
      @forelse ($course->students->sortBy('name') as $s)
        <tr>
          <td>{{ $s->name }}</td>
          <td>{{ $s->roll_no }}</td>
          <td>
            <form action="{{ route('teacher.roster.unenroll', [$course, $s]) }}" method="POST" onsubmit="return confirm('Remove {{ $s->name }} from this course?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">Remove</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="3" class="text-muted">No students enrolled yet.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <div class="col-md-6">
    <h5>Enroll Students</h5>
    @if ($available->isEmpty())
      <p class="text-muted">No students left to enroll. @if (auth()->user()->isAdmin()) <a href="{{ route('admin.users.create') }}">Create a student user</a>. @endif</p>
    @else
      <form action="{{ route('teacher.roster.enroll', $course) }}" method="POST">
        @csrf
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" id="all" onclick="document.querySelectorAll('.pick').forEach(c => c.checked = this.checked)">
          <label class="form-check-label fw-semibold" for="all">Select all</label>
        </div>
        <div class="border rounded bg-white p-2 mb-2" style="max-height:320px;overflow:auto">
          @foreach ($available as $s)
            <div class="form-check">
              <input class="form-check-input pick" type="checkbox" name="student_ids[]" value="{{ $s->id }}" id="s{{ $s->id }}">
              <label class="form-check-label" for="s{{ $s->id }}">{{ $s->name }} ({{ $s->roll_no ?? 'no roll no' }})</label>
            </div>
          @endforeach
        </div>
        <button class="btn btn-primary">Enroll selected</button>
      </form>
    @endif
  </div>
</div>

<div class="mt-3">
  <a href="{{ route('teacher.attendance.show', $course) }}" class="btn btn-sm btn-outline-primary">Go to Attendance</a>
  <a href="{{ route('teacher.gradebook', $course) }}" class="btn btn-sm btn-outline-primary">Go to Gradebook</a>
</div>
@endsection
