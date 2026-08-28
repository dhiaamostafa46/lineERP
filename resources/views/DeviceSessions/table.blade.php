<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped table-hover gy-7 gs-7" id="hr-DeviceSessions-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('models/DeviceSessions.fields.user_id')</th>
                    <th>@lang('models/DeviceSessions.fields.device_name')</th>
                    <th>@lang('models/DeviceSessions.fields.device_type')</th>
                    <th>@lang('models/DeviceSessions.fields.ip_address')</th>
                    <th>@lang('models/DeviceSessions.fields.last_activity_at')</th>
                    <th>@lang('models/DeviceSessions.fields.status')</th>
                    <th class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($DeviceSessions as $deviceSession)
                    <tr>
                        {{-- اسم المستخدم --}}
                        <td>{{ $deviceSession->user->name ?? 'N/A' }}</td>

                        {{-- اسم الجهاز أو user agent --}}
                        <td>
                            {{ $deviceSession->device_name ?? $deviceSession->device_type . ' - ' . $deviceSession->browser }}
                        </td>

                        {{-- نوع الجهاز + نظام التشغيل + أيقونة --}}
                        <td>
                            @if ($deviceSession->device_type === 'mobile')
                                <i class="fa-solid fa-mobile-screen-button text-primary"></i>
                            @elseif($deviceSession->device_type === 'tablet')
                                <i class="fa-solid fa-tablet-screen-button text-warning"></i>
                            @else
                                <i class="fa-solid fa-desktop text-success"></i>
                            @endif
                            {{ ucfirst($deviceSession->device_type) }} ({{ $deviceSession->os ?? '' }})
                        </td>

                        {{-- IP Address --}}
                        <td>{{ $deviceSession->ip ?? 'N/A' }}</td>

                        {{-- آخر نشاط --}}
                        <td>{{ optional($deviceSession->last_activity_at)->diffForHumans() ?? 'N/A' }}</td>

                        {{-- حالة الجهاز --}}
                        <td class="{{$deviceSession->status_badge}}">

                            {{ $deviceSession->status_text }}

                        </td>

                        {{-- أزرار الإجراءات --}}
                        <td class="text-center w-120px">

                            <div class='btn-group'>
                                @can('DeviceSessions.show')
                                    <form action="{{ route('DeviceSessions.show', $deviceSession->id) }}" method="GET">
                                        @csrf
                                        @method('PATCH')
                                        @if ($deviceSession->is_active)
                                            <button type="submit" class='btn btn-icon btn-sm btn-primary mx-1 btn-xs'
                                                title="">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        @else
                                            <button type="submit" class='btn btn-icon btn-sm btn-primary btn-xs'>
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        @endif
                                    </form>
                                @endcan

                                {!! Form::open(['route' => ['DeviceSessions.destroy', $deviceSession->id], 'method' => 'delete']) !!}
                                @can('DeviceSessions.destroy')
                                    {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-icon btn-sm btn-primary mx-1 btn-xs',
                                        'onclick' => "return confirm('هل أنت متأكد من الحذف؟')",
                                        'title' => 'حذف الجلسة',
                                    ]) !!}
                                @endcan
                                {!! Form::close() !!}
                            </div>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($DeviceSessions->hasPages())
        <div class="card-footer clearfix py-4">
            <div class="float-right">
                @include('adminlte-templates::common.paginate', ['records' => $DeviceSessions])
            </div>
        </div>
    @endif
</div>
