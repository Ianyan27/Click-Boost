<link rel="stylesheet" href="{{ asset('css/modal/delete-modal.css') }}">

<div class="delete-modal" id="deleteMdl">
    <div class="modal-header">
        <button type="button" class="close-delete-modal">
            ✕
        </button>
        <h3 id="deleteTitle">Delete</h3>
    </div>

    <form method="POST" action="{{ route('clickup.delete') }}" id="deleteForm">
        @csrf
        @method('DELETE')

        <input type="hidden" name="endpoint" id="deleteEndpoint">

        <p id="deleteMessage"></p>

        <div class="submit-btn-ctn">
            <button type="button" class="close-delete-modal">
                No, keep it
            </button>
            <button type="submit">
                Yes, delete
            </button>
        </div>
    </form>
</div>
<script>
document.querySelectorAll('.close-delete-modal').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('deleteMdl').style.display = 'none';
    });
});
</script>