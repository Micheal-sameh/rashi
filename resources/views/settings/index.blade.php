@extends('layouts.sideBar')

@section('content')
    <div class="container py-4" style="max-width: 1000px;">

        <!-- Heading -->
        <h2 class="mb-4 fw-bold text-primary">
            <i class="fa fa-cogs me-2"></i> {{ __('messages.Application Settings') }}
        </h2>

        <!-- Alerts -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa fa-exclamation-triangle me-2"></i>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Settings Form -->
        <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4 table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.value') }}</th>
                                <th>{{ __('messages.type') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($settings as $setting)
                                <tr>
                                    <!-- Name -->
                                    <td>
                                        <input type="text" name="settings[{{ $setting->id }}][name]"
                                            value="{{ $setting->name }}" class="form-control" readonly disabled>
                                    </td>

                                    <!-- Value -->
                                    <td>
                                        @if ($setting->type === 'file')
                                            <input type="file" name="settings[{{ $setting->id }}][value]"
                                                class="form-control">
                                            @if ($setting->value)
                                            {{-- @dd() --}}
                                                <small class="text-muted d-block mt-1">
                                                    Current: <a href="{{ $setting->getFirstMediaUrl('app_logo') }}" target="_blank">View
                                                        File</a>
                                                </small>
                                            @endif
                                        @else
                                            <input type="text" name="settings[{{ $setting->id }}][value]"
                                                value="{{ $setting->value }}" class="form-control">
                                        @endif
                                    </td>

                                    <!-- Type -->
                                    <td>
                                        <input type="text" value="{{ $setting->type }}" class="form-control" disabled>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save me-1"></i> {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
@endsection
