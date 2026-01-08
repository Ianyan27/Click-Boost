<link rel="stylesheet" href="{{ asset('css/modal/create-modal.css') }}">

<div class="create-modal folder" id="createFolderMdl">
    <div class="modal-header">
        <div class="button-section">
            <button id="closeModal">
                <i class="fa-solid fa-x"></i>
            </button>
        </div>
        <div class="title-section">
            <span>Create Folder</span>
        </div>
    </div>
    <div class="modal-body">
        <form action="{{ route('create#folder') }}" method="POST" id="createFolderForm">
            @csrf
            <div class="create-dropdown-container">
                <label for="space">Select Space:</label>
                <select name="space" id="space">
                    @foreach ($spaces as $space)
                        <option value="{{ $space->id }}">{{ $space->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="create-name-container">
                <label for="name">Folder Name:</label>
                <input type="text" placeholder="Enter Folder Name...." name="name">
            </div>
            <div class="submit-btn-ctn">
                <button type="submit" id="closeModal">
                    Create Task
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let closeModal = document.getElementById('closeModal');
    let closeCreateFolderModal = document.getElementById('createFolderMdl');
    let resetCreateFolderForm = document.getElementById('createFolderForm');

    closeModal.addEventListener('click', function(){
        closeCreateFolderModal.style.display = 'none';
        createFolderBtn.style.visibility = 'visible';
        resetCreateFolderForm.reset();
        modalStatus = true;
    })
</script>