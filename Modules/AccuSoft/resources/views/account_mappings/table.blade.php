<div class="card-body">
    <div class="row g-5 g-xl-8">
        @foreach ($accountMappings as $accountMapping)
            <div class="col-xl-4 col-lg-6 col-md-6">
                <!--begin::Card-->
                <div class="card card-flush h-xl-100">
                    <!--begin::Card header-->
                    <div class="card-header pt-5">
                        <!--begin::Card title-->
                        <div class="card-title d-flex flex-column">
                            <!--begin::Title-->
                            <h3 class="fs-2 fw-bolder text-dark">{{ $accountMapping->name }}</h3>
                            <!--end::Title-->
                        </div>
                        <!--end::Card title-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-5">
                        <!--begin::Items-->
                        <div class="d-flex flex-column">
                            <!--begin::Item-->
                            <div class="d-flex flex-stack mb-3">
                                <span class="text-gray-600 fw-semibold">@lang('accusoft::models/as_account_mappings.fields.mapping_key'):</span>
                                <span class="text-gray-800 fw-bold">{{ $accountMapping->mapping_key }}</span>
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex flex-stack mb-3">
                                <span class="text-gray-600 fw-semibold">@lang('accusoft::models/as_account_mappings.fields.account_id'):</span>
                                <span class="text-gray-800 fw-bold">{{ $accountMapping->account->name ?? '-' }}</span>
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex flex-stack">
                                <span class="text-gray-600 fw-semibold">@lang('crud.created_at'):</span>
                                <span class="text-gray-800 fw-bold">{{ $accountMapping->created_at->format('Y-m-d') }}</span>
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Items-->
                    </div>
                    <!--end::Card body-->
                    <!--begin::Card footer-->
                    <div class="card-footer d-flex justify-content-end pt-5">
                        <a href="{{ route('accusoft.AccountMapping.show', [$accountMapping->id]) }}"
                           class='btn btn-sm btn-icon btn-primary' title="@lang('crud.view')">
                            <i class="fa-solid fa-eye fs-4"></i>
                        </a>
                        <a href="{{ route('accusoft.AccountMapping.edit', [$accountMapping->id]) }}"
                           class='btn btn-sm btn-icon btn-primary mx-2' title="@lang('crud.edit')">
                            <i class="fa-solid fa-edit fs-4"></i>
                        </a>

                        {{-- {!! Form::open(['route' => ['accusoft.AccountMapping.destroy', $accountMapping->id], 'method' => 'delete', 'class' => 'd-inline', 'onsubmit' => "return confirm('".__('crud.are_you_sure')."')"]) !!}
                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="@lang('crud.delete')">
                            <i class="fa-solid fa-trash fs-4"></i>
                        </button>
                        {!! Form::close() !!} --}}
                    </div>
                    <!--end::Card footer-->
                </div>
                <!--end::Card-->
            </div>
        @endforeach
    </div>
</div>



