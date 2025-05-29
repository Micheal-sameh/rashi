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
                <option value="">{{ __('messages.groups') }}</option>
                @foreach ($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- User Table Wrapper -->
    <div id="userTableWrapper" class="w-100">
        @include('users.user-table', ['users' => $users])
    </div>
</div>

<!-- Popup Modal -->
<div id="popupModal" class="popup-modal" style="display:none; justify-content:center; align-items:center;">
    <div class="popup-content">
        <span class="popup-close" onclick="closePopup()">&times;</span>
        <img id="popupImage" src="" alt="Popup Image" />
    </div>
</div>

<!-- Popup CSS -->
<style>
    .zoomable-image {
        transition: transform 0.2s;
        cursor: pointer;
    }
    .zoomable-image:hover {
        transform: scale(1.05);
    }

    .popup-modal {
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.6);
        display: flex;
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
</style>

<!-- JS -->
<script>
    function openPopup(src) {
        document.getElementById('popupImage').src = src;
        document.getElementById('popupModal').style.display = 'flex';
    }

    function closePopup() {
        document.getElementById('popupModal').style.display = 'none';
    }

    // Close on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closePopup();
        }
    });

    // Filtering and Sorting
    let debounceTimeout;
    let currentSortBy = '{{ request('sort_by', '') }}';
    let currentSortDirection = '{{ request('direction', 'asc') }}';

    document.getElementById('nameFilter').addEventListener('input', function () {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(applyFilters, 500);
    });

    document.getElementById('groupFilter').addEventListener('change', applyFilters);

    function applySort(column) {
        if (currentSortBy === column) {
            currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            currentSortBy = column;
            currentSortDirection = 'asc';
        }
        applyFilters();
    }

    function applyFilters() {
        const name = document.getElementById('nameFilter').value.trim();
        const group = document.getElementById('groupFilter').value;

        const query = new URLSearchParams();

        if (name !== '') query.append('name', name);
        if (group !== '') query.append('group_id', group);
        if (currentSortBy !== '') {
            query.append('sort_by', currentSortBy);
            query.append('direction', currentSortDirection);
        }
        query.append('is_filter', 1);

        fetch(`{{ route('users.index') }}?${query.toString()}`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('userTableWrapper').innerHTML = html;
            })
            .catch(err => console.error(err));
    }
</script>
@endsection
