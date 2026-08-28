<div class="card-body p-0">
    <div class="table-responsive">

        <table class="table table-striped text-center gy-7 gs-7" id="db-categories-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    {{-- <th class="w-10px pe-2">
                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                            <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#db-categories-table .form-check-input" value="1" />
                        </div>
                    </th> --}}
                    <th>@lang('basicdata::models/db_service_points.fields.name')</th>
                      <th>@lang('basicdata::models/db_service_points.fields.code')</th>
                          <th>@lang('basicdata::models/db_service_points.fields.type')</th>
                    <th>@lang('basicdata::models/db_service_points.fields.status')</th>
                       <th>@lang('basicdata::models/db_service_points.fields.created_at')</th>
                    <th class="text-center table-action" >@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($servicePoints as $servicePoint)
                    <tr>
                        {{-- <td>
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="{{ $category->id }}" />
                            </div>
                        </td> --}}
                        <td>{{ $servicePoint->name }}</td>

                         <td>{{ $servicePoint->code }}</td>
                          <td>{{ $servicePoint->type_text }}</td>
                        <td>
                            <span class="badge {{ $servicePoint->status_badge }}">{{ $servicePoint->status_text }}</span>
                        </td>
                         <td>{{ $servicePoint->created_at }}</td>
                        <td style="width: 120px" class="table-action">
                            {!! Form::open(['route' => ['basicdata.service_points.destroy', $servicePoint->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('basicdata.service_points.show', [$servicePoint->id]) }}"
                                    class='btn btn-sm btn-primary float-right'>
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('basicdata.service_points.edit', [$servicePoint->id]) }}"
                                    class='btn btn-sm btn-primary float-right mx-1'>
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-sm btn-primary float-right',
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
            @include('adminlte-templates::common.paginate', ['records' => $servicePoints])
        </div>
    </div>
</div>



