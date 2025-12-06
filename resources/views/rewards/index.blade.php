@extends('layouts.sideBar')

@section('content')
    <div class="container py-4" style="max-width: 1200px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">{{ __('messages.rewards') }}</h2>
            <a href="{{ route('rewards.create') }}" class="btn btn-success shadow-sm">
                <i class="fa fa-plus-circle me-1"></i> {{ __('messages.create_reward') }}
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($rewards->isEmpty())
            <div class="alert alert-info shadow-sm">{{ __('messages.no_rewards_found') }}</div>
        @else
            <!-- Desktop Table View -->
            <div class="d-none d-lg-block">
                <div class="table-responsive shadow-sm rounded-4 overflow-hidden">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.quantity') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.points') }}</th>
                                <th>{{ __('messages.group') }}</th>
                                <th>{{ __('messages.image') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rewards as $index => $reward)
                                <tr id="reward-row-{{ $reward->id }}" class="align-middle">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-semibold">{{ $reward->name }}</td>
                                    <td id="quantity-{{ $reward->id }}">{{ $reward->quantity }}</td>
                                    <td>
                                        <span class="badge {{ $reward->status == 1 ? 'bg-success' : 'bg-secondary' }}">
                                            {{ \App\Enums\RewardStatus::getStringValue($reward->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $reward->points }}</td>
                                    <td>{{ $reward->group ? $reward->group->name : __('messages.no_group') }}</td>
                                    <td>
                                        @if ($reward->hasMedia('rewards_images'))
                                            <img src="{{ $reward->getFirstMediaUrl('rewards_images') }}" alt="Reward Image"
                                                width="60" class="rounded shadow-sm" style="cursor: pointer;"
                                                data-bs-toggle="tooltip" title="{{ __('messages.click_to_zoom') }}"
                                                data-bs-target="#imageModal{{ $reward->id }}"
                                                onclick="new bootstrap.Modal(document.getElementById('imageModal{{ $reward->id }}')).show()">
                                        @else
                                            <span class="text-muted">{{ __('messages.no_image') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary shadow-sm"
                                            onclick="addQuantity({{ $reward->id }})">
                                            <i class="fa fa-plus me-1"></i>{{ __('messages.add_quantity') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="d-lg-none">
                <div class="row g-3">
                    @foreach ($rewards as $index => $reward)
                        <div class="col-12">
                            <div class="card shadow-sm h-100">
                                <div class="card-body d-flex align-items-center p-3">
                                    <div class="me-3">
                                        @if ($reward->hasMedia('rewards_images'))
                                            <img src="{{ $reward->getFirstMediaUrl('rewards_images') }}" alt="Reward Image"
                                                width="80" height="80" class="rounded shadow-sm" style="cursor: pointer; object-fit: cover;"
                                                data-bs-toggle="tooltip" title="{{ __('messages.click_to_zoom') }}"
                                                data-bs-target="#imageModal{{ $reward->id }}"
                                                onclick="new bootstrap.Modal(document.getElementById('imageModal{{ $reward->id }}')).show()">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                <i class="fa fa-gift text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="card-title fw-bold mb-0">{{ $reward->name }}</h6>
                                            <small class="text-muted">#{{ $index + 1 }}</small>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <small class="text-muted"><i class="fa fa-box me-1"></i>{{ __('messages.quantity') }}: <span id="quantity-{{ $reward->id }}">{{ $reward->quantity }}</span></small>
                                            <small class="text-muted"><i class="fa fa-star me-1"></i>{{ __('messages.points') }}: {{ $reward->points }}</small>
                                        </div>
                                        <div class="mb-2">
                                            <span class="badge {{ $reward->status == 1 ? 'bg-success' : 'bg-secondary' }}">
                                                {{ \App\Enums\RewardStatus::getStringValue($reward->status) }}
                                            </span>
                                        </div>
                                        <button class="btn btn-sm btn-primary"
                                            onclick="addQuantity({{ $reward->id }})">
                                            <i class="fa fa-plus me-1"></i>{{ __('messages.add_quantity') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {!! $rewards->links() !!}
            </div>
        @endif
    </div>

    <!-- Add Quantity Modal -->
    <div class="modal fade" id="addQuantityModal" tabindex="-1" aria-labelledby="addQuantityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-sm rounded-4">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addQuantityModalLabel">{{ __('messages.add_quantity') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="add_quantity" class="form-label">{{ __('messages.quantity_to_add') }}</label>
                    <input type="number" class="form-control" id="add_quantity" name="quantity"
                        placeholder="Enter quantity" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                    <button type="button" class="btn btn-primary"
                        onclick="updateQuantity()">{{ __('messages.save') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Magnify Image Modal -->
    @foreach ($rewards as $reward)
        <div class="modal fade" id="imageModal{{ $reward->id }}" tabindex="-1"
            aria-labelledby="imageModalLabel{{ $reward->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 shadow-sm">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="imageModalLabel{{ $reward->id }}">{{ $reward->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-3">
                        @if ($reward->hasMedia('rewards_images'))
                            <img src="{{ $reward->getFirstMediaUrl('rewards_images') }}" alt="Reward Full Image"
                                class="img-fluid rounded shadow-sm">
                        @else
                            <p>{{ __('messages.no_image') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        let currentRewardId = null;
        let currentModal = null;

        function addQuantity(rewardId) {
            currentRewardId = rewardId;
            currentModal = new bootstrap.Modal(document.getElementById('addQuantityModal'));
            currentModal.show();
        }

        function updateQuantity() {
            const quantityToAdd = document.getElementById('add_quantity').value;

            if (quantityToAdd && !isNaN(quantityToAdd) && quantityToAdd > 0) {
                fetch(`/rewards/${currentRewardId}/add-quantity`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({
                            quantity: quantityToAdd
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById(`quantity-${data.reward.id}`).textContent = data.reward.quantity;
                            currentModal.hide();
                        } else {
                            alert('{{ __('messages.failed_update_quantity') }}');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('{{ __('messages.error_occurred') }}');
                    });
            } else {
                alert('{{ __('messages.enter_valid_quantity') }}');
            }
        }

        // Enable tooltips
        document.addEventListener('DOMContentLoaded', () => {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(el => new bootstrap.Tooltip(el))
        });
    </script>
@endsection
