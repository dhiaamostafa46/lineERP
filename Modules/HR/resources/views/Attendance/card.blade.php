<div class="row">
    @foreach ($Places as $Place)
        <div class="col-md-6 col-xxl-4">
            <div class="card">
                <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                    <div class="symbol symbol-90px symbol-circle mb-5">

                        <img src="/admin_assets/media/logos/locat2.png" alt="image" />
                    </div>
                    <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">{{ $Place->name }}</a>
                    <div class="fw-semibold text-gray-500 mb-6">{{ $Place->address }}</div>
                    <div class="d-flex flex-center flex-wrap text-center">



                        @if ($shiftEmployees->where('places_id' ,$Place->id)->isEmpty())
                            <a onclick="getLocation(1, {{ $Place->id }})"
                                class="border btn btn-light-primary border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                <div class="fs-6 fw-bold text-gray-700">
                                    @lang('hr::models/hr_attendances.presence')
                                </div>
                            </a>

                        @else
                            <a onclick="getLocation(2, {{ $Place->id }})"
                                class="border btn btn-light-warning border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                <div class="fs-6 fw-bold text-gray-700">
                                    @lang('hr::models/hr_attendances.checkout')
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>



<input type="hidden" id="type">
<input type="hidden" id="idplace">

@section('scripts')
    <script>
        function getLocation(type, id) {
            document.getElementById("type").value = type;
            document.getElementById("idplace").value = id;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showLocation, showError);
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        }

        function showLocation(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            const type = document.getElementById("type").value;
            const idplace = document.getElementById("idplace").value;
            if (!type || !idplace) {
                alert('يجب تحديد النوع ومعرف المكان.');
                return;
            }

            $.ajax({
                url: '{{ route('hr.hr-Attendance.Attendance-location') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    latitude: latitude,
                    longitude: longitude,
                    type: type,
                    idplace: idplace
                },
                success: function(response) {
                    alert(response.message);
                    window.location.reload();
                },
                error: function(xhr, status, error) {
                    alert('حدث خطأ أثناء إرسال البيانات: ' + (xhr.responseJSON.message || error));
                }
            });
        }

        function showError(error) {
            let message;
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    message = "تم رفض طلب الموقع من قبل المستخدم.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    message = "معلومات الموقع غير متاحة.";
                    break;
                case error.TIMEOUT:
                    message = "انتهى وقت الطلب للحصول على الموقع.";
                    break;
                case error.UNKNOWN_ERROR:
                    message = "حدث خطأ غير معروف.";
                    break;
            }
            alert(message);
        }
    </script>
@endsection
