@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold text-primary">{{ __('messages.orders') }}</h1>
            {{-- <button id="testBroadcast" class="btn btn-info btn-sm">
                <i class="fa fa-broadcast-tower me-1"></i>Test WebSocket
            </button> --}}
        </div>

        <!-- Pending Orders Card -->
        <div class="row g-3 mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm border-0 bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 opacity-75">{{ __('messages.pending_orders') }}</h6>
                                <h2 class="mb-0 fw-bold">{{ number_format($pendingOrdersCount) }}</h2>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Filter Form -->
        <form method="GET" action="{{ route('orders.index') }}" class="row g-3 mb-4">
            <div class="col-md-3">
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

            <div class="col-md-3">
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

            <div class="col-md-3">
                <label for="membership_code" class="form-label fw-semibold">{{ __('messages.membership_code') }}</label>
                <input type="text" name="membership_code" id="membership_code" class="form-control"
                       placeholder="{{ __('messages.membership_code') }}" value="{{ request('membership_code') }}">
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary shadow-sm">
                    <i class="fa fa-search me-1"></i>{{ __('messages.search') }}
                </button>
            </div>
        </form>

        <!-- Orders Table (Desktop) -->
        <div class="table-responsive shadow-sm rounded-4  d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('messages.reward') }}</th>
                        <th>{{ __('messages.quantity') }}</th>
                        <th>{{ __('messages.points') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.user_name') }}</th>
                        <th>{{ __('messages.servant') }}</th>
                        <th>{{ __('messages.ordered_at') }}</th>
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
                            <td>{{ $order->created_at->format('d-m-Y') }}</td>
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

                                    <!-- Cancel Button -->
                                    <button type="button" class="btn btn-danger btn-sm shadow-sm cancel-order"
                                        data-id="{{ $order->id }}">
                                        <i class="fa fa-times me-1"></i>{{ __('messages.cancel') }}
                                    </button>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">{{ __('No rewards found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Orders Cards (Mobile) -->
        <div class="d-block d-md-none">
            @forelse($orders as $order)
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <strong>{{ __('messages.reward') }}:</strong><br>
                                <span class="text-primary reward-detail" style="cursor:pointer;"
                                    data-name="{{ $order->reward->name ?? '' }}"
                                    data-points="{{ $order->reward->points ?? '' }}"
                                    data-image="{{ $order->reward?->getFirstMediaUrl('rewards_images') ?: asset('images/default.png') }}">
                                    {{ $order->relationloaded('reward') ? $order->reward->name : '' }}
                                </span>
                            </div>
                            <div class="col-6">
                                <strong>{{ __('messages.quantity') }}:</strong><br>
                                {{ $order->quantity }}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <strong>{{ __('messages.points') }}:</strong><br>
                                {{ $order->points }}
                            </div>
                            <div class="col-6">
                                <strong>{{ __('messages.ordered_at') }}:</strong><br>
                                {{ $order->created_at->format('d-m-Y') }}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <strong>{{ __('messages.status') }}:</strong><br>
                                <span
                                    class="badge {{ $order->status == \App\Enums\OrderStatus::COMPLETED ? 'bg-success' : ($order->status == \App\Enums\OrderStatus::CANCELLED ? 'bg-danger' : 'bg-warning text-dark') }}">
                                    {{ App\Enums\OrderStatus::getStringValue($order->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <strong>{{ __('messages.user_name') }}:</strong><br>
                                <span class="text-primary user-detail" style="cursor:pointer;"
                                    data-name="{{ $order->user->name ?? '' }}"
                                    data-membership_code="{{ $order->user->membership_code ?? '' }}"
                                    data-phone="{{ $order->user->phone ?? '' }}"
                                    data-image="{{ $order->user?->getFirstMediaUrl('profile_images') ?: asset('images/default.png') }}">
                                    {{ $order->relationloaded('user') ? $order->user->name : '' }}
                                </span>
                            </div>
                            <div class="col-6">
                                <strong>{{ __('messages.servant') }}:</strong><br>
                                <span class="text-primary servant-detail" style="cursor:pointer;"
                                    data-name="{{ $order->servant?->name ?? '' }}"
                                    data-membership_code="{{ $order->servant?->membership_code ?? '' }}"
                                    data-phone="{{ $order->servant?->phone ?? '' }}"
                                    data-image="{{ $order->servant?->getFirstMediaUrl('profile_images') ?: asset('images/default.png') }}">
                                    {{ $order->relationloaded('servant') ? $order->servant?->name : '' }}
                                </span>
                            </div>
                        </div>
                        @if ($order->status !== \App\Enums\OrderStatus::COMPLETED && $order->status !== \App\Enums\OrderStatus::CANCELLED)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <!-- Receive Form -->
                                    <form action="{{ route('orders.received', $order->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-success btn-sm shadow-sm">
                                            <i class="fa fa-check me-1"></i>{{ __('messages.received') }}
                                        </button>
                                    </form>

                                    <!-- Cancel Button -->
                                    <button type="button" class="btn btn-danger btn-sm shadow-sm cancel-order"
                                        data-id="{{ $order->id }}">
                                        <i class="fa fa-times me-1"></i>{{ __('messages.cancel') }}
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center text-muted">{{ __('No rewards found.') }}</div>
            @endforelse
        </div>
    </div>
            <div class="d-flex justify-content-center pt-3">
            @if ($orders->hasPages())
                <nav>
                    <ul class="pagination">
                        {{-- Previous Page Link --}}
                        @if ($orders->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">&laquo;</a>
                            </li>
                        @endif

                        @php
                            $current = $orders->currentPage();
                            $last = $orders->lastPage();
                            $start = max($current - 2, 2);
                            $end = min($current + 2, $last - 1);
                        @endphp

                        {{-- First page --}}
                        <li class="page-item {{ $current === 1 ? 'active' : '' }}">
                            <a class="page-link" href="{{ $orders->url(1) }}">1</a>
                        </li>

                        {{-- Dots before start --}}
                        @if ($start > 2)
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        @endif

                        {{-- Page range --}}
                        @for ($page = $start; $page <= $end; $page++)
                            <li class="page-item {{ $current === $page ? 'active' : '' }}">
                                <a class="page-link" href="{{ $orders->url($page) }}">{{ $page }}</a>
                            </li>
                        @endfor

                        {{-- Dots after end --}}
                        @if ($end < $last - 1)
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        @endif

                        {{-- Last page --}}
                        @if ($last > 1)
                            <li class="page-item {{ $current === $last ? 'active' : '' }}">
                                <a class="page-link" href="{{ $orders->url($last) }}">{{ $last }}</a>
                            </li>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($orders->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">&raquo;</a>
                            </li>
                        @else
                            <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                        @endif
                    </ul>
                </nav>
            @endif
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

    <!-- Cancel Confirmation Modal -->
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title" id="cancelOrderModalLabel">
                        <i class="fa fa-exclamation-triangle me-2"></i>{{ __('messages.confirm_cancellation') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fa fa-times-circle fa-4x text-danger mb-3"></i>
                    <p class="fs-5 mb-0">{{ __('messages.are_you_sure_cancel_order') }}</p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>{{ __('messages.no') }}
                    </button>
                    <button type="button" class="btn btn-danger px-4" id="confirmCancelOrder">
                        <i class="fa fa-check me-1"></i>{{ __('messages.yes_cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- New Order Notification Modal -->
    <div class="modal fade" id="newOrderModal" tabindex="-1" aria-labelledby="newOrderModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title" id="newOrderModalLabel">
                        <i class="fa fa-bell me-2"></i>{{ __('messages.new_order_created') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fa fa-shopping-cart fa-4x text-success mb-3"></i>
                    <h5 class="mb-3" id="newOrderTitle">{{ __('New Order Received!') }}</h5>
                    <div class="text-start">
                        <p class="mb-2"><strong>{{ __('messages.order') }} ID:</strong> <span id="newOrderId"></span></p>
                        <p class="mb-2"><strong>{{ __('messages.user_name') }}:</strong> <span id="newOrderCustomer"></span></p>
                        <p class="mb-2"><strong>{{ __('messages.reward') }}:</strong> <span id="newOrderReward"></span></p>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-success px-4" onclick="window.location.reload()">
                        <i class="fa fa-refresh me-1"></i>{{ __('Refresh Page') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let orderIdToCancel = null;

                // Test WebSocket broadcast button
                document.getElementById('testBroadcast')?.addEventListener('click', function() {
                    console.log('🧪 Testing WebSocket broadcast...');
                    fetch('/orders/test-broadcast')
                        .then(response => response.json())
                        .then(data => {
                            console.log('✅ Test broadcast response:', data);
                            if (data.success) {
                                alert('Test event sent! Check console for WebSocket messages.');
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('❌ Test broadcast error:', error);
                            alert('Error sending test event: ' + error.message);
                        });
                });

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
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        orderIdToCancel = this.dataset.id;
                        const cancelModal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
                        cancelModal.show();
                    });
                });

                // Confirm cancel order
                document.getElementById('confirmCancelOrder').addEventListener('click', function() {
                    if (!orderIdToCancel) return;

                    fetch(`/orders/cancel/${orderIdToCancel}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({})
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw new Error(data.message || 'Something went wrong');
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                const row = document.querySelector(`button.cancel-order[data-id="${orderIdToCancel}"]`)?.closest('tr');
                                if (row) {
                                    const statusBadge = row.querySelector('td:nth-child(4) .badge');
                                    if (statusBadge) {
                                        statusBadge.textContent = data.status;
                                        statusBadge.className = 'badge bg-danger';
                                    }
                                    const receiveBtn = row.querySelector('.receive-order');
                                    const cancelBtn = row.querySelector('.cancel-order');
                                    if (receiveBtn) receiveBtn.remove();
                                    if (cancelBtn) cancelBtn.remove();
                                }
                                bootstrap.Modal.getInstance(document.getElementById('cancelOrderModal')).hide();
                                orderIdToCancel = null;
                            } else {
                                alert(data.message || 'Something went wrong');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert(error.message || 'An error occurred while cancelling the order');
                            bootstrap.Modal.getInstance(document.getElementById('cancelOrderModal')).hide();
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

                // Real-time order updates via WebSocket
                console.log('📡 Setting up real-time order updates...');

                if (typeof Echo !== 'undefined') {
                    console.log('✅ Echo is available');
                    console.log('🔌 Subscribing to orders channel...');

                    const ordersChannel = Echo.channel('orders');

                    ordersChannel.listen('.order.created', (e) => {
                        console.log('✅ New order received via WebSocket:', e);

                        // Play notification sound
                        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuAze/aiTYIGGS44tyYSAoOVKzn77BdGAg+ltf0xnElBSl+zPLaiDcIF2W56t2XSAoPU6vl76tZFgo8lNT0wnMoCDqPzu7fjhwMOqPU8sxxJQUpfsvy2og3CBhlueuel0YLDBKq5fCtXhkHPJLZ88JxJQUsd8zz24k4CBZgtOrclkgKDlOr5e+rWRYKPJPU9MJxKAg5js/u3I0bCzug0fPMcSYFKX7L8tmJNwgXZLjp3JdICw0Sq+XwrF4ZBzyR2fPCcSUFLHfM89uJOAgVYLPq3JZICg5Tq+XvqlkWCjyS1PTBcSgINY7P7tyNGws7otDzzHEmBSl+zO/aiTcIF2S46dqYSAoNEqrl8KxeGQc8kdnzwnElBSx3zPPbiTgIFWCz6tyWSAoOU6rl76pZFgo8kdTzwXEoCDaOzu7cjhsLO6HP88txJgUpfsvy2YgyB');
                        audio.play().catch(e => console.log('⚠️ Audio play failed:', e));

                        // Show browser notification
                        if ('Notification' in window && Notification.permission === 'granted') {
                            new Notification('New Order!', {
                                body: `Order #${e.order.id} created by ${e.order.user_name}`,
                                icon: '/favicon.ico'
                            });
                        }

                        // Update modal content
                        document.getElementById('newOrderId').textContent = e.order.id;
                        document.getElementById('newOrderCustomer').textContent = e.order.user_name;
                        document.getElementById('newOrderReward').textContent = e.order.reward_name;

                        // Show popup modal
                        const newOrderModal = new bootstrap.Modal(document.getElementById('newOrderModal'));
                        newOrderModal.show();

                        console.log('🔔 New order popup displayed');
                    });

                    // Subscribe success handler
                    ordersChannel.subscribed(() => {
                        console.log('✅ Successfully subscribed to orders channel');
                    });

                    // Error handler
                    ordersChannel.error((error) => {
                        console.error('❌ Orders channel error:', error);
                    });

                    // Listen for all events on this channel for debugging
                    ordersChannel.listenForWhisper('*', (e) => {
                        console.log('👂 Whisper event received:', e);
                    });

                    // Request notification permission
                    if ('Notification' in window && Notification.permission === 'default') {
                        Notification.requestPermission().then(permission => {
                            console.log('🔔 Notification permission:', permission);
                        });
                    }
                } else {
                    console.error('❌ Echo is not defined. WebSocket connection failed.');
                    console.log('Please check:');
                    console.log('1. Is Soketi running? (npx soketi start)');
                    console.log('2. Are Echo scripts loaded?');
                    console.log('3. Check browser console for errors');
                }
            });
        </script>
    @endpush
@endsection
