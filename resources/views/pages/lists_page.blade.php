@extends('layouts.app')
@extends('layouts.modal.list-modal')

@section('content')
<x-delete-modal 
    entity="List"
    message="You're about to delete this List."
/>
<x-view-modal entity="List" modalId="viewMdl">
    <div class="view-folder-name-container">
        <label>Folder:</label>
        <input id="modalFolder" type="text" readonly>
    </div>
    <div class="view-folder-space-container">
        <label>List Name:</label>
        <input id="modalListName" type="text" readonly>
    </div>
    <div class="view-folder-space-container">
        <label>Content:</label>
        <input id="modalListContent" type="text" readonly>
    </div>
    <div class="view-folder-space-container">
        <label for="due_date">Due Date:</label>
        <input id="modalDueDate" type="text" readonly>
    </div>
    <div class="view-folder-lists-container">
        <label>Tasks:</label>
        <ul id="modalTasks"></ul>
    </div>
</x-view-modal>

<div class="clickup-container">
    <div class="list-container">
        <div class="button-section-container">
            <div>
                <span class="screen-title">Lists</span>
            </div>
            <div>
                <button id="createListBtn">
                    Create List
                </button>
            </div>
        </div>
        <div class="clickup-table-container">
            <table>
                <thead>
                    <tr>
                        <th colspan="5">Lists</th>
                        <th colspan="3" class="th-short-text">Folder</th>
                        <th colspan="2" class="th-numbers">Number of Tasks</th>
                        <th colspan="2" class="th-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lists as $list)
                        <tr>
                            <td colspan="5">{{ $list->name}}</td>
                            <td colspan="3" class="th-short-text">{{ $list->folder_name }}</td>
                            <td colspan="2" class="th-numbers">{{ $list->task_count }}</td>
                            <td class="action-column" colspan="2">
                                <div class="action-buttons">
                                    <button class="view-data-btn"
                                        data-list = '@json($list)'>
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button>
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                    <button class="delete-btn" data-entity="list" data-id="{{ $list->id }}" data-name="{{ $list->name }}">
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
    let createTaskBtn = document.getElementById('createListBtn');
    let createTaskModal = document.getElementById('createListMdl');

    createTaskBtn.addEventListener('click', function(){
        createTaskModal.style.display = 'block';
        createTaskBtn.style.visibility = 'hidden';
        modalStatus = true;
    })
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('viewMdl');

    document.querySelectorAll('.view-data-btn').forEach(button => {
        button.addEventListener('click', function () {

            const list = JSON.parse(this.dataset.list);

            document.getElementById('modalFolder').value = list.folder_name;
            document.getElementById('modalListName').value = list.name;
            document.getElementById('modalListContent').value = list.content;
            document.getElementById('modalDueDate').value = list.due_date ?? '';

            // ✅ TASKS
            const modalTasks = document.getElementById('modalTasks');
            modalTasks.innerHTML = '';

            if (!list.tasks || list.tasks.length === 0) {
                modalTasks.innerHTML = '<li><em>No tasks in this list</em></li>';
            } else {
                list.tasks.forEach(task => {
                    const li = document.createElement('li');
                    li.textContent = task.name;
                    modalTasks.appendChild(li);
                });
            }

            modal.style.display = 'block';
        });
    });
});
</script>
@endsection