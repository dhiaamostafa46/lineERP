<div class="card-body p-0">
    <div class="table-responsive">

        <table class="table table-striped text-center gy-7 gs-7" id="AS-FiscalYear-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th class="ps-4">@lang('accusoft::models/as_fiscal_years.fields.name')</th>
                    <th>@lang('accusoft::models/as_fiscal_years.fields.start_date')</th>
                    <th>@lang('accusoft::models/as_fiscal_years.fields.end_date')</th>
                    <th>@lang('accusoft::models/as_fiscal_years.fields.status')</th>
                    <th class="text-end pe-4 table-action">@lang('crud.action')</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($fiscalYears as $fiscalYear)
                    <tr>
                        <td class="ps-4 fw-bold"> {{ $fiscalYear->start_date->format('Y') }}</td>
                        <td>{{ $fiscalYear->start_date->format('Y-m-d') }}</td>
                        <td>{{ $fiscalYear->end_date->format('Y-m-d') }}</td>
                        <td>
                            <span class="{{ $fiscalYear->status_badge }}">
                                {{ $fiscalYear->status_text }}
                            </span>
                        </td>
                        <td style="width: 120px" class="table-action">

                            <div class='btn-group'>
                                <a href="{{ route('accusoft.FiscalYear.show', [$fiscalYear->id]) }}"
                                    class='btn btn-sm btn-primary float-right'>
                                    <i class="fa-solid fa-eye"></i>
                                </a>




                                @if (!$fiscalYear->is_closed)
                                    <a href="{{ route('accusoft.FiscalYear.edit', [$fiscalYear->id]) }}"
                                        class='btn btn-sm btn-primary float-right mx-1' title="@lang('lang.edit')">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>

                                    {!! Form::open(['route' => ['accusoft.FiscalYear.close', $fiscalYear->id], 'method' => 'get', 'class' => 'd-inline']) !!}
                                    {!! Form::button('<i class="fa-solid fa-calculator"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-sm btn-danger float-right mx-1',
                                        'title' => 'إقفال السنة المالية',
                                        'onclick' => "return confirm('هل أنت متأكد من رغبتك في إقفال السنة المالية؟ سيتم ترحيل الحسابات وتصفير قائمة الدخل ونقل النتيجة وتدوير الأرصدة الافتتاحية.')",
                                    ]) !!} 
                                    {!! Form::close() !!}
                                @else
                                    <span class="badge badge-light-danger fw-bold fs-7 align-self-center mx-1">
                                        <i class="fa-solid fa-lock text-danger me-1"></i> @lang('lang.closed')
                                    </span>
                                @endif
                            </div>

                        </td>




                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
    <div class="card-footer clearfix py-4">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $fiscalYears])
        </div>
    </div>
</div>
