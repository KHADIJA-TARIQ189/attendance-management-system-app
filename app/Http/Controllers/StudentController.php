<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    // View-only: a student may only ever look at their own course data
    protected function myCourse(int $courseId): Course
    {
        return Auth::user()->courses()->findOrFail($courseId);
    }

    public function attendance(Course $course)
    {
        $course = $this->myCourse($course->id);
        $records = $course->attendances()
            ->where('student_id', Auth::id())
            ->orderByDesc('date')
            ->get();
        $percent = $course->attendancePercentFor(Auth::id());

        return view('attendance.student', compact('course', 'records', 'percent'));
    }

    public function gradebook(Course $course)
    {
        $course = $this->myCourse($course->id);
        $studentId = Auth::id();

        $quizzes = $course->quizzes()->with(['marks' => fn ($q) => $q->where('student_id', $studentId)])->get();
        $assignments = $course->assignments()->with(['marks' => fn ($q) => $q->where('student_id', $studentId)])->get();

        return view('gradebook.student', compact('course', 'quizzes', 'assignments'));
    }
}
