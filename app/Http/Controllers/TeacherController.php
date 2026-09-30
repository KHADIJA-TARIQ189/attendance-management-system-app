<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentMark;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizMark;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    // Lets a teacher touch only a course they own; admins may touch any
    // course (oversight access), matching the "role:teacher,admin" routes.
    protected function authorizedCourse(int $courseId): Course
    {
        if (Auth::user()->role === 'admin') {
            return Course::findOrFail($courseId);
        }
        return Course::where('teacher_id', Auth::id())->findOrFail($courseId);
    }

    public function roster(Course $course)
    {
        $course = $this->authorizedCourse($course->id);
        $course->load('students');

        $enrolledIds = $course->students->pluck('id');
        $available = User::where('role', 'student')
            ->whereNotIn('id', $enrolledIds)
            ->orderBy('name')
            ->get();

        return view('roster.show', ['course' => $course, 'available' => $available]);
    }

    public function enrollStudent(Request $request, Course $course)
    {
        $course = $this->authorizedCourse($course->id);
        $data = $request->validate([
            'student_ids'   => ['required', 'array', 'min:1'],
            'student_ids.*' => [Rule::exists('users', 'id')->where('role', 'student')],
        ], ['student_ids.required' => 'Select at least one student to enroll.']);

        $course->students()->syncWithoutDetaching($data['student_ids']);
        return back()->with('status', count($data['student_ids']) . ' student(s) enrolled.');
    }

    public function unenrollStudent(Course $course, User $student)
    {
        $course = $this->authorizedCourse($course->id);
        $course->students()->detach($student->id);
        return back()->with('status', $student->name . ' removed from course.');
    }

    public function showAttendance(Request $request, Course $course)
    {
        $course = $this->authorizedCourse($course->id);
        $date = $request->query('date', now()->toDateString());
        $students = $course->students()->orderBy('name')->get();

        $existing = Attendance::where('course_id', $course->id)->where('date', $date)
            ->pluck('status', 'student_id');

        return view('attendance.mark', compact('course', 'students', 'date', 'existing'));
    }

    public function saveAttendance(Request $request, Course $course)
    {
        $course = $this->authorizedCourse($course->id);
        $data = $request->validate([
            'date' => ['required', 'date'],
            'status' => ['required', 'array'],
            'status.*' => ['in:present,absent,late'],
        ]);

        foreach ($data['status'] as $studentId => $status) {
            Attendance::updateOrCreate(
                ['student_id' => $studentId, 'course_id' => $course->id, 'date' => $data['date']],
                ['status' => $status, 'marked_by' => Auth::id(), 'source' => 'manual']
            );
        }

        return back()->with('status', 'Attendance saved for ' . $data['date']);
    }

    public function gradebook(Course $course)
    {
        $course = $this->authorizedCourse($course->id);
        $course->load('quizzes.marks', 'assignments.marks', 'students');
        return view('gradebook.teacher', compact('course'));
    }

    public function storeQuiz(Request $request, Course $course)
    {
        $course = $this->authorizedCourse($course->id);
        $data = $request->validate([
            'title' => ['required', 'string'],
            'total_marks' => ['required', 'integer', 'min:1'],
            'quiz_date' => ['nullable', 'date'],
        ]);
        $course->quizzes()->create($data);
        return back()->with('status', 'Quiz added.');
    }

    public function storeAssignment(Request $request, Course $course)
    {
        $course = $this->authorizedCourse($course->id);
        $data = $request->validate([
            'title' => ['required', 'string'],
            'total_marks' => ['required', 'integer', 'min:1'],
            'due_date' => ['nullable', 'date'],
        ]);
        $course->assignments()->create($data);
        return back()->with('status', 'Assignment added.');
    }

    public function saveQuizMarks(Request $request, Quiz $quiz)
    {
        $this->authorizedCourse($quiz->course_id);
        $data = $request->validate(['marks' => ['required', 'array']]);
        foreach ($data['marks'] as $studentId => $marks) {
            QuizMark::updateOrCreate(
                ['quiz_id' => $quiz->id, 'student_id' => $studentId],
                ['marks_obtained' => $marks]
            );
        }
        return back()->with('status', 'Quiz marks saved.');
    }

    public function saveAssignmentMarks(Request $request, Assignment $assignment)
    {
        $this->authorizedCourse($assignment->course_id);
        $data = $request->validate(['marks' => ['required', 'array']]);
        foreach ($data['marks'] as $studentId => $marks) {
            AssignmentMark::updateOrCreate(
                ['assignment_id' => $assignment->id, 'student_id' => $studentId],
                ['marks_obtained' => $marks]
            );
        }
        return back()->with('status', 'Assignment marks saved.');
    }
}
