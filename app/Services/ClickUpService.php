<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClickUpService {

    protected string $baseUrl;

    protected string $token;

    protected string $userFolderId;

    public function __construct() {
        $this->baseUrl = config('services.clickup.base_url');
        $this->token = config('services.clickup.token');
        $this->userFolderId = config('services.clickup.user_folder_id');
    }

    protected function request(): PendingRequest {
        return Http::withHeaders([
            'Authorization'     => $this->token,
            'Accept'            => 'application/json',
            'Content-Type'      => 'application/json'
        ])->timeout(30)->retry(2, 500);
    }

    public function get($endpoint){
        return $this->request()->get($this->baseUrl . $endpoint);
    }

    public function post($endpoint, array $data) {
        return $this->request()->post($this->baseUrl . $endpoint, $data);
    }

    public function getTeams() {
        return $this->request()->get($this->baseUrl . "/team");
    }

    public function syncWorkspaceMembersToUserFolder(){

        $responseMembers = $this->get("/team");

        $members = collect($responseMembers->json()['teams'])->flatMap(function($team) {
            return $team['members'];
        })->map(function ($member) {
            return (object) [
                'username' => $member['user']['username'] ?? '',
                'email' => $member['user']['email'],
            ];
        })->unique('email')->values();

        $responseList = $this->get("/folder/{$this->userFolderId}/list");

        $lists = collect($responseList->json()['lists'])->map(function ($list) {
            return (object) [
                'id' => $list['id'],
                'name' => $list['name']
            ];
        });

        $userTaskId = collect($lists)->pluck('id')->first();

        $responseList = $this->get("/list/{$userTaskId}/task");

        $users = collect($responseList->json()['tasks'])->map(function ($task){
            $customFields = collect($task['custom_fields']);
            return (object) [
                'name' => $task['name'],
                'role' => $customFields->firstWhere('name', 'Role')['value'] ?? null,
                'email' => $customFields->firstWhere('name', 'User Email')['value'] ?? null
            ];
        });

        Log::info('Mapped All Members: ' . $members->toJson());
        Log::info('Mapped Existing Users: ' . $users->toJson());

        // Find members that don't exist in the user folder
        $existingEmails = $users->pluck('email')->filter()->toArray();
        $missingMembers = $members->filter(function($member) use ($existingEmails) {
            return !in_array($member->email, $existingEmails);
        });

        Log::info('Missing Members to Create: ' . $missingMembers->toJson());

        // Create tasks for missing members
        foreach ($missingMembers as $member) {
            try {
                $taskData = [
                    'name' => $member->username ?: $member->email, // Fallback to email if username is empty
                    'custom_fields' => [
                        [
                            'id' => config('services.clickup.fields.user_email'),
                            'value' => $member->email
                        ],
                        [
                            'id' => config('services.clickup.fields.user_role'),
                            'value' => 'Member' // Default role
                        ]
                    ]
                ];

                $response = $this->post("/list/{$userTaskId}/task", $taskData);

                if ($response->successful()) {
                    Log::info("Successfully created task for user: {$member->email}");
                } else {
                    Log::error("Failed to create task for user: {$member->email}", [
                        'status' => $response->status(),
                        'response' => $response->json()
                    ]);
                }

            } catch (\Exception $e) {
                Log::error("Exception creating task for user: {$member->email}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Check if it's a duplicate error
                if (str_contains($e->getMessage(), 'duplicate') || str_contains($e->getMessage(), 'already exists')) {
                    Log::warning("Duplicate detected for user: {$member->email}");
                    continue; // Skip to next user
                }
                
                // For other critical errors, you might want to stop or continue based on your needs
                // throw $e; // Uncomment to stop on critical errors
            }
        }

        Log::info('User folder sync completed');
    }
}