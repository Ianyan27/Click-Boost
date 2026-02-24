<link rel="stylesheet" href="{{ asset('css/modal/create-modal.css') }}">

<div class="create-modal space" id="createSpaceMdl">
    <div class="modal-header">
        <div class="button-section">
            <button id="closeModal">
                <i class="fa-solid fa-x"></i>
            </button>
        </div>
        <div class="title-section">
            <span>Create Space</span>
        </div>
    </div>
    <div class="modal-body">
        <form action="{{ route('clickup.spaces') }}" method="POST" id="createSpaceForm">
            @csrf
            <div class="create-dropdown-container">
                <label for="team">Select Workspace:</label>
                <select name="team" id="team">
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="create-name-container">
                <label for="name">Space Name:</label>
                <input type="text" placeholder="Enter Space Name...." name="name">
            </div>
            <div class="submit-btn-ctn">
                <button type="submit" id="closeModal">
                    Create Space
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let closeModal = document.getElementById('closeModal');
    let closeCreateSpaceModal = document.getElementById('createSpaceMdl');
    let resetCreateSpaceForm = document.getElementById('createSpaceForm');

    closeModal.addEventListener('click', function(){
        closeCreateSpaceModal.style.display = 'none';
        createSpaceBtn.style.visibility = 'visible';
        resetCreateSpaceForm.reset();
        modalStatus = true;
    })
</script>