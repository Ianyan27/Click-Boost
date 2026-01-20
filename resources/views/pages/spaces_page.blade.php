@extends('layouts.app')
@extends('layouts.modal.space-modal')
@extends('layouts.modal.view-data-modal.view-space-modal')

@section('content')
<x-delete-modal 
    entity="Space"
    message="You're about to delete this space."
/>
<x-view-modal entity="Space" modalId="viewMdl">
    <div class="view-space-name-container">
        <label>Space Name:</label>
        <input id="modalSpaceName" type="text" readonly>
    </div>
</x-view-modal>
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
                                    <button class="edit-space-btn"
                                        data-space-id ="{{ $space->id }}"
                                        data-space-name="{{ $space->name }}">
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        const modal = document.getElementById('viewMdl');

        document.querySelectorAll('.view-data-btn').forEach(button => {
            button.addEventListener('click', function () {
                const space = JSON.parse(this.dataset.space);

                document.getElementById('modalSpaceName').value = space.name;
                
                modal.style.display = 'block';
            })
        })
    })
    $(document).on('click', '.edit-space-btn', function() {
        const spaceId = $(this).data('space-id');
        const spaceName = $(this).data('space-name');
        
        $('#editSpaceName').val(spaceName);
        $('#editSpaceMdl form').attr('action', `/spaces/${spaceId}`);
        $('#editSpaceMdl').modal('show');
    });

    // Handle form submission
    $('#editSpaceMdl form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            name: $('#editSpaceName').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'PUT',
            data: formData,
            success: function(response) {
                $('#editSpaceMdl').modal('hide');
                // Show success message
                // Refresh table or update row
            },
            error: function(xhr) {
                // Show validation errors
                if (xhr.responseJSON.errors) {
                    $('#editSpaceNameError').text(xhr.responseJSON.errors.name[0]);
                }
            }
        });
    });
</script>
@endsection