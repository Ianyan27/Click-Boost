@extends('layouts.app')
@extends('layouts.modal.folder-modal')

@section('content')
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
                        <th colspan="7">Folders</th>
                        <th colspan="3">Space</th>
                        <th colspan="2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($folders as $folder)
                        <tr>
                            <td colspan="7">{{ $folder->name}}</td>
                            <td colspan="3">{{ $folder->space_name }}</td>
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
    let createFolderBtn = document.getElementById('createFolderBtn');
    let createFolderModal = document.getElementById('createFolderMdl');

    createFolderBtn.addEventListener('click', function(){
        createFolderModal.style.display = 'block';
        createFolderBtn.style.visibility = 'hidden';
        modalStatus = true;
    })
</script>
@endsection