<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <h1>@lang('hr::models/hr_employees.plural')</h1>
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class=" text-muted text-hover-primary">
                            @lang('lang.dashboard')
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        @lang('hr::models/hr_employees.plural')
                    </li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a class="btn btn-sm btn-primary float-right" href="{{ route('hr.employees.create') }}">
                    <i class="fa-solid fa-plus"></i>
                    @lang('crud.add_new')
                </a>
                <a class="btn btn-sm btn-secondary float-right" href="{{ route('hr.attendance.import') }}">
                    <i class="fa-solid fa-file-export"></i>
                    @lang('crud.export_all')
                </a>
                <button type="button" class="btn btn-sm btn-secondary float-right" data-bs-toggle="modal"
                    data-bs-target="#kt_modal_1">
                    <i class="fa-solid fa-file-import"></i>
                    @lang('crud.import')
                </button>

                <div class="modal fade" tabindex="-1" id="kt_modal_1">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-body">
                                <div class="row mb-5">
                                    <div class="form-group col-md-8 col-sm-12 my-auto">
                                        <h2>@lang('hr::models/hr_employees.fields.template')</h2>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <a href="{{ asset('uploads/files/evix_example_employee.xlsx') }}"
                                            class="btn btn-sm btn-primary" download>
                                            <i class="fa-solid fa-file"></i>
                                            @lang('crud.download')
                                        </a>
                                    </div>
                                </div>
                                <hr class="mb-5">
                                {!! Form::open(['route' => 'hr.attendance.import', 'class' => 'row', 'files' => true]) !!}
                                <div class="form-group col-sm-8">
                                    {!! Form::label('file', __('hr::models/hr_employees.fields.file') . ':') !!}
                                    {!! Form::file('file', null, ['class' => 'form-control d-none']) !!}
                                </div>
                                <div class="form-group col-sm-4">
                                    {!! Form::button('Import', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-sm btn-success',
                                    ]) !!}
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <div class="clearfix"></div>
            @if (true)
                <div class="card shadow-sm my-3 ">
                    <div class="card-header collapsible cursor-pointer rotate {{ $start_filter ? 'collapsed' : 'active' }}"
                        aria-expanded="{{ $start_filter }}" wire:click='toggleFilter()'>
                        <h3 class="card-title">
                            <i class="fa-solid fa-filter fs-2 me-2"></i>
                            @lang('crud.search')
                        </h3>
                        <div class="card-toolbar rotate-180">
                            <i class="ki-duotone ki-down fs-1"></i>
                        </div>
                    </div>
                    <div id="kt_docs_card_collapsible" class="collapse {{ $start_filter ? 'show' : '' }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-lg-4 col-md-6 col-sm-12 mb-3">
                                    <label for="username">
                                        @lang('models/employees.fields.username')
                                    </label>
                                    <input type="text" class="form-control" wire:model.lazy="username"
                                        id="username">
                                </div>
                                <div class="form-group col-lg-4 col-md-6 col-sm-12 mb-3">
                                    <label for="job_id">
                                        @lang('hr::models/hr_employees.fields.job_id')
                                    </label>
                                    <select class="form-select" wire:model='job_id'>
                                        <option value="" selected readonly>@lang('hr::lang.select_job')</option>
                                        @forelse ($jobs as $item_id => $item_name)
                                            <option value="{{ $item_id }}">
                                                {{ $item_name }}
                                            </option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>

                                <div class="form-group col-lg-4 col-md-6 col-sm-12 mb-3">
                                    <label for="department_id">
                                        @lang('hr::models/hr_employees.fields.department_id')
                                    </label>
                                    <select class="form-select" wire:model='department_id'>
                                        <option value="" selected readonly>@lang('hr::lang.select_department')
                                        </option>
                                        @forelse ($departments as $item_id => $item_name)
                                            <option value="{{ $item_id }}">
                                                {{ $item_name }}
                                            </option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>

                                <div class="form-group col-lg-4 col-md-6 col-sm-12 mb-3">
                                    <label for="shift_id">
                                        @lang('hr::models/hr_employees.fields.shift_id')
                                    </label>
                                    <select class="form-select" wire:model='shift_id'>
                                        <option value="" selected readonly>@lang('hr::lang.select_shift')</option>
                                        @forelse ($shifts as $item_id => $item_name)
                                            <option value="{{ $item_id }}">
                                                {{ $item_name }}
                                            </option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>

                                <div class="form-group col-sm-4 mb-4">
                                    <label for="pagination">
                                        @lang('crud.pagination')
                                    </label>
                                    <select class="form-select" wire:model='pagination'>
                                        @forelse (config('statusSystem.pagination') as $item_id => $item_name)
                                            <option value="{{ $item_id }}">
                                                {{ $item_name }}
                                            </option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-4">
                            <span wire:click="filter" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                @lang('crud.search')
                                <div class="clearfix" wire:loading wire:target="filter">
                                    <div class="spinner-border spinner-border-sm text-white" role="status">
                                    </div>
                                </div>
                            </span>
                            <span wire:click="resetInputs" class="btn btn-sm btn-danger">
                                <i class="fa-solid fa-circle-xmark"></i>
                                @lang('crud.reset')
                            </span>
                            <span wire:click="custom_export" class="btn btn-sm btn btn-sm btn-secondary">
                                <i class="fa-solid fa-file-export"></i>
                                @lang('crud.export')
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('msg'))
                @php
                    $msg = session('msg');
                @endphp

                @if (!$msg['status'])
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($msg['messages'] as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @else

                @endif
            @endif

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped gy-7 gs-7" id="hr-employees-table">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                                    <th>@lang('hr::models/hr_employees.fields.id')</th>
                                    <th>@lang('hr::models/hr_employees.fields.employee_id')</th>
                                    <th>@lang('hr::models/hr_employees.fields.job_id')</th>
                                    <th>@lang('hr::models/hr_employees.fields.department_id')</th>
                                    <th>@lang('models/employees.fields.phone')</th>
                                    <th>@lang('hr::models/hr_employees.fields.job_level')</th>
                                    <th>@lang('hr::models/hr_employees.fields.specialty')</th>
                                    <th>@lang('hr::models/hr_employees.fields.license_expired_at')</th>
                                    <th>@lang('models/employees.fields.identity_expired_at')</th>
                                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    <tr>
                                        <td>{{ $employee->main_employee->id ?? '' }}</td>
                                        
                                        <td>{{ $employee->main_employee->full_name ?? '' }}</td>
                                        <td>{{ $employee->job->name ?? '' }}</td>
                                        <td>{{ $employee->department->name ?? '' }}</td>
                                        <td>{{ $employee->main_employee->phone ?? '' }}</td>
                                       
                                        <td>{{ $employee->job_level }}</td>
                                        <td>{{ $employee->specialty }}</td>
                                        <td class="upComingCheck">{{ $employee->license_expired_at }}</td>
                                        <td class="upComingCheck">
                                            {{ @optional($employee->main_employee->identity)->identity_expired_at ?? '' }}
                                        </td>
                                        <td style="width: 120px">
                                            {!! Form::open(['route' => ['hr.employees.destroy', $employee->id], 'method' => 'delete']) !!}
                                            <div class='btn-group'>
                                                <a href="{{ route('hr.employees.show', [$employee->id]) }}"
                                                    class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                <a href="{{ route('hr.employees.edit', [$employee->id]) }}"
                                                    class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                                    <i class="fa-solid fa-edit"></i>
                                                </a>
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
                            {{ $employees->onEachSide(2)->links('vendor/livewire/bootstrap') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
