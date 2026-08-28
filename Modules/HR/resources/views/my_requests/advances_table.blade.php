<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-advances-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_advances.fields.amount')</th>
                    <th>@lang('hr::models/hr_advances.fields.due_at')</th>
                    <th>@lang('hr::models/hr_advances.fields.status')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($advances as $advance)
                    <tr>
                        <td>{{ $advance->amount }}</td>
                        <td>{{ $advance->due_at }}</td>
                        <td><span class="{{ $advance->status_badge }}">{{ $advance->status_text }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $advances])
        </div>
    </div>
</div>
