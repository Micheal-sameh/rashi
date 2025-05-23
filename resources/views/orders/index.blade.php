@extends('layouts.sideBar')

@section('content')
    <div class="container" style="width: 95%;">
        <h1 class="mb-4">{{ __('messages.orders') }}</h1>

        <!-- Search Filter Form -->
        <form method="GET" action="{{ route('orders.index') }}" class="row mb-4">
            <div class="col-md-4">
                {{-- @dd(App\Enums\OrderStatus::all()) --}}
                <label for="status" class="form-label">{{ __('messages.status') }}</label>
                <select name="status" id="status" class="form-select">
                    <option value="">{{ __('messages.status') }}</option>
                    @foreach(App\Enums\OrderStatus::all() as $value)
                        <option value="{{ $value['value'] }}" {{ request('status') == $value ? 'selected' : '' }}>
                            {{ $value['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label for="user_id" class="form-label">{{ __('messages.user_name') }}</label>
                <select name="user_id" id="user_id" class="form-select">
                    <option value="">{{ __('messages.users') }}</option>
                    @foreach(App\Models\User::OrderBy('name')->get() as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">{{ __('messages.search') }}</button>
            </div>
        </form>

        <!-- Orders Table -->
        <table class="table table-bordered table-striped">
            <thead>
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
                            <span class="reward-detail"
                                  style="cursor: pointer; color: blue; text-decoration: underline;"
                                  data-name="{{ $order->reward->name ?? '' }}"
                                  data-points="{{ $order->reward->points ?? '' }}"
                                  data-image="{{ $order->reward?->getFirstMediaUrl('rewards_images') ?: asset('images/default.png') }}">
                                {{ $order->relationloaded('reward') ? $order->reward->name : '' }}
                            </span>
                        </td>
                        <td>{{ $order->quantity }}</td>
                        <td>{{ $order->points }}</td>
                        <td>{{ App\Enums\OrderStatus::getStringValue($order->status) }}</td>
                        <td>
                            <span class="user-detail"
                                  style="cursor: pointer; color: blue; text-decoration: underline;"
                                  data-name="{{ $order->user->name ?? '' }}"
                                  data-membership_code="{{ $order->user->membership_code ?? '' }}"
                                  data-phone="{{ $order->user->phone ?? '' }}"
                                  data-image="{{ $order->user?->getFirstMediaUrl('profile_images') ?: asset('images/default.png') }}">
                                {{ $order->relationloaded('user') ? $order->user->name : '' }}
                            </span>
                        </td>
                        <td>
                            <span class="servant-detail"
                                  style="cursor: pointer; color: blue; text-decoration: underline;"
                                  data-name="{{ $order->servant?->name ?? '' }}"
                                  data-membership_code="{{ $order->servant?->membership_code ?? '' }}"
                                  data-phone="{{ $order->servant?->phone ?? '' }}"
                                  data-image="{{ $order->servant?->getFirstMediaUrl('profile_images') ?: asset('images/default.png') }}">
                                {{ $order->relationloaded('servant') ? $order->servant?->name : '' }}
                            </span>
                        </td>
                        <td>
                            @if ($order->status !== \App\Enums\OrderStatus::COMPLETED)
                                <button class="btn btn-success btn-sm receive-order" data-id="{{ $order->id }}">
                                    {{ __('messages.received') }}
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">{{ __('No rewards found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Info Modal -->
    <div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="infoModalLabel">Details</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="modalContent">
            <!-- Filled dynamically -->
          </div>
        </div>
      </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.receive-order').forEach(button => {
                    button.addEventListener('click', function () {
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
                                row.querySelector('td:nth-child(6)').textContent = data.servant_name;
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

                document.querySelectorAll('.reward-detail, .user-detail, .servant-detail').forEach(el => {
                    el.addEventListener('click', function () {
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
