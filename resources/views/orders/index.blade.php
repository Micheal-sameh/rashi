@extends('layouts.sideBar')

@section('content')
    <div class="container py-4" style="max-width: 1200px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold text-primary">{{ __('messages.orders') }}</h1>
        </div>

        <!-- Search Filter Form -->
        <form method="GET" action="{{ route('orders.index') }}" class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="status" class="form-label fw-semibold">{{ __('messages.status') }}</label>
                <select name="status" id="status" class="form-select">
                    <option value="">{{ __('messages.status') }}</option>
                    @foreach (App\Enums\OrderStatus::all() as $value)
                        <option value="{{ $value['value'] }}" {{ request('status') == $value['value'] ? 'selected' : '' }}>
                            {{ $value['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label for="user_id" class="form-label fw-semibold">{{ __('messages.user_name') }}</label>
                <select name="user_id" id="user_id" class="form-select">
                    <option value="">{{ __('messages.users') }}</option>
                    @foreach (App\Models\User::OrderBy('name')->get() as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary shadow-sm">
                    <i class="fa fa-search me-1"></i>{{ __('messages.search') }}
                </button>
            </div>
        </form>

        <!-- Orders Table -->
        <div class="table-responsive shadow-sm rounded-4 overflow-hidden">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('messages.reward') }}</th>
                        <th>{{ __('messages.quantity') }}</th>
                        <th>{{ __('messages.points') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.user_name') }}</th>
                        <th>{{ __('messages.servant') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>
                                <span class="text-primary reward-detail" style="cursor:pointer;"
                                    data-name="{{ $order->reward->name ?? '' }}"
                                    data-points="{{ $order->reward->points ?? '' }}"
                                    data-image="{{ $order->reward?->getFirstMediaUrl('rewards_images') ?: asset('images/default.png') }}">
                                    {{ $order->relationloaded('reward') ? $order->reward->name : '' }}
                                </span>
                            </td>
                            <td>{{ $order->quantity }}</td>
                            <td>{{ $order->points }}</td>
                            <td>
                                <span
                                    class="badge {{ $order->status == \App\Enums\OrderStatus::COMPLETED ? 'bg-success' : ($order->status == \App\Enums\OrderStatus::CANCELLED ? 'bg-danger' : 'bg-warning text-dark') }}">
                                    {{ App\Enums\OrderStatus::getStringValue($order->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="text-primary user-detail" style="cursor:pointer;"
                                    data-name="{{ $order->user->name ?? '' }}"
                                    data-membership_code="{{ $order->user->membership_code ?? '' }}"
                                    data-phone="{{ $order->user->phone ?? '' }}"
                                    data-image="{{ $order->user?->getFirstMediaUrl('profile_images') ?: asset('images/default.png') }}">
                                    {{ $order->relationloaded('user') ? $order->user->name : '' }}
                                </span>
                            </td>
                            <td>
                                <span class="text-primary servant-detail" style="cursor:pointer;"
                                    data-name="{{ $order->servant?->name ?? '' }}"
                                    data-membership_code="{{ $order->servant?->membership_code ?? '' }}"
                                    data-phone="{{ $order->servant?->phone ?? '' }}"
                                    data-image="{{ $order->servant?->getFirstMediaUrl('profile_images') ?: asset('images/default.png') }}">
                                    {{ $order->relationloaded('servant') ? $order->servant?->name : '' }}
                                </span>
                            </td>
                            <td>
                                @if ($order->status !== \App\Enums\OrderStatus::COMPLETED && $order->status !== \App\Enums\OrderStatus::CANCELLED)
                                    <!-- Receive Form -->
                                    <form action="{{ route('orders.received', $order->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-success btn-sm shadow-sm">
                                            <i class="fa fa-check me-1"></i>{{ __('messages.received') }}
                                        </button>
                                    </form>

                                    <!-- Cancel Form -->
                                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST"
                                        class="d-inline ms-1">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-danger btn-sm shadow-sm"
                                            onclick="return confirm('{{ __('Are you sure?') }}')">
                                            <i class="fa fa-times me-1"></i>{{ __('messages.cancel') }}
                                        </button>
                                    </form>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">{{ __('No rewards found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Info Modal -->
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content rounded-4 shadow-sm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="infoModalLabel">{{ __('messages.details') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent"></div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle receive button
                document.querySelectorAll('.receive-order').forEach(button => {
                    button.addEventListener('click', function() {
                        const orderId = this.dataset.id;

                        fetch(`/orders/received/${orderId}`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({})
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    const row = this.closest('tr');
                                    row.querySelector('td:nth-child(4)').textContent = data.status;
                                    row.querySelector('td:nth-child(6)').textContent = data
                                        .servant_name;
                                    this.nextElementSibling?.remove(); // Remove cancel button
                                    this.remove();
                                } else {
                                    alert(data.message || 'Something went wrong');
                                }
                            })
                            .catch(error => {
                                console.error(error);
                                alert('An error occurred');
                            });
                    });
                });

                // Handle cancel button
                document.querySelectorAll('.cancel-order').forEach(button => {
                    button.addEventListener('click', function() {
                        const orderId = this.dataset.id;

                        if (!confirm('Are you sure you want to cancel this order?')) {
                            return;
                        }

                        fetch(`/orders/cancel/${orderId}`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({})
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    const row = this.closest('tr');
                                    row.querySelector('td:nth-child(4)').textContent = data.status;
                                    this.previousElementSibling?.remove(); // Remove receive button
                                    this.remove(); // Remove cancel button
                                } else {
                                    alert(data.message || 'Something went wrong');
                                }
                            })
                            .catch(error => {
                                console.error(error);
                                alert('An error occurred');
                            });
                    });
                });

                // Handle modal info display
                document.querySelectorAll('.reward-detail, .user-detail, .servant-detail').forEach(el => {
                    el.addEventListener('click', function() {
                        let content = '';
                        const imageUrl = this.dataset.image || '{{ asset('images/default.png') }}';

                        if (this.classList.contains('reward-detail')) {
                            content = `<img src="${imageUrl}" alt="Reward Image" class="img-fluid mb-2" style="max-height: 200px;"><br>
                                       <strong>Name:</strong> ${this.dataset.name}<br>
                                       <strong>Points:</strong> ${this.dataset.points}`;
                        } else if (this.classList.contains('user-detail')) {
                            content = `<img src="${imageUrl}" alt="User Image" class="img-fluid mb-2" style="max-height: 200px;"><br>
                                       <strong>Name:</strong> ${this.dataset.name}<br>
                                       <strong>Membership Code:</strong> ${this.dataset.membership_code}<br>
                                       <strong>Phone:</strong> ${this.dataset.phone}`;
                        } else if (this.classList.contains('servant-detail')) {
                            content = `<img src="${imageUrl}" alt="Servant Image" class="img-fluid mb-2" style="max-height: 200px;"><br>
                                       <strong>Name:</strong> ${this.dataset.name}<br>
                                       <strong>Membership Code:</strong> ${this.dataset.membership_code}<br>
                                       <strong>Phone:</strong> ${this.dataset.phone}`;
                        }

                        document.getElementById('modalContent').innerHTML = content;
                        const modal = new bootstrap.Modal(document.getElementById('infoModal'));
                        modal.show();
                    });
                });
            });
        </script>
    @endpush
@endsection
