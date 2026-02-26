@extends('layouts.app')
@extends('layouts.modal.list-modal')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modal/edit-modal.css') }}">
@endpush

@section('content')
<x-delete-modal 
    entity="List"
    message="You're about to delete this list."
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
<div class="edit-modal" id="editModal" style="display:none;">
<div class="edit-modal-overlay" onclick="closeEditModal()"></div>
    <div class="edit-modal-content">
        <div class="edit-modal-header">
            <h3>Edit List</h3>
            <button class="close-btn" onclick="closeEditModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form class="edit-list" id="editListForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="editListName">List Name</label>
                <input 
                    type="text" 
                    id="editListName" 
                    name="name" 
                    class="form-input"
                    placeholder="Enter list name"
                    required
                >
            </div>
            <div class="form-group">
                <label for="editListDueDate">Due Date</label>
                <input 
                    type="date" 
                    id="editListDueDate" 
                    name="due_date" 
                    class="form-input"
                    />
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
                                    <button class="edit-data-btn"
                                        data-list = '@json($list)'
                                        onclick="openListEditModal(this)">
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
    function openListEditModal(btn) {
        const list = JSON.parse(btn.getAttribute('data-list'));

        document.getElementById('editListName').value = list.name;
        const cleaned = list.due_date.replace('.', ',');
        const date = new Date(cleaned);
        const formatted = [
            date.getFullYear(),
            String(date.getMonth() + 1).padStart(2, '0'),
            String(date.getDate()).padStart(2, '0')
        ].join('-');
        console.log([list.due_date, cleaned, date, formatted]);
        document.getElementById('editListDueDate').value = formatted;

        document.getElementById('editListForm').action = '{{ route("clickup.lists.update", ["id" => "__ID__"]) }}'
            .replace('__ID__', list.id);

        document.getElementById('editModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {

        const createFolderBtn   = document.getElementById('createFolderBtn');
        const createFolderModal = document.getElementById('createFolderMdl');
        const viewFolderModal   = document.getElementById('viewFolderMdl');
        const viewModal         = document.getElementById('viewMdl');

        createFolderBtn.addEventListener('click', function () {
            createFolderModal.style.display = 'block';
            createFolderBtn.style.visibility = 'hidden';
            modalStatus = true;
        });

        document.querySelectorAll('.view-data-btn').forEach(button => {
            button.addEventListener('click', function () {
                const folder = JSON.parse(this.dataset.folder);

                document.getElementById('modalFolderName').value  = folder.name;
                document.getElementById('modalFolderSpace').value = folder.space_name;

                const modalLists = document.getElementById('modalLists');
                modalLists.innerHTML = '';

                if (!folder.lists || folder.lists.length === 0) {
                    modalLists.innerHTML = '<li><em>No lists in this folder</em></li>';
                } else {
                    folder.lists.forEach(list => {
                        const li = document.createElement('li');
                        li.textContent = list.name ?? 'Unknown List';
                        modalLists.appendChild(li);
                    });
                }

                viewModal.style.display = 'block';
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeEditModal();
        });

    });
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