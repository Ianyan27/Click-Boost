@extends('layouts.app')
@extends('layouts.modal.folder-modal')

@section('content')
<x-delete-modal 
    entity="Folder"
    message="You're about to delete this folder."
/>
<x-view-modal entity="Folder" modalId="viewMdl">
    <div class="view-folder-name-container">
        <label>Folder Name:</label>
        <input id="modalFolderName" type="text" readonly>
    </div>

    <div class="view-folder-space-container">
        <label>Space:</label>
        <input id="modalFolderSpace" type="text" readonly>
    </div>
    <div class="view-folder-lists-container">
        <label>Lists:</label>
        <ul id="modalLists"></ul>
    </div>
</x-view-modal>

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
                                    <button>
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
    let createFolderBtn = document.getElementById('createFolderBtn');
    let viewFolderModal = document.getElementById('viewFolderMdl');

    createFolderBtn.addEventListener('click', function(){
        createFolderModal.style.display = 'block';
        createFolderBtn.style.visibility = 'hidden';
        modalStatus = true;
    })
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('viewMdl');

    document.querySelectorAll('.view-data-btn').forEach(button => {
        button.addEventListener('click', function () {

            const folder = JSON.parse(this.dataset.folder);

            // ✅ Basic info
            document.getElementById('modalFolderName').value = folder.name;
            document.getElementById('modalFolderSpace').value = folder.space_name;

            // ✅ Lists container
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

            modal.style.display = 'block';
        });
    });
});
</script>
@endsection