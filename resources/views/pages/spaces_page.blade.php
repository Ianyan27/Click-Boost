@extends('layouts.app')
@extends('layouts.modal.create-modal.create-space-modal')
@extends('layouts.modal.view-data-modal.view-space-modal')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modal/create-modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal/edit-modal.css') }}">
@endpush

@section('content')
<x-delete-modal 
    entity="Space"
    message="You're about to delete this space."
/>
<x-view-modal entity="Space" modalId="viewMdl">
    <div class="view-space-name-container form-field full-width">
        <label>Space Name:</label>
        <input id="modalSpaceName" type="text" readonly>
    </div>
</x-view-modal>
<div class="edit-modal" id="editModal" style="display:none;">
    <div class="edit-modal-overlay" onclick="closeEditModal()"></div>
    <div class="edit-modal-content">
        <div class="edit-modal-header">
            <h3>Edit Space</h3>
            <button class="close-btn" onclick="closeEditModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form class="edit-space-name-form" id="editSpaceForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="editSpaceName">Space Name</label>
                <input 
                    type="text" 
                    id="editSpaceName" 
                    name="name" 
                    class="form-input"
                    placeholder="Enter space name"
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
                                    <button class="edit-data-btn"
                                        data-space = '@json($space)'
                                        onclick="openEditModal(this)">
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
    document.addEventListener('DOMContentLoaded', function () {

        let createSpaceBtn = document.getElementById('createSpaceBtn');
        let createSpaceModal = document.getElementById('createSpaceMdl');

        createSpaceBtn.addEventListener('click', function(){
            createSpaceModal.style.display = 'block';
            createSpaceBtn.style.visibility = 'hidden';
            modalStatus = true;
        });
        
        const modal = document.getElementById('viewMdl');

        document.querySelectorAll('.view-data-btn').forEach(button => {
            button.addEventListener('click', function () {
                const space = JSON.parse(this.dataset.space);

                document.getElementById('modalSpaceName').value = space.name;
                
                modal.style.display = 'block';
            })
        })
    });

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const name = btn.dataset.name;

            const form = document.getElementById('deleteForm');
            form.action = '{{ route("clickup.delete", ["id" => "__ID__"]) }}'.replace('__ID__', id);
            document.getElementById('deleteMessage').textContent = `You're about to delete "${name}".`;
            document.getElementById('deleteMdl').style.display = 'block';
        });
    });

    let spaceEditModal = document.getElementById('editModal');

    function openEditModal(btn) {
        const space = JSON.parse(btn.getAttribute('data-space'));

        document.getElementById('editSpaceName').value = space.name;

        document.getElementById('editSpaceForm').action = '{{ route("clickup.spaces.update", ["id" => "__ID__"]) }}'.replace('__ID__', space.id);

        spaceEditModal.style.display = 'flex';
    }

    function closeEditModal(){
        spaceEditModal.style.display = 'none';
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeEditModal();
        }
    })
</script>
@endsection