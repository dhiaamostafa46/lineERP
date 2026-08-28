<div>
    <!-- Items Card -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    @lang('store::models/st_opening_balances.items.title')
                </h6>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" wire:click.prevent="addItem">
                        <i class="fas fa-plus fa-sm text-white-50"></i> @lang('crud.add_item')
                    </button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0" id="opening-balance-items-table">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 5%;" class="text-center">#</th>
                                <th style="width: 30%;">@lang('store::models/st_opening_balances.items.product_id')</th>
                                <th style="width: 20%;">@lang('store::models/st_opening_balances.items.unit_id')</th>
                                <th style="width: 12%;">@lang('store::models/st_opening_balances.items.quantity')</th>
                                <th style="width: 12%;">@lang('store::models/st_opening_balances.items.unit_cost')</th>
                                <th style="width: 12%;">@lang('store::models/st_opening_balances.items.total_cost')</th>
                                <th style="width: 4%;" class="text-center">@lang('crud.action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $index => $item)
                            <tr wire:key="item-{{ $index }}" class="item-row">
                                <!-- # -->
                                <td class="text-center align-middle">
                                    <span class="badge badge-secondary">{{ $index + 1 }}</span>
                                </td>

                                <!-- المنتج -->
                                <td>
                                    <select
                                        class="form-control form-control-sm product-select @error('items.'.$index.'.product_id') is-invalid @enderror"
                                        wire:model.live="items.{{ $index }}.product_id"
                                        required>
                                        <option value="">-- اختر المنتج --</option>
                                        @foreach($allProducts as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('items.'.$index.'.product_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>

                                <!-- الوحدة -->
                                <td>
                                    <select
                                        class="form-control form-control-sm unit-select @error('items.'.$index.'.unit_id') is-invalid @enderror"
                                        wire:model.live="items.{{ $index }}.unit_id"
                                        required
                                        @if(empty($item['product_id'])) disabled @endif>
                                        <option value="">-- اختر الوحدة --</option>
                                        @if(isset($allUnits[$index]))
                                            @foreach($allUnits[$index] as $unitId => $unitName)
                                                <option value="{{ $unitId }}">{{ $unitName }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('items.'.$index.'.unit_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>

                                <!-- الكمية -->
                                <td>
                                    <input
                                        type="number"
                                        class="form-control form-control-sm text-center quantity-input @error('items.'.$index.'.quantity') is-invalid @enderror"
                                        wire:model.blur="items.{{ $index }}.quantity"
                                        data-index="{{ $index }}"
                                        step="0.01"
                                        min="0.01"
                                        required>
                                    @error('items.'.$index.'.quantity')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>

                                <!-- سعر التكلفة -->
                                <td>
                                    <input
                                        type="number"
                                        class="form-control form-control-sm text-end bg-light cost-input @error('items.'.$index.'.unit_cost') is-invalid @enderror"
                                        wire:model.blur="items.{{ $index }}.unit_cost"
                                        data-index="{{ $index }}"
                                        step="0.01"
                                        min="0"
                                        readonly
                                        title="سعر التكلفة من المنتج">
                                    @error('items.'.$index.'.unit_cost')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>

                                <!-- الإجمالي -->
                                <td>
                                    <input
                                        type="text"
                                        class="form-control form-control-sm text-end bg-info text-white font-weight-bold total-display"
                                        data-index="{{ $index }}"
                                        value="{{ number_format($item['total_cost'], 2) }}"
                                        readonly
                                        tabindex="-1">
                                </td>

                                <!-- الإجراءات -->
                                <td class="text-center align-middle">
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm"
                                        wire:click="removeItem({{ $index }})"
                                        @if(count($items) <= 1) disabled @endif
                                        title="@lang('crud.delete')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- صف الملاحظات -->
                            <tr wire:key="notes-{{ $index }}">
                                <td colspan="7" class="bg-light p-2">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-sticky-note"></i>
                                            </span>
                                        </div>
                                        <input
                                            type="text"
                                            class="form-control form-control-sm"
                                            wire:model.defer="items.{{ $index }}.notes"
                                            placeholder="@lang('store::models/st_opening_balances.items.notes')">
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                                    <p>@lang('store::models/st_opening_balances.no_items')</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @error('total_items')
                    <div class="alert alert-danger m-3">{{ $message }}</div>
                @enderror
            </div>

            <!-- Footer: الإجماليات -->
            <div class="card-footer bg-light">
                <div class="row justify-content-end text-end">
                    <div class="col-md-8">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-end font-weight-bold text-dark" style="width: 60%;">
                                        @lang('store::models/st_opening_balances.fields.total_items'):
                                    </td>
                                    <td class="text-end" style="width: 40%;">
                                        <span class="h5 text-primary font-weight-bold mb-0">
                                            {{ $this->totalItems }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end font-weight-bold text-dark">
                                        @lang('store::models/st_opening_balances.fields.total_quantity'):
                                    </td>
                                    <td class="text-end">
                                        <span class="h5 text-info font-weight-bold mb-0">
                                            {{ number_format($this->totalQuantity, 2) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr class="border-top">
                                    <td class="text-end font-weight-bold text-dark pt-3">
                                        <h5 class="mb-0">
                                            <i class="fas fa-calculator"></i>
                                            @lang('store::models/st_opening_balances.fields.total_value'):
                                        </h5>
                                    </td>
                                    <td class="text-end pt-3">
                                        <h4 class="text-success font-weight-bold mb-0">
                                            {{ number_format($this->totalValue, 2) }}
                                        </h4>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Inputs للإرسال مع النموذج -->
    @foreach($items as $index => $item)
        <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item['product_id'] }}">
        <input type="hidden" name="items[{{ $index }}][product_name]" value="{{ $item['product_name'] }}">
        <input type="hidden" name="items[{{ $index }}][unit_id]" value="{{ $item['unit_id'] }}">
        <input type="hidden" name="items[{{ $index }}][unit_name]" value="{{ $item['unit_name'] }}">
        <input type="hidden" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] }}">
        <input type="hidden" name="items[{{ $index }}][unit_cost]" value="{{ $item['unit_cost'] }}">
        <input type="hidden" name="items[{{ $index }}][total_cost]" value="{{ $item['total_cost'] }}">
        <input type="hidden" name="items[{{ $index }}][notes]" value="{{ $item['notes'] }}">
    @endforeach

    <!-- إجماليات الفاتورة -->
    <input type="hidden" name="total_items" value="{{ $this->totalItems }}">
    <input type="hidden" name="total_quantity" value="{{ $this->totalQuantity }}">
    <input type="hidden" name="total_value" value="{{ $this->totalValue }}">
</div>

@push('styles')
<style>
    .item-row:hover {
        background-color: #f8f9fa !important;
    }

    .table td {
        vertical-align: middle !important;
    }

    .form-control-sm {
        font-size: 0.875rem;
    }

    /* تحسين Select2 */
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(1.5em + 0.5rem + 2px) !important;
        font-size: 0.875rem !important;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: calc(1.5em + 0.5rem) !important;
    }

    /* ألوان مخصصة */
    .bg-info.text-white {
        background-color: #17a2b8 !important;
    }

    /* تحسين responsive */
    @media (max-width: 992px) {
        .table {
            font-size: 0.75rem;
        }

        .form-control-sm {
            font-size: 0.75rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('livewire:init', () => {
    const initSelect2 = (el) => {
        const $el = $(el);
        const model = $el.attr('wire:model.live');

        $el.select2({
            theme: 'bootstrap4',
            width: '100%',
            dir: 'rtl',
            language: 'ar',
            placeholder: $el.find('option:first').text()
        }).on('change', function() {
            if (model) {
                @this.set(model, $(this).val());
            }
        });
    };

    const reinitSelect2 = () => {
        document.querySelectorAll('.product-select, .unit-select').forEach(el => {
            if (!$(el).hasClass('select2-hidden-accessible')) {
                initSelect2(el);
            }
        });
    };

    // حساب الإجمالي فورياً عند تغيير الكمية
    const calculateRowTotal = (index) => {
        const quantityInput = document.querySelector(`.quantity-input[data-index="${index}"]`);
        const costInput = document.querySelector(`.cost-input[data-index="${index}"]`);
        const totalDisplay = document.querySelector(`.total-display[data-index="${index}"]`);

        if (quantityInput && costInput && totalDisplay) {
            const quantity = parseFloat(quantityInput.value) || 0;
            const cost = parseFloat(costInput.value) || 0;
            const total = quantity * cost;

            // تحديث العرض فوراً
            totalDisplay.value = total.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    };

    // إضافة مستمعين للأحداث
    const attachCalculationListeners = () => {
        document.querySelectorAll('.quantity-input').forEach(input => {
            // حساب فوري أثناء الكتابة
            input.addEventListener('input', function() {
                const index = this.getAttribute('data-index');
                calculateRowTotal(index);
            });

            // حساب عند الخروج من الحقل
            input.addEventListener('blur', function() {
                const index = this.getAttribute('data-index');
                calculateRowTotal(index);
            });
        });

        document.querySelectorAll('.cost-input').forEach(input => {
            input.addEventListener('input', function() {
                const index = this.getAttribute('data-index');
                calculateRowTotal(index);
            });
        });
    };

    // تهيئة أولية
    setTimeout(() => {
        reinitSelect2();
        attachCalculationListeners();
    }, 100);

    // إعادة التهيئة بعد كل تحديث
    Livewire.hook('message.processed', () => {
        setTimeout(() => {
            reinitSelect2();
            attachCalculationListeners();
        }, 100);
    });

    // إظهار التنبيهات
    Livewire.on('show-alert', (event) => {
        let data = event[0] || event;
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: data.type,
                title: data.type === 'error' ? 'خطأ' : 'نجح',
                text: data.message,
                confirmButtonText: 'موافق'
            });
        } else {
            alert(data.message);
        }
    });
});
</script>
@endpush
