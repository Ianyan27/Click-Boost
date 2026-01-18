@extends('layouts.app')

@section('content')
    <div class="first-section">
        <span class="dashboard-welcome-text">Welcome back {{ session('user')['name'] }}</span>
    </div>
    <div class="second-section">
        <div class="task-statuses-container">
            <div class="task-statuses-title-container">
                <span class="task-statuses-title">Task Statuses</span>
            </div>
            <div class="task-statuses-cards">
                @foreach ($statusCounts as $status => $count)
                    <div class="task-status-card">
                        <span class="status-title">{{ Str::ucfirst($status) }}</span>
                        <span class="status-count">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="assignees-task-counter-container">
            <div class="assignees-task-title-container">
                <span>Assignees Data</span>
            </div>
            <div class="assignees-data-cards">
                @foreach ($assigneeStats as $stats)
                    <div class="assignee-card">
                        <span class="assignee-name">{{ $stats->assignee }}</span>
                        <div class="assignee-stats">
                            <div class="stat-item">
                                <span class="stat-label">Total Tasks</span>
                                <span class="stat-value">{{ $stats->task_count }}</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Top Status</span>
                                <div class="top-status-badge">
                                    <span class="top-status-name" {{ strtolower(str_replace(' ', '-', $stats->top_status)) }}>
                                        {{ $stats->top_status }}
                                    </span>
                                    <span class="top-status-count">{{ $stats->top_status_count }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection