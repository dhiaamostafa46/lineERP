<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-EndService-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('hr::models/hr_end_service.fields.id')</th>
                    <th>@lang('hr::models/hr_end_service.fields.employee')</th>
                    <th>@lang('hr::models/hr_end_service.fields.end_date')</th>
                    <th>@lang('hr::models/hr_end_service.fields.description')</th>
                    <th>@lang('hr::models/hr_end_service.fields.reason')</th>
                    <th>@lang('hr::models/hr_end_service.fields.reward_amount')</th>
                    {{-- <th>@lang('hr::models/hr_end_service.fields.approved')</th> --}}
                    <th>@lang('hr::models/hr_end_service.fields.status')</th>

                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($EndServices as $EndService)
                <tr>
                    <td>{{ $EndService->id }}</td>
                    <td>{{ $EndService->employee->username ?? 'غير متوفر' }}</td>
                    <td>{{ $EndService->end->format('Y-m-d') }}</td>
                    <td>{{ $EndService->description }}</td>
                    <td>{{ $EndService->getReasonTextAttribute() }}</td>
                    <td>{{ $EndService->reward_amount }}</td>
                    {{-- <td><span class="{{ $EndService->approved ? 'badge badge-success' : 'badge badge-danger' }}">{{ $EndService->approved ? __('lang.approved') : __('lang.pending') }}</span></td> --}}
                   <td>
                    @livewire('hr::trackers.get-status', ['model' => $EndService], key('trackers_get_status'))
                   </td>



                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.EndService.destroy', $EndService->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            @can('hr.EndService.show')
                            <a href="{{ route('hr.EndService.show', [$EndService->id]) }}" class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @endcan
                            @can('hr.EndService.edit')
                            <a href="{{ route('hr.EndService.edit', [$EndService->id]) }}" class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            @endcan
                            @can('hr.EndService.destroy')
                            {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                            'type' => 'submit',
                            'class' => 'btn btn-icon btn-sm btn-light-danger btn-xs',
                            'onclick' => "return confirm('هل أنت متأكد؟')",
                            ]) !!}
                            @endcan
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4 {{ $EndServices->total() < 10 ? 'd-none' : '' }}">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $EndServices])
        </div>
    </div>
</div>
