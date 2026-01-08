@extends('layouts.app')
@extends('layouts.modal.space-modal')

@section('content')
<div class="clickup-container">
    <div class="space-container">
        <div class="button-section-container">
            <div>
                <span class="screen-title">Spaces</span>
            </div>
            <div>
                <button id="createSpaceBtn">
                    Create Space
                </button>
            </div>
        </div>
        <div class="clickup-table-container">
            <table>
                <thead>
                    <tr>
                        <th colspan="10">Spaces</th>
                        <th colspan="2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($spaces as $space)
                        <tr>
                            <td colspan="10">{{ $space->name}}</td>
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
    let createSpaceBtn = document.getElementById('createSpaceBtn');
    let createSpaceModal = document.getElementById('createSpaceMdl');

    createSpaceBtn.addEventListener('click', function(){
        createSpaceModal.style.display = 'block';
        createSpaceBtn.style.visibility = 'hidden';
        modalStatus = true;
    })
</script>
@endsection