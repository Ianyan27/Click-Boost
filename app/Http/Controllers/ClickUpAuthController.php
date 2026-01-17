<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ClickUpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ClickUpAuthController extends Controller
{
    private $clickUpService;
    private $listId;
    private $emailFieldId;
    private $roleFieldId;

    public function __construct(ClickUpService $clickUpService)
    {
        $this->clickUpService = $clickUpService;
        $this->listId = config('services.clickup.list_id');
        $this->emailFieldId = config('services.clickup.email_field_id');
        $this->roleFieldId = config('services.clickup.role_field_id');
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('landing_page_folder.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $name = $request->input('name');
        $email = $request->input('email');

        // Get all tasks (users) from Members List
        $tasks = $this->clickUpService->getListTasks($this->listId);

        // Check if user exists in the Members List
        $userTask = $this->clickUpService->findUserByEmail($tasks, $email, $this->emailFieldId);

        // If user doesn't exist, deny access
        if (!$userTask) {
            return back()
                ->withErrors(['email' => 'Email not found. Please contact administrator to register.'])
                ->withInput();
        }

        // Get role from custom fields
        $role = $this->clickUpService->getCustomFieldValue($userTask, $this->roleFieldId);

        // Create session for authenticated user
        Session::put('user', [
            'id' => $userTask['id'],
            'name' => $userTask['name'],
            'email' => $email,
            'role' => $role ?? 'member',
            'clickup_task_id' => $userTask['id']
        ]);

        Session::put('authenticated', true);

        return redirect()->intended('/dashboard');
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        Session::flush();
        return redirect('/login');
    }

    /**
     * Get authenticated user
     */
    public function user()
    {
        return Session::get('user');
    }

    /**
     * Sync team members to Members List
     * Creates tasks for team members who don't exist yet
     */
    public function syncMembers()
    {
        $result = $this->clickUpService->syncTeamMembersToList(
            $this->listId,
            $this->emailFieldId,
            $this->roleFieldId
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => "Sync completed successfully!",
                'created' => $result['created'],
                'skipped' => $result['skipped'],
                'total_created' => $result['total_created'],
                'total_skipped' => $result['total_skipped']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed',
                'error' => $result['error']
            ], 500);
        }
    }
}