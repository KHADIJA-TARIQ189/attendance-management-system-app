@extends('layouts.app')
@section('title', 'Mark Attendance')
@section('content')
<h3>{{ $course->code }} — {{ $course->title }}: Attendance</h3>

<form method="GET" class="row g-2 mb-3">
  <div class="col-auto">
    <input type="date" name="date" value="{{ $date }}" class="form-control" onchange="this.form.submit()">
  </div>
</form>

<form method="POST" action="{{ route('teacher.attendance.save', $course) }}">
  @csrf
  <input type="hidden" name="date" value="{{ $date }}">
  <table class="table bg-white">
    <thead><tr><th>Student</th><th>Present</th><th>Late</th><th>Absent</th></tr></thead>
    <tbody>
    @foreach ($students as $s)
      @php $cur = $existing[$s->id] ?? 'absent'; @endphp
      <tr>
        <td>{{ $s->name }} <span class="text-muted small">({{ $s->roll_no }})</span></td>
        @foreach (['present','late','absent'] as $opt)
          <td class="text-center">
            <input type="radio" name="status[{{ $s->id }}]" value="{{ $opt }}" {{ $cur === $opt ? 'checked' : '' }}>
          </td>
        @endforeach
      </tr>
    @endforeach
    </tbody>
  </table>
  <button class="btn btn-primary">Save Attendance</button>
</form>
@endsection
