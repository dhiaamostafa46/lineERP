<div class="card-body p-0">
    <div class="table-responsive">
        {{-- <a type="button" class="btn btn-sm btn-primary float-left" data-bs-toggle="modal" data-bs-target="#NewAbsent">@lang('crud.create')</a>
         --}}
        <table class="table table-striped gy-7 gs-7" id="hr-holidays-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_holidays.fields.employee_id')</th>
                    <th>@lang('hr::models/hr_absentrequest.fields.requestdate')</th>
                    <th>@lang('hr::models/hr_holidays.fields.from_at')</th>
                    <th>@lang('hr::models/hr_holidays.fields.end_at')</th>
                    <th>@lang('hr::models/hr_holidays.fields.details')</th>
                    <th>@lang('hr::models/hr_holidays.fields.status')</th>
                    {{-- <th colspan="3" class="text-center">@lang('crud.action')</th> --}}
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($absentrequests as $absent)
                <tr>
                    <td>{{ $absent->employee->username ?? '' }}</td>
                    {{-- <td>
                        @livewire('hr::trackers.get-status', ['model' => $absent], key('trackers_get_status_'.$absent->id))
                      
                    </td> --}}
                    <td>{{ $absent->request_date->format('Y-m-d')}}</td>
                    <td>{{ $absent->from_at}}</td>
                    <td>{{ $absent->end_at}}</td>
                    <td>{{ $absent->details ?? '' }}</td>
                    <td>  <span class="{{ $absent->status_badge }}">{{ $absent->status_text }}</span></td>
                    
                    
                    <td style="width: 120px">
                        {{-- <span class="{{ $absent->status_badge }}">
                            {{ $absent->status_text }}
                        </span> --}}
                        <div class="btn-group">
                            {{-- <a type="button" class="btn btn-sm btn-primary float-left" data-bs-toggle="modal" data-bs-target="#NewAdvance">@lang('crud.create')</a>
              --}}             
                          @if($absent->status == 1)
                            <a type="button" class="btn btn-sm btn-danger" href="/hr/absentstatus/{{$absent->id}}/3">
                                @lang('hr::models/hr_trackers.fields.reject')
                            </a>
                            <a type="button" class="btn btn-sm btn-primary" href="/hr/absentstatus/{{$absent->id}}/2">
                                @lang('hr::models/hr_trackers.fields.approve')
                            </a>
                            @endif
                           
                        {!! Form::open(['route' => ['hr.absentrequests.destroy', $absent->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            {{-- {{ route('hr.absentrequests.show', [$absent->id]) }} --}}
                            {{-- <a href="#"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-check"></i>
                            </a> --}}
                            {{-- <a href="{{ route('hr.holidays.edit', [$absent->id]) }}"
                                class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a> --}}
                            {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                            'type' => 'submit',
                            'class' => 'btn btn-icon btn-sm btn-light-danger btn-xs',
                            'onclick' => "return confirm('Are you sure?')",
                            ]) !!}
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $absentrequests])
        </div>
    </div>
</div>

