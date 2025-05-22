@extends('layouts.sideBar')

@section('content')
<div class="container" style="width: 95%;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>{{ __('messages.rewards_list') }}</h2>
        <a class="btn btn-success" href="{{ route('rewards.create') }}">{{ __('messages.create_reward') }}</a>
    </div>

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
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $reward->name }}</td>
                        <td>{{ $reward->quantity }}</td>
                        <td>{{ \App\Enums\RewardStatus::getStringValue($reward->status) }}</td>
                        <td>{{ $reward->points }}</td>
                        <td>
                            @if ($reward->hasMedia('rewards_images'))
                            <img src="{{ $reward->getFirstMediaUrl('rewards_images') }}"
                                 alt="Reward Image"
                                 width="60"
                                 style="cursor: pointer;"
                                 data-bs-toggle="modal"
                                 data-bs-target="#imageModal{{ $reward->id }}">

                            <!-- Modal -->
                            <div class="modal fade" id="imageModal{{ $reward->id }}" tabindex="-1" aria-labelledby="imageModalLabel{{ $reward->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="imageModalLabel{{ $reward->id }}">{{ $reward->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('messages.close') }}"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img src="{{ $reward->getFirstMediaUrl('rewards_images') }}" alt="Reward Full Image" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <span class="text-muted">{{ __('messages.no_image') }}</span>
                        @endif
                        </td>
                        <td>
                            {{-- <a href="{{ route('rewards.edit', $reward->id) }}" class="btn btn-sm btn-primary">{{ __('messages.edit') }}</a> --}}

                            {{-- <form action="{{ route('rewards.destroy', $reward->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('{{ __('messages.confirm_delete') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">{{ __('messages.delete') }}</button>
                            </form> --}}
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
@endsection
