@extends('layouts.app')
@section('title', 'My Gradebook')
@section('content')
<h3>{{ $course->code }} — {{ $course->title }}: My Gradebook</h3>

<div class="row">
  <div class="col-md-6">
    <h5>Quizzes</h5>
    <table class="table bg-white">
      <thead><tr><th>Title</th><th>Marks</th></tr></thead>
      <tbody>
      @forelse ($quizzes as $quiz)
        <tr><td>{{ $quiz->title }}</td>
          <td>{{ $quiz->marks->first()->marks_obtained ?? '—' }} / {{ $quiz->total_marks }}</td></tr>
      @empty
        <tr><td colspan="2" class="text-muted">No quizzes yet.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div class="col-md-6">
    <h5>Assignments</h5>
    <table class="table bg-white">
      <thead><tr><th>Title</th><th>Marks</th></tr></thead>
      <tbody>
      @forelse ($assignments as $a)
        <tr><td>{{ $a->title }}</td>
          <td>{{ $a->marks->first()->marks_obtained ?? '—' }} / {{ $a->total_marks }}</td></tr>
      @empty
        <tr><td colspan="2" class="text-muted">No assignments yet.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
