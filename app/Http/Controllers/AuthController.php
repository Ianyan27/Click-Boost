<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Services\ClickUpService;
use Illuminate\Support\Facades\Log;

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
        Log::info('[GoogleCallback] Callback started');

        try {
            $googleUser = Socialite::driver('google')->user();

            Log::info('[GoogleCallback] Google user retrieved', [
                'google_id' => $googleUser->getId(),
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
            ]);

            $email = $googleUser->getEmail();

            if (!$email) {
                Log::warning('[GoogleCallback] No email returned from Google');
                return redirect()
                    ->route('login')
                    ->with('error', 'No email received from Google account.');
            }

            // Check if user exists in ClickUp
            Log::info('[GoogleCallback] Checking ClickUp user by email', [
                'email' => $email
            ]);

            $clickUpUser = $this->clickUpService->findUserByEmail($email);

            if ($clickUpUser) {
                Log::info('[GoogleCallback] ClickUp user authorized', [
                    'task_id' => $clickUpUser['task_id'],
                    'email' => $clickUpUser['email'],
                    'role' => $clickUpUser['role']
                ]);

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

                Log::info('[GoogleCallback] User session stored');

                return redirect()
                    ->route('dashboard')
                    ->with('success', 'Login successful!');
            }

            Log::warning('[GoogleCallback] Unauthorized user attempted login', [
                'email' => $email
            ]);

            return redirect()
                ->route('login')
                ->with('error', 'Unauthorized user. Your email is not registered in our system.');

        } catch (\Throwable $e) {
            Log::error('[GoogleCallback] Authentication failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()
                ->route('login')
                ->with('error', 'Authentication failed. Please try again.');
        }
    }

    public function dashboard()
    {
        // Check if user is authenticated (from original dashboard())
        if (!session('authenticated')) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user = session('user');
        
        // Double check user data exists (from original dashboard())
        if (!$user) {
            session()->flush();
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        // Now fetch and process data (from original index())
        try {
            $response = $this->clickUpService->get("/list/901612792997/task");

            $tasks = collect($response->json()['tasks'])->map(function ($task) {
                return (object) [
                    'name' => $task['name'],
                    'status' => $task['status']['status'] ?? 'unknown',
                    'assignees' => collect($task['assignees'])->pluck('username')->toArray()
                ];
            });

            $statusCounts = $tasks->pluck('status')->countBy();

            $responseTeam = $this->clickUpService->getTeams();

            $members = collect($responseTeam->json()['teams'])
                ->flatMap(function ($team) {
                    return $team['members'];
                })
                ->map(function ($member) {
                    return (object) [
                        'username' => $member['user']['username'],
                        'email' => $member['user']['email']
                    ];
                })
                ->unique('email')
                ->values();

            // Get task statistics per assignee
            $assigneeStats = $tasks
                ->flatMap(function ($task) {
                    // Create a row for each assignee in the task
                    return collect($task->assignees)->map(function ($assignee) use ($task) {
                        return [
                            'assignee' => $assignee,
                            'status' => $task->status
                        ];
                    });
                })
                ->groupBy('assignee')
                ->map(function ($assigneeTasks, $assignee) {
                    $statusCounts = collect($assigneeTasks)->pluck('status')->countBy();
                    $topStatus = $statusCounts->sortDesc()->keys()->first();
                    
                    return (object) [
                        'assignee' => $assignee,
                        'task_count' => $assigneeTasks->count(),
                        'top_status' => $topStatus,
                        'top_status_count' => $statusCounts[$topStatus],
                        'status_breakdown' => $statusCounts->toArray()
                    ];
                })
                ->values();

            // Return the view with all data (user + fetched data)
            return view('landing_page_folder.dashboard', compact('tasks', 'members', 'statusCounts', 'assigneeStats', 'user'));
        } catch (\Exception $e) {
            // Handle errors (from original index())
            return view('landing_page_folder.dashboard')->with('error', $e->getMessage());
        }
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login')->with('success', 'Logged out successfully!');
    }
}