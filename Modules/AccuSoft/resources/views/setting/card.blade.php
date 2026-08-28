<div class="row g-6 g-xl-9 mb-6 mb-xl-9">

   @can('accusoft.FiscalYear.index')
    <!--begin::Col - الفترة المالية-->
    <div class="col-md-6 col-lg-4 col-xl-4">
        <div class="card h-100 border border-dashed border-gray-300 border-hover-primary shadow-sm hover-elevate-up transition-all">
            <div class="card-body d-flex justify-content-center text-center flex-column p-8">

                <a href="{{ route('accusoft.FiscalYear.index') }}" class="text-gray-800 text-hover-primary d-flex flex-column align-items-center">
                    <div class="symbol symbol-75px mb-6">
                        <div class="symbol-label bg-light-primary">
                            <span class="svg-icon svg-icon-3x svg-icon-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                    <path d="M8 14h.01"></path>
                                    <path d="M12 14h.01"></path>
                                    <path d="M16 14h.01"></path>
                                    <path d="M8 18h.01"></path>
                                    <path d="M12 18h.01"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="fs-4 fw-bold mb-2 text-gray-900">
                        @lang('accusoft::models/as_setting.financial_period')
                    </div>
                </a>
                <div class="fs-7 fw-semibold text-gray-600 mt-1">
                    @lang('accusoft::models/as_setting.financial_period_desc')
                </div>

            </div>
        </div>
    </div>
      @endcan



 @can('accusoft.AccountMapping.index')
    <!--begin::Col - التوجيه المحاسبي-->
    <div class="col-md-6 col-lg-4 col-xl-4">
        <div class="card h-100 border border-dashed border-gray-300 border-hover-success shadow-sm hover-elevate-up transition-all">
            <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                <a href="{{ route('accusoft.AccountMapping.index') }}" class="text-gray-800 text-hover-primary d-flex flex-column align-items-center">
                    <div class="symbol symbol-75px mb-6">
                        <div class="symbol-label bg-light-success">
                            <span class="svg-icon svg-icon-3x svg-icon-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    <polyline points="7.5 4.21 12 6.81 16.5 4.21"></polyline>
                                    <polyline points="7.5 19.79 7.5 14.6 3 12"></polyline>
                                    <polyline points="21 12 16.5 14.6 16.5 19.79"></polyline>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="fs-4 fw-bold mb-2 text-gray-900">
                        @lang('accusoft::models/as_setting.accounting_guidance')
                    </div>
                </a>
                <div class="fs-7 fw-semibold text-gray-600 mt-1">
                    @lang('accusoft::models/as_setting.accounting_guidance_desc')
                </div>

            </div>
        </div>
    </div>
  @endcan


  @can('accusoft.AccountingSettings.index')
    <!--begin::Col - الإعدادات العامة-->
    <div class="col-md-6 col-lg-4 col-xl-4">
        <div class="card h-100 border border-dashed border-gray-300 border-hover-info shadow-sm hover-elevate-up transition-all">
            <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                <a  href="{{ route('accusoft.AccountingSettings.index') }}"  class="text-gray-800 text-hover-primary d-flex flex-column align-items-center">
                    <div class="symbol symbol-75px mb-6">
                        <div class="symbol-label bg-light-info">
                            <span class="svg-icon svg-icon-3x svg-icon-info">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M12 1v6m0 6v6m9-9h-6m-6 0H3"></path>
                                    <path d="m19.07 4.93-4.24 4.24m0 5.66 4.24 4.24M4.93 4.93l4.24 4.24m0 5.66-4.24 4.24"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="fs-4 fw-bold mb-2 text-gray-900">
                        @lang('accusoft::models/as_setting.general_settings')
                    </div>
                </a>
                <div class="fs-7 fw-semibold text-gray-600 mt-1">
                    @lang('accusoft::models/as_setting.general_settings_desc')
                </div>

            </div>
        </div>
    </div>
  @endcan


    {{-- <!--begin::Col - الضرائب-->
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card h-100 border border-dashed border-gray-300 border-hover-warning shadow-sm hover-elevate-up transition-all">
            <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                <a href="#" class="text-gray-800 text-hover-primary d-flex flex-column align-items-center">
                    <div class="symbol symbol-75px mb-6">
                        <div class="symbol-label bg-light-warning">
                            <span class="svg-icon svg-icon-3x svg-icon-warning">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
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
                        @lang('accusoft::models/as_setting.taxes_and_zakat')
                    </div>
                </a>
                <div class="fs-7 fw-semibold text-gray-600 mt-1">
                    @lang('accusoft::models/as_setting.taxes_and_zakat_desc')
                </div>

            </div>
        </div>
    </div> --}}





    @can('accusoft.assetcategories.index')
    <!--begin::Col - فئات الأصول-->
    <div class="col-md-6 col-lg-4 col-xl-4">
        <div class="card h-100 border border-dashed border-gray-300 border-hover-primary shadow-sm hover-elevate-up transition-all">
            <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                <a href="{{ route('accusoft.assetcategories.index') }}" class="text-gray-800 text-hover-primary d-flex flex-column align-items-center">
                    <div class="symbol symbol-75px mb-6">
                        <div class="symbol-label bg-light-primary">
                            <i class="fas fa-tags fs-2x text-primary"></i>
                        </div>
                    </div>
                    <div class="fs-4 fw-bold mb-2 text-gray-900">
                        @lang('accusoft::models/as_asset_categories.plural')
                    </div>
                </a>
                <div class="fs-7 fw-semibold text-gray-600 mt-1">
                    إعداد فئات الأصول وطرق الإهلاك
                </div>
            </div>
        </div>
    </div>
    @endcan

</div>
