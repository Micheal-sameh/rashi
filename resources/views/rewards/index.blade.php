@extends('layouts.sideBar')

@section('content')
<div class="container" style="width: 95%;">
    <h2>{{ __('messages.rewards') }}</h2>
    <a class="btn btn-success" style="margin-bottom: 6px" href="{{ route('rewards.create') }}">{{ __('messages.create_reward') }}</a>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($rewards->isEmpty())
        <div class="alert alert-info">{{ __('messages.no_rewards_found') }}</div>
    @else
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.quantity') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.points') }}</th>
                    <th>{{ __('messages.image') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rewards as $index => $reward)
                    <tr id="reward-row-{{ $reward->id }}">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $reward->name }}</td>
                        <td id="quantity-{{ $reward->id }}">{{ $reward->quantity }}</td>
                        <td>{{ \App\Enums\RewardStatus::getStringValue($reward->status) }}</td>
                        <td>{{ $reward->points }}</td>
                        <td>
                            @if ($reward->hasMedia('rewards_images'))
                                <img src="{{ $reward->getFirstMediaUrl('rewards_images') }} "
                                     alt="Reward Image"
                                     width="60"
                                     style="cursor: pointer;"
                                     data-bs-toggle="modal"
                                     data-bs-target="#imageModal{{ $reward->id }}">
                            @else
                                <span class="text-muted">{{ __('messages.no_image') }}</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="addQuantity({{ $reward->id }})">{{ __('messages.add_quantity') }}</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {!! $rewards->links() !!}
        </div>
    @endif
</div>

<!-- Add Quantity Modal -->
<div class="modal fade" id="addQuantityModal" tabindex="-1" aria-labelledby="addQuantityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addQuantityModalLabel">{{ __('messages.add_quantity') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="add_quantity" class="form-label">{{ __('messages.quantity_to_add') }}</label>
                    <input
                        type="number"
                        class="form-control"
                        id="add_quantity"
                        name="quantity"
                        required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                <button type="button" class="btn btn-primary" onclick="updateQuantity()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Magnify Image Modal -->
@foreach ($rewards as $reward)
    <div class="modal fade" id="imageModal{{ $reward->id }}" tabindex="-1" aria-labelledby="imageModalLabel{{ $reward->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel{{ $reward->id }}">{{ $reward->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    @if ($reward->hasMedia('rewards_images'))
                        <img src="{{ $reward->getFirstMediaUrl('rewards_images') }}" alt="Reward Full Image" class="img-fluid">
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

    // Open the Add Quantity Modal and set the rewardId
    function addQuantity(rewardId) {
        currentRewardId = rewardId;

        // Show the modal
        currentModal = new bootstrap.Modal(document.getElementById('addQuantityModal'));
        currentModal.show();
    }

    // Handle the form submission for adding quantity via AJAX
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
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the quantity in the table
                    const quantityCell = document.getElementById(`quantity-${data.reward.id}`);
                    quantityCell.textContent = data.reward.quantity;

                    // Close the modal after success
                    currentModal.hide();
                } else {
                    alert('Failed to update the quantity.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the quantity.');
            });
        } else {
            alert('Please enter a valid quantity.');
        }
    }
</script>

@endsection
