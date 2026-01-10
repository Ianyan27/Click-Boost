<link rel="stylesheet" href="{{ asset('css/modal/create-modal.css') }}">

<div class="view-modal task" id="viewListMdl">
    <div class="modal-header">
        <div class="button-section">
            <button id="closeModal">
                <i class="fa-solid fa-x"></i>
            </button>
        </div>
        <div class="title-section">
            <span>View Lists</span>
        </div>
    </div>
    <div class="modal-body">
        <div class="view-list-dropdown-container">
            <label>Folder:</label>
            <input id="modalFolder" type="text" readonly>
        </div>
        <div class="view-list-name-container">
            <label>List:</label>
            <input id="modalName" type="text" readonly>
        </div>
        <div class="view-list-content-container">
            <label>Content:</label>
            <input id="modalContent" type="text" readonly>
        </div>
        <div class="view-list-due-date-container">
            <label for="due_date">Due Date:</label>
            <input id="modalDueDate" type="text" readonly>
        </div>
        <div class="submit-btn-ctn">
            <button id="closeModal">
                Close
            </button>
        </div>
    </div>
</div>