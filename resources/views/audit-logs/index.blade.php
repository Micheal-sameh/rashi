@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">
                <i class="fa fa-history me-2"></i>{{ __('messages.audit_logs') }}
            </h2>
        </div>

        {{-- Search Filters --}}
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body">
                <form action="{{ route('audit-logs.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label for="action" class="form-label">{{ __('messages.action') }}</label>
                            <select name="action" id="action" class="form-select">
                                <option value="">{{ __('messages.all') }}</option>
                                <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>
                                    {{ __('messages.created') }}
                                </option>
                                <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>
                                    {{ __('messages.updated') }}
                                </option>
                                <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>
                                    {{ __('messages.deleted') }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="model_type" class="form-label">{{ __('messages.model_type') }}</label>
                            <select name="model_type" id="model_type" class="form-select">
                                <option value="">{{ __('messages.all') }}</option>
                                @foreach ($modelTypes as $modelType)
                                    <option value="{{ $modelType }}"
                                        {{ request('model_type') == $modelType ? 'selected' : '' }}>
                                        {{ __('messages.' . $modelType) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="user_id" class="form-label">{{ __('messages.user') }}</label>
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="">{{ __('messages.all') }}</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">{{ __('messages.date_from') }}</label>
                            <input type="date" name="date_from" id="date_from" class="form-control"
                                value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">{{ __('messages.date_to') }}</label>
                            <input type="date" name="date_to" id="date_to" class="form-control"
                                value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search me-1"></i> {{ __('messages.search') }}
                            </button>
                            <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">
                                <i class="fa fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Audit Logs Table --}}
        @if ($auditLogs->count())
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.id') }}</th>
                                <th>{{ __('messages.action') }}</th>
                                <th>{{ __('messages.model') }}</th>
                                <th>{{ __('messages.user') }}</th>
                                <th>{{ __('messages.date_time') }}</th>
                                <th>{{ __('messages.ip_address') }}</th>
                                <th class="text-center">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($auditLogs as $log)
                                <tr>
                                    <td>{{ $log->id }}</td>
                                    <td>
                                        @if ($log->action === 'created')
                                            <span class="badge bg-success">
                                                <i class="fa fa-plus-circle"></i> {{ __('messages.created') }}
                                            </span>
                                        @elseif ($log->action === 'updated')
                                            <span class="badge bg-warning">
                                                <i class="fa fa-edit"></i> {{ __('messages.updated') }}
                                            </span>
                                        @elseif ($log->action === 'deleted')
                                            <span class="badge bg-danger">
                                                <i class="fa fa-trash"></i> {{ __('messages.deleted') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ __('messages.' . $log->model_name) }}</span>
                                        @if ($log->model_route)
                                            <a href="{{ $log->model_route }}" class="text-decoration-none" target="_blank">
                                                <small class="text-primary">#{{ $log->model_id }}</small>
                                                <i class="fa fa-external-link-alt fa-xs"></i>
                                            </a>
                                        @else
                                            <small class="text-muted">#{{ $log->model_id }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $log->user_name ?? 'System' }}</td>
                                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $log->ip_address }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('audit-logs.show', $log->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($auditLogs->hasPages())
                    <div class="card-footer d-flex justify-content-center">
                        {{ $auditLogs->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        @else
            <div class="alert alert-info">
                <i class="fa fa-info-circle me-2"></i> {{ __('messages.no_audit_logs_found') }}
            </div>
        @endif
    </div>
@endsection
