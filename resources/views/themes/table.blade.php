<!--begin::Search Form-->
<div class="mb-7">
    <div class="row align-items-center">
        <div class="col-lg-9 col-xl-8">
            <div class="row align-items-center">
                <div class="my-2 col-md-4 my-md-0">
                    <div class="input-icon">
                        <input type="text" class="form-control" placeholder="Search..."
                            id="kt_datatable_search_query" />
                        <span><i class="flaticon2-search-1 text-muted"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Search Form-->

<!--begin: Datatable-->
<table class="table table-bordered table-hover" id="kt_datatableasd">
    <thead>
        <tr>
            <th>@lang('models.themes.fields.name')</th>
            <th>@lang('crud.action')</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($themes as $theme)
        <tr>
            <td>{{ $theme->name }}</td>
            <td>
                <div class='btn btn-sm-group'>
                    @can('themes edit')
                    <a href="{{ route('themes.edit', [$theme->id])  }}"
                        class='btn btn-sm btn-icon btn-shadow mx-1 btn-transparent-primary'>
                        <i class="fa fa-edit"></i>
                    </a>
                    @endcan
                    @can('themes destroy')
                    <button type="button" class="btn btn-sm btn-icon btn-shadow mx-1 btn-transparent-danger"
                        data-toggle="modal" data-target="#country-{{ $theme->id }}-modal">
                        <i class="fa fa-trash"></i>
                    </button>
                    @endcan
                </div>
                {{-- {!! Form::close() !!} --}}
            </td>
        </tr>
        @endforeach
        {!! Form::close() !!}
    </tbody>
</table>
<!--end: Datatable-->

@can('themes destroy')
@foreach ($themes as $theme)
<!-- Modal -->
<div class="modal fade" id="country-{{ $theme->id }}-modal" tabindex="-1" role="dialog"
    aria-labelledby="country-{{ $theme->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h2 class="text-danger">
                    @lang('crud.are_you_sure')
                </h2>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('crud.close')</button>
                {!! Form::open(['route' => ['themes.destroy', $theme->id], 'method' => 'delete']) !!}
                {!! Form::button('<i class="fa fa-trash"></i>', ['type' => 'submit', 'class' => 'btn
                btn-transparent-danger']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endforeach
@endcan
