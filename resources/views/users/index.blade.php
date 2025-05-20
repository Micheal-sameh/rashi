@extends('layouts.sideBar')

@section('content')
<div class="container" style="width: 95%;">
    <h1>{{ __('messages.users') }}</h1>

    <!-- Filter Bar -->
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" id="nameFilter" class="form-control" placeholder="{{ __('messages.filter_by_name') }}">
        </div>
        <div class="col-md-6">
            <select id="groupFilter" class="form-control">
                <option value="">{{ __('messages.all_groups') }}</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Table Wrapper -->
    <div id="userTableWrapper">
        @include('users.user-table', ['users' => $users])
    </div>
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
        width: 30%;
        height: 60%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.9);
    }

    .modal-content {
        margin: auto;
        display: block;
        max-width: 80%;
        max-height: 100%;
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

<!-- JS for Modal and Filter -->
<script>
    function openModal(src) {
        document.getElementById('imageModal').style.display = "block";
        document.getElementById('modalImage').src = src;
    }

    function closeModal() {
        document.getElementById('imageModal').style.display = "none";
    }

    let debounceTimeout;

    document.getElementById('nameFilter').addEventListener('input', function () {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(applyFilters, 500);
    });

    document.getElementById('groupFilter').addEventListener('change', applyFilters);

    function applyFilters() {
        const name = document.getElementById('nameFilter').value;
        const group = document.getElementById('groupFilter').value;

        fetch(`{{ route('users.index') }}?name=${encodeURIComponent(name)}&group=${group}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('userTableWrapper').innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
</script>
@endsection
