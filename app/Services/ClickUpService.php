<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClickUpService
{
    protected $baseUrl;
    protected $token;
    
    public function __construct()
    {
        $this->baseUrl = config('services.clickup.base_url');
        $this->token = config('services.clickup.token');
    }

    /**
     * Base request with common headers
     */
    protected function request()
    {
        return Http::withHeaders([
            'Authorization' => $this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])
        ->timeout(30)
        ->retry(2, 500);
    }

    /**
     * Generic GET request
     */
    public function get($endpoint)
    {
        return $this->request()->get($this->baseUrl . $endpoint);
    }

    /**
     * Get all team members
     */
    public function getTeamMembers()
    {
        try {
            $response = $this->request()->get("/team");

            if ($response->successful()) {
                $data = $response->json();
                
                // Get members from the first team
                if (isset($data['teams']) && count($data['teams']) > 0) {
                    return $data['teams'][0]['members'] ?? [];
                }
                
                return [];
            }

            Log::error('ClickUp API Error: Get Team Members', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('ClickUp Service Exception: Get Team Members', [
                'message' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get folder details
     */
    public function getFolder($folderId)
    {
        try {
            $response = $this->request()->get("/folder/{$folderId}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('ClickUp Service Exception: Get Folder', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get lists in a folder
     */
    public function getFolderLists($folderId)
    {
        try {
            $response = $this->request()->get("/folder/{$folderId}/list");

            if ($response->successful()) {
                return $response->json()['lists'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('ClickUp Service Exception: Get Folder Lists', [
                'message' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get tasks in a list
     */
    public function getListTasks($listId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => env('CLICKUP_API_TOKEN'),
                'Accept' => 'application/json'
            ])->get("https://api.clickup.com/api/v2/list/901613065989/task");

            if ($response->successful()) {
                return $response->json()['tasks'];
            }

            Log::error('ClickUp API Error: Get List Tasks', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('ClickUp Service Exception: Get List Tasks', [
                'message' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Sync team members to Members List
     * Creates tasks for team members who don't exist in the list
     */
    public function syncTeamMembersToList($listId, $emailFieldId, $roleFieldId)
    {
        try {
            // Get all team members
            $teamMembers = $this->getTeamMembers();
            
            // Get all existing tasks (users)
            $existingTasks = $this->getListTasks($listId);
            
            $created = [];
            $skipped = [];
            
            foreach ($teamMembers as $member) {
                $memberEmail = $member['user']['email'] ?? null;
                $memberName = $member['user']['username'] ?? $memberEmail;
                
                if (!$memberEmail) {
                    continue; // Skip members without email
                }
                
                // Check if user already exists in tasks
                $exists = $this->findUserByEmail($existingTasks, $memberEmail, $emailFieldId);
                
                if ($exists) {
                    $skipped[] = $memberEmail;
                } else {
                    // Create new task for this team member
                    $newTask = $this->createUserTask($listId, $memberName, $memberEmail, $emailFieldId, $roleFieldId);
                    
                    if ($newTask) {
                        $created[] = $memberEmail;
                    }
                }
            }
            
            return [
                'success' => true,
                'created' => $created,
                'skipped' => $skipped,
                'total_created' => count($created),
                'total_skipped' => count($skipped)
            ];
            
        } catch (\Exception $e) {
            Log::error('ClickUp Service Exception: Sync Team Members', [
                'message' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create a new task (user) in the Members List
     */
    public function createUserTask($listId, $name, $email, $emailFieldId, $roleFieldId)
    {
        try {
            $response = $this->request()->post("/list/{$listId}/task", [
                'name' => $name,
                'custom_fields' => [
                    [
                        'id' => $emailFieldId,
                        'value' => $email
                    ],
                    [
                        'id' => $roleFieldId,
                        'value' => 'member'
                    ]
                ]
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('ClickUp API Error: Create User Task', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('ClickUp Service Exception: Create User Task', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Find user task by email in custom fields
     */
    public function findUserByEmail($tasks, $email, $emailFieldId)
    {
        foreach ($tasks as $task) {
            if (isset($task['custom_fields'])) {
                foreach ($task['custom_fields'] as $field) {
                    if ($field['id'] === $emailFieldId && 
                        isset($field['value']) && 
                        strtolower($field['value']) === strtolower($email)) {
                        return $task;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Get custom field value by field ID
     */
    public function getCustomFieldValue($task, $fieldId)
    {
        if (isset($task['custom_fields'])) {
            foreach ($task['custom_fields'] as $field) {
                if ($field['id'] === $fieldId) {
                    return $field['value'] ?? null;
                }
            }
        }
        return null;
    }
}