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
        $response = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
            'Accept' => 'application/json'
        ])->get('https://api.clickup.com/api/v2/list/901612792997/task');

        $tasks = collect($response->json()['tasks'])->map(function ($task){
            return (object) [
                'name' => $task['name'],
                'status' => $task['status']['status'] ?? 'unknown'
            ];
        });

        $statusCounts = $tasks->pluck('status')->countBy();

        Log::info($tasks);
        Log::info($statusCounts);

        return view('landing_page_folder.dashboard', compact('tasks', 'statusCounts'));
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

        // $spaceId = $spaces->firstWhere('name', 'Click Boost')->id;

        // $responseFolder = Http::withHeaders([
        //     'Authorization' => env('CLICKUP_API_TOKEN'),
        //     'Accept' => 'application/json' 
        // ])->get("https://api.clickup.com/api/v2/space/{$spaceId}/folder");

        // $folders = collect($responseFolder->json()['folders'])->map(function($folder){
        //     return (object) [
        //         'id' => $folder['id'],
        //         'name' => $folder['name']
        //     ];
        // });

        // $folderId = $folders->firstWhere('name', 'Capstone Project')->id;

        // $responseLists = Http::withHeaders([
        //     'Authorization' => env('CLICKUP_API_TOKEN'),
        //     'Accept' => 'application/json'
        // ])->get("https://api.clickup.com/api/v2/folder/{$folderId}/list");

        // $lists = collect($responseLists->json()['lists'])->map(function ($list) {
        //     return (object) [
        //         'id' => $list['id'],
        //         'name' => $list['name']
        //     ];
        // });

        // Log::info($lists);

        //     return $lists;
        }

    public function getFolders(){

        $responseFolder = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
            'Accept' => 'application/json' 
        ])->get("https://api.clickup.com/api/v2/space/90165960030/folder");

        $folders = collect($responseFolder->json()['folders'])->map(function($folder){
            return (object) [
                'id' => $folder['id'],
                'name' => $folder['name']
            ];
        });

        $responseSpaces = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
            'Accept' => 'application/json'
        ])->get("https://api.clickup.com/api/v2/team/9016484231/space");

        $spaces = collect($responseSpaces->json()['spaces'])->map(function($space) {
            return (object) [
                'id' => $space['id'],
                'name' => $space['name']
            ];
        });

        return view('pages.folders_page', compact('folders', 'spaces'));
    }

    public function getLists(){

        $responseFolders = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
            'Accept' => 'application/json'
        ])->get('https://api.clickup.com/api/v2/space/90165960030/folder');

        $folders = collect($responseFolders->json()['folders'])->map(function($folder){
            return (object) [
                'id' => $folder['id'],
                'name' => $folder['name']
            ];
        });

        $responseLists = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
            'Accept' => 'application/json'
        ])->get('https://api.clickup.com/api/v2/folder/90167945606/list');

        $lists = collect($responseLists->json()['lists'])->map(function ($list) {
            return (object) [
                'name' => $list['name']
            ];
        });

        Log::info($lists);

        return view('pages.lists_page', compact('lists', 'folders'));
    }

    public function getTasks() {

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

        $responseTasks = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
            'Accept' => 'application/json'
        ])->get('https://api.clickup.com/api/v2/list/901612792997/task');

        $tasks = collect($responseTasks->json()['tasks'])->map(function ($task){
            return (object) [
                'name' => $task['name'],
                'status' => $task['status']['status']
            ];
        });

        $taskStatuses = collect($tasks)->pluck('status')->unique()->sort()->values();

        return view('pages.tasks_list_page', compact('tasks', 'lists', 'taskStatuses'));
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
            'status'        => 'required|string'
        ]);

        $listId = $validated['list'];

        $duedate = $validated['due_date'] ? strtotime($validated['due_date']) * 1000 : null;

        $response = Http::withHeaders([
                'Authorization' => $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->post("https://api.clickup.com/api/v2/list/{$listId}/task", [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'due_date' => $duedate,
        ]);

        if($response->Failed()){
            Log::info($response);
            return back()->withErrors(['clickup_error' => 'Failed to create task in ClickUp.'
        ]);
        }

        return redirect()->back()->with('success', 'Task successfully created in ClickUp!');
    }
}
