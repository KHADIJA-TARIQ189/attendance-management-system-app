<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', ['users' => User::orderBy('role')->orderBy('name')->paginate(20)]);
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6'],
            'role' => ['required', 'in:admin,teacher,student'],
            'roll_no' => ['nullable', 'string', 'unique:users,roll_no'],
            'lora_tag_id' => ['nullable', 'string', 'unique:users,lora_tag_id'],
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('admin.users.index')->with('status', 'User created.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('status', 'User removed.');
    }
}
