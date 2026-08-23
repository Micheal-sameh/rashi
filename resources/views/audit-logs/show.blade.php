@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <x-page-header icon="fa-history" :title="__('messages.audit_log_details')">
            <x-slot:actions>
                <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-1"></i> {{ __('messages.back') }}
                </a>
            </x-slot>
        </x-page-header>

        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="rs-label-md">{{ __('messages.action') }}</h5>
                        @if ($auditLog->action === 'created')
                            <span class="badge" style="background: var(--color-success-container); color: var(--color-on-success-container);">
                                <i class="fa fa-plus-circle"></i> {{ __('messages.created') }}
                            </span>
                        @elseif ($auditLog->action === 'updated')
                            <span class="badge" style="background: var(--color-warning-container); color: var(--color-warning);">
                                <i class="fa fa-edit"></i> {{ __('messages.updated') }}
                            </span>
                        @elseif ($auditLog->action === 'deleted')
                            <span class="badge" style="background: var(--color-error-container); color: var(--color-on-error-container);">
                                <i class="fa fa-trash"></i> {{ __('messages.deleted') }}
                            </span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h5 class="rs-label-md">{{ __('messages.model') }}</h5>
                        <p class="rs-body-lg mb-0"><span class="badge" style="background: var(--color-secondary-container); color: var(--color-secondary);">{{ __('messages.' . $auditLog->model_name) }}</span> #{{ $auditLog->model_id }}</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="rs-label-md">{{ __('messages.user') }}</h5>
                        <p class="rs-body-lg mb-0">{{ $auditLog->user_name ?? 'System' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="rs-label-md">{{ __('messages.date_time') }}</h5>
                        <p class="rs-body-lg mb-0">{{ $auditLog->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="rs-label-md">{{ __('messages.ip_address') }}</h5>
                        <p class="rs-body-lg mb-0">{{ $auditLog->ip_address }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="rs-label-md">{{ __('messages.user_agent') }}</h5>
                        <p class="rs-body-lg mb-0 text-truncate">{{ $auditLog->user_agent }}</p>
                    </div>
                </div>

                <hr>

                @if ($auditLog->action === 'created')
                    <h5 class="rs-title-lg mb-3">{{ __('messages.new_values') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('messages.field') }}</th>
                                    <th>{{ __('messages.value') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($auditLog->new_values ?? [] as $key => $value)
                                    <tr>
                                        <td class="fw-bold">{{ $key }}</td>
                                        <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif ($auditLog->action === 'deleted')
                    <h5 class="rs-title-lg mb-3">{{ __('messages.deleted_values') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('messages.field') }}</th>
                                    <th>{{ __('messages.value') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($auditLog->old_values ?? [] as $key => $value)
                                    <tr>
                                        <td class="fw-bold">{{ $key }}</td>
                                        <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif ($auditLog->action === 'updated')
                    <h5 class="rs-title-lg mb-3">{{ __('messages.changes') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('messages.field') }}</th>
                                    <th>{{ __('messages.old_value') }}</th>
                                    <th>{{ __('messages.new_value') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($auditLog->changes as $field => $change)
                                    <tr>
                                        <td class="fw-bold">{{ $field }}</td>
                                        <td class="text-danger">
                                            {{ is_array($change['old']) ? json_encode($change['old']) : $change['old'] }}
                                        </td>
                                        <td class="text-success">
                                            {{ is_array($change['new']) ? json_encode($change['new']) : $change['new'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
