<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index()
    {
        return view('courses.index', [
            'courses'  => Course::with('teacher')->withCount('students')->orderBy('code')->get(),
            'teachers' => User::where('role', 'teacher')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('courses.create', ['teachers' => User::where('role', 'teacher')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'unique:courses,code'],
            'title' => ['required', 'string'],
            'credit_hours' => ['required', 'integer', 'min:1', 'max:6'],
            'teacher_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'teacher')],
        ]);

        Course::create($data);
        return redirect()->route('admin.courses.index')->with('status', 'Course created.');
    }

    // Admin: assign (or change / remove) the teacher of an existing course
    public function assignTeacher(Request $request, Course $course)
    {
        $data = $request->validate([
            'teacher_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'teacher')],
        ]);
        $course->update(['teacher_id' => $data['teacher_id'] ?: null]);

        return back()->with('status', $data['teacher_id']
            ? "Teacher assigned to {$course->code}."
            : "Teacher removed from {$course->code}.");
    }

    public function enroll(Request $request, Course $course)
    {
        $data = $request->validate(['student_id' => ['required', 'exists:users,id']]);
        $course->students()->syncWithoutDetaching([$data['student_id']]);
        return back()->with('status', 'Student enrolled.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return back()->with('status', 'Course removed.');
    }
}
