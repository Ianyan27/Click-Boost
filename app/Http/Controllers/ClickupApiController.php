<?php

namespace App\Http\Controllers;

use Faker\Provider\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\CssSelector\Node\FunctionNode;

class ClickupApiController extends Controller
{

    public function index(){
        try {
            $response = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
            'Accept' => 'application/json'
        ])->get('https://api.clickup.com/api/v2/list/901612792997/task');

        $tasks = collect($response->json()['tasks'])->map(function ($task){
            return (object) [
                'name' => $task['name'],
                'status' => $task['status']['status'] ?? 'unknown',
                'assignees' => collect($task['assignees'])->pluck('username')->toArray()
            ];
        });

        $statusCounts = $tasks->pluck('status')->countBy();

        $responseTeam = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
            'Accept' => 'application/json'
        ])->get("https://api.clickup.com/api/v2/team");

        $members = collect($responseTeam->json()['teams'])
            ->flatMap(function($team){
                return $team['members'];
            })
            ->map(function($member){
                return (object) [
                    'username' => $member['user']['username'],
                    'email' => $member['user']['email']
                ];
            })
            ->unique('email')
            ->values();

        // Get task statistics per assignee
        $assigneeStats = $tasks
            ->flatMap(function($task) {
                // Create a row for each assignee in the task
                return collect($task->assignees)->map(function($assignee) use ($task) {
                    return [
                        'assignee' => $assignee,
                        'status' => $task->status
                    ];
                });
            })
            ->groupBy('assignee')
            ->map(function($assigneeTasks, $assignee) {
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
        return view('landing_page_folder.dashboard', compact('tasks', 'members', 'statusCounts', 'assigneeStats'));           
        } catch (\Exception $e) {
            return view('landing-page-folder.dashboard')->with('error', $e->getMessage());
        }
    }
    public function getTeams(){
        $responseTeam = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
            'Accept' => 'application/json'
        ])->get("https://api.clickup.com/api/v2/team");

        $teams = collect($responseTeam->json()['teams'])->map(function($team){
            return (object) [
                'id' => $team['id'],
                'name' => $team['name']
            ];
        });

        $teamId = collect($teams)->pluck('id')->first();

        $responseSpaces = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
            'Accept' => 'application/json'
        ])->get("https://api.clickup.com/api/v2/team/{$teamId}/space");

        $spaces = collect($responseSpaces->json()['spaces'])->map(function($space){
            return (object) [
                'id' => $space['id'],
                'name' => $space['name']
            ];
        });

        return view('pages.spaces_page', compact('spaces', 'teams'));

    }

    public function getFolders(){
        $responseSpaces = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
            'Accept' => 'application/json'
        ])->get("https://api.clickup.com/api/v2/team/9016484231/space");

        $spaces = collect($responseSpaces->json()['spaces'])->map(function ($space) {
            return (object) [
                'id'   => $space['id'],
                'name' => $space['name']
            ];
        });

        $folders = $spaces->flatMap(function ($space) {

            $responseFolder = Http::withHeaders([
                'Authorization' => env('CLICKUP_API_TOKEN'),
                'Accept' => 'application/json'
            ])->get("https://api.clickup.com/api/v2/space/{$space->id}/folder");

            return collect($responseFolder->json()['folders'] ?? [])
                ->map(function ($folder) use ($space) {

                    $lists = collect($folder['lists'] ?? [])
                        ->map(function ($list) {
                            return (object) [
                                'id'   => $list['id'],
                                'name' => $list['name']
                            ];
                        });

                    return (object) [
                        'id'          => $folder['id'],
                        'name'        => $folder['name'],
                        'lists_count' => $lists->count(),
                        'lists'       => $lists,
                        'space_id'    => $space->id,
                        'space_name'  => $space->name
                    ];
                });
        });

        return view('pages.folders_page', compact('folders', 'spaces'));
    }

    public function getLists(){
        $responseFolders = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
            'Accept' => 'application/json'
        ])->get('https://api.clickup.com/api/v2/space/90165960030/folder');

        $folders = collect($responseFolders->json()['folders'] ?? [])
            ->map(function ($folder) {
                return (object) [
                    'id'   => $folder['id'],
                    'name' => $folder['name']
                ];
            });

        $lists = $folders->flatMap(function ($folder) {

            $responseLists = Http::withHeaders([
                'Authorization' => env('CLICKUP_API_TOKEN'),
                'Accept' => 'application/json'
            ])->get("https://api.clickup.com/api/v2/folder/{$folder->id}/list");

            return collect($responseLists->json()['lists'] ?? [])
                ->map(function ($list) use ($folder) {

                    // ✅ GET TASKS UNDER THIS LIST
                    $responseTasks = Http::withHeaders([
                        'Authorization' => env('CLICKUP_API_TOKEN'),
                        'Accept' => 'application/json'
                    ])->get("https://api.clickup.com/api/v2/list/{$list['id']}/task");

                    $tasks = collect($responseTasks->json()['tasks'] ?? [])
                        ->map(function ($task) {
                            return (object) [
                                'id'   => $task['id'],
                                'name' => $task['name']
                            ];
                        });

                    return (object) [
                        'id'           => $list['id'],
                        'name'         => $list['name'],
                        'content'      => !empty($list['content']) ? $list['content'] : 'No Content.',
                        'due_date'     => isset($list['due_date'])
                            ? date('F d, Y', $list['due_date'] / 1000)
                            : null,
                        'task_count'   => $tasks->count(),
                        'tasks'        => $tasks,
                        'folder_id'    => $folder->id,
                        'folder_name'  => $folder->name
                    ];
                });
        });

        return view('pages.lists_page', compact('lists', 'folders'));
    }


    public function getTasks() {

        $responseTeam = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
            'Accept' => 'application/json'
        ])->get("https://api.clickup.com/api/v2/team");

        $members = collect($responseTeam->json()['teams'])
            ->flatMap(function($team){
                return $team['members'];
            })
            ->map(function($member){
                return (object) [
                    'id' => $member['user']['id'],
                    'username' => $member['user']['username'],
                    'email' => $member['user']['email']
                ];
            })
            ->unique('email')
            ->values();

        $responseLists = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
            'Accept' => 'application/json'
        ])->get('https://api.clickup.com/api/v2/folder/90167945606/list');

        $lists = collect($responseLists->json()['lists'])->map(function ($list) {
            return (object) [
                'id' => $list['id'],
                'name' => $list['name']
            ];
        });

        $tasks = $lists->flatMap(function ($list) {
            $responseTasks = Http::withHeaders([
                'Authorization' => env('CLICKUP_API_TOKEN'),
                'Accept' => 'application/json'
            ])->get("https://api.clickup.com/api/v2/list/{$list->id}/task");

            return collect($responseTasks->json()['tasks'] ?? [])->map(function ($task) use ($list) {
                return (object) [
                    'id'              => $task['id'],
                    'name'            => $task['name'],
                    'status'          => $task['status']['status'],
                    'description'     => $task['description'],
                    'due_date'        => date('F d, Y', $task['due_date'] / 1000),
                    'assignees'       => collect($task['assignees'] ?? [])->map(function ($assignee) {
                        return (object) [
                            'id'      => $assignee['id'],
                            'username'=> $assignee['username'],
                            'emai'    => $assignee['email']
                        ];
                    })->values(),
                    'list_id'         => $list->id,
                    'list_name'       => $list->name
            ];
            });
        });

        $taskStatuses = collect($tasks)->pluck('status')->unique()->sort()->values();

        return view('pages.tasks_page', compact('tasks', 'lists', 'members', 'taskStatuses'));
    }

    public function getMembers(){

        $responseTeam = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
            'Accept' => 'application/json'
        ])->get("https://api.clickup.com/api/v2/team");

        $members = collect($responseTeam->json()['teams'])
            ->flatMap(function($team){
                return $team['members'];
            })
            ->map(function($member){
                return (object) [
                    'username' => $member['user']['username'],
                    'email' => $member['user']['email']
                ];
            })
            ->unique('email')
            ->values();

        return view('pages.members_page', compact('members'));
    }

    public function createSpace(Request $request){

        $token = env('CLICKUP_API_TOKEN');

        $validated = $request->validate([
            'team' => 'required|numeric',
            'name' => 'required|string|max:255'
        ]);

        $teamId = $validated['team'];

        $response = Http::withHeaders([
            'Authorization' => $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post("https://api.clickup.com/api/v2/team/{$teamId}/space", [
            'name' => $validated['name'],
            'multiple_assignees' => true
        ]);

        Log::info($response);

        if($response->Failed()){
            Log::info($response);
            return back()->withErrors(['clickup_error' => 'Failed to create task in ClickUp.']);
        }

        return redirect()->back()->with('success', 'Space successfully created in ClickUp!');
    }

    public function createFolder(Request $request){

        $token = env('CLICKUP_API_TOKEN');

        $validated = $request->validate([
            'space' => 'required|numeric',
            'name' => 'required|string|max:255'
        ]);

        $spaceId = $validated['space'];

        $response = Http::withHeaders([
            'Authorization' => $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post("https://api.clickup.com/api/v2/space/{$spaceId}/folder", [
            'name' => $validated['name']
        ]);

        Log::info($response);

        if($response->Failed()){
            Log::info($response);
            return back()->withErrors(['clickup_error' => 'Failed to create task in ClickUp.']);
        }

        return redirect()->back()->with('success', 'Folder successfully created in ClikUp!');
    }

    public function createList(Request $request){
        
        $token = env('CLICKUP_API_TOKEN');

        $validated = $request->validate([
            'folder'      => 'required|numeric',
            'name'        => 'required|string|max:255',
            'content'     => 'nullable|string',
            'due_date'    => 'nullable|string',
        ]);

        $folderId = $validated['folder'];

        $duedate = $validated['due_date'] ? strtotime($validated['due_date']) * 1000 : null;

        $response = Http::withHeaders([
                'Authorization' => $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->post("https://api.clickup.com/api/v2/folder/{$folderId}/list", [
            'name' => $validated['name'],
            'content' => $validated['content'],
            'due_date' => $duedate,
        ]);

        if($response->Failed()){
            Log::info($response);
            return back()->withErrors(['clickup_error' => 'Failed to create task in ClickUp.'
        ]);
        }

        return redirect()->back()->with('success', 'Task successfully created in ClickUp!');

    }

    public function createTask(Request $request){

        $token = env('CLICKUP_API_TOKEN');

        $validated = $request->validate([
            'list'          => 'required|numeric',
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'due_date'      => 'nullable|string',
            'status'        => 'required|string',
            'assignees'     => 'nullable|array'
        ]);

        $listId = $validated['list'];

        $duedate = $validated['due_date'] ? strtotime($validated['due_date']) * 1000 : null;

        $assignees = array_map(
            'intval', $validated['assignees'] ?? []
        );

        Log::info('Assignees from request', [
            'assignees' => $assignees
        ]);


        $response = Http::withHeaders([
                'Authorization' => $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->post("https://api.clickup.com/api/v2/list/{$listId}/task", [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'due_date' => $duedate,
            'assignees' => $assignees       
        ]);

        Log::info('ClickUp response', [
            'status' => $response->status(),
            'body'   => $response->json()
        ]);

        if($response->Failed()){
            Log::info($response);
            return back()->withErrors(['clickup_error' => 'Failed to create task in ClickUp.'
        ]);
        }

        return redirect()->back()->with('success', 'Task successfully created in ClickUp!');
    }

    public function delete(Request $request){
        
        $request->validate([
            'endpoint' => 'required|string',
        ]);

        $endpoint = $request->endpoint;

        $response = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
            'Accept' => 'application/json',
        ])->delete("https://api.clickup.com/api/v2/{$endpoint}");
    
        if ($response->failed()) {
            return back()->withErrors([
                'delete_error' => 'Failed to delete item in ClickUp.'
            ]);
        }

        return back()->with('success', 'Item deleted successfully.');

    }
}
