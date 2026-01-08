<link rel="stylesheet" href="{{ asset('css/modal/create-modal.css') }}">

<div class="create-modal list" id="createListMdl">
    <div class="modal-header">
        <div class="button-section">
            <button id="closeModal">
                <i class="fa-solid fa-x"></i>
            </button>
        </div>
        <div class="title-section">
            <span>Create List</span>
        </div>
    </div>
    <div class="modal-body">
        <form action="{{ route('clickup.lists') }}" method="POST" id="createListForm">
            @csrf
            <div class="create-dropdown-container">
                <label for="folder">Select Folder:</label>
                <select name="folder" id="folder">
                    @foreach ($folders as $folder)
                        <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="create-name-container">
                <label for="name">List Name:</label>
                <input type="text" placeholder="Enter List Name...." name="name">
            </div>
            <div class="create-description-container">
                <label for="content">Enter List Content:</label>
                <input type="text" placeholder="Enter List Content...." name="content">
            </div>
            <div class="create-due-date-container">
                <label for="due_date">Select Due Date:</label>
                <input type="date" placeholder="List Due Date" name="due_date">
            </div>
            <div class="submit-btn-ctn">
                <button type="submit" id="closeModal">
                    Create List
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let closeModal = document.getElementById('closeModal');
    let closeCreatelistModal = document.getElementById('createListMdl');

    closeModal.addEventListener('click', function(){
        closeCreatelistModal.style.display = 'none';
        createListBtn.style.visibility = 'visible';
        modalStatus = true;
    })
</script>