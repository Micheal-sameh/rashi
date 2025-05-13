@extends('layouts.sideBar')

@section('content')
<div class="container">
    <h2>{{__('messages.competitions')}}</h2>

    <a href="{{ route('competitions.create') }}" class="btn btn-success mb-3">{{__('messages.create_competitions')}}</a>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($competitions->count())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{{__('messages.name')}}</th>
                    <th>{{__('messages.start_at')}}</th>
                    <th>{{__('messages.end_at')}}</th>
                    <th>{{__('messages.status')}}</th>
                    <th>{{__('messages.image')}}</th>
                    <th>{{__('messages.actions')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($competitions as $competition)
                    <tr>
                        <td>{{ $competition->name }}</td>
                        <td>{{ Carbon\Carbon::parse($competition->start_at)->format('d-m-Y') }}</td>
                        <td>{{  Carbon\Carbon::parse($competition->end_at)->format('d-m-Y') }}</td>
                        <td>
                            @php
                                $btnClasses = [
                                    App\Enums\CompetitionStatus::PENDING => 'btn-primary',
                                    App\Enums\CompetitionStatus::ACTIVE => 'btn-warning',
                                    App\Enums\CompetitionStatus::FINISHED => 'btn-purple',
                                    App\Enums\CompetitionStatus::CANCELLED => 'btn-danger',
                                ];
                                $btnClass = $btnClasses[$competition->status] ?? 'btn-secondary';
                            @endphp

                            <form action="{{ route('competitions.changeStatus', $competition->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $btnClass }}">
                                    {{ App\Enums\CompetitionStatus::getStringValue($competition->status) }}
                                </button>
                            </form>
                        </td>

                        <td>
                            @if($competition->hasMedia('competitions_images'))
                                <img src="{{ $competition->getFirstMediaUrl('competitions_images') }}"
                                     alt="Image"
                                     width="60"
                                     class="zoomable-image"
                                     style="cursor: pointer;"
                                     onclick="openModal(this.src)">
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <!-- Edit Button -->
                            <a href="{{ route('competitions.edit', $competition->id) }}" class="btn btn-sm btn-primary" title="Edit Competition">
                                <i class="fa fa-edit"></i>
                            </a>

                            <!-- Delete Form -->
                            <form action="{{ route('competitions.cancel', $competition->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('put')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this competition?')"><i class="fa fa-trash"></i></button>
                            </form>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="text-center">
            @if($competitions->hasPages())
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

<!-- Modal HTML -->
<div id="imageModal" class="modal" onclick="closeModal()">
    <span class="close">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<!-- Modal CSS -->
<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        padding-top: 60px;
        left: 0;
        top: 0;
        width: 70%;
        height: 70%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.9);
    }

    .modal-content {
        margin: auto;
        display: block;
        max-width: 80%;
        max-height: 80%;
    }

    .close {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }
</style>

<!-- Modal JavaScript -->
<script>
    function openModal(src) {
        document.getElementById('imageModal').style.display = "block";
        document.getElementById('modalImage').src = src;
    }

    function closeModal() {
        document.getElementById('imageModal').style.display = "none";
    }
</script>
@endsection
