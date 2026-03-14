@extends('layouts.app')
@extends('layouts.modal.create-modal.create-folder-modal')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modal/edit-modal.css') }}">
@endpush

@section('content')
<x-delete-modal 
    entity="Folder"
    message="You're about to delete this folder."
/>
<x-view-modal entity="Folder" modalId="viewMdl">
    <div class="view-folder-name-container form-field full-width">
        <label>Folder Name:</label>
        <input id="modalFolderName" type="text" readonly>
    </div>
    <div class="view-folder-space-container form-field full-width">
        <label>Space:</label>
        <input id="modalFolderSpace" type="text" readonly>
    </div>
    <div class="view-folder-lists-container form-field full-width">
        <label>Lists:</label>
        <ul id="modalLists" style="width: 100%"></ul>
    </div>
</x-view-modal>
<div class="edit-modal" id="editModal" style="display:none;">
<div class="edit-modal-overlay" onclick="closeEditModal()"></div>
    <div class="edit-modal-content">
        <div class="edit-modal-header">
            <h3>Edit Folder</h3>
            <button class="close-btn" onclick="closeEditModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form class="edit-folder" id="editFolderForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="editFolderName">Folder Name</label>
                <input 
                    type="text" 
                    id="editFolderName" 
                    name="name" 
                    class="form-input"
                    placeholder="Enter folder name"
                    required
                >
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
    <div class="folder-container">
        <div class="button-section-container">
            <div>
                <span class="screen-title">Folders</span>
            </div>
            <div>
                <button id="createFolderBtn">
                    Create folder
                </button>
            </div>
        </div>
        <div class="clickup-table-container">
            <table>
                <thead>
                    <tr>
                        <th colspan="5">Folders</th>
                        <th colspan="3" class="th-short-text">Space</th>
                        <th colspan="2" class="th-numbers">Number of Lists</th>
                        <th colspan="2" class="th-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($folders as $folder)
                        <tr>
                            <td colspan="5">{{ $folder->name}}</td>
                            <td colspan="3" class="th-short-text">{{ $folder->space_name }}</td>
                            <td colspan="2" class="th-numbers">{{ $folder->lists_count }}</td>
                            <td class="action-column" colspan="2">
                                <div class="action-buttons">
                                    <button class="view-data-btn"
                                        data-folder = '@json($folder)'>
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button class="edit-data-btn"
                                        data-folder = '@json($folder)'
                                        onclick="openFolderEditModal(this)">
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                    <button class="delete-btn" data-entity = "folder" data-id="{{ $folder->id }}" data-name="{{ $folder->name }}">
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

    function openFolderEditModal(btn) {
        const folder = JSON.parse(btn.getAttribute('data-folder'));

        document.getElementById('editFolderName').value = folder.name;
        document.getElementById('editFolderForm').action = '{{ route("clickup.folders.update", ["id" => "__ID__"]) }}'
            .replace('__ID__', folder.id);

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
</script>
@endsection