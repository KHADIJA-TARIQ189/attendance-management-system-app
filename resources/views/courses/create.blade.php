@extends('layouts.app')
@section('title', 'New Course')
@section('content')
<h3>New Course</h3>
<form method="POST" action="{{ route('admin.courses.store') }}" class="col-md-6">
  @csrf
  <div class="mb-3"><label class="form-label">Code</label><input name="code" class="form-control" required placeholder="CS-301"></div>
  <div class="mb-3"><label class="form-label">Title</label><input name="title" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Credit Hours</label><input type="number" name="credit_hours" class="form-control" value="3" required></div>
  <div class="mb-3">
    <label class="form-label">Teacher</label>
    <select name="teacher_id" class="form-select">
      <option value="">— unassigned —</option>
      @foreach ($teachers as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
    </select>
  </div>
  <button class="btn btn-primary">Create</button>
</form>
@endsection
