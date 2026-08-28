<div class="employee-dashboard">

    {{-- Custom Styles --}}
    <style>
        .employee-dashboard .card {
            transition: all 0.3s ease;
        }

        .employee-dashboard .card-link:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075) !important;
        }

        .employee-dashboard .btn-outline-dashed.active {
            border-color: var(--bs-primary) !important;
            background-color: var(--bs-light-primary) !important;
        }

        .employee-dashboard .symbol img {
            object-fit: cover;
        }
    </style>

    @if (!$openpage)
        {{-- Empty State --}}
        <div class="card card-flush">
            <div class="card-body text-center p-lg-20">
               <img src="{{ asset('admin_assets/media/illustrations/sigma-1/4.png') }}" alt="" class="mw-100 h-200px h-sm-300px mb-10">
            </div>
        </div>
    @else


        {{-- Profile Header --}}



        {{-- Navigation Tabs --}}
        <div class="card shadow-sm mb-7">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title">
                    <span class="card-label fw-bold text-gray-800 fs-3">@lang('hr::models/profileemployees.fields.available_sections')</span>
                </h3>
            </div>
            <div class="card-body pt-4">
                @php
                    $icons = [
                        'main' => 'ki-user',
                        'vacations' => 'ki-calendar-8',
                        'settlement' => 'ki-book',
                        'documents' => 'ki-document',
                        'salary' => 'ki-wallet',
                        'attendance' => 'ki-time',
                        'complaints' => 'ki-shield-search',
                        'evaluations' => 'ki-star',
                        'warnings' => 'ki-information-2',
                    ];
                @endphp
                <div class="row g-3 g-lg-6">
                    @foreach ($employee->availableTabs() as $link)
                        <div class="col-3 col-sm-2 col-md-2 col-lg-1">
                            <a wire:click='changeTab("{{ $link }}")' wire:loading.attr="disabled"
                                wire:target="changeTab('{{ $link }}')"
                                class="btn w-100 btn-outline btn-outline-dashed d-flex flex-column justify-content-center align-items-center p-2 h-100 rounded-3 @if ($tab == $link) active @endif"
                                style="min-height: 80px; cursor: pointer;">

                                <i
                                    class="ki-duotone {{ $icons[$link] ?? 'ki-abstract-28' }} fs-5 mb-1 @if ($tab == $link) text-primary @else text-gray-500 @endif">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                                <span
                                    class="fs-6 fw-semibold @if ($tab == $link) text-primary @else text-gray-700 @endif"
                                    style="white-space: normal; overflow-wrap: break-word; text-align: center;">@lang('hr::models/profileemployees.fields.' . $link)</span>

                                <div wire:loading wire:target="changeTab('{{ $link }}')"
                                    class="spinner-border spinner-border-sm position-absolute top-50 start-50 translate-middle text-primary">
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Dynamic Content --}}
        @if ($tab)
            @livewire('hr::profile.' . $tab, ['employeeid' => $employee->id], key('profile-' . $tab))
        @endif

    @endif





    @push('scripts')
        <script>
            (function() {
                'use strict';

                let isListenerAttached = false;

                function setupAttendanceListener() {
                    if (isListenerAttached) return;

                    document.addEventListener('click', function(e) {
                        const button = e.target.closest('.attendance-btn');
                        if (button) {
                            e.preventDefault();
                            const type = button.getAttribute('data-type');
                            const placeId = button.getAttribute('data-place');

                            if (type && placeId) {
                                handleAttendance(type, placeId);
                            }
                        }
                    });

                    isListenerAttached = true;
                }

                function handleAttendance(type, placeId) {
                    if (!navigator.geolocation) {
                        alert('المتصفح لا يدعم خدمة تحديد الموقع الجغرافي.');
                        return;
                    }

                    let lastPosition = null;
                    const watchId = navigator.geolocation.watchPosition(
                        function(position) {
                            // حفظ آخر موقع تم الحصول عليه
                            lastPosition = position;
                        },
                        function(error) {
                            showGeolocationError(error);
                        }, {
                            enableHighAccuracy: true,
                            maximumAge: 0,
                            timeout: 1000 // نحاول خلال 3 ثوانٍ فقط
                        }
                    );

                    // بعد 3 ثوانٍ، نوقف المراقبة ونرسل آخر موقع تم الحصول عليه
                    setTimeout(() => {
                        navigator.geolocation.clearWatch(watchId);
                        if (lastPosition) {
                            sendAttendanceData(
                                lastPosition.coords.latitude,
                                lastPosition.coords.longitude,
                                type,
                                placeId
                            );
                        } else {
                            alert('تعذر الحصول على الموقع خلال 3 ثوانٍ.');
                        }
                    }, 3000);
                }

                function sendAttendanceData(latitude, longitude, type, placeId) {
                    $.ajax({
                        url: '{{ route('hr.hr-Attendance.Attendance-location') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            latitude: latitude,
                            longitude: longitude,
                            type: type,
                            idplace: placeId
                        },
                        success: function(response) {
                            if (response && response.message) {
                                alert(response.message + '\nLat: ' + latitude + '\nLong: ' + longitude);
                            }
                            window.location.reload();
                        },
                        error: function(xhr) {
                            let errorMessage = 'حدث خطأ أثناء إرسال البيانات';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage += ': ' + xhr.responseJSON.message;
                            }
                            alert(errorMessage);
                        }
                    });
                }

                function showGeolocationError(error) {
                    let message;
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            message = "تم رفض طلب الموقع من قبل المستخدم.";
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message = "معلومات الموقع غير متاحة.";
                            break;
                        case error.TIMEOUT:
                            message = "انتهت مهلة تحديد الموقع.";
                            break;
                        default:
                            message = "حدث خطأ غير معروف.";
                            break;
                    }
                    alert(message);
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', setupAttendanceListener);
                } else {
                    setupAttendanceListener();
                }

                document.addEventListener('livewire:navigated', setupAttendanceListener);
                document.addEventListener('livewire:load', setupAttendanceListener);

            })();
        </script>
    @endpush

</div>
