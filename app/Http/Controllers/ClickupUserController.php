<?php

namespace App\Http\Controllers;

use App\Models\ClickupUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ClickupUserController extends Controller
{
    public function create() {
        return view('landing_page_folder.login');
    }

    public function check(Request $request){

        $request->validate([
            'email' => 'required|email',
        ]);

        $user = ClickupUser::where('email', $request->email)->first();
        $password = ClickupUser::where('password', $request->password)->first();

        if($user && Hash::check($request->password, $user->password)) {
            //Auth::login($user);

            $request->session()->put('name', $user->username);
            $request->session()->put('email', $user->email);
            $request->session()->put('role', $user->role);

            return redirect()->intended('/dashboard');
        } elseif ($user && !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Incorrect password.'
            ])->withInput($request->except('email'));
        } elseif (!$user) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.'
            ])->withInput($request->except('email'));
        }
    }

    public function store(Request $request){

        $request->validate([
            'username' => 'required|string',
            'email' => 'required|email|unique:clickup_user,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
        ]);

        // clickup endpoint to save the ticket on clickup as task
        
        ClickupUser::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->back()->with('success', 'User registered successfully!');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Session::flush();
        return redirect('/');
    }
}
