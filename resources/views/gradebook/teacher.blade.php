@extends('layouts.app')
@section('title', 'Gradebook')
@section('content')
<h3>{{ $course->code }} — {{ $course->title }}: Gradebook</h3>

<div class="row">
  <div class="col-md-6">
    <h5>Quizzes</h5>
    <form method="POST" action="{{ route('teacher.quizzes.store', $course) }}" class="row g-2 mb-3">
      @csrf
      <div class="col-4"><input name="title" class="form-control form-control-sm" placeholder="Quiz title" required></div>
      <div class="col-3"><input type="number" name="total_marks" class="form-control form-control-sm" placeholder="Total" required></div>
      <div class="col-3"><input type="date" name="quiz_date" class="form-control form-control-sm"></div>
      <div class="col-2"><button class="btn btn-sm btn-primary w-100">Add</button></div>
    </form>
    @foreach ($course->quizzes as $quiz)
      <div class="card mb-2"><div class="card-body">
        <strong>{{ $quiz->title }}</strong> <span class="text-muted">/{{ $quiz->total_marks }}</span>
        <form method="POST" action="{{ route('teacher.quizzes.marks', $quiz) }}">
          @csrf
          @foreach ($course->students as $s)
            @php $mark = $quiz->marks->firstWhere('student_id', $s->id); @endphp
            <div class="input-group input-group-sm my-1">
              <span class="input-group-text" style="width:120px">{{ $s->name }}</span>
              <input type="number" step="0.5" name="marks[{{ $s->id }}]" class="form-control" value="{{ $mark->marks_obtained ?? '' }}">
            </div>
          @endforeach
          <button class="btn btn-sm btn-outline-primary mt-1">Save Marks</button>
        </form>
      </div></div>
    @endforeach
  </div>

  <div class="col-md-6">
    <h5>Assignments</h5>
    <form method="POST" action="{{ route('teacher.assignments.store', $course) }}" class="row g-2 mb-3">
      @csrf
      <div class="col-4"><input name="title" class="form-control form-control-sm" placeholder="Assignment title" required></div>
      <div class="col-3"><input type="number" name="total_marks" class="form-control form-control-sm" placeholder="Total" required></div>
      <div class="col-3"><input type="date" name="due_date" class="form-control form-control-sm"></div>
      <div class="col-2"><button class="btn btn-sm btn-primary w-100">Add</button></div>
    </form>
    @foreach ($course->assignments as $assignment)
      <div class="card mb-2"><div class="card-body">
        <strong>{{ $assignment->title }}</strong> <span class="text-muted">/{{ $assignment->total_marks }}</span>
        <form method="POST" action="{{ route('teacher.assignments.marks', $assignment) }}">
          @csrf
          @foreach ($course->students as $s)
            @php $mark = $assignment->marks->firstWhere('student_id', $s->id); @endphp
            <div class="input-group input-group-sm my-1">
              <span class="input-group-text" style="width:120px">{{ $s->name }}</span>
              <input type="number" step="0.5" name="marks[{{ $s->id }}]" class="form-control" value="{{ $mark->marks_obtained ?? '' }}">
            </div>
          @endforeach
          <button class="btn btn-sm btn-outline-primary mt-1">Save Marks</button>
        </form>
      </div></div>
    @endforeach
  </div>
</div>
@endsection
