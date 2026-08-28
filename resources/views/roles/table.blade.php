<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="roles-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>  @lang('lang.name')</th>
                    {{-- <th>Guard Name</th> --}}
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    {{-- <td>{{ $role->guard_name }}</td> --}}
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['roles.destroy', $role->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            {{-- <a href="{{ route('roles.show', [$role->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a> --}}
                            @can('roles.edit')
                            <a href="{{ route('roles.edit', [$role->id]) }}"
                                class='btn btn-icon btn-sm btn-primary mx-1 btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            @endcan
                        @if(($role->name != 'موظف' ) && ($role->name !='employee'))


                            @can('roles.destroy')
                            {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                            'type' => 'submit',
                            'class' => 'btn btn-icon btn-sm btn-primary btn-xs',
                            'onclick' => "return confirm('Are you sure?')",
                            ]) !!}
                            @endcan
                        @endif
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4 {{ $roles->total() < 2 ? 'd-none' : '' }}">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $roles])
        </div>
    </div>
</div>
