@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>{{ __('messages.create_point_transfer') }}</h4>
                    </div>

                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('point-transfers.store') }}" id="transferForm">
                            @csrf

                            <!-- Family Code Selection -->
                            <div class="mb-4">
                                <label for="family_code" class="form-label fw-semibold">
                                    <i class="fas fa-users me-1"></i>{{ __('messages.family_code') }}
                                </label>
                                <div class="input-group">
                                    <input type="text"
                                           class="form-control @error('family_code') is-invalid @enderror"
                                           id="family_code"
                                           name="family_code"
                                           placeholder="E1C1F001"
                                           value="{{ old('family_code', request('family_code')) }}"
                                           required>
                                    <button type="button" class="btn btn-secondary" id="loadFamilyBtn">
                                        <i class="fas fa-sync"></i> {{ __('messages.load_members') }}
                                    </button>
                                </div>
                                <small class="text-muted">{{ __('messages.enter_family_code_help') }}</small>
                                @error('family_code')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div id="familyMembersSection" style="display: {{ $familyMembers ? 'block' : 'none' }};">
                                <!-- Sender -->
                                <div class="mb-4">
                                    <label for="sender_id" class="form-label fw-semibold">
                                        <i class="fas fa-user-minus me-1"></i>{{ __('messages.sender') }}
                                    </label>
                                    <select class="form-select @error('sender_id') is-invalid @enderror"
                                            id="sender_id"
                                            name="sender_id"
                                            required>
                                        <option value="">{{ __('messages.select_sender') }}</option>
                                        @if($familyMembers)
                                            @foreach($familyMembers as $member)
                                                <option value="{{ $member->id }}"
                                                        data-points="{{ $member->points }}"
                                                        {{ old('sender_id') == $member->id ? 'selected' : '' }}>
                                                    {{ $member->name }} ({{ $member->membership_code }}) - {{ $member->points }} {{ __('messages.points') }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('sender_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="senderBalance" class="mt-2"></div>
                                </div>

                                <!-- Receiver -->
                                <div class="mb-4">
                                    <label for="receiver_id" class="form-label fw-semibold">
                                        <i class="fas fa-user-plus me-1"></i>{{ __('messages.receiver') }}
                                    </label>
                                    <select class="form-select @error('receiver_id') is-invalid @enderror"
                                            id="receiver_id"
                                            name="receiver_id"
                                            required>
                                        <option value="">{{ __('messages.select_receiver') }}</option>
                                        @if($familyMembers)
                                            @foreach($familyMembers as $member)
                                                <option value="{{ $member->id }}"
                                                        data-points="{{ $member->points }}"
                                                        {{ old('receiver_id') == $member->id ? 'selected' : '' }}>
                                                    {{ $member->name }} ({{ $member->membership_code }}) - {{ $member->points }} {{ __('messages.points') }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('receiver_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="receiverBalance" class="mt-2"></div>
                                </div>

                                <!-- Points -->
                                <div class="mb-4">
                                    <label for="points" class="form-label fw-semibold">
                                        <i class="fas fa-coins me-1"></i>{{ __('messages.points') }}
                                    </label>
                                    <input type="number"
                                           class="form-control @error('points') is-invalid @enderror"
                                           id="points"
                                           name="points"
                                           min="1"
                                           value="{{ old('points') }}"
                                           placeholder="{{ __('messages.enter_points') }}"
                                           required>
                                    @error('points')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="pointsValidation" class="mt-2"></div>
                                </div>

                                <!-- Reason -->
                                <div class="mb-4">
                                    <label for="reason" class="form-label fw-semibold">
                                        <i class="fas fa-comment me-1"></i>{{ __('messages.reason') }}
                                    </label>
                                    <textarea class="form-control @error('reason') is-invalid @enderror"
                                              id="reason"
                                              name="reason"
                                              rows="3"
                                              maxlength="255"
                                              placeholder="{{ __('messages.optional_reason') }}">{{ old('reason') }}</textarea>
                                    @error('reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Validation Summary -->
                                <div id="validationSummary" class="alert" style="display: none;"></div>

                                <!-- Submit Buttons -->
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('point-transfers.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>{{ __('messages.back') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-paper-plane me-1"></i>{{ __('messages.transfer_points') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Load family members
    $('#loadFamilyBtn').click(function() {
        const familyCode = $('#family_code').val();
        if (!familyCode) {
            alert('{{ __("messages.please_enter_family_code") }}');
            return;
        }

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __("messages.loading") }}');

        $.ajax({
            url: '{{ route("point-transfers.getFamilyMembers") }}',
            method: 'POST',
            data: {
                family_code: familyCode,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    updateMemberDropdowns(response.members);
                    $('#familyMembersSection').slideDown();
                }
            },
            error: function(xhr) {
                alert('{{ __("messages.error_loading_members") }}');
            },
            complete: function() {
                $('#loadFamilyBtn').prop('disabled', false).html('<i class="fas fa-sync"></i> {{ __("messages.load_members") }}');
            }
        });
    });

    // Update dropdowns with family members
    function updateMemberDropdowns(members) {
        const senderSelect = $('#sender_id');
        const receiverSelect = $('#receiver_id');

        senderSelect.empty().append('<option value="">{{ __("messages.select_sender") }}</option>');
        receiverSelect.empty().append('<option value="">{{ __("messages.select_receiver") }}</option>');

        members.forEach(member => {
            const option = `<option value="${member.id}" data-points="${member.points}">
                ${member.name} (${member.membership_code}) - ${member.points} {{ __("messages.points") }}
            </option>`;
            senderSelect.append(option);
            receiverSelect.append(option);
        });
    }

    // Show sender balance
    $('#sender_id').change(function() {
        const points = $(this).find(':selected').data('points');
        if (points !== undefined) {
            $('#senderBalance').html(`<span class="badge bg-info">{{ __("messages.available") }}: ${points} {{ __("messages.points") }}</span>`);
        }
        validateTransfer();
    });

    // Show receiver balance
    $('#receiver_id').change(function() {
        const points = $(this).find(':selected').data('points');
        if (points !== undefined) {
            $('#receiverBalance').html(`<span class="badge bg-info">{{ __("messages.current") }}: ${points} {{ __("messages.points") }}</span>`);
        }
        validateTransfer();
    });

    // Validate points input
    $('#points').on('input', function() {
        validateTransfer();
    });

    // Real-time validation
    function validateTransfer() {
        const senderId = $('#sender_id').val();
        const receiverId = $('#receiver_id').val();
        const points = parseInt($('#points').val());

        if (!senderId || !receiverId || !points) {
            return;
        }

        $.ajax({
            url: '{{ route("point-transfers.validate") }}',
            method: 'POST',
            data: {
                sender_id: senderId,
                receiver_id: receiverId,
                points: points,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                const summary = $('#validationSummary');
                if (response.valid) {
                    summary.removeClass('alert-danger').addClass('alert-success')
                           .html('<i class="fas fa-check-circle"></i> {{ __("messages.transfer_valid") }}')
                           .slideDown();
                    $('#submitBtn').prop('disabled', false);
                } else {
                    summary.removeClass('alert-success').addClass('alert-danger')
                           .html('<i class="fas fa-exclamation-circle"></i> ' + response.errors.join('<br>'))
                           .slideDown();
                    $('#submitBtn').prop('disabled', true);
                }
            }
        });
    }
});
</script>
@endpush
