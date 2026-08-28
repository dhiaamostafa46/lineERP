<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle text-center gs-7 gy-4 mb-0" id="notifications-table">
            <thead>
                <tr class="fw-bold fs-7 text-gray-800 border-bottom border-gray-200 bg-light text-center">
                    <th class="text-center" style="min-width: 140px;">@lang('models/notifications.fields.notification_type')</th>
                    <th class="text-center" style="min-width: 260px;">@lang('models/notifications.plural')</th>
                    <th class="text-center" style="min-width: 160px;">@lang('models/notifications.fields.notifiable_id')</th>
                    <th class="text-center" style="min-width: 100px;">@lang('models/notifications.fields.priority')</th>
                    <th class="text-center" style="min-width: 110px;">@lang('models/notifications.fields.status')</th>
                    <th class="text-center" style="min-width: 140px;">@lang('models/notifications.fields.created_at')</th>
                    <th class="text-center pe-6" style="width: 120px;">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($notifications as $notification)
                    <tr class="text-center {{ is_null($notification->read_at) ? 'bg-light-primary' : '' }}">
                        <!-- 1. Module & Type Badge -->
                        <td class="text-center">
                            <span class="badge badge-light-{{ $notification->color }} fs-8 px-3 py-2 rounded-pill fw-bold d-inline-flex align-items-center">
                                <i class="ki-duotone {{ $notification->icon }} fs-6 me-1 text-{{ $notification->color }}">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                {{ $notification->module_name }}
                            </span>
                        </td>

                        <!-- 2. Title & Notes under Title (Centered) -->
                        <td class="text-center">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <a href="{{ route('notifications.read', [$notification->id]) }}"
                                   class="text-gray-900 text-hover-primary fw-bold fs-6 mb-1 text-decoration-none">
                                    {{ $notification->title ?: $notification->type_name }}
                                </a>

                                @php
                                    $extra = $notification->extra ?? [];
                                    $bodyText = $notification->body ?: ($extra['body'] ?? null);
                                @endphp

                                @if ($bodyText)
                                    <div class="text-gray-700 fs-7 mb-1 fw-semibold text-center">
                                        {{ $bodyText }}
                                    </div>
                                @endif

                                @if (!empty($extra) && is_array($extra))
                                    <div class="d-flex flex-wrap justify-content-center gap-2 fs-8 text-gray-500 mt-1">
                                        @foreach ($extra as $k => $v)
                                            @if ($k !== 'body' && !empty($v))
                                                @php
                                                    $translatedKey = __('models/notifications.info.' . $k);
                                                    if ($translatedKey === 'models/notifications.info.' . $k) {
                                                        $translatedKey = $k;
                                                    }
                                                    $displayVal = is_array($v) ? implode(', ', $v) : $v;
                                                    if ($k === 'Holiday') {
                                                        $displayVal = str_replace('00:00:00', '', $displayVal);
                                                    }
                                                @endphp
                                                <span class="text-gray-600 fw-semibold">
                                                    <span class="text-gray-500 fw-normal me-1">{{ $translatedKey }}:</span>
                                                    {{ $displayVal }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </td>

                        <!-- 3. Beneficiary / Employee Name (Centered Text) -->
                        <td class="text-center">
                            @php
                                $userName = $notification->target_user_name;
                            @endphp
                            @if ($userName)
                                <span class="text-gray-800 fs-7 fw-bold">{{ $userName }}</span>
                            @else
                                <span class="text-muted fs-8">عام للنظام</span>
                            @endif
                        </td>

                        <!-- 4. Priority -->
                        <td class="text-center">
                            <span class="badge badge-light-{{ $notification->priority_color }} fs-8 px-3 py-1.5 rounded-pill fw-bold">
                                {{ $notification->priority_name }}
                            </span>
                        </td>

                        <!-- 5. Status -->
                        <td class="text-center">
                            @if ($notification->status == \App\Models\NotificationItem::STATUS_PENDING)
                                <span class="badge badge-light-warning fw-bold fs-8 px-3 py-1.5 rounded-pill">@lang('models/notifications.status.pending')</span>
                            @elseif ($notification->status == \App\Models\NotificationItem::STATUS_READ)
                                <span class="badge badge-light-info fw-bold fs-8 px-3 py-1.5 rounded-pill">@lang('models/notifications.status.read')</span>
                            @elseif ($notification->status == \App\Models\NotificationItem::STATUS_CONFIRMED)
                                <span class="badge badge-light-success fw-bold fs-8 px-3 py-1.5 rounded-pill">@lang('models/notifications.status.confirmed')</span>
                            @else
                                <span class="badge badge-light-secondary fw-bold fs-8 px-3 py-1.5 rounded-pill">@lang('models/notifications.status.cancelled')</span>
                            @endif
                        </td>

                        <!-- 6. Created At -->
                        <td class="text-center">
                            <div class="d-flex flex-column align-items-center justify-content-center fs-8 text-gray-700">
                                <span class="fw-bold">{{ $notification->created_at ? $notification->created_at->format('Y-m-d H:i') : '-' }}</span>
                                <span class="text-muted fs-9 mt-0.5">{{ $notification->created_at ? $notification->created_at->diffForHumans() : '' }}</span>
                            </div>
                        </td>

                        <!-- 7. Actions -->
                        <td class="text-center pe-6">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('notifications.read', [$notification->id]) }}"
                                   class="btn btn-icon btn-sm btn-light-primary rounded-circle"
                                   title="@lang('crud.show')">
                                    <i class="fa-solid fa-arrow-right-to-bracket fs-7"></i>
                                </a>

                                @if ($notification->status == \App\Models\NotificationItem::STATUS_PENDING)
                                    <a href="{{ route('notifications.confirm', [$notification->id]) }}"
                                       class="btn btn-icon btn-sm btn-light-success rounded-circle"
                                       title="@lang('models/notifications.status.confirmed')">
                                        <i class="fa-solid fa-check fs-7"></i>
                                    </a>
                                @endif

                                {!! Form::open(['route' => ['notifications.destroy', $notification->id], 'method' => 'delete', 'class' => 'd-inline']) !!}
                                {!! Form::button('<i class="fa-solid fa-trash fs-7"></i>', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-icon btn-sm btn-light-danger rounded-circle',
                                    'onclick' => "return confirm('" . __('crud.are_you_sure') . "')",
                                    'title' => __('crud.delete')
                                ]) !!}
                                {!! Form::close() !!}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-10">
                            <div class="symbol symbol-60px circle bg-light-primary mb-3">
                                <span class="symbol-label">
                                    <i class="ki-duotone ki-notification-status fs-2x text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </span>
                            </div>
                            <h5 class="fs-6 text-gray-800 fw-bold mb-1">@lang('models/notifications.no_notifications')</h5>
                            <p class="fs-7 text-gray-500 fw-semibold mb-0">لم يتم العثور على أي إشعارات مطابقة للفلتر المحدد.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($notifications->hasPages())
        <div class="card-footer py-4 bg-light rounded-bottom border-top border-gray-200">
            <div class="d-flex justify-content-center">
                {!! $notifications->links() !!}
            </div>
        </div>
    @endif
</div>
