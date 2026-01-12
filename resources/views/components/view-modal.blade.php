<link rel="stylesheet" href="{{ asset('css/modal/view-modal.css') }}">

<div class="view-modal {{ strtolower($entity) }}" id="{{ $modalId }}">
    <div class="modal-header">
        <div class="button-section">
            <button type="button" class="close-view-modal" id="closeModal">
                ✕
            </button>
        </div>

        <div class="title-section">
            <span>View {{ $entity }}</span>
        </div>
    </div>

    <div class="modal-body">
        {{ $slot }}

        <div class="submit-btn-ctn">
            <button type="button" id="closeModal" class="close-view-modal">
                Close
            </button>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.close-view-modal').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('viewMdl').style.display = 'none';
    });
});
</script>
