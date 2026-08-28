<div class="card-body p-0">
    <div class="table-responsive">

        <table class="table table-striped text-center gy-7 gs-7" id="reservations-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('store::models/st_reservations.fields.document_number')</th>
                    <th>@lang('store::models/st_reservations.fields.document_date')</th>
                    <th>@lang('store::models/st_reservations.fields.store_id')</th>
                    <th>@lang('store::models/st_reservations.fields.total_value')</th>
                    <th>@lang('store::models/st_reservations.fields.status')</th>
                    <th class="text-center min-w-100px">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reservations as $reservation)
                    <tr>
                        <td>{{ $reservation->document_number }}</td>
                        <td>{{ $reservation->document_date->format('Y-m-d') }}</td>
                        <td>{{ $reservation->store->name ?? '' }}</td>
                        <td>{{ number_format($reservation->total_value, 2) }}</td>
                        <td>{{ $reservation->status_text }}</td>

                        <td style="width: 170px" class="table-action">
                            <div class='btn-group'>
                                <a href="{{ route('store.reservation.show', [$reservation->id]) }}"
                                    class='btn btn-sm btn-primary float-right' title="@lang('crud.show')">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @can('store.reservation.print')
                                <a href="{{ route('store.reservation.show', [$reservation->id]) }}?print=1" target="_blank"
                                    class='btn btn-sm btn-primary float-right mx-1' title="@lang('lang.print')">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                @endcan

                                @if($reservation->is_editable)
                                    {!! Form::open(['route' => ['store.reservation.authorize', $reservation->id], 'method' => 'POST', 'class' => 'd-inline']) !!}
                                    {!! Form::button('<i class="fa-solid fa-check-double"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-sm btn-primary float-right mx-1',
                                        'title' => 'حجز',
                                        'onclick' => "return confirm('هل أنت متأكد من حجز هذه الكمية؟ سيتم خصمها من المتاح.')",
                                    ]) !!}
                                    {!! Form::close() !!}

                                    <a href="{{ route('store.reservation.edit', [$reservation->id]) }}"
                                        class='btn btn-sm btn-primary float-right mx-1'>
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                @endif

                                @if($reservation->can_be_returned)
                                    {!! Form::open(['route' => ['store.reservation.return', $reservation->id], 'method' => 'POST', 'class' => 'd-inline']) !!}
                                    {!! Form::button('<i class="fa-solid fa-undo"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-sm btn-primary float-right mx-1',
                                        'title' => 'إرجاع للمستودع',
                                        'onclick' => "return confirm('هل أنت متأكد من إرجاع هذه الكمية للمستودع؟')",
                                    ]) !!}
                                    {!! Form::close() !!}
                                @endif

                                @if($reservation->is_deletable)
                                    {!! Form::open(['route' => ['store.reservation.destroy', $reservation->id], 'method' => 'delete', 'class' => 'd-inline']) !!}
                                    {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-sm btn-danger float-right',
                                        'onclick' => "return confirm('Are you sure?')",
                                    ]) !!}
                                    {!! Form::close() !!}
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $reservations])
        </div>
    </div>
</div>
