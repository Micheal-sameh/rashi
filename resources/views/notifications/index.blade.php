@extends('layouts.sideBar')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Notifications</h3>
                    <div class="card-tools">
                        <a href="{{ route('notifications.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Send Notification
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Message</th>
                                <th>Type</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notifications as $notification)
                                <tr>
                                    <td>{{ $notification->id }}</td>
                                    <td>{{ Str::limit($notification->message, 100) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $notification->type == 'success' ? 'success' : ($notification->type == 'warning' ? 'warning' : ($notification->type == 'error' ? 'danger' : 'info')) }}">
                                            {{ $notification->type }}
                                        </span>
                                    </td>
                                    <td>{{ $notification->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No notifications found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
