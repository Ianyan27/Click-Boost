<link rel="stylesheet" href="{{ asset('css/modal/create-modal.css') }}">

<div class="view-modal task" id="viewTaskMdl">
    <div class="modal-header">
        <div class="button-section">
            <button id="closeModal">
                <i class="fa-solid fa-x"></i>
            </button>
        </div>
        <div class="title-section">
            <span>View Task</span>
        </div>
    </div>
    <div class="modal-body">
        <div class="view-task-dropdown-container">
            <label for="list">Select List:</label>
            <select name="list" id="list">
                @foreach ($lists as $list)
                    <option value="{{ $list->id }}">{{ $list->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="view-task-name-container">
            <label>Task Name:</label>
            <input id="modalName" type="text" readonly>
        </div>
        <div class="view-task-description-container">
            <label for="description">Enter Task Description:</label>
            <input id="modalDescription" type="text" readonly>
        </div>
        <div class="view-task-due-date-container">
            <label for="due_date">Select Due Date:</label>
            <input id="modalDueDate" type="text" readonly>
        </div>
        <div class="view-task-status-container">
            <label for="status">Task Status:</label>
            <input id="modalStatus" type="text">
        </div>
        <div class="submit-btn-ctn">
            <button id="closeModal">
                Close
            </button>
        </div>
    </div>
</div>