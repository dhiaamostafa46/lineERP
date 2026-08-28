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
                    <th>@lang('basicdata::models/db_units.fields.name')</th>
                      <th>@lang('basicdata::models/db_units.fields.conversion_factor')</th>
                    <th>@lang('basicdata::models/db_units.fields.status')</th>
                       <th>@lang('basicdata::models/db_units.fields.created_at')</th>
                    <th class="text-center table-action" >@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($units as $unit)
                    <tr>
                        {{-- <td>
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="{{ $category->id }}" />
                            </div>
                        </td> --}}
                        <td>{{ $unit->name }}</td>

                         <td>{{ $unit->conversion_factor }}</td>
                        <td>
                            <span class="badge {{ $unit->status_badge }}">{{ $unit->status_text }}</span>
                        </td>
                         <td>{{ $unit->created_at }}</td>
                        <td style="width: 120px" class="table-action">
                            {!! Form::open(['route' => ['basicdata.units.destroy', $unit->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('basicdata.units.show', [$unit->id]) }}"
                                    class='btn btn-sm btn-primary float-right'>
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('basicdata.units.edit', [$unit->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $units])
        </div>
    </div>
</div>



