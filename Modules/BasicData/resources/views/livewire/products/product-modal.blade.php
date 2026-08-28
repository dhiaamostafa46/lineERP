<div>
    @if($isOpen)
        <!-- Modal Backdrop -->
        <div class="modal-backdrop fade show" style="z-index: 1050;" wire:click="closeModal"></div>

        <!-- Product/Service Modal Dialog -->
        <div class="modal fade show d-block" tabindex="-1" style="z-index: 1055;" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content border-0 shadow-xl rounded-4" style="background: #ffffff;">
                    
                    <!-- Modal Header -->
                    <div class="modal-header border-bottom py-3 px-6 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="symbol symbol-35px symbol-circle bg-light-primary text-primary d-flex align-items-center justify-content-center">
                                <i class="fa-solid {{ $type == 2 ? 'fa-bell-concierge' : 'fa-box' }} fs-5"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-gray-900 fs-5 mb-0">
                                    @if($is_edit)
                                        {{ __('crud.edit') }} {{ $type == 2 ? 'خدمة (Service)' : __('basicdata::models/db_products.singular') }}
                                    @else
                                        {{ __('crud.add_new') }} {{ $type == 2 ? 'خدمة (Service)' : __('basicdata::models/db_products.singular') }}
                                    @endif
                                </h5>
                                <span class="text-muted fs-8">إدارة كافة بيانات المنتج والأسعار والمقاسات والوحدات</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-icon btn-ghost-secondary rounded-circle" wire:click="closeModal" aria-label="Close">
                            <i class="fa-solid fa-xmark fs-5"></i>
                        </button>
                    </div>

                    <!-- Navigation Tabs -->
                    <div class="px-6 pt-3 bg-light border-bottom">
                        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-6 fw-bold">
                            <li class="nav-item">
                                <a class="nav-link text-active-primary py-3 px-4 cursor-pointer {{ $activeTab === 'basic' ? 'active text-primary border-bottom border-2 border-primary' : 'text-gray-600' }}" 
                                   wire:click="setTab('basic')">
                                    <i class="fa-solid fa-circle-info fs-7 me-1"></i>
                                    البيانات الأساسية
                                </a>
                            </li>

                            @if($type == 1)
                                <li class="nav-item">
                                    <a class="nav-link text-active-primary py-3 px-4 cursor-pointer {{ $activeTab === 'sizes' ? 'active text-primary border-bottom border-2 border-primary' : 'text-gray-600' }}" 
                                       wire:click="setTab('sizes')">
                                        <i class="fa-solid fa-ruler-combined fs-7 me-1"></i>
                                        المقاسات والتنويعات
                                        @if($have_sizes)
                                            <span class="badge badge-sm bg-light-primary text-primary ms-1">{{ count($sizes) }}</span>
                                        @endif
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link text-active-primary py-3 px-4 cursor-pointer {{ $activeTab === 'units' ? 'active text-primary border-bottom border-2 border-primary' : 'text-gray-600' }}" 
                                       wire:click="setTab('units')">
                                        <i class="fa-solid fa-scale-balanced fs-7 me-1"></i>
                                        الوحدات والتحويل
                                        <span class="badge badge-sm bg-light-secondary text-gray-700 ms-1">{{ count($units) }}</span>
                                    </a>
                                </li>
                            @endif

                            <li class="nav-item">
                                <a class="nav-link text-active-primary py-3 px-4 cursor-pointer {{ $activeTab === 'other' ? 'active text-primary border-bottom border-2 border-primary' : 'text-gray-600' }}" 
                                   wire:click="setTab('other')">
                                    <i class="fa-solid fa-clock fs-7 me-1"></i>
                                    أوقات العمل وتفاصيل إضافية
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Modal Body Form -->
                    <form wire:submit.prevent="save">
                        <div class="modal-body py-4 px-6" style="max-height: calc(85vh - 180px); overflow-y: auto;">
                            
                            @if ($errors->has('save_error'))
                                <div class="alert alert-danger py-2 px-3 mb-4 fs-7">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $errors->first('save_error') }}
                                </div>
                            @endif

                            <!-- ================= TAB 1: BASIC INFO ================= -->
                            <div class="{{ $activeTab === 'basic' ? '' : 'd-none' }}">
                                
                                <!-- Type & Status & Kitchen -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-7 required">
                                            @lang('basicdata::models/db_products.fields.type')
                                        </label>
                                        <select wire:model.live="type" class="form-select form-select-solid fs-7">
                                            <option value="1">📦 منتج مخزني (Product)</option>
                                            <option value="2">🛎️ خدمة (Service)</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-7 required">
                                            @lang('basicdata::models/db_products.fields.status')
                                        </label>
                                        <select wire:model="status" class="form-select form-select-solid fs-7">
                                            <option value="1">@lang('basicdata::lang.active')</option>
                                            <option value="0">@lang('basicdata::lang.inactive')</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-7">
                                            المطبخ / الطابعة (Kitchen)
                                        </label>
                                        <select wire:model="kitchen_id" class="form-select form-select-solid fs-7">
                                            <option value="">-- بدون مطبخ --</option>
                                            @foreach($kitchens as $kId => $kName)
                                                <option value="{{ $kId }}">{{ $kName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Names (Multilingual) -->
                                <div class="row g-3 mb-4">
                                    @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold fs-7 required">
                                                {{ $language }} - @lang('basicdata::models/db_products.fields.name')
                                            </label>
                                            <input type="text" 
                                                   wire:model="name.{{ $locale }}" 
                                                   class="form-control form-control-solid fs-7 @error('name.'.$locale) is-invalid @enderror" 
                                                   placeholder="أدخل الاسم بـ {{ $language }}" />
                                            @error('name.'.$locale)
                                                <div class="invalid-feedback fs-8">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Barcode & Category & Base Unit -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-7">
                                            @lang('basicdata::models/db_products.fields.barcode')
                                        </label>
                                        <input type="text" 
                                               wire:model="barcode" 
                                               class="form-control form-control-solid fs-7 font-monospace" 
                                               placeholder="الباركود..." />
                                        @error('barcode') <div class="text-danger fs-8">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-7 required">
                                            @lang('basicdata::models/db_products.fields.category_id')
                                        </label>
                                        <select wire:model="category_id" class="form-select form-select-solid fs-7 @error('category_id') is-invalid @enderror">
                                            <option value="">-- @lang('basicdata::lang.select') --</option>
                                            @foreach($categories as $cId => $cName)
                                                <option value="{{ $cId }}">{{ $cName }}</option>
                                            @endforeach
                                        </select>
                                        @error('category_id') <div class="invalid-feedback fs-8">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-7">
                                            @lang('basicdata::models/db_products.fields.base_unit_id')
                                        </label>
                                        <select wire:model="base_unit_id" class="form-select form-select-solid fs-7">
                                            <option value="">-- @lang('basicdata::lang.select') --</option>
                                            @foreach($availableUnits as $uId => $uName)
                                                <option value="{{ $uId }}">{{ $uName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Pricing & Taxes -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-7 required">
                                            @lang('basicdata::models/db_products.fields.prod_price') (سعر البيع)
                                        </label>
                                        <div class="input-group input-group-solid">
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   wire:model="prod_price" 
                                                   class="form-control form-control-solid fs-7 font-monospace @error('prod_price') is-invalid @enderror" 
                                                   placeholder="0.00" />
                                            <span class="input-group-text fs-8 text-muted">{{ config('app.currency', 'SAR') }}</span>
                                        </div>
                                        @error('prod_price') <div class="text-danger fs-8">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-7">
                                            @lang('basicdata::models/db_products.fields.cost_price') (سعر التكلفة)
                                        </label>
                                        <div class="input-group input-group-solid">
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   wire:model="cost_price" 
                                                   class="form-control form-control-solid fs-7 font-monospace" 
                                                   placeholder="0.00" />
                                            <span class="input-group-text fs-8 text-muted">{{ config('app.currency', 'SAR') }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-7">
                                            حساب الضريبة (Tax Account)
                                        </label>
                                        <select wire:model="tax_id" class="form-select form-select-solid fs-7">
                                            <option value="">-- الافتراضي (15%) --</option>
                                            @foreach($taxes as $tId => $tName)
                                                <option value="{{ $tId }}">{{ $tName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Image Upload -->
                                <div class="p-3 bg-light rounded-3 border">
                                    <label class="form-label fw-semibold fs-7 mb-2">
                                        @lang('basicdata::models/db_products.fields.img')
                                    </label>
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="symbol symbol-60px symbol-2by3 flex-shrink-0">
                                            @if ($img)
                                                <img src="{{ $img->temporaryUrl() }}" class="rounded-3 border object-fit-cover w-60px h-60px">
                                            @elseif ($existing_img)
                                                <img src="{{ $existing_img }}" class="rounded-3 border object-fit-cover w-60px h-60px">
                                            @else
                                                <div class="symbol-label bg-white border text-muted fs-8 rounded-3 w-60px h-60px d-flex align-items-center justify-content-center">
                                                    <i class="fa-solid fa-image fs-4"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="file" wire:model="img" class="form-control form-control-solid form-control-sm fs-8" accept="image/*" />
                                            <span class="text-muted fs-9 mt-1 d-block">الصيغ المدعومة: PNG, JPG, JPEG (الحجم الأقصى 2MB)</span>
                                            @error('img') <span class="text-danger fs-8">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- ================= TAB 2: SIZES & VARIATIONS ================= -->
                            @if($type == 1)
                                <div class="{{ $activeTab === 'sizes' ? '' : 'd-none' }}">
                                    
                                    <!-- Toggle Have Sizes Switch -->
                                    <div class="d-flex align-items-center justify-content-between p-3 bg-light-primary rounded-3 mb-4">
                                        <div>
                                            <h6 class="fw-bold text-gray-900 mb-1">هل يحتوي هذا الصنف على مقاسات / أحجام متعددة؟</h6>
                                            <span class="text-muted fs-8">مثل: صغير، وسط، كبير مع أسعار وباركودات مختلفة لكل مقاس</span>
                                        </div>
                                        <div class="form-check form-switch form-check-custom form-check-solid">
                                            <input class="form-check-input h-20px w-35px" type="checkbox" wire:model.live="have_sizes" id="toggleHaveSizes" />
                                        </div>
                                    </div>

                                    @if($have_sizes)
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <span class="fs-7 fw-bold text-gray-800">جدول المقاسات والتنويعات</span>
                                            <button type="button" class="btn btn-sm btn-primary py-1 px-3 fs-8" wire:click="addSizeRow">
                                                <i class="fa-solid fa-plus fs-9 me-1"></i> إضافة مقاس جديد
                                            </button>
                                        </div>

                                        <div class="table-responsive border rounded-3">
                                            <table class="table table-bordered align-middle mb-0" style="font-size: 13px;">
                                                <thead class="bg-light text-gray-700 fw-bold">
                                                    <tr>
                                                        <th style="min-width: 140px;">اسم المقاس (عربي) *</th>
                                                        <th style="min-width: 140px;">اسم المقاس (English)</th>
                                                        <th style="min-width: 110px;">سعر البيع *</th>
                                                        <th style="min-width: 110px;">سعر التكلفة</th>
                                                        <th style="min-width: 120px;">الباركود</th>
                                                        <th class="text-center" style="width: 40px;">#</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($sizes as $idx => $size)
                                                        <tr>
                                                            <td>
                                                                <input type="text" 
                                                                       wire:model="sizes.{{ $idx }}.ar.name" 
                                                                       class="form-control form-control-sm form-control-solid @error('sizes.'.$idx.'.ar.name') is-invalid @enderror" 
                                                                       placeholder="مثال: كبير" />
                                                            </td>
                                                            <td>
                                                                <input type="text" 
                                                                       wire:model="sizes.{{ $idx }}.en.name" 
                                                                       class="form-control form-control-sm form-control-solid" 
                                                                       placeholder="e.g. Large" />
                                                            </td>
                                                            <td>
                                                                <input type="number" 
                                                                       step="0.01" 
                                                                       min="0" 
                                                                       wire:model="sizes.{{ $idx }}.sale_price" 
                                                                       class="form-control form-control-sm form-control-solid font-monospace @error('sizes.'.$idx.'.sale_price') is-invalid @enderror" 
                                                                       placeholder="0.00" />
                                                            </td>
                                                            <td>
                                                                <input type="number" 
                                                                       step="0.01" 
                                                                       min="0" 
                                                                       wire:model="sizes.{{ $idx }}.cost_price" 
                                                                       class="form-control form-control-sm form-control-solid font-monospace" 
                                                                       placeholder="0.00" />
                                                            </td>
                                                            <td>
                                                                <input type="text" 
                                                                       wire:model="sizes.{{ $idx }}.barcode" 
                                                                       class="form-control form-control-sm form-control-solid font-monospace" 
                                                                       placeholder="باركود المقاس..." />
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" 
                                                                        class="btn btn-sm btn-icon btn-light-danger w-28px h-28px" 
                                                                        wire:click="removeSizeRow({{ $idx }})" 
                                                                        title="حذف المقاس">
                                                                    <i class="fa-solid fa-trash fs-9"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center py-4 text-muted fs-8">
                                                                لم تتم إضافة أي مقاسات بعد. اضغط على زر "إضافة مقاس جديد".
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-10 border rounded-3 bg-light">
                                            <i class="fa-solid fa-ruler-combined fs-2tx text-muted mb-2"></i>
                                            <p class="text-muted fs-7 mb-0">قم بتفعيل خيار المقاسات أعلاه لإضافة أحجام وأسعار مختلفة لهذا المنتج.</p>
                                        </div>
                                    @endif

                                </div>

                                <!-- ================= TAB 3: MULTIPLE UNITS ================= -->
                                <div class="{{ $activeTab === 'units' ? '' : 'd-none' }}">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div>
                                            <span class="fs-7 fw-bold text-gray-800">وحدات القياس المتعددة ومعاملات التحويل</span>
                                            <span class="text-muted fs-8 d-block">حدد وحدات الشراء والبيع والتحويل (مثال: كرتون = 12 حبة)</span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-primary py-1 px-3 fs-8" wire:click="addUnitRow">
                                            <i class="fa-solid fa-plus fs-9 me-1"></i> إضافة وحدة
                                        </button>
                                    </div>

                                    <div class="table-responsive border rounded-3">
                                        <table class="table table-bordered align-middle mb-0" style="font-size: 13px;">
                                            <thead class="bg-light text-gray-700 fw-bold">
                                                <tr>
                                                    <th style="min-width: 180px;">الوحدة *</th>
                                                    <th style="min-width: 120px;">معامل التحويل (Conversion Factor) *</th>
                                                    <th class="text-center" style="width: 120px;">وحدة أساسية (Base)</th>
                                                    <th class="text-center" style="width: 40px;">#</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($units as $idx => $u)
                                                    <tr>
                                                        <td>
                                                            <select wire:model="units.{{ $idx }}.unit_id" class="form-select form-select-sm form-select-solid">
                                                                <option value="">-- اختر الوحدة --</option>
                                                                @foreach($availableUnits as $uId => $uName)
                                                                    <option value="{{ $uId }}">{{ $uName }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="number" 
                                                                   step="0.01" 
                                                                   min="0.01" 
                                                                   wire:model="units.{{ $idx }}.conversion_factor" 
                                                                   class="form-control form-control-sm form-control-solid font-monospace" 
                                                                   placeholder="1.00" />
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="form-check form-switch form-check-custom form-check-solid d-inline-block">
                                                                <input class="form-check-input h-20px w-35px" 
                                                                       type="checkbox" 
                                                                       wire:model="units.{{ $idx }}.is_base" 
                                                                       value="1" />
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-icon btn-light-danger w-28px h-28px" 
                                                                    wire:click="removeUnitRow({{ $idx }})" 
                                                                    title="حذف الوحدة">
                                                                <i class="fa-solid fa-trash fs-9"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-4 text-muted fs-8">
                                                            لم تتم إضافة أي وحدات. اضغط على زر "إضافة وحدة".
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- ================= TAB 4: SCHEDULE & DETAILS ================= -->
                            <div class="{{ $activeTab === 'other' ? '' : 'd-none' }}">
                                
                                <!-- Multilingual Details / Description -->
                                <div class="row g-3 mb-4">
                                    @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold fs-7">
                                                {{ $language }} - تفاصيل / وصف الصنف
                                            </label>
                                            <textarea wire:model="details.{{ $locale }}" 
                                                      class="form-control form-control-solid fs-7" 
                                                      rows="2" 
                                                      placeholder="أدخل الوصف بـ {{ $language }}..."></textarea>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Min Quantity & Calories -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-7">
                                            حد الطلب الأدنى للمخزون (Min Quantity)
                                        </label>
                                        <input type="number" 
                                               step="0.01" 
                                               min="0" 
                                               wire:model="min_quantity" 
                                               class="form-control form-control-solid fs-7 font-monospace" 
                                               placeholder="0.00" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-7">
                                            السعرات الحرارية (Calories)
                                        </label>
                                        <input type="number" 
                                               step="0.01" 
                                               min="0" 
                                               wire:model="calories" 
                                               class="form-control form-control-solid fs-7 font-monospace" 
                                               placeholder="0" />
                                    </div>
                                </div>

                                <!-- Working Hours (s_from, s_to) -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-7">
                                            وقت بدء تقديم الصنف / الخدمة (From)
                                        </label>
                                        <input type="time" wire:model="s_from" class="form-control form-control-solid fs-7" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-7">
                                            وقت انتهاء تقديم الصنف / الخدمة (To)
                                        </label>
                                        <input type="time" wire:model="s_to" class="form-control form-control-solid fs-7" />
                                    </div>
                                </div>

                                <!-- Work Days -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-7 mb-2">أيام العمل المتاحة لتقديم الصنف</label>
                                    <div class="row g-2">
                                        @php
                                            $daysList = [
                                                'sat' => 'السبت (Saturday)',
                                                'sun' => 'الأحد (Sunday)',
                                                'mon' => 'الاثنين (Monday)',
                                                'tue' => 'الثلاثاء (Tuesday)',
                                                'wed' => 'الأربعاء (Wednesday)',
                                                'thu' => 'الخميس (Thursday)',
                                                'fri' => 'الجمعة (Friday)',
                                            ];
                                        @endphp
                                        @foreach($daysList as $key => $dName)
                                            <div class="col-md-3 col-6">
                                                <label class="form-check form-check-custom form-check-solid p-2 border rounded-2 cursor-pointer d-flex align-items-center gap-2">
                                                    <input class="form-check-input h-15px w-15px" type="checkbox" value="{{ $key }}" wire:model="work_days" />
                                                    <span class="form-check-label fs-8 fw-semibold text-gray-800">{{ $dName }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>

                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer border-top py-3 px-6 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                @if($activeTab !== 'basic')
                                    <button type="button" class="btn btn-sm btn-light fs-7" wire:click="setTab('basic')">
                                        <i class="fa-solid fa-arrow-right fs-9 me-1"></i> العودة للرئيسية
                                    </button>
                                @endif
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-light fs-7" wire:click="closeModal">
                                    @lang('basicdata::lang.cancel')
                                </button>
                                <button type="submit" class="btn btn-sm front-btn-primary fs-7" wire:loading.attr="disabled">
                                    <span wire:loading.remove>
                                        <i class="fa-solid fa-check fs-8 me-1"></i>
                                        @lang('basicdata::lang.save')
                                    </span>
                                    <span wire:loading>
                                        <i class="fa-solid fa-spinner fa-spin fs-8 me-1"></i>
                                        جاري الحفظ والربط...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif
</div>
