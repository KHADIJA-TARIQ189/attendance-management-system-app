<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    // Courses this user can open: admin = all, teacher = own, student = enrolled
    protected function courses()
    {
        $u = Auth::user();
        if ($u->isAdmin())   return Course::orderBy('code')->get();
        if ($u->isTeacher()) return $u->coursesTaught()->orderBy('code')->get();
        return $u->courses()->orderBy('courses.code')->get();
    }

    public function attendance()
    {
        return view('menu.courses', ['type' => 'attendance', 'courses' => $this->courses()]);
    }

    public function gradebook()
    {
        return view('menu.courses', ['type' => 'gradebook', 'courses' => $this->courses()]);
    }

    public function profile()
    {
        return view('profile.show', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        ]);
        $user->update($data);
        return back()->with('status', 'Profile updated.');
    }

    public function settings()
    {
        return view('settings.index');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'min:6', 'confirmed'],
        ]);
        Auth::user()->update(['password' => $data['password']]); // hashed by the User model cast
        return back()->with('status', 'Password changed.');
    }
}
