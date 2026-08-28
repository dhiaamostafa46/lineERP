<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-Templates-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('models/Templates.fields.id')</th>
                    <th>@lang('models/Templates.fields.name')</th>
                    <th>@lang('models/Templates.fields.document_type')</th>
                    <th>@lang('models/Templates.fields.print_format')</th>
                    <th>@lang('models/Templates.fields.status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($Templates as $Template)
                                <tr>
                                    <td>{{ $Template->id }}</td>
                                    <td>{{ $Template->name }}</td>
                                    <td>{{ $Template->document_type }}</td>
                                    <td>{{ $Template->print_format }}</td>
                                    <td><span class="{{ $Template->status_badge }}">{{ $Template->status_text }}</span></td>

                                    <td style="width: 120px">
                                        {!! Form::open(['route' => ['Templates.destroy', $Template->id], 'method' => 'delete']) !!}
                                        <div class='btn-group'>
                                            <a href="{{ route('Templates.show', [$Template->id]) }}"
                                                class='btn btn-icon btn-sm btn-primary btn-xs'>
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="{{ route('Templates.edit', [$Template->id]) }}"
                                                class='btn btn-icon btn-sm btn-primary mx-1  btn-xs' title="تعديل في المحرر">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                            {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                        'type' => 'submit',
                        'class' => 'btn btn-icon btn-sm btn-primary   btn-xs',
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

    <div class="card-footer clearfix py-4 {{ $Templates->hasPages() ? '' : 'd-none' }}">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $Templates])
        </div>
    </div>
</div>