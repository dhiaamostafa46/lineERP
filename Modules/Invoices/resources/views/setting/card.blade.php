<div class="row g-6 g-xl-9 mb-6 mb-xl-9">
    @can('invoices.Setting.edit')
        <!--begin::Col - الإعدادات العامة-->
        <div class="col-md-6 col-lg-4 col-xl-4">
            <div
                class="card h-100 border border-dashed border-gray-300 border-hover-info shadow-sm hover-elevate-up transition-all">
                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                    <a href="{{ route('invoices.Setting.edit', 1) }}"
                        class="text-gray-800 text-hover-primary d-flex flex-column align-items-center">
                        <div class="symbol symbol-75px mb-6">
                            <div class="symbol-label bg-light-info">
                                <span class="svg-icon svg-icon-3x svg-icon-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="3"></circle>
                                        <path d="M12 1v6m0 6v6m9-9h-6m-6 0H3"></path>
                                        <path
                                            d="m19.07 4.93-4.24 4.24m0 5.66 4.24 4.24M4.93 4.93l4.24 4.24m0 5.66-4.24 4.24">
                                        </path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="fs-4 fw-bold mb-2 text-gray-900">
                            @lang('invoices::models/invoices_setting.sections.general_settings')
                        </div>
                    </a>
                    <div class="fs-7 fw-semibold text-gray-600 mt-1">
                        @lang('invoices::models/invoices_setting.sections.general_settings_desc')
                    </div>

                </div>
            </div>
        </div>
    @endcan


    <!--begin::Col - الضرائب-->
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div
            class="card h-100 border border-dashed border-gray-300 border-hover-warning shadow-sm hover-elevate-up transition-all">
            <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                <a href="{{ route('invoices.Setting.zatca') }}" class="text-gray-800 text-hover-primary d-flex flex-column align-items-center">
                    <div class="symbol symbol-75px mb-6">
                        <div class="symbol-label bg-light-warning">
                            <span class="svg-icon svg-icon-3x svg-icon-warning">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="fs-4 fw-bold mb-2 text-gray-900">
                        @lang('invoices::models/invoices_setting.sections.taxes_and_zakat')
                    </div>
                </a>
                <div class="fs-7 fw-semibold text-gray-600 mt-1">
                    @lang('invoices::models/invoices_setting.sections.taxes_and_zakat_desc')
                </div>

            </div>
        </div>
    </div>
</div>
