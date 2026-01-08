@extends('layouts.app')
@extends('layouts.modal.task-modal')
@extends('layouts.modal.view-data-modal.view-task-modal')

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
                        <th colspan="5">Task Name</th>
                        <th colspan="3">Lists Name</th>
                        <th colspan="2">Status</th>
                        <th colspan="2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        <tr>
                            <td colspan="5">{{ $task->name }}</td>
                            <td colspan="3">{{ $task->list_name }}</td>
                            <td colspan="2">{{ $task->status }}</td>
                            <td class="action-column" colspan="2">
                                <div class="action-buttons">
                                    <button class="view-task-btn"
                                        data-task = '@json($task)'>
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button>
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                    <button>
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

    document.querySelectorAll('.view-task-btn').forEach(button => {
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