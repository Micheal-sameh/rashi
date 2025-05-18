@extends('layouts.sideBar')

@section('content')
    <div class="container" style="width: 95%;">
        <h2>{{ __('messages.groups') }}</h2>

        <a href="{{ route('groups.create') }}" class="btn btn-success mb-3">
            {{ __('messages.create_groups') }}
        </a>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groups as $index => $group)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <form action="{{ route('groups.update', $group->id) }}" method="POST" class="group-form">
                                @csrf
                                @method('PUT')
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name', $group->name) }}"
                                    class="form-control group-name"
                                    data-original="{{ $group->name }}">
                        </td>
                        <td>
                                <button type="submit" class="btn btn-sm btn-primary mt-2">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No groups found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('.group-form');

        forms.forEach(form => {
            form.addEventListener('submit', function (e) {
                const input = form.querySelector('.group-name');
                const originalValue = input.dataset.original.trim();
                const currentValue = input.value.trim();

                if (originalValue === currentValue) {
                    e.preventDefault();
                    alert('No changes detected.');
                }
            });
        });
    });
</script>
@endpush
