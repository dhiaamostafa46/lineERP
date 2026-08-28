<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped text-center gy-7 gs-7" id="assetCategories-table">
        <thead>
            <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                <th class="text-center">@lang('accusoft::models/as_asset_categories.fields.name')</th>
                <th class="text-center">@lang('accusoft::models/as_asset_categories.fields.asset_account_id')</th>
                <th class="text-center">@lang('accusoft::models/as_asset_categories.fields.default_depreciation_method')</th>
                <th class="text-center">@lang('accusoft::models/as_asset_categories.fields.has_accounting_effect')</th>
                <th class="text-center">@lang('accusoft::models/as_asset_categories.fields.default_useful_life')</th>
                <th class="text-center table-action">@lang('crud.action')</th>
            </tr>
        </thead>
        <tbody class="fw-semibold text-gray-600">
            @foreach($categories as $category)
                <tr>
                    <td class="text-center">{{ $category->name }}</td>
                    <td class="text-center">{{ $category->assetAccount->name ?? '-' }}</td>
                    <td class="text-center">{{ __('accusoft::models/as_asset_categories.methods.' . $category->default_depreciation_method) }}</td>
                    <td class="text-center">
                        @if($category->has_accounting_effect)
                            <span class="badge badge-light-success">{{ $category->has_accounting_effect_text }}</span>
                        @else
                            <span class="badge badge-light-danger">{{ $category->has_accounting_effect_text }}</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $category->default_useful_life ?? '-' }}</td>
                    <td style="width: 120px" class="table-action">
                        <div class='btn-group'>
                            <a href="{{ route('accusoft.assetcategories.show', [$category->id]) }}"
                                class='btn btn-sm btn-primary float-right' title="@lang('lang.show')">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            <a href="{{ route('accusoft.assetcategories.edit', [$category->id]) }}"
                                class='btn btn-sm btn-primary mx-1' title="@lang('lang.edit')">
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            
                            {!! Form::open(['route' => ['accusoft.assetcategories.destroy', $category->id], 'method' => 'delete', 'style' => 'display:inline']) !!}
                                <button type="submit" class="btn btn-sm btn-primary float-right" title="@lang('lang.delete')" onclick="return confirm('@lang('lang.are_you_sure')')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            {!! Form::close() !!}
                        </div>
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
