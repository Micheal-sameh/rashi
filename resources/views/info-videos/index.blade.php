@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold display-6 text-dark mb-2">{{ __('messages.info_videos') }}</h1>
                <p class="text-muted mb-0">{{ __('messages.manage_info_videos') }}</p>
            </div>
            <a href="{{ route('info-videos.create') }}"
                class="btn btn-primary btn-lg px-4 py-3 shadow-lg rounded-pill d-flex align-items-center gap-2">
                <i class="fa fa-plus-circle fa-lg"></i>
                <span class="fw-semibold">{{ __('messages.create_info_video') }}</span>
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($infoVideos->isEmpty())
            <!-- Empty State -->
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fa fa-video fa-4x text-muted opacity-25 mb-3"></i>
                </div>
                <h3 class="text-muted mb-3">{{ __('messages.no_info_videos_found') }}</h3>
                <p class="text-muted mb-4">{{ __('messages.start_creating_info_videos') }}</p>
                <a href="{{ route('info-videos.create') }}" class="btn btn-primary btn-lg px-5 rounded-pill">
                    {{ __('messages.create_first_info_video') }}
                </a>
            </div>
        @else
            <!-- Info Videos Table -->
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 fw-semibold text-muted" style="width: 50px;">
                                        <i class="fa fa-grip-vertical"></i>
                                    </th>
                                    <th class="py-3 fw-semibold text-muted">{{ __('messages.rank') }}</th>
                                    <th class="py-3 fw-semibold text-muted">{{ __('messages.name') }}</th>
                                    <th class="py-3 fw-semibold text-muted">{{ __('messages.link') }}</th>
                                    <th class="py-3 fw-semibold text-muted">{{ __('messages.status') }}</th>
                                    <th class="pe-4 py-3 fw-semibold text-muted text-end">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-videos">
                                @foreach ($infoVideos as $video)
                                    <tr class="border-bottom border-light sortable-item" data-id="{{ $video->id }}" style="cursor: move;">
                                        <td class="ps-4 py-3">
                                            <i class="fa fa-grip-vertical text-muted"></i>
                                        </td>
                                        <td class="py-3">
                                            <span class="badge bg-light text-dark border px-3 py-2">{{ $video->rank }}</span>
                                        </td>
                                        <td class="py-3">
                                            <span class="fw-semibold">{{ $video->name }}</span>
                                        </td>
                                        <td class="py-3">
                                            <a href="{{ $video->link }}" target="_blank" class="text-decoration-none">
                                                {{ Str::limit($video->link, 40) }}
                                            </a>
                                        </td>
                                        <td class="py-3">
                                            @if($video->appear == 1)
                                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                                                    <i class="fa fa-eye me-1"></i>
                                                    {{ __('messages.visible') }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">
                                                    <i class="fa fa-eye-slash me-1"></i>
                                                    {{ __('messages.hidden') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="pe-4 py-3 text-end">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('info-videos.edit', $video->id) }}"
                                                   class="btn btn-sm btn-outline-primary rounded-start">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('info-videos.destroy', $video->id) }}"
                                                      style="display: inline-block;"
                                                      onsubmit="return confirm('{{ __('messages.confirm_delete') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-end">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById('sortable-videos');
            if (tbody) {
                const sortable = new Sortable(tbody, {
                    animation: 150,
                    handle: '.sortable-item',
                    onEnd: function(evt) {
                        updateRanks();
                    }
                });

                function updateRanks() {
                    const ranks = {};
                    document.querySelectorAll('.sortable-item').forEach((row, index) => {
                        ranks[row.dataset.id] = index;
                    });

                    fetch('{{ route('info-videos.update-rank') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ ranks: ranks })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Ranks updated successfully');
                        }
                    })
                    .catch(error => {
                        console.error('Error updating ranks:', error);
                    });
                }
            }
        });
    </script>
    @endpush
@endsection
