<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped text-center gy-7 gs-7" id="transfer-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('store::models/st_direct_transfers.fields.document_number')</th>
                    <th>@lang('store::models/st_direct_transfers.fields.document_date')</th>
                    <th>@lang('store::models/st_direct_transfers.fields.from_store_id')</th>
                    <th>@lang('store::models/st_direct_transfers.fields.to_store_id')</th>
                    <th>@lang('store::models/st_direct_transfers.fields.total_items')</th>
                    <th>@lang('store::models/st_direct_transfers.fields.total_quantity')</th>
                    <th>@lang('store::models/st_direct_transfers.fields.status')</th>
                    <th class="text-center min-w-100px table-action">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody class="fs-6 fw-semibold text-gray-600">
                @foreach($transfers as $transfer)
                    <tr>
                        <td>{{ $transfer->document_number }}</td>
                        <td>{{ $transfer->document_date->format('Y-m-d') }}</td>
                        <td>{{ $transfer->fromStore->name ?? '' }}</td>
                        <td>{{ $transfer->toStore->name ?? '' }}</td>
                        <td>{{ $transfer->total_items }}</td>
                        <td>{{ number_format($transfer->total_quantity, 2) }}</td>
                        <td>
                            @php
                                $badgeClass = 'secondary';
                                if ($transfer->status == 1) $badgeClass = 'warning';
                                elseif ($transfer->status == 2) $badgeClass = ($transfer->is_direct ? 'success' : 'info');
                                elseif ($transfer->status == 3) $badgeClass = 'primary';
                                elseif ($transfer->status == 5) $badgeClass = 'success';
                                elseif ($transfer->status == 4) $badgeClass = 'danger';
                                elseif ($transfer->status == 6) $badgeClass = 'warning';
                            @endphp
                            <span class="badge badge-{{ $badgeClass }}">
                                {{ $transfer->status_text }}
                            </span>
                            @if($transfer->return_status == 1)
                                <span class="badge badge-light-danger ms-1">
                                    {{ __('store::ui.partial_return') }}
                                </span>
                            @endif
                        </td>
                        <td style="width: 170px" class="table-action">
                            <div class='btn-group'>
                                <a href="{{ route('store.direct_transfer.show', [$transfer->id]) }}" class='btn btn-sm btn-primary float-right mx-1' title="@lang('crud.show')">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @can('store.direct_transfer.print')
                                <a href="{{ route('store.direct_transfer.show', [$transfer->id]) }}?print=1" target="_blank"
                                    class='btn btn-sm btn-primary float-right mx-1' title="@lang('lang.print')">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                @endcan
                                @if(!$transfer->is_direct && in_array($transfer->status, [2, 3, 6]))
                                    @if(auth()->id() == ($transfer->toStore?->manager_user_id ?? 0) || auth()->user()->hasRole('admin'))
                                    <a href="{{ route('store.direct_transfer.validate', [$transfer->id]) }}" class='btn btn-sm btn-light-success float-right mx-1' title="{{ __('store::ui.receive') }}">
                                        <i class="fa-solid fa-check-double"></i>
                                    </a>
                                    <a href="{{ route('store.direct_transfer.return', [$transfer->id]) }}" class='btn btn-sm btn-light-danger float-right mx-1' title="{{ __('store::ui.return_breadcrumb') }}">
                                        <i class="fa-solid fa-arrow-rotate-left"></i>
                                    </a>
                                    @endif
                                @endif

                                @if($transfer->is_editable)
                                <a href="{{ route('store.direct_transfer.edit', [$transfer->id]) }}" class='btn btn-sm btn-light-info float-right mx-1'>
                                    <i class="fa-solid fa-edit"></i>
                                </a>
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
            @include('adminlte-templates::common.paginate', ['records' => $transfers])
        </div>
    </div>
</div>
