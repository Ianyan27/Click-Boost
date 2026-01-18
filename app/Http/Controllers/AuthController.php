<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Services\ClickUpService;

class AuthController extends Controller
{
    protected $clickUpService;

    public function __construct(ClickUpService $clickUpService)
    {
        $this->clickUpService = $clickUpService;
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = $googleUser->getEmail();

            // Check if user exists in ClickUp
            $clickUpUser = $this->clickUpService->findUserByEmail($email);

            if ($clickUpUser) {
                // Store user info in session
                session([
                    'user' => [
                        'name' => $clickUpUser['name'],
                        'email' => $clickUpUser['email'],
                        'role' => $clickUpUser['role'],
                        'avatar' => $googleUser->getAvatar(),
                    ],
                    'authenticated' => true,
                ]);

                return redirect()->route('dashboard')->with('success', 'Login successful!');
            } else {
                return redirect()->route('login')->with('error', 'Unauthorized user. Your email is not registered in our system.');
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }

    public function dashboard()
    {
        $user = session('user');
        return view('dashboard', compact('user'));
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('user.login')->with('success', 'Logged out successfully!');
    }
}