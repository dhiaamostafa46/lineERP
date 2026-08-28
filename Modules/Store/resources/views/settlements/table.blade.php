<div class="card-body p-0">
    <div class="table-responsive">

        <table class="table table-striped text-center gy-7 gs-7" id="db-categories-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">

                    <th>@lang('store::models/st_settlements.fields.document_number')</th>
                    <th>@lang('store::models/st_settlements.fields.document_date')</th>
                    <th>@lang('store::models/st_settlements.fields.store_id')</th>
                    <th>@lang('store::models/st_settlements.fields.total_items')</th>
                    <th>@lang('store::models/st_settlements.fields.total_quantity')</th>
                    <th>@lang('store::models/st_settlements.fields.total_value')</th>
             
                    <th>@lang('store::models/st_settlements.fields.status')</th>
                    <th class="text-center min-w-100px table-action">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($settlements as $settlement)
                    <tr>
                        <td>{{ $settlement->document_number }}</td>
                        <td>{{ $settlement->document_date->format('Y-m-d') }}</td>
                        <td>{{ $settlement->store->name ?? '' }}</td>
                        <td>{{ $settlement->total_items }}</td>
                        <td>{{ number_format($settlement->total_quantity, 4) }}</td>
                        <td>{{ number_format($settlement->total_value, 2) }}</td>
                       
                        <td>{{ $settlement->status_text }}</td>

                          <td style="width: 170px" class="table-action text-center">
                            <div class='btn-group'>
                                <a href="{{ route('store.settlement.show', [$settlement->id]) }}"
                                    class='btn btn-sm btn-primary' title="@lang('crud.view')">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @can('store.settlement.print')
                                <a href="{{ route('store.settlement.show', [$settlement->id]) }}?print=1" target="_blank"
                                    class='btn btn-sm btn-primary mx-1' title="@lang('lang.print')">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                @endcan
                                
                                @if($settlement->status == \Modules\Store\App\Models\StSettlement::STATUS_DRAFT)
                                    {!! Form::open(['route' => ['store.settlement.authorize', $settlement->id], 'method' => 'POST', 'class' => 'd-inline']) !!}
                                    {!! Form::button('<i class="fa-solid fa-check-double"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-sm btn-primary mx-1',
                                        'title' => 'تعميد',
                                        'onclick' => "return confirm('هل أنت متأكد من تعميد هذا السند؟ سيتم تحديث المخزون والقيد المحاسبي.')",
                                    ]) !!}
                                    {!! Form::close() !!}
                                @endif

                                @if($settlement->is_editable)
                                    <a href="{{ route('store.settlement.edit', [$settlement->id]) }}"
                                        class='btn btn-sm btn-primary mx-1' title="@lang('crud.edit')">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                @endif
                                
                                @if($settlement->is_deletable)
                                    {!! Form::open(['route' => ['store.settlement.destroy', $settlement->id], 'method' => 'delete', 'class' => 'd-inline']) !!}
                                    {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-sm btn-danger',
                                        'title' => 'حذف',
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
            @include('adminlte-templates::common.paginate', ['records' => $settlements])
        </div>
    </div>
</div>
