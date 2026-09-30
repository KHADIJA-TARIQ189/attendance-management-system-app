<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Sends every logged-in user to the dashboard for their role
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin())   return view('dashboard.admin');
        if ($user->isTeacher()) return view('dashboard.teacher', [
            'courses' => $user->coursesTaught()->withCount('students')->get(),
        ]);

        // student
        $courses = $user->courses()->get()->map(function ($course) use ($user) {
            $course->my_percent = $course->attendancePercentFor($user->id);
            return $course;
        });

        return view('dashboard.student', ['courses' => $courses]);
    }
}
