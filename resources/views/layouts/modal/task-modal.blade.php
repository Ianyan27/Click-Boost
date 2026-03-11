<link rel="stylesheet" href="{{ asset('css/modal/create-modal.css') }}">

<div class="create-modal task" id="createTaskMdl">
    <div class="modal-header">
        <div class="button-section">
            <button id="closePopUpModal">
                <i class="fa-solid fa-x"></i>
            </button>
        </div>
        <div class="title-section">
            <span>Create Task</span>
        </div>
    </div>
    <div class="modal-body">
        <form action="{{ route('clickup.tasks') }}" method="POST" id="createTaskForm">
            @csrf

            {{-- Select List (full width) --}}
            <div class="form-field full-width">
                <label for="list">Select List</label>
                <select name="list" id="list">
                    @foreach ($lists as $list)
                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Task Name (full width) --}}
            <div class="form-field full-width">
                <label for="name">Task Name</label>
                <input type="text" placeholder="Enter task name" name="name" id="name">
            </div>

            {{-- Description (full width) --}}
            <div class="form-field full-width">
                <label for="description">Description</label>
                <textarea placeholder="Enter task description" name="description" id="description" rows="4"></textarea>
            </div>

            {{-- Status + Priority (two columns) --}}
            <div class="form-row">
                <div class="form-field">
                    <label for="status">Status</label>
                    <select name="status" id="status">
                        @foreach ($taskStatuses as $task)
                            <option value="{{ trim($task) }}">{{ ucwords(strtolower($task)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label for="priority">Priority</label>
                    <select name="priority" id="priority">
                        <option value="1">Urgent</option>
                        <option value="2">High</option>
                        <option value="3">Normal</option>
                        <option value="4" selected>Low</option>
                    </select>
                </div>
            </div>

            {{-- Assignees + Due Date (two columns) --}}
            <div class="form-row">
                <div class="form-field assignees-field">
                    <label>Assignees</label>
                    <div class="assignees-dropdown" id="assigneesDropdown">
                        <div class="assignees-trigger" id="assigneesTrigger">
                            <span class="trigger-placeholder" id="assigneesPlaceholder">Select assignees</span>
                            <i class="fa-solid fa-chevron-down trigger-icon" id="assigneesChevron"></i>
                        </div>
                        <div class="assignees-panel" id="assigneesPanel">
                            <ul>
                                @foreach ($members as $member)
                                    @php $count = $taskCounts->get($member->id, 0); @endphp
                                    <li>
                                        <label class="assignee-option {{ $count >= 5 ? 'is-overloaded' : '' }}">
                                            <input
                                                type="checkbox"
                                                name="assignees[]"
                                                value="{{ $member->id }}"
                                                data-task-count="{{ $count }}"
                                                data-name="{{ $member->username ?: $member->email ?: 'Unknown User' }}"
                                                class="assignee-checkbox"
                                            >
                                            <span class="assignee-avatar">
                                                {{ strtoupper(substr($member->username ?: $member->email ?: 'U', 0, 1)) }}
                                            </span>
                                            <span class="assignee-name">
                                                {{ $member->username ?: $member->email ?: 'Unknown User' }}
                                            </span>
                                            @if ($count >= 5)
                                                <span class="task-count-badge overloaded">{{ $count }} tasks</span>
                                            @elseif ($count > 0)
                                                <span class="task-count-badge">{{ $count }} tasks</span>
                                            @endif
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="form-field">
                    <label for="due_date">Due Date</label>
                    <input type="date" name="due_date" id="due_date">
                </div>
            </div>

            {{-- Overload warning banner — shown below the row when overloaded users are checked --}}
            <div class="overload-warning-banner" id="overloadWarningBanner" style="display:none;">
                <div class="overload-warning-icon">⚠️</div>
                <div class="overload-warning-text">
                    <strong>Overload Warning</strong>
                    <span id="overloadWarningBody"></span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-submit">Create Task</button>
            </div>

        </form>
    </div>
</div>

<script>

    (function () {
        const trigger     = document.getElementById('assigneesTrigger');
        const panel       = document.getElementById('assigneesPanel');
        const chevron     = document.getElementById('assigneesChevron');
        const placeholder = document.getElementById('assigneesPlaceholder');
        const banner      = document.getElementById('overloadWarningBanner');
        const bannerBody  = document.getElementById('overloadWarningBody');

        if (!trigger || !panel) return;

        // Toggle open/close
        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = panel.classList.toggle('open');
            trigger.classList.toggle('open', isOpen);
            chevron.classList.toggle('rotated', isOpen);
        });

        // Close when clicking outside
        document.addEventListener('click', function (e) {
            if (!trigger.contains(e.target) && !panel.contains(e.target)) {
                panel.classList.remove('open');
                trigger.classList.remove('open');
                chevron.classList.remove('rotated');
            }
        });

        function updateUI() {
            const allChecked = Array.from(document.querySelectorAll('.assignee-checkbox:checked'));

            // Update trigger placeholder
            const names = allChecked.map(cb => cb.dataset.name);
            if (names.length === 0) {
                placeholder.textContent = 'Select assignees';
                placeholder.classList.remove('has-selection');
            } else {
                placeholder.textContent = names.join(', ');
                placeholder.classList.add('has-selection');
            }

            // Build overload list from checked users with >= 5 tasks
            const overloaded = allChecked.filter(cb => parseInt(cb.dataset.taskCount, 10) >= 5);

            if (overloaded.length > 0) {
                const lines = overloaded.map(cb => {
                    const count = cb.dataset.taskCount;
                    const name  = cb.dataset.name;
                    return `${name} is currently handling ${count} tasks.`;
                });
                bannerBody.textContent = lines.join(' ');
                banner.style.display = 'flex';
            } else {
                banner.style.display = 'none';
            }
        }

        document.querySelectorAll('.assignee-checkbox').forEach(function (checkbox) {
            checkbox.addEventListener('change', updateUI);
        });
    })();
</script>