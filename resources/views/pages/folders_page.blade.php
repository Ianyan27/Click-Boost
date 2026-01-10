@extends('layouts.app')
@extends('layouts.modal.folder-modal')

@section('content')
<x-delete-modal 
    entity="Folder"
    message="You're about to delete this folder."
/>
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
                            <td colspan="2" class="th-numbers">{{ $folder->lists }}</td>
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
    let createFolderModal = document.getElementById('createFolderMdl');

    createFolderBtn.addEventListener('click', function(){
        createFolderModal.style.display = 'block';
        createFolderBtn.style.visibility = 'hidden';
        modalStatus = true;
    })
</script>
@endsection