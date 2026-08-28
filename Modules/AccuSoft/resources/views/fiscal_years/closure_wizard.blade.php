@extends('layouts.app')

@section('title', __('accusoft::general.fiscal_year_closure'))

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ __('accusoft::general.fiscal_year_closure') }} - {{ $fiscalYear->full_name }}</h3>
    </div>
    <div class="card-body">
        <div id="closure-wizard">
            <!-- Steps Navigation -->
            <ul class="nav nav-pills nav-justified mb-5">
                <li class="nav-item">
                    <a class="nav-link active" id="step-1-tab" data-bs-toggle="pill" href="#step-1">1. {{ __('accusoft::general.check_eligibility') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled" id="step-2-tab" data-bs-toggle="pill" href="#step-2">2. {{ __('accusoft::general.closure_options') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled" id="step-3-tab" data-bs-toggle="pill" href="#step-3">3. {{ __('accusoft::general.confirm_closure') }}</a>
                </li>
            </ul>

            <!-- Steps Content -->
            <div class="tab-content">
                <!-- Step 1: Check Eligibility -->
                <div class="tab-pane fade show active" id="step-1">
                    <div id="validation-loading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">{{ __('accusoft::general.checking_data') }}...</p>
                    </div>
                    
                    <div id="validation-results" class="d-none">
                        <div class="alert alert-danger d-none" id="validation-error-alert">
                            <h4 class="alert-heading">{{ __('accusoft::general.cannot_proceed') }}</h4>
                            <ul id="error-list"></ul>
                        </div>

                        <div class="alert alert-warning d-none" id="validation-warning-alert">
                            <h4 class="alert-heading">{{ __('accusoft::general.warnings_found') }}</h4>
                            <ul id="warning-list"></ul>
                        </div>

                        <div class="alert alert-success d-none" id="validation-success-alert">
                            <h4 class="alert-heading">{{ __('accusoft::general.ready_to_close') }}</h4>
                            <p>{{ __('accusoft::general.all_checks_passed') }}</p>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button class="btn btn-primary" id="btn-next-step-2" disabled>{{ __('accusoft::general.next') }}</button>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Options -->
                <div class="tab-pane fade" id="step-2">
                    <form id="closure-options-form">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="auto_post_drafts" name="auto_post_drafts">
                            <label class="form-check-label" for="auto_post_drafts">
                                {{ __('accusoft::general.auto_post_draft_entries') }}
                                <br>
                                <small class="text-muted">{{ __('accusoft::general.auto_post_draft_warning') }}</small>
                            </label>
                        </div>
                    </form>

                    <div class="d-flex justify-content-between mt-4">
                        <button class="btn btn-secondary" onclick="showStep(1)">{{ __('accusoft::general.previous') }}</button>
                        <button class="btn btn-primary" onclick="showStep(3)">{{ __('accusoft::general.next') }}</button>
                    </div>
                </div>

                <!-- Step 3: Confirmation -->
                <div class="tab-pane fade" id="step-3">
                    <div class="alert alert-info">
                        <h5>{{ __('accusoft::general.summary') }}</h5>
                        <p>{{ __('accusoft::general.fiscal_year') }}: <strong>{{ $fiscalYear->full_name }}</strong></p>
                        <p>{{ __('accusoft::general.estimated_net_result') }}: <strong id="summary-net-result"></strong></p>
                    </div>

                    <p class="text-danger fw-bold">{{ __('accusoft::general.closure_confirmation_warning') }}</p>

                    <div class="d-flex justify-content-between mt-4">
                        <button class="btn btn-secondary" onclick="showStep(2)">{{ __('accusoft::general.previous') }}</button>
                        <button class="btn btn-danger" id="btn-execute-closure">
                            {{ __('accusoft::general.execute_closure') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const fiscalYearId = {{ $fiscalYear->id }};
    let validationData = {};

    $(document).ready(function() {
        checkEligibility();
    });

    function checkEligibility() {
        $.ajax({
            url: "{{ route('accusoft.api.fiscal-years.check-closure', $fiscalYear->id) }}", // Assuming named route exists
            type: 'GET',
            success: function(response) {
                validationData = response.data;
                renderValidationResults(response.data);
            },
            error: function() {
                alert('Error checking eligibility');
            }
        });
    }

    function renderValidationResults(data) {
        $('#validation-loading').addClass('d-none');
        $('#validation-results').removeClass('d-none');

        // Errors
        if (data.errors.length > 0) {
            $('#validation-error-alert').removeClass('d-none');
            $('#error-list').html(data.errors.map(e => `<li>${e}</li>`).join(''));
            $('#btn-next-step-2').prop('disabled', true);
        } else {
            $('#btn-next-step-2').prop('disabled', false);
        }

        // Warnings
        if (data.warnings.length > 0) {
            $('#validation-warning-alert').removeClass('d-none');
            $('#warning-list').html(data.warnings.map(w => `<li>${w}</li>`).join(''));
        }

        // Success
        if (data.success && data.errors.length === 0 && data.warnings.length === 0) {
            $('#validation-success-alert').removeClass('d-none');
        }
        
        // Populate Summary
        $('#summary-net-result').text(data.info.estimated_net_result);
    }

    function showStep(step) {
        $('.nav-link').removeClass('active');
        $(`#step-${step}-tab`).addClass('active').removeClass('disabled');
        $('.tab-pane').removeClass('show active');
        $(`#step-${step}`).addClass('show active');
    }

    $('#btn-next-step-2').click(function() {
        showStep(2);
    });

    $('#btn-execute-closure').click(function() {
        if (!confirm("{{ __('accusoft::general.are_you_sure') }}")) return;

        const options = {
            auto_post_drafts: $('#auto_post_drafts').is(':checked'),
            _token: "{{ csrf_token() }}"
        };

        $.ajax({
            url: "{{ route('accusoft.api.fiscal-years.close', $fiscalYear->id) }}",
            type: 'POST',
            data: options,
            success: function(response) {
                alert(response.message);
                window.location.reload(); 
            },
            error: function(xhr) {
                alert(xhr.responseJSON.message || 'Error occurred');
            }
        });
    });
</script>
@endpush
@endsection
