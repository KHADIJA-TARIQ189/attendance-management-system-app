<?php

use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ResultSaveController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ---------- SIDE MENU (all roles) ----------
    Route::get('/attendance', [MenuController::class, 'attendance'])->name('menu.attendance');
    Route::get('/gradebook', [MenuController::class, 'gradebook'])->name('menu.gradebook');
    Route::get('/profile', [MenuController::class, 'profile'])->name('profile');
    Route::put('/profile', [MenuController::class, 'updateProfile'])->name('profile.update');
    Route::get('/settings', [MenuController::class, 'settings'])->name('settings');
    Route::put('/settings/password', [MenuController::class, 'updatePassword'])->name('settings.password');

    // ---------- ADMIN ----------
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('courses', [CourseController::class, 'index'])->name('courses.index');
        Route::get('courses/create', [CourseController::class, 'create'])->name('courses.create');
        Route::post('courses', [CourseController::class, 'store'])->name('courses.store');
        Route::patch('courses/{course}/teacher', [CourseController::class, 'assignTeacher'])->name('courses.assign');
        Route::post('courses/{course}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
        Route::delete('courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    });

    // ---------- TEACHER (admin can also access, for oversight) ----------
    Route::middleware('role:teacher,admin')->group(function () {
        Route::get('courses/{course}/roster', [TeacherController::class, 'roster'])->name('teacher.roster.show');
        Route::post('courses/{course}/roster', [TeacherController::class, 'enrollStudent'])->name('teacher.roster.enroll');
        Route::delete('courses/{course}/roster/{student}', [TeacherController::class, 'unenrollStudent'])->name('teacher.roster.unenroll');

        Route::get('courses/{course}/attendance', [TeacherController::class, 'showAttendance'])->name('teacher.attendance.show');
        Route::post('courses/{course}/attendance', [ResultSaveController::class, 'saveAttendance'])->name('teacher.attendance.save');

        Route::get('courses/{course}/gradebook', [TeacherController::class, 'gradebook'])->name('teacher.gradebook');
        Route::post('courses/{course}/quizzes', [TeacherController::class, 'storeQuiz'])->name('teacher.quizzes.store');
        Route::post('courses/{course}/assignments', [TeacherController::class, 'storeAssignment'])->name('teacher.assignments.store');
        Route::post('quizzes/{quiz}/marks', [ResultSaveController::class, 'saveQuizMarks'])->name('teacher.quizzes.marks');
        Route::post('assignments/{assignment}/marks', [ResultSaveController::class, 'saveAssignmentMarks'])->name('teacher.assignments.marks');
    });

    // ---------- STUDENT (view-only) ----------
    Route::middleware('role:student')->group(function () {
        Route::get('my-courses/{course}/attendance', [StudentController::class, 'attendance'])->name('student.attendance');
        Route::get('my-courses/{course}/gradebook', [StudentController::class, 'gradebook'])->name('student.gradebook');
    });
});
