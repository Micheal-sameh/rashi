@extends('layouts.sideBar')

@section('content')
    <div class="container">
        <h2>{{ __('messages.competitions') }}</h2>

        <a href="{{ route('competitions.create') }}" class="btn btn-success mb-3">
            {{ __('messages.create_competitions') }}
        </a>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($competitions->count())
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.start_at') }}</th>
                        <th>{{ __('messages.end_at') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.image') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($competitions as $competition)
                        @php
                            $btnClasses = [
                                App\Enums\CompetitionStatus::PENDING => 'btn-primary',
                                App\Enums\CompetitionStatus::ACTIVE => 'btn-warning',
                                App\Enums\CompetitionStatus::FINISHED => 'btn-purple',
                                App\Enums\CompetitionStatus::CANCELLED => 'btn-danger',
                            ];
                            $btnClass = $btnClasses[$competition->status] ?? 'btn-secondary';
                        @endphp
                        <tr>
                            <td>{{ $competition->name }}</td>
                            <td>{{ Carbon\Carbon::parse($competition->start_at)->format('d-m-Y') }}</td>
                            <td>{{ Carbon\Carbon::parse($competition->end_at)->format('d-m-Y') }}</td>
                            <td>
                                <form action="{{ route('competitions.changeStatus', $competition->id) }}" method="POST"
                                    class="status-form" data-id="{{ $competition->id }}">
                                    @csrf
                                    @method('put')
                                    <button type="submit" class="btn btn-sm {{ $btnClass }}"
                                        id="status-btn-{{ $competition->id }}">
                                        {{ App\Enums\CompetitionStatus::getStringValue($competition->status) }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                @if ($competition->hasMedia('competitions_images'))
                                    <img src="{{ $competition->getFirstMediaUrl('competitions_images') }}"
                                        alt="Image"
                                        width="60"
                                        class="zoomable-image"
                                        style="cursor: pointer;"
                                        onclick="openPopup(this.src)">
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('competitions.edit', $competition->id) }}" class="btn btn-sm btn-primary"
                                    title="Edit Competition">
                                    <i class="fa fa-edit"></i>
                                </a>

                                <form action="{{ route('competitions.cancel', $competition->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('put')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this competition?')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-center">
                @if ($competitions->hasPages())
                    <div class="pagination">
                        @foreach ($competitions->getUrlRange(1, $competitions->lastPage()) as $page => $url)
                            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <p>No competitions found.</p>
        @endif
    </div>

    <!-- Popup Modal -->
    <div id="popupModal" class="popup-modal">
        <div class="popup-content">
            <span class="popup-close" onclick="closePopup()">&times;</span>
            <img id="popupImage" src="" alt="Popup Image" />
        </div>
    </div>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Popup CSS -->
    <style>
        .popup-modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            position: relative;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        .popup-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .popup-close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 30px;
            font-weight: bold;
            color: #333;
            cursor: pointer;
        }

        .zoomable-image {
            transition: transform 0.2s;
        }

        .zoomable-image:hover {
            transform: scale(1.05);
        }
    </style>

    <!-- Popup Script -->
    <script>
        function openPopup(src) {
            $('#popupImage').attr('src', src);
            $('#popupModal').fadeIn();
        }

        function closePopup() {
            $('#popupModal').fadeOut();
        }

        // ESC key closes popup
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePopup();
            }
        });

        // AJAX Setup for CSRF
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // AJAX status change
        $(document).ready(function () {
            $('.status-form').on('submit', function (e) {
                e.preventDefault();

                let form = $(this);
                let id = form.data('id');
                let url = form.attr('action');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { _method: 'PUT' },
                    success: function (response) {
                        let statusBtn = $('#status-btn-' + id);
                        statusBtn.text(response.competition);
                        statusBtn
                            .removeClass()
                            .addClass('btn btn-sm ' + response.status_class);
                    },
                    error: function () {
                        alert('Failed to update status. Please try again.');
                    }
                });
            });
        });
    </script>
@endsection
