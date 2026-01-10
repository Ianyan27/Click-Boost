@extends('layouts.app')
@extends('layouts.modal.space-modal')
@extends('layouts.modal.view-data-modal.view-space-modal')

@section('content')
<x-delete-modal 
    entity="Space"
    message="You're about to delete this space."
/>
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
                        <th colspan="2" class="th-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($spaces as $space)
                        <tr>
                            <td colspan="10">{{ $space->name}}</td>
                            <td class="action-column" colspan="2">
                                <div class="action-buttons">
                                    <button class="view-data-btn"
                                        data-space= '@json($space)'>
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button>
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                    <button class="delete-btn" data-entity="space" data-id="{{ $space->id }}" data-name="{{ $space->name }}">
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
    let createSpaceBtn = document.getElementById('createSpaceBtn');
    let createSpaceModal = document.getElementById('createSpaceMdl');

    createSpaceBtn.addEventListener('click', function(){
        createSpaceModal.style.display = 'block';
        createSpaceBtn.style.visibility = 'hidden';
        modalStatus = true;
    })
</script>
@endsection