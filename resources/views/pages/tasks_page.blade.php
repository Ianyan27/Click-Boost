@extends('layouts.app')
@extends('layouts.modal.task-modal')
{{-- @extends('layouts.modal.view-data-modal.view-task-modal') --}}

@section('content')
<x-delete-modal 
    entity="Task"
    message="You're about to delete this task."
/>
<x-view-modal entity="Task" modalId="viewTaskMdl">
    <div class="view-task-dropdown-container">
        <label>List:</label>
        <input id="modalList" type="text" readonly>
    </div>
    <div class="view-task-name-container">
        <label>Task Name:</label>
        <input id="modalName" type="text" readonly>
    </div>
    <div class="view-task-description-container">
        <label>Description:</label>
        <input id="modalDescription" type="text" readonly>
    </div>
    <div class="view-task-due-date-container">
        <label>Due Date:</label>
        <input id="modalDueDate" type="text" readonly>
    </div>
    <div class="view-task-status-container">
        <label>Status:</label>
        <input id="modalStatus" type="text" readonly>
    </div>
    <div class="view-task-assignees-container">
        <label>Assigned Assignees:</label>
        <ul id="modalAssignees"></ul>
    </div>
</x-view-modal>
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
                        <th colspan="5">Task Name</th>
                        <th colspan="3" class="th-short-text">Lists Name</th>
                        <th colspan="2" class="th-short-text">Status</th>
                        <th colspan="2" class="th-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        <tr>
                            <td colspan="5">{{ $task->name }}</td>
                            <td colspan="3" class="th-short-text">{{ $task->list_name }}</td>
                            <td colspan="2" class="td-status">
                                <span class="status-badge {{ strtolower(str_replace(' ', '-', $task->status)) }}">
                                    {{ $task->status }}
                                </span>
                            </td>
                            <td class="action-column" colspan="2">
                                <div class="action-buttons">
                                    <button class="view-data-btn"
                                        data-task = '@json($task)'>
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button>
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                    <button class="delete-btn" data-entity="task" data-id="{{ $task->id }}" data-name="{{ $task->name }}">
                                        <i class="fa-solid fa-trash"></i>
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
    
    let viewTaskModal = document.getElementById('viewTaskMdl');

    createTaskBtn.addEventListener('click', function(){
        createTaskModal.style.display = 'block';
        createTaskBtn.style.visibility = 'hidden';
        // modalStatus = true;
    });

    document.getElementById('closePopUpModal').addEventListener('click', function(){
        createTaskModal.style.display = 'none';
        createTaskBtn.style.visibility = 'visible';
    })
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const modal = document.getElementById('viewTaskMdl');
        const closeBtn = document.getElementById('closeModal');

        document.querySelectorAll('.view-data-btn').forEach(button => {
            button.addEventListener('click', function () {

                const task = JSON.parse(this.dataset.task);

                document.getElementById('modalList').value = task.list_name;
                document.getElementById('modalName').value = task.name;
                document.getElementById('modalStatus').value = task.status;
                document.getElementById('modalDescription').value = task.description;
                document.getElementById('modalDueDate').value = task.due_date;
            
                const assigneesList = document.getElementById('modalAssignees');
                assigneesList.innerHTML = '';

                if (task.assignees.length === 0) {
                    assigneesList.innerHTML = '<li><em>Unassigned</em></li>';
                } else {
                    task.assignees.forEach(a => {
                        const li = document.createElement('li');
                        li.textContent = a.username || a.email || 'Unknown User';
                        assigneesList.appendChild(li);
                    });
                }

                modal.style.display = 'block';
            });
        });

    closeBtn.addEventListener('click', function () {
        modal.style.display = 'none';
    });
});
</script>
@endsection