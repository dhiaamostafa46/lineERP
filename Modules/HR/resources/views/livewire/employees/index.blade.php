<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <h1>@lang('hr::models/hr_employees.plural')</h1>
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">
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
                <a class="btn btn-sm btn-secondary float-right" href="{{ route('hr.employees.export') }}">
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
                                {!! Form::open(['route' => 'hr.employees.import', 'class' => 'row', 'files' => true]) !!}
                                <div class="form-group col-sm-8">
                                    {!! Form::label('file', __('hr::models/hr_employees.fields.file') . ':') !!}
                                    {!! Form::file('file', null, ['class' => 'form-control']) !!}
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

            {{-- قسم الفلترة --}}
            @if (true)
                <div class="card shadow-sm my-3">
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
                                            <option value="{{ $item_id }}">{{ $item_name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                                <div class="form-group col-lg-4 col-md-6 col-sm-12 mb-3">
                                    <label for="department_id">
                                        @lang('hr::models/hr_employees.fields.department_id')
                                    </label>
                                    <select class="form-select" wire:model='department_id'>
                                        <option value="" selected readonly>@lang('hr::lang.select_department')</option>
                                        @forelse ($departments as $item_id => $item_name)
                                            <option value="{{ $item_id }}">{{ $item_name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                                <div class="form-group col-lg-4 col-md-6 col-sm-12 mb-3">
                                    <label for="branch_id">
                                        @lang('hr::models/hr_employees.fields.branch_id')
                                    </label>
                                    <select class="form-select" wire:model='branch_id'>
                                        <option value="" selected readonly>@lang('hr::lang.select_branch')</option>
                                        @forelse ($branchs as $item_id => $item_name)
                                            <option value="{{ $item_id }}">{{ $item_name }}</option>
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
                                            <option value="{{ $item_id }}">{{ $item_name }}</option>
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
                                            <option value="{{ $item_id }}">{{ $item_name }}</option>
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
                                    <div class="spinner-border spinner-border-sm text-white" role="status"></div>
                                </div>
                            </span>
                            <span wire:click="resetInputs" class="btn btn-sm btn-danger">
                                <i class="fa-solid fa-circle-xmark"></i>
                                @lang('crud.reset')
                            </span>
                            <span wire:click="custom_export" class="btn btn-sm btn-secondary">
                                <i class="fa-solid fa-file-export"></i>
                                @lang('crud.export')
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- عرض رسائل الخطأ بعد الاستيراد - تختفي بعد دقيقتين --}}
            @if (session('import_errors'))
                <div class="badge d-block text-start w-100 p-4 mb-4 shadow-sm border-0"
                    style="background: #fff4f4; border-radius: 16px; position: relative;" id="import-errors-alert">

                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 45px; height: 45px;">
                                <i class="fa-solid fa-triangle-exclamation fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-1 fw-bold text-danger">حدثت أخطاء أثناء استيراد الموظفين</h5>
                                <small class="text-muted">يرجى مراجعة التفاصيل أدناه لتصحيح الأخطاء.</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="badge"
                            aria-label="Close"></button>
                    </div>

                    <hr class="border-danger opacity-50 my-3">

                    <div class="mb-3">
                        <span class="badge bg-danger text-white fs-6 px-3 py-2 rounded-pill">
                            <i class="fa-solid fa-list me-2"></i>
                            عدد الأخطاء: <strong>{{ count(session('import_errors')) }}</strong>
                        </span>
                    </div>

                    <div class="accordion accordion-flush" id="importErrorsAccordion">
                        @foreach (session('import_errors') as $index => $error)
                            <div class="accordion-item border rounded-3 mb-2 overflow-hidden">
                                <h2 class="accordion-header" id="heading{{ $index }}">
                                    <button class="accordion-button collapsed bg-light" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}"
                                        aria-expanded="false" aria-controls="collapse{{ $index }}">
                                        <span class="badge bg-danger me-3">الصف {{ $error['row'] }}</span>
                                        <strong class="text-dark">{{ $error['message'] }}</strong>
                                    </button>
                                </h2>
                                <div id="collapse{{ $index }}" class="accordion-collapse collapse"
                                    aria-labelledby="heading{{ $index }}"
                                    data-bs-parent="#importErrorsAccordion">
                                    <div class="accordion-body"
                                        style="background-color: #fff8f8; border-top: 2px solid #dc3545;">
                                        @if (!empty($error['details']))
                                            <table class="table table-sm table-borderless mb-0">
                                                <tbody>
                                                    @foreach ($error['details'] as $key => $value)
                                                        <tr>
                                                            <td class="fw-bold text-danger" style="width: 200px;">
                                                                {{ $key }}</td>
                                                            <td class="text-dark">{{ $value }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 p-3 rounded-3 bg-warning bg-opacity-10 border-start border-3 border-warning">
                        <i class="fa-solid fa-circle-info text-warning me-2"></i>
                        <strong>ملاحظة:</strong> يرجى تصحيح الأخطاء في الملف وإعادة المحاولة.
                        <span class="text-muted">ستختفي هذه الرسالة تلقائياً بعد دقيقتين.</span>
                    </div>
                </div>
            @endif


            {{-- رسالة النجاح - تختفي بعد دقيقتين أيضاً --}}
            @if (session('import_success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-circle-check fs-2 me-3"></i>
                        <div>
                            <h4 class="mb-1 text-success">✅ تم الاستيراد بنجاح!</h4>
                            <p class="mb-0">{{ session('import_success') }}</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- رسائل عامة --}}
            @if (session('msg'))
                @php
                    $msg = session('msg');
                @endphp
                @if (!$msg['status'])
                    <div class="alert alert-danger">
                        <ul>

                            {{-- dd($msg) --}}
                            @if (!empty($msg['messages']))
                                @foreach ((array) $msg['messages'] as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                @endif
            @endif

            {{-- جدول الموظفين --}}
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped gy-7 gs-7" id="hr-employees-table">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                    <th>@lang('hr::models/hr_employees.fields.id')</th>
                                    <th>@lang('hr::models/hr_employees.fields.employee_no')</th>
                                    <th>@lang('hr::models/hr_employees.fields.employee_id')</th>
                                    <th>@lang('hr::models/hr_employees.fields.job_id')</th>
                                    <th>@lang('hr::models/hr_employees.fields.department_id')</th>
                                    <th>@lang('models/employees.fields.phone')</th>
                                    <th>@lang('hr::models/hr_employees.fields.branch_id')</th>
                                    <th>@lang('models/employees.fields.identity_expired_at')</th>
                                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    <tr>
                                        <td>{{ $employee->main_employee->id ?? '' }}</td>
                                        <td>{{ $employee->job_number ?? '' }}</td>
                                        <td>{{ $employee->main_employee->full_name ?? '' }}</td>
                                        <td>{{ $employee->job->name ?? '' }}</td>
                                        <td>{{ $employee->department->name ?? '' }}</td>
                                        <td>{{ $employee->main_employee->phone ?? '' }}</td>
                                        <td>{{ $employee->main_employee->Branch->name ?? '' }}</td>
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
