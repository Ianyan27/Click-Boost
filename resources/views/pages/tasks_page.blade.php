@extends('layouts.app')
@extends('layouts.modal.task-modal')
{{-- @extends('layouts.modal.view-data-modal.view-task-modal') --}}

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modal/edit-modal.css') }}">
@endpush

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
<div class="edit-modal" id="editModal" style="display:none;">
<div class="edit-modal-overlay" onclick="closeEditModal()"></div>
    <div class="edit-modal-content">
        <div class="edit-modal-header">
            <h3>Edit Task</h3>
            <button class="close-btn" onclick="closeEditModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form class="edit-task" id="editTaskForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="editTaskName">Task Name</label>
                <input 
                    type="text" 
                    id="editTaskName" 
                    name="name" 
                    class="form-input"
                    placeholder="Enter task name"
                    required
                >
            </div>
            <div class="form-group">
                <label for="editTaskStatus">Status</label>
                <select id="editTaskStatus" name="status" class="form-input" required>
                    @foreach ($taskStatuses as $task)
                        <option value="{{ $task }}">{{ $task }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="editTaskDueDate">Due Date</label>
                <input 
                    type="date" 
                    id="editTaskDueDate" 
                    name="due_date" 
                    class="form-input"
                    />
            </div>
            <input type="hidden" id="originalAssignees" name="original_assignees" value="">

            <div class="form-group">
                <label>Current Assignees</label>
                <ul id="editTaskAssignees" class="current-assignees-list"></ul>
            </div>

            <div class="form-group">
                <label>Add Assignees</label>
                <ul id="editTaskAddAssignees" class="add-assignees-list"></ul>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-save">
                    <i class="fa-solid fa-floppy-disk"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
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
                                    <button class="edit-data-btn"
                                        data-task = '@json($task)'
                                        onclick="openTaskEditModal(this)">
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

    const allMembers = @json($members);

    function openTaskEditModal(btn) {
        const task = JSON.parse(btn.getAttribute('data-task'));

        document.getElementById('editTaskName').value = task.name;
        document.getElementById('editTaskStatus').appendChild(new Option(task.status, task.status, true, true));

        const cleaned = task.due_date.replace('.', ',');
        const date = new Date(cleaned);
        const formatted = [
            date.getFullYear(),
            String(date.getMonth() + 1).padStart(2, '0'),
            String(date.getDate()).padStart(2, '0')
        ].join('-');

        const assigneesList = document.getElementById('editTaskAssignees');
        const addAssigneesList = document.getElementById('editTaskAddAssignees');
        assigneesList.innerHTML = '';
        addAssigneesList.innerHTML = '';

        const currentIds = task.assignees.map(a => String(a.id));

        // Store original IDs so controller can diff
        document.getElementById('originalAssignees').value = JSON.stringify(currentIds);

        task.assignees.forEach(element => {
            const name = element.username || element.email || 'Unknown User';
            addAssigneeTag(assigneesList, element.id, name);
        });

        const available = allMembers.filter(m => !currentIds.includes(String(m.id)));
        if (available.length === 0) {
            addAssigneesList.innerHTML = '<li class="no-available-members">All members are already assigned.</li>';
        } else {
            available.forEach(member => {
                const name = member.username || member.email || 'Unknown User';
                const initials = name.slice(0, 2).toUpperCase();
                const li = document.createElement('li');
                li.className = 'add-assignee-item';
                li.innerHTML = `
                    <label class="add-assignee-label">
                        <input type="checkbox" name="new_assignees[]" value="${member.id}" class="add-assignee-checkbox">
                        <span class="assignee-avatar">${initials}</span>
                        <span class="assignee-name">${name}</span>
                        <span class="assignee-check-icon"><i class="fa-solid fa-check"></i></span>
                    </label>
                `;
                addAssigneesList.appendChild(li);
            });
        }

        document.getElementById('editTaskDueDate').value = formatted;

        document.getElementById('editTaskForm').action = '{{ route("clickup.tasks.update", ["id" => "__ID__"]) }}'
            .replace('__ID__', task.id);

        document.getElementById('editModal').style.display = 'flex';
    }

    function addAssigneeTag(container, id, name) {
        const li = document.createElement('li');
        li.className = 'assignee-tag';
        li.dataset.id = id;
        li.innerHTML = `
            <span>${name}</span>
            <input type="hidden" name="assignees[]" value="${id}">
            <button type="button" onclick="removeAssignee(this)" class="remove-assignee-btn">
                <i class="fa-solid fa-xmark"></i>
            </button>
        `;
        container.appendChild(li);
    }

    function removeAssignee(btn) {
        btn.closest('li').remove();
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeEditModal();
    });

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