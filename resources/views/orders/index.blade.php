@extends('layouts.sideBar')

@section('content')
    <div class="container" style="width: 95%;">
        <h1 class="mb-4">{{ __('messages.orders') }}</h1>
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
                        <td>{{ $order->relationloaded('reward') ? $order->reward->name : '' }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>{{ $order->points }}</td>
                        <td>{{ App\Enums\OrderStatus::getStringValue($order->status) }}</td>
                        <td>{{ $order->relationloaded('user') ? $order->user->name : '' }}</td>
                        <td>{{ $order->relationloaded('servant') ? $order->servant?->name : '' }}</td>
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const buttons = document.querySelectorAll('.receive-order');

                buttons.forEach(button => {
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
                                    this.remove(); // Remove the "Mark as Received" button
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
            });
        </script>
    @endpush
@endsection
