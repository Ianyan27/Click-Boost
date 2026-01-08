@extends('layouts.app')
@extends('layouts.modal.list-modal')

@section('content')
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
                        <th colspan="7">Lists</th>
                        <th colspan="3">Folder</th>
                        <th colspan="2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lists as $list)
                        <tr>
                            <td colspan="7">{{ $list->name}}</td>
                            <td colspan="3">{{ $list->folder_name }}</td>
                            <td class="action-column" colspan="2">
                                <div class="action-buttons">
                                    <button>
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
    let createTaskBtn = document.getElementById('createListBtn');
    let createTaskModal = document.getElementById('createListMdl');

    createTaskBtn.addEventListener('click', function(){
        createTaskModal.style.display = 'block';
        createTaskBtn.style.visibility = 'hidden';
        modalStatus = true;
    })
</script>
@endsection