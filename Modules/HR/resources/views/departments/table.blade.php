<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-departments-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_departments.fields.id')</th>
                    <th>@lang('hr::models/hr_departments.fields.name')</th>
                    <th>@lang('hr::models/hr_departments.fields.status')</th>
                    <th>@lang('hr::models/hr_departments.fields.code')</th>
                    <th>@lang('hr::models/hr_departments.fields.type')</th>
                    <th>@lang('hr::models/hr_departments.fields.parent_id')</th>
                    <th>@lang('hr::models/hr_departments.fields.owner_id')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($departments as $department)
                    <tr>
                        <td>{{ $department->id }}</td>
                        <td>{{ $department->name }}</td>
                        <td><span class="{{ $department->status_badge }}">{{ $department->status_text }}</span></td>
                        <td>{{ $department->code }}</td>
                        <td><span class="{{ $department->type_badge }}">{{ $department->type_text }}</span></td>
                        <td>{{ $department->parent->name ?? '' }}</td>
                        <td>{{ $department->owner->username ?? '' }}</td>



                        <td style="width: 120px">
                            {!! Form::open(['route' => ['hr.departments.destroy', $department->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                @can('hr.departments.show')
                                    {{-- <a href="{{ route('hr.departments.show', [$department->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a> --}}
                                @endcan
                                @can('hr.departments.edit')
                                    <a href="{{ route('hr.departments.edit', [$department->id]) }}"
                                        class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                @endcan
                                @can('hr.departments.destroy')
                                    {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-icon btn-sm btn-light-danger btn-xs',
                                        'onclick' => "return confirm('Are you sure?')",
                                    ]) !!}
                                @endcan
                            </div>
                            {!! Form::close() !!}
                        </td>
                        <!--<td>-->
                        {{-- @livewire('hr::trackers.create-modal', ['department' => $department],
                        key($department->id)) --}}
                        <!--</td>-->
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4 {{ $departments->hasPages() ? '' : 'd-none' }}">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $departments])
        </div>
    </div>
</div>
@push('scripts')
    <script src="{{ asset('admin_assets') }}/plugins/custom/formrepeater/formrepeater.bundle.js"></script>
    <script>
        $('.approval_payroll').repeater({
            initEmpty: false,

            defaultValues: {
                'text-input': 'foo'
            },

            show: function() {
                $(this).slideDown();
            },

            hide: function(deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
    </script>
@endpush
