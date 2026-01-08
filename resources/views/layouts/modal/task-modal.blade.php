<link rel="stylesheet" href="{{ asset('css/modal/create-modal.css') }}">

<div class="create-modal task" id="createTaskMdl">
    <div class="modal-header">
        <div class="button-section">
            <button id="closeModal">
                <i class="fa-solid fa-x"></i>
            </button>
        </div>
        <div class="title-section">
            <span>Create Task</span>
        </div>
    </div>
    <div class="modal-body">
        <form action="{{ route('create#task') }}" method="POST" id="createTaskForm">
            @csrf
            <div class="create-task-dropdown-container">
                <label for="list">Select List:</label>
                <select name="list" id="list">
                    @foreach ($lists as $list)
                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="create-task-name-container">
                <label for="name">Task Name:</label>
                <input type="text" placeholder="Enter Task Name...." name="name">
            </div>
            <div class="create-task-description-container">
                <label for="description">Enter Task Description:</label>
                <input type="text" placeholder="Enter Task Description...." name="description">
            </div>
            <div class="create-task-due-date-container">
                <label for="due_date">Select Due Date:</label>
                <input type="date" placeholder="Task Due Date" name="due_date">
            </div>
            <div class="create-task-status-container">
                <label for="status">Task Status:</label>
                <select name="status" id="status">
                    @foreach ($taskStatuses as $task)
                        <option value=" {{{ $task }}} "> {{ $task }}</option>
                    @endforeach
                </select>
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
    let closeCreateTaskModal = document.getElementById('createTaskMdl');
    let resetCreateTaskForm = document.getElementById('createTaskForm');

    closeModal.addEventListener('click', function(){
        closeCreateTaskModal.style.display = 'none';
        createTaskBtn.style.visibility = 'visible';
        resetCreateTaskForm.reset();
        modalStatus = true;
    })
</script>