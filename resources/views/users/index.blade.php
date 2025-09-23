@extends('layouts.sideBar')

@section('content')
    <div class="container py-4" style="max-width: 1200px;">
        <h1 class="fw-bold text-primary mb-4">{{ __('messages.users') }}</h1>

        <!-- Filter Bar -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <input type="text" id="nameFilter" class="form-control shadow-sm"
                    placeholder="{{ __('messages.filter_by_name') }}">
            </div>
            <div class="col-md-6">
                <select id="groupFilter" class="form-select shadow-sm">
                    <option value="">{{ __('messages.groups') }}</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- User Table -->
        <div id="userTableWrapper" class="table-responsive shadow-sm rounded-4 overflow-hidden">
            @include('users.user-table', ['users' => $users])
        </div>
    </div>

    <!-- Popup Modal -->
    <div id="popupModal" class="popup-modal" style="display:none; justify-content:center; align-items:center;">
        <div class="popup-content shadow-lg">
            <span class="popup-close" onclick="closePopup()">&times;</span>
            <img id="popupImage" src="" alt="Popup Image" />
        </div>
    </div>

    <!-- Popup & Table CSS -->
    <style>
        /* Table hover and styling */
        table.table-hover tbody tr:hover {
            background-color: #f8f9fa;
            cursor: pointer;
            transition: background 0.2s;
        }

        .zoomable-image {
            transition: transform 0.2s;
            cursor: pointer;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .zoomable-image:hover {
            transform: scale(1.05);
        }

        /* Popup modal styling */
        .popup-modal {
            position: fixed;
            z-index: 1050;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.2s ease-in-out;
        }

        .popup-content {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
            text-align: center;
            position: relative;
            animation: scaleIn 0.2s ease-in-out;
        }

        .popup-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .popup-close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 28px;
            font-weight: bold;
            color: #333;
            cursor: pointer;
            transition: color 0.2s;
        }

        .popup-close:hover {
            color: #ff4d4f;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.9);
            }

            to {
                transform: scale(1);
            }
        }

        /* Responsive tweaks */
        @media (max-width: 768px) {
            .popup-content {
                padding: 15px;
            }

            #nameFilter,
            #groupFilter {
                margin-bottom: 10px;
            }
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

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') closePopup();
        });

        // Filtering with debounce
        let debounceTimeout;
        let currentSortBy = '{{ request('sort_by', '') }}';
        let currentSortDirection = '{{ request('direction', 'asc') }}';

        document.getElementById('nameFilter').addEventListener('input', function() {
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

            if (name) query.append('name', name);
            if (group) query.append('group_id', group);
            if (currentSortBy) {
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
