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
                    <th>@lang('basicdata::models/db_categories.fields.name')</th>
                    <th>@lang('basicdata::models/db_categories.fields.parent_id')</th>
                    <th>@lang('basicdata::models/db_categories.fields.img')</th>
                      <th>@lang('basicdata::models/db_categories.fields.sort')</th>
                    <th>@lang('basicdata::models/db_categories.fields.status')</th>
                    <th>@lang('basicdata::models/db_categories.fields.type')</th>
                       <th>@lang('basicdata::models/db_categories.fields.created_at')</th>
                    <th class="text-center table-action" >@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        {{-- <td>
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="{{ $category->id }}" />
                            </div>
                        </td> --}}
                        <td>
                            @if($category->parent_id)
                                <span class="ms-10 text-muted">↳ {{ $category->name }}</span>
                            @else
                                <span class="fw-bold">{{ $category->name }}</span>
                            @endif
                        </td>
                        <td>{{ $category->parent?->name ?? '-' }}</td>
                        <td> <img src="{{ $category->imgThumbPath }}" alt="Category Image" style="max-height:50px;"></td>
                         <td>{{ $category->sort }}</td>
                        <td>
                            <span class="badge {{ $category->status_badge }}">{{ $category->status_text }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $category->type == 1 ? 'badge-light-success' : 'badge-light-danger' }}">
                                {{ $category->type_text }}
                            </span>
                        </td>
                         <td>{{ $category->created_at->format('Y-m-d') }}</td>
                        <td style="width: 120px" class="table-action">
                            {!! Form::open(['route' => ['basicdata.categories.destroy', $category->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('basicdata.categories.show', [$category->id]) }}"
                                    class='btn btn-sm btn-primary float-right'>
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('basicdata.categories.edit', [$category->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $categories])
        </div>
    </div>
</div>



