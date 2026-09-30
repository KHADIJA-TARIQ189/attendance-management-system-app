@extends('layouts.app')
@section('title', 'My Attendance')
@section('content')
<h3>{{ $course->code }} — {{ $course->title }}</h3>
<p>Overall attendance: <span class="badge {{ $percent >= 75 ? 'bg-success' : 'bg-danger' }} fs-6">{{ $percent }}%</span>
@if ($percent < 75) <span class="text-danger">(below the 75% threshold)</span>@endif
</p>
<table class="table bg-white">
  <thead><tr><th>Date</th><th>Status</th><th>Source</th></tr></thead>
  <tbody>
  @forelse ($records as $r)
    <tr>
      <td>{{ $r->date->format('d M Y') }}</td>
      <td><span class="badge bg-{{ $r->status === 'present' ? 'success' : ($r->status === 'late' ? 'warning' : 'danger') }}">{{ ucfirst($r->status) }}</span></td>
      <td>{{ $r->source === 'lorawan' ? '📡 Auto (LoRaWAN)' : 'Manual' }}</td>
    </tr>
  @empty
    <tr><td colspan="3" class="text-muted">No records yet.</td></tr>
  @endforelse
  </tbody>
</table>
@endsection
