@extends('layouts.app')

@section('content')
    <div class="first-section">
        <span class="dashboard-welcome-text">Welcome back {{ session('name') }}</span>
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
    </div>
@endsection