@extends('layouts.sideBar')

@section('content')
<div class="container">
    <h2>{{__('messages.quizzes')}}</h2>

    <a href="{{ route('quizzes.create') }}" class="btn btn-success mb-3">{{__('messages.create_quizzes')}}</a>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($quizzes->count())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{{__('messages.name')}}</th>
                    <th>{{__('messages.date')}}</th>
                    <th>{{__('messages.actions')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quizzes as $quiz)
                    <tr>
                        <td>{{ $quiz->name }}</td>
                        <td>{{ Carbon\Carbon::parse($quiz->date)->format('d-m-Y') }}</td>

                        </td>

                        <td>
                            <!-- Edit Button -->
                            <a href="{{ route('quizzes.edit', $quiz->id) }}" class="btn btn-sm btn-primary" title="Edit quiz">
                                <i class="fa fa-edit"></i>
                            </a>

                            <!-- Delete Form -->
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="text-center">
            @if($quizzes->hasPages())
                <div class="pagination">
                    @foreach ($quizzes->getUrlRange(1, $quizzes->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <p>No quizzes found.</p>
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
