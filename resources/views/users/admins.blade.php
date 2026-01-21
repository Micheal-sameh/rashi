@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="fw-bold mb-2">
                    <i class="fas fa-user-shield me-2 text-primary"></i>{{ __('messages.admin_users') }}
                </h1>
                <p class="text-muted mb-0">{{ __('messages.manage_admin_users') }}</p>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-users me-2"></i>{{ __('messages.all_users') }}
            </a>
        </div>

        <!-- Search Bar -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body">
                <form action="{{ route('users.admins') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-12 col-md-9 col-lg-10">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control border-start-0"
                                       placeholder="{{ __('messages.search_by_name_email_code') }}"
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-3 col-lg-2">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fa fa-search me-2"></i>{{ __('messages.search') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-2 opacity-75">{{ __('messages.total_admins') }}</h6>
                                <h2 class="fw-bold display-6 mb-0">{{ $admins->total() }}</h2>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="fas fa-user-shield fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden d-none d-lg-block">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <tr>
                            <th class="py-3 px-4">#</th>
                            <th class="py-3">{{ __('messages.image') }}</th>
                            <th class="py-3">{{ __('messages.name') }}</th>
                            <th class="py-3">{{ __('messages.email') }}</th>
                            <th class="py-3">{{ __('messages.membership_code') }}</th>
                            <th class="py-3">{{ __('messages.phone') }}</th>
                            <th class="py-3">{{ __('messages.groups') }}</th>
                            <th class="py-3 text-center">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $index => $admin)
                            <tr class="border-bottom">
                                <td class="px-4 fw-semibold">{{ $admins->firstItem() + $index }}</td>
                                <td>
                                    @if($admin->getFirstMediaUrl('user_image'))
                                        <img src="{{ $admin->getFirstMediaUrl('user_image') }}"
                                             alt="{{ $admin->name }}"
                                             class="rounded-circle zoomable-image shadow-sm"
                                             style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;"
                                             onclick="openModal('{{ $admin->getFirstMediaUrl('user_image') }}')">
                                    @else
                                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-user text-primary fs-5"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <div class="fw-semibold">{{ $admin->name }}</div>
                                            <div class="badge bg-primary bg-opacity-10 text-primary mt-1">
                                                <i class="fas fa-shield-alt me-1"></i>Admin
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-envelope text-muted me-2"></i>
                                    <span>{{ $admin->email }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        <i class="fas fa-id-card me-2"></i>{{ $admin->membership_code ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <i class="fas fa-phone text-muted me-2"></i>
                                    <span>{{ $admin->phone ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($admin->groups->isNotEmpty())
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($admin->groups as $group)
                                                <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">
                                                    {{ $group->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">{{ __('messages.no_groups') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('users.show', $admin->id) }}"
                                       class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                       title="{{ __('messages.view_details') }}">
                                        <i class="fa fa-eye me-1"></i>{{ __('messages.view') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-user-shield fa-3x mb-3 opacity-25"></i>
                                        <p class="mb-0">{{ __('messages.no_admin_users_found') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="d-lg-none">
            @forelse($admins as $admin)
                <div class="card shadow-sm border-0 rounded-4 mb-3">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            @if($admin->getFirstMediaUrl('user_image'))
                                <img src="{{ $admin->getFirstMediaUrl('user_image') }}"
                                     alt="{{ $admin->name }}"
                                     class="rounded-circle shadow-sm"
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                     style="width: 60px; height: 60px;">
                                    <i class="fas fa-user text-primary fs-4"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-2">{{ $admin->name }}</h6>
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    <i class="fas fa-shield-alt me-1"></i>Admin
                                </span>
                            </div>
                        </div>

                        <div class="border-top pt-3">
                            <div class="row g-3">
                                <div class="col-12">
                                    <small class="text-muted d-block mb-1">{{ __('messages.email') }}</small>
                                    <div class="fw-semibold">
                                        <i class="fas fa-envelope text-primary me-2"></i>{{ $admin->email }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">{{ __('messages.membership_code') }}</small>
                                    <div class="fw-semibold">{{ $admin->membership_code ?? 'N/A' }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">{{ __('messages.phone') }}</small>
                                    <div class="fw-semibold">{{ $admin->phone ?? 'N/A' }}</div>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block mb-2">{{ __('messages.groups') }}</small>
                                    @if($admin->groups->isNotEmpty())
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($admin->groups as $group)
                                                <span class="badge bg-success bg-opacity-10 text-success">
                                                    {{ $group->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">{{ __('messages.no_groups') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="border-top pt-3 mt-3">
                            <a href="{{ route('users.show', $admin->id) }}"
                               class="btn btn-primary w-100">
                                <i class="fa fa-eye me-2"></i>{{ __('messages.view_details') }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-user-shield fa-3x mb-3 text-muted opacity-25"></i>
                        <p class="text-muted mb-0">{{ __('messages.no_admin_users_found') }}</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($admins->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $admins->links() }}
            </div>
        @endif
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body p-0">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3 bg-white rounded-circle p-2 shadow"
                            data-bs-dismiss="modal" aria-label="Close"></button>
                    <img id="modalImage" src="" alt="User Image" class="img-fluid rounded w-100">
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            new bootstrap.Modal(document.getElementById('imageModal')).show();
        }
    </script>
@endsection
