@extends('layouts.app')
@extends('layouts.modal.task-modal')

@section('content')
<div class="clickup-container">
    <div class="task-container">
        <div class="button-section-container">
            <div>
                <span class="screen-title">Tasks</span>
            </div>
            <div>
                <button id="createTskBtn">
                    Create Task
                </button>
            </div>
        </div>
        <div class="clickup-table-container">
            <table>
                <thead>
                    <tr>
                        <th colspan="8">Task Name</th>
                        <th colspan="2">Status</th>
                        <th colspan="2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        <tr>
                            <td colspan="8">{{ $task->name }}</td>
                            <td colspan="2">{{ $task->status }}</td>
                            <td class="action-column" colspan="2">
                                <div class="action-buttons">
                                    <button>
                                        <i class="fa-solid fa-eye"></i>
                                        <span>View</span>
                                    </button>
                                    <button>
                                        <i class="fa-solid fa-pencil"></i>
                                        <span>Edit</span>
                                    </button>
                                    <button>
                                        <i class="fa-solid fa-trash"></i>
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    let createTaskBtn = document.getElementById('createTskBtn');
    let createTaskModal = document.getElementById('createTaskMdl');

    createTaskBtn.addEventListener('click', function(){
        createTaskModal.style.display = 'block';
        createTaskBtn.style.visibility = 'hidden';
        modalStatus = true;
    })
</script>
@endsection

<script src="{{ asset('js/modal/show-create-task-modal.js') }}"></script>