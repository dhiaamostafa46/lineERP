<div class="card-body p-0">
    <div class="table-responsive">

        <table class="table table-striped text-center gy-7 gs-7" id="db-categories-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">

                    <th>@lang('finance::models/fnc_safe.fields.name')</th>
                    {{-- <th>@lang('finance::models/fnc_safe.fields.payment_method')</th> --}}


                    <th>@lang('finance::models/fnc_safe.fields.status')</th>
                    <th>@lang('finance::models/fnc_safe.fields.created_at')</th>
                    <th class="text-center table-action">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($safes as $safe)
                    <tr>
                        <td>{{ $safe->name }}</td>
                        {{-- <td>{{ $safe->payment_method_text }}</td> --}}
                        <td>
                            <span class="badge {{ $safe->status_badge }}">{{ $safe->status_text }}</span>
                        </td>
                        <td>{{ $safe->created_at->format('Y-m-d') }}</td>
                        <td style="width: 120px" class="table-action">
                            {!! Form::open(['route' => ['fnc.safes.destroy', $safe->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('fnc.safes.show', [$safe->id]) }}"
                                    class='btn btn-sm btn-primary float-right'>
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('fnc.safes.edit', [$safe->id]) }}"
                                    class='btn btn-sm btn-primary float-right mx-1'>
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                {{-- {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-sm btn-primary float-right',
                                    'onclick' => "return confirm('" . __('crud.are_you_sure') . "')",
                                ]) !!} --}}
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
            @include('adminlte-templates::common.paginate', ['records' => $safes])
        </div>
    </div>
</div>
