@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <!-- Header Section -->
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-5">
            <div>
                <h1 class="fw-bold display-6 text-dark mb-2">{{ __('messages.rewards') }}</h1>
                <p class="text-muted mb-0">{{ __('messages.manage_your_rewards_catalog') }}</p>
            </div>
            <a href="{{ route('rewards.create') }}"
                class="btn btn-primary btn-lg px-4 py-3 shadow-lg rounded-pill d-flex align-items-center gap-2">
                <i class="fa fa-plus-circle fa-lg"></i>
                <span class="fw-semibold">{{ __('messages.create_reward') }}</span>
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 bg-gradient-primary text-white shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-2 opacity-75">{{ __('messages.total_rewards') }}</h6>
                                <h2 class="fw-bold display-6 mb-0">{{ $rewards->total() }}</h2>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="fa fa-gift fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card border-0 bg-gradient-success text-white shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-2 opacity-75">{{ __('messages.active_rewards') }}</h6>
                                <h2 class="fw-bold display-6 mb-0">{{ $activeRewardsCount }}</h2>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="fa fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card border-0 bg-gradient-warning text-white shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-2 opacity-75">{{ __('messages.total_points_value') }}</h6>
                                <h2 class="fw-bold display-6 mb-0">{{ $totalPointsValue }}</h2>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="fa fa-star fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card border-0 bg-gradient-info text-white shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-2 opacity-75">{{ __('messages.available_quantity') }}</h6>
                                <h2 class="fw-bold display-6 mb-0">{{ $totalQuantity }}</h2>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="fa fa-box fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($rewards->isEmpty())
            <!-- Empty State -->
            <div class="text-center py-8">
                <div class="mb-4">
                    <i class="fa fa-gift fa-4x text-muted opacity-25 mb-3"></i>
                </div>
                <h3 class="text-muted mb-3">{{ __('messages.no_rewards_found') }}</h3>
                <p class="text-muted mb-4">{{ __('messages.start_creating_rewards_to_get_started') }}</p>
                <a href="{{ route('rewards.create') }}" class="btn btn-primary btn-lg px-5 rounded-pill">
                    {{ __('messages.create_first_reward') }}
                </a>
            </div>
        @else
            <!-- Desktop Table View -->
            <div class="d-none d-lg-block">
                <div class="card border-0 shadow-lg rounded-4 ">
                    <div class="card-header bg-transparent border-0 py-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-semibold">{{ __('messages.rewards_list') }}</h5>
                            <div class="d-flex gap-2">
                                <input type="text" id="searchInput" class="form-control rounded-pill"
                                    placeholder="{{ __('messages.search_rewards') }}" style="width: 250px;">
                                <button class="btn btn-outline-secondary rounded-pill px-3">
                                    <i class="fa fa-filter me-1"></i>
                                    {{ __('messages.filter') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3 fw-semibold text-muted">{{ __('messages.reward') }}</th>
                                        <th class="py-3 fw-semibold text-muted">{{ __('messages.quantity') }}</th>
                                        <th class="py-3 fw-semibold text-muted">{{ __('messages.points') }}</th>
                                        <th class="py-3 fw-semibold text-muted">{{ __('messages.group') }}</th>
                                        <th class="py-3 fw-semibold text-muted">{{ __('messages.status') }}</th>
                                        <th class="pe-4 py-3 fw-semibold text-muted text-end">{{ __('messages.actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rewards as $reward)
                                        <tr class="border-bottom border-light" id="reward-row-{{ $reward->id }}">
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="position-relative">
                                                        @if ($reward->hasMedia('rewards_images'))
                                                            <img src="{{ $reward->getFirstMediaUrl('rewards_images') }}"
                                                                alt="{{ $reward->name }}" class="rounded-3 shadow-sm"
                                                                width="60" height="60"
                                                                style="object-fit: cover; cursor: pointer;"
                                                                onclick="openImageModal({{ $reward->id }})">
                                                            <div class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-1"
                                                                style="width: 24px; height: 24px;">
                                                                <i class="fa fa-eye fa-xs"></i>
                                                            </div>
                                                        @else
                                                            <div class="bg-gradient-primary rounded-3 d-flex align-items-center justify-content-center text-white"
                                                                style="width: 60px; height: 60px;">
                                                                <i class="fa fa-gift fa-lg"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-semibold mb-1">{{ $reward->name }}</h6>
                                                        <small class="text-muted">#{{ $reward->id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span id="quantity-{{ $reward->id }}"
                                                        class="fw-bold fs-5">{{ $reward->quantity }}</span>
                                                    @if ($reward->status == \App\Enums\RewardStatus::ACTIVE || $reward->status == \App\Enums\RewardStatus::CANCELLED)
                                                        <button class="btn btn-sm btn-outline-primary rounded-circle p-1"
                                                            onclick="addQuantity({{ $reward->id }})"
                                                            data-bs-toggle="tooltip"
                                                            title="{{ __('messages.add_quantity') }}">
                                                            <i class="fa fa-plus fa-xs"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <span
                                                    class="badge bg-gradient-warning text-dark fs-6 px-3 py-2 rounded-pill">
                                                    <i class="fa fa-star me-1"></i>
                                                    {{ $reward->points }}
                                                </span>
                                            </td>
                                            <td class="py-3">
                                                @if ($reward->group)
                                                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                                        <i class="fa fa-users me-1"></i>
                                                        {{ $reward->group->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="py-3">
                                                @php
                                                    $statusConfig = [
                                                        \App\Enums\RewardStatus::ACTIVE => [
                                                            'class' => 'bg-success-subtle text-success',
                                                            'icon' => 'check-circle',
                                                        ],
                                                        \App\Enums\RewardStatus::CANCELLED => [
                                                            'class' => 'bg-danger-subtle text-danger',
                                                            'icon' => 'times-circle',
                                                        ],
                                                        \App\Enums\RewardStatus::INACTIVE => [
                                                            'class' => 'bg-secondary-subtle text-secondary',
                                                            'icon' => 'pause-circle',
                                                        ],
                                                    ];
                                                    $config =
                                                        $statusConfig[$reward->status] ??
                                                        $statusConfig[\App\Enums\RewardStatus::INACTIVE];
                                                @endphp
                                                <span id="status-badge-{{ $reward->id }}"
                                                    class="badge d-inline-flex align-items-center gap-1 px-3 py-2 rounded-pill {{ $config['class'] }}">
                                                    <i class="fa fa-{{ $config['icon'] }} fa-xs"></i>
                                                    {{ \App\Enums\RewardStatus::getStringValue($reward->status) }}
                                                </span>
                                            </td>
                                            <td class="pe-4 py-3 text-end">
                                                @if ($reward->status == \App\Enums\RewardStatus::ACTIVE)
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <button
                                                            class="btn btn-sm btn-outline-primary rounded-pill px-3 d-flex align-items-center gap-1"
                                                            onclick="addQuantity({{ $reward->id }})">
                                                            <i class="fa fa-plus"></i>
                                                            <span>{{ __('messages.add') }}</span>
                                                        </button>
                                                        <button
                                                            class="btn btn-sm btn-outline-danger rounded-pill px-3 d-flex align-items-center gap-1"
                                                            onclick="cancelReward({{ $reward->id }})">
                                                            <i class="fa fa-times"></i>
                                                            <span>{{ __('messages.cancel') }}</span>
                                                        </button>
                                                    </div>
                                                @elseif($reward->status == \App\Enums\RewardStatus::CANCELLED)
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <button
                                                            class="btn btn-sm btn-outline-primary rounded-pill px-3 d-flex align-items-center gap-1"
                                                            onclick="addQuantity({{ $reward->id }})">
                                                            <i class="fa fa-plus"></i>
                                                            <span>{{ __('messages.add') }}</span>
                                                        </button>
                                                    </div>
                                                @elseif($reward->status == \App\Enums\RewardStatus::INACTIVE)
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <button
                                                            class="btn btn-sm btn-outline-success rounded-pill px-3 d-flex align-items-center gap-1"
                                                            onclick="activateReward({{ $reward->id }})">
                                                            <i class="fa fa-check"></i>
                                                            <span>{{ __('messages.activate') }}</span>
                                                        </button>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="d-lg-none">
                <div class="row g-3">
                    @foreach ($rewards as $reward)
                        <div class="col-12" id="reward-card-{{ $reward->id }}">
                            <div class="card border-0 shadow-sm rounded-4 ">
                                <div class="card-body p-0">
                                    <div class="d-flex align-items-start p-4">
                                        <div class="position-relative me-3">
                                            @if ($reward->hasMedia('rewards_images'))
                                                <img src="{{ $reward->getFirstMediaUrl('rewards_images') }}"
                                                    alt="{{ $reward->name }}" class="rounded-3 shadow-sm" width="80"
                                                    height="80" style="object-fit: cover; cursor: pointer;"
                                                    onclick="openImageModal({{ $reward->id }})">
                                                <div class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-1"
                                                    style="width: 20px; height: 20px;">
                                                    <i class="fa fa-eye fa-2xs"></i>
                                                </div>
                                            @else
                                                <div class="bg-gradient-primary rounded-3 d-flex align-items-center justify-content-center text-white"
                                                    style="width: 80px; height: 80px;">
                                                    <i class="fa fa-gift fa-lg"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="fw-bold mb-1">{{ $reward->name }}</h6>
                                                    <small class="text-muted">#{{ $reward->id }}</small></br>
                                                    <small class="text-muted">{{ __('messages.group_label') }} {{ $reward->group->name }}</small>
                                                </div>
                                                @php
                                                    $statusConfig = [
                                                        \App\Enums\RewardStatus::ACTIVE => [
                                                            'class' => 'bg-success-subtle text-success',
                                                            'icon' => 'check-circle',
                                                        ],
                                                        \App\Enums\RewardStatus::CANCELLED => [
                                                            'class' => 'bg-danger-subtle text-danger',
                                                            'icon' => 'times-circle',
                                                        ],
                                                        \App\Enums\RewardStatus::INACTIVE => [
                                                            'class' => 'bg-secondary-subtle text-secondary',
                                                            'icon' => 'pause-circle',
                                                        ],
                                                    ];
                                                    $config =
                                                        $statusConfig[$reward->status] ??
                                                        $statusConfig[\App\Enums\RewardStatus::INACTIVE];
                                                @endphp
                                                <span id="status-badge-mobile-{{ $reward->id }}"
                                                    class="badge d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill {{ $config['class'] }}">
                                                    <i class="fa fa-{{ $config['icon'] }} fa-xs"></i>
                                                </span>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <div class="bg-light rounded-3 p-2 text-center">
                                                        <small
                                                            class="text-muted d-block">{{ __('messages.quantity') }}</small>
                                                        <span id="quantity-mobile-{{ $reward->id }}"
                                                            class="fw-bold fs-5">{{ $reward->quantity }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="bg-light rounded-3 p-2 text-center">
                                                        <small
                                                            class="text-muted d-block">{{ __('messages.points') }}</small>
                                                        <span class="fw-bold fs-5 text-warning">
                                                            <i class="fa fa-star fa-xs"></i>
                                                            {{ $reward->points }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($reward->status == \App\Enums\RewardStatus::ACTIVE)
                                                <div class="d-flex gap-2">
                                                    <button
                                                        class="btn btn-sm btn-primary rounded-pill flex-fill d-flex align-items-center justify-content-center gap-1 py-2"
                                                        onclick="addQuantity({{ $reward->id }})">
                                                        <i class="fa fa-plus"></i>
                                                        <span>{{ __('messages.add_quantity') }}</span>
                                                    </button>
                                                    <button
                                                        class="btn btn-sm btn-outline-danger rounded-pill flex-fill d-flex align-items-center justify-content-center gap-1 py-2"
                                                        onclick="cancelReward({{ $reward->id }})">
                                                        <i class="fa fa-times"></i>
                                                        <span>{{ __('messages.cancel') }}</span>
                                                    </button>
                                                </div>
                                            @elseif($reward->status == \App\Enums\RewardStatus::CANCELLED)
                                                <div class="d-flex gap-2">
                                                    <button
                                                        class="btn btn-sm btn-primary rounded-pill flex-fill d-flex align-items-center justify-content-center gap-1 py-2"
                                                        onclick="addQuantity({{ $reward->id }})">
                                                        <i class="fa fa-plus"></i>
                                                        <span>{{ __('messages.add_quantity') }}</span>
                                                    </button>
                                                </div>
                                            @elseif($reward->status == \App\Enums\RewardStatus::INACTIVE)
                                                <div class="d-flex gap-2">
                                                    <button
                                                        class="btn btn-sm btn-success rounded-pill flex-fill d-flex align-items-center justify-content-center gap-1 py-2"
                                                        onclick="activateReward({{ $reward->id }})">
                                                        <i class="fa fa-check"></i>
                                                        <span>{{ __('messages.activate') }}</span>
                                                    </button>
                                                </div>
                                            @else
                                                <div class="text-center py-2">
                                                    <span
                                                        class="text-muted">{{ __('messages.no_actions_available') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center pt-2">
                @if ($rewards->hasPages())
                    <nav>
                        <ul class="pagination">
                            {{-- Previous Page Link --}}
                            @if ($rewards->onFirstPage())
                                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $rewards->previousPageUrl() }}"
                                        rel="prev">&laquo;</a>
                                </li>
                            @endif

                            @php
                                $current = $rewards->currentPage();
                                $last = $rewards->lastPage();
                                $start = max($current - 2, 2);
                                $end = min($current + 2, $last - 1);
                            @endphp

                            {{-- First page --}}
                            <li class="page-item {{ $current === 1 ? 'active' : '' }}">
                                <a class="page-link" href="{{ $rewards->url(1) }}">1</a>
                            </li>

                            {{-- Dots before start --}}
                            @if ($start > 2)
                                <li class="page-item disabled"><span class="page-link">…</span></li>
                            @endif

                            {{-- Page range --}}
                            @for ($page = $start; $page <= $end; $page++)
                                <li class="page-item {{ $current === $page ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $rewards->url($page) }}">{{ $page }}</a>
                                </li>
                            @endfor

                            {{-- Dots after end --}}
                            @if ($end < $last - 1)
                                <li class="page-item disabled"><span class="page-link">…</span></li>
                            @endif

                            {{-- Last page --}}
                            @if ($last > 1)
                                <li class="page-item {{ $current === $last ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $rewards->url($last) }}">{{ $last }}</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($rewards->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $rewards->nextPageUrl() }}" rel="next">&raquo;</a>
                                </li>
                            @else
                                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>

        @endif
    </div>

    <!-- Add Quantity Modal (Modern) -->
    <div class="modal fade" id="addQuantityModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 bg-gradient-primary text-white rounded-top-4 py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fa fa-plus-circle fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 fw-bold">{{ __('messages.add_quantity') }}</h5>
                            <small class="opacity-75">{{ __('messages.increase_reward_stock') }}</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label for="add_quantity" class="form-label fw-semibold mb-2">
                            {{ __('messages.quantity_to_add') }}
                            <span class="text-muted">({{ __('messages.minimum_1_unit') }})</span>
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-0">
                                <i class="fa fa-box text-primary"></i>
                            </span>
                            <input type="number" class="form-control border-0 bg-light rounded-end" id="add_quantity"
                                name="quantity" placeholder="0" min="1" style="height: 56px;">
                            <span class="input-group-text bg-light border-0">{{ __('messages.units') }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2"
                        data-bs-dismiss="modal">
                        {{ __('messages.cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold"
                        onclick="updateQuantity()">
                        <i class="fa fa-save me-1"></i>
                        {{ __('messages.update_quantity') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Magnify Image Modal -->
    @foreach ($rewards as $reward)
        <div class="modal fade" id="imageModal{{ $reward->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 bg-light rounded-top-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa fa-image text-primary"></i>
                            <h5 class="modal-title mb-0">{{ $reward->name }}</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        @if ($reward->hasMedia('rewards_images'))
                            <img src="{{ $reward->getFirstMediaUrl('rewards_images') }}" alt="{{ $reward->name }}"
                                class="img-fluid rounded-3 shadow-sm" style="max-height: 70vh; object-fit: contain;">
                        @else
                            <div class="py-5 text-center">
                                <i class="fa fa-image fa-4x text-muted opacity-25 mb-3"></i>
                                <p class="text-muted">{{ __('messages.no_image_available') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Confirmation Modal (Modern) -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 bg-gradient-warning text-dark rounded-top-4 py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-dark bg-opacity-10 rounded-circle p-2">
                            <i class="fa fa-exclamation-triangle fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 fw-bold">{{ __('messages.confirmation_required') }}</h5>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <i class="fa fa-question-circle fa-3x text-warning mb-3"></i>
                        <p class="fs-5 mb-0" id="confirmModalBody"></p>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 flex-fill"
                        data-bs-dismiss="modal">
                        {{ __('messages.cancel') }}
                    </button>
                    <button type="button" class="btn btn-danger rounded-pill px-4 py-2 flex-fill fw-semibold"
                        id="confirmModalYesBtn">
                        <i class="fa fa-check me-1"></i>
                        {{ __('messages.confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Toast Container -->
    <div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-4" style="z-index: 1080;"></div>

    <style>
        .modal-content {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .modal-backdrop {
            backdrop-filter: blur(5px);
        }

        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .badge {
            font-weight: 500;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .table> :not(caption)>*>* {
            padding: 1rem 0.5rem;
        }

        .btn-rounded {
            border-radius: 50px;
        }

        .quantity-badge {
            min-width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
        }
    </style>

    <script>
        let currentRewardId = null;
        let currentModal = null;

        function openImageModal(rewardId) {
            const imageModal = new bootstrap.Modal(document.getElementById('imageModal' + rewardId));
            imageModal.show();
        }

        function addQuantity(rewardId) {
            currentRewardId = rewardId;
            document.getElementById('add_quantity').value = '';
            currentModal = new bootstrap.Modal(document.getElementById('addQuantityModal'));
            currentModal.show();
            document.getElementById('add_quantity').focus();
        }

        function updateQuantity() {
            const quantityToAdd = document.getElementById('add_quantity').value;
            const inputElement = document.getElementById('add_quantity');

            if (!quantityToAdd || isNaN(quantityToAdd) || quantityToAdd < 1) {
                inputElement.classList.add('is-invalid');
                showToast('{{ __('messages.enter_valid_quantity') }}', 'danger');
                return;
            }

            inputElement.classList.remove('is-invalid');

            fetch(`/rewards/${currentRewardId}/add-quantity`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        quantity: parseInt(quantityToAdd)
                    })
                })
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update desktop view
                        const desktopQuantity = document.getElementById(`quantity-${data.reward.id}`);
                        if (desktopQuantity) {
                            desktopQuantity.textContent = data.reward.quantity;
                            animateValue(desktopQuantity, parseInt(desktopQuantity.textContent) - parseInt(
                                quantityToAdd), data.reward.quantity, 500);
                        }

                        // Update mobile view
                        const mobileQuantity = document.getElementById(`quantity-mobile-${data.reward.id}`);
                        if (mobileQuantity) {
                            mobileQuantity.textContent = data.reward.quantity;
                            animateValue(mobileQuantity, parseInt(mobileQuantity.textContent) - parseInt(quantityToAdd),
                                data.reward.quantity, 500);
                        }

                        currentModal.hide();
                        showSuccessMessage('{{ __('messages.quantity_updated_successfully') }}', quantityToAdd);
                    } else {
                        showToast(data.message || '{{ __('messages.failed_update_quantity') }}', 'danger');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('{{ __('messages.error_occurred') }}', 'danger');
                });
        }

        function activateReward(rewardId) {
            const rewardName = document.querySelector(`#reward-row-${rewardId} h6`)?.textContent ||
                document.querySelector(`#reward-card-${rewardId} h6`)?.textContent;

            showConfirmModal(
                `{{ __('messages.confirm_activate_reward') }}<br><strong>"${rewardName}"</strong>?`,
                () => {
                    fetch(`/rewards/${rewardId}/activate`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                            }
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('Network response was not ok');
                            return res.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Update status badges
                                updateStatusBadges(rewardId, data.reward.status, data.status_text);

                                // Enable action buttons for ACTIVE status
                                enableActionButtons(rewardId);

                                showSuccessMessage('{{ __('messages.reward_activated_successfully') }}', null,
                                    'success');
                            } else {
                                showToast(data.message || '{{ __('messages.failed_activate_reward') }}', 'danger');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            showToast('{{ __('messages.error_occurred') }}', 'danger');
                        });
                }
            );
        }

        function cancelReward(rewardId) {
            const rewardName = document.querySelector(`#reward-row-${rewardId} h6`)?.textContent ||
                document.querySelector(`#reward-card-${rewardId} h6`)?.textContent;

            showConfirmModal(
                `{{ __('messages.confirm_cancel_reward') }}<br><strong>"${rewardName}"</strong>?`,
                () => {
                    fetch(`/rewards/${rewardId}/cancel`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                            }
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('Network response was not ok');
                            return res.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Update status badges
                                updateStatusBadges(rewardId, data.reward.status, data.status_text);

                                // Disable action buttons
                                disableActionButtons(rewardId);

                                showSuccessMessage('{{ __('messages.reward_cancelled_successfully') }}', null,
                                    'warning');
                            } else {
                                showToast(data.message || '{{ __('messages.failed_cancel_reward') }}', 'danger');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            showToast('{{ __('messages.error_occurred') }}', 'danger');
                        });
                }
            );
        }

        function updateStatusBadges(rewardId, status, statusText) {
            const statusConfig = {
                {{ \App\Enums\RewardStatus::ACTIVE }}: {
                    class: 'bg-success-subtle text-success',
                    icon: 'check-circle'
                },
                {{ \App\Enums\RewardStatus::CANCELLED }}: {
                    class: 'bg-danger-subtle text-danger',
                    icon: 'times-circle'
                },
                {{ \App\Enums\RewardStatus::INACTIVE }}: {
                    class: 'bg-secondary-subtle text-secondary',
                    icon: 'pause-circle'
                },
            };

            const config = statusConfig[status] || statusConfig[{{ \App\Enums\RewardStatus::INACTIVE }}];

            // Update desktop badge
            const desktopBadge = document.getElementById(`status-badge-${rewardId}`);
            if (desktopBadge) {
                desktopBadge.className =
                    `badge d-inline-flex align-items-center gap-1 px-3 py-2 rounded-pill ${config.class}`;
                desktopBadge.innerHTML = `<i class="fa fa-${config.icon} fa-xs"></i> ${statusText}`;
            }

            // Update mobile badge
            const mobileBadge = document.getElementById(`status-badge-mobile-${rewardId}`);
            if (mobileBadge) {
                mobileBadge.className =
                    `badge d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill ${config.class}`;
                mobileBadge.innerHTML = `<i class="fa fa-${config.icon} fa-xs"></i>`;
            }
        }

        function disableActionButtons(rewardId) {
            // Desktop
            const desktopRow = document.getElementById(`reward-row-${rewardId}`);
            if (desktopRow) {
                const actionsCell = desktopRow.querySelector('td:last-child');
                if (actionsCell) {
                    actionsCell.innerHTML = `
                        <span class="text-muted">{{ __('messages.no_actions_available') }}</span>
                    `;
                }
            }

            // Mobile
            const mobileCard = document.getElementById(`reward-card-${rewardId}`);
            if (mobileCard) {
                const actionsDiv = mobileCard.querySelector('.d-flex.gap-2');
                if (actionsDiv) {
                    actionsDiv.innerHTML = `
                        <div class="text-center py-2 w-100">
                            <span class="text-muted">{{ __('messages.no_actions_available') }}</span>
                        </div>
                    `;
                }
            }
        }

        function enableActionButtons(rewardId) {
            // Desktop
            const desktopRow = document.getElementById(`reward-row-${rewardId}`);
            if (desktopRow) {
                const actionsCell = desktopRow.querySelector('td:last-child');
                if (actionsCell) {
                    actionsCell.innerHTML = `
                        <div class="d-flex justify-content-end gap-2">
                            <button
                                class="btn btn-sm btn-outline-primary rounded-pill px-3 d-flex align-items-center gap-1"
                                onclick="addQuantity(${rewardId})">
                                <i class="fa fa-plus"></i>
                                <span>{{ __('messages.add') }}</span>
                            </button>
                            <button
                                class="btn btn-sm btn-outline-danger rounded-pill px-3 d-flex align-items-center gap-1"
                                onclick="cancelReward(${rewardId})">
                                <i class="fa fa-times"></i>
                                <span>{{ __('messages.cancel') }}</span>
                            </button>
                        </div>
                    `;
                }
            }

            // Mobile
            const mobileCard = document.getElementById(`reward-card-${rewardId}`);
            if (mobileCard) {
                const actionsContainer = mobileCard.querySelector('.flex-grow-1');
                if (actionsContainer) {
                    const existingActions = actionsContainer.querySelector('.d-flex.gap-2:last-child');
                    if (existingActions) {
                        existingActions.innerHTML = `
                            <button
                                class="btn btn-sm btn-primary rounded-pill flex-fill d-flex align-items-center justify-content-center gap-1 py-2"
                                onclick="addQuantity(${rewardId})">
                                <i class="fa fa-plus"></i>
                                <span>{{ __('messages.add_quantity') }}</span>
                            </button>
                            <button
                                class="btn btn-sm btn-outline-danger rounded-pill flex-fill d-flex align-items-center justify-content-center gap-1 py-2"
                                onclick="cancelReward(${rewardId})">
                                <i class="fa fa-times"></i>
                                <span>{{ __('messages.cancel') }}</span>
                            </button>
                        `;
                    }
                }
            }
        }

        function showConfirmModal(message, onConfirm) {
            const modalBody = document.getElementById('confirmModalBody');
            const yesBtn = document.getElementById('confirmModalYesBtn');

            modalBody.innerHTML = message;

            // Clone and replace button to remove previous event listeners
            const newYesBtn = yesBtn.cloneNode(true);
            yesBtn.parentNode.replaceChild(newYesBtn, yesBtn);

            newYesBtn.addEventListener('click', () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
                modal.hide();
                onConfirm();
            });

            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            modal.show();
        }

        function showToast(message, type = 'success', duration = 3000) {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toastId = 'toast-' + Date.now();
            const toastEl = document.createElement('div');
            toastEl.id = toastId;
            toastEl.className = `toast border-0 shadow-lg rounded-3`;
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');

            const bgClass = type === 'success' ? 'bg-success' :
                type === 'danger' ? 'bg-danger' :
                type === 'warning' ? 'bg-warning' : 'bg-info';

            toastEl.innerHTML = `
                <div class="toast-header border-0 text-white ${bgClass} rounded-top-3">
                    <strong class="me-auto">
                        <i class="fa fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'times-circle' : 'info-circle'} me-2"></i>
                        ${type === 'success' ? '{{ __('messages.success') }}' : type === 'danger' ? '{{ __('messages.error') }}' : '{{ __('messages.warning') }}'}
                    </strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body bg-white">
                    ${message}
                </div>
            `;

            container.appendChild(toastEl);
            const toast = new bootstrap.Toast(toastEl, {
                delay: duration
            });
            toast.show();

            toastEl.addEventListener('hidden.bs.toast', () => {
                toastEl.remove();
            });
        }

        function showSuccessMessage(message, quantity = null, type = 'success') {
            let fullMessage = message;
            if (quantity) {
                fullMessage = `${message} (+${quantity} {{ __('messages.units_added') }})`;
            }
            showToast(fullMessage, type);
        }

        function animateValue(element, start, end, duration) {
            if (start === end) return;

            const range = end - start;
            const startTime = performance.now();

            function updateValue(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const current = Math.floor(start + (range * progress));

                element.textContent = current;

                if (progress < 1) {
                    requestAnimationFrame(updateValue);
                }
            }

            requestAnimationFrame(updateValue);
        }

        // Initialize tooltips and search functionality
        document.addEventListener('DOMContentLoaded', () => {
            // Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    const rows = document.querySelectorAll('#reward-row-{{ $reward->id }}');

                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });
            }

            // Show session messages
            @if (session('success'))
                showSuccessMessage('{{ session('success') }}');
            @endif

            @if (session('error'))
                showToast('{{ session('error') }}', 'danger');
            @endif
        });
    </script>
@endsection
