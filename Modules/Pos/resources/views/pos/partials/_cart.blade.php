<!-- Cart View -->
<div class="cart-container bg-white rounded shadow-sm h-100 d-flex flex-column">
    <div class="cart-header" style="flex-direction: column; align-items: stretch; gap: 1rem;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 x-text="isReturn ? '{{ __('pos::pos.return_order') }}' : '{{ __('pos::pos.current_order') }}'" :class="{ 'text-danger': isReturn }"></h2>
            <div class="d-flex align-items-center gap-2">
                <span class="items-count" :class="{ 'bg-danger text-white': isReturn }" x-text="cartItems.length + ' {{ __('pos::pos.items_count') }}'"></span>
                <button class="btn btn-sm btn-outline-warning" x-show="!isReturn && cartItems.length > 0" @click="holdCart()" title="{{ __('pos::pos.hold_cart') }}">
                    <i class="fas fa-pause"></i>
                </button>
            </div>
        </div>
        <div class="customer-selection d-flex gap-1" wire:ignore x-init="$($refs.customerSelect).on('change', (e) => { customerId = e.target.value; })">
            <select x-ref="customerSelect" class="form-select select2-ajax-customers" style="width: 100%;">
                <option value="">{{ __('pos::pos.select_customer') }}</option>
            </select>
            <button class="btn btn-outline-primary px-2" @click="showCustomerModal = true" title="إضافة عميل جديد">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>

    <div class="cart-items flex-grow-1 overflow-auto">
        <div x-show="cartItems.length === 0" class="empty-cart" style="display: none;">
            <i class="fas fa-shopping-cart"></i>
            <p>{{ __('pos::pos.empty_cart') }}</p>
        </div>
        
        <div x-show="cartItems.length > 0" class="cart-table-wrapper" style="display: none;">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>{{ __('pos::pos.product') }}</th>
                        <th>{{ __('pos::pos.unit') }}</th>
                        <th>{{ __('pos::pos.price') }}</th>
                        <th style="width: 100px;">{{ __('pos::pos.qty') }}</th>
                        <th>{{ __('pos::pos.subtotal') }}</th>
                        <th style="width: 30px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in cartItems" :key="item.cart_key">
                        <tr>
                            <td class="product-name" :title="item.name" x-text="item.name"></td>
                            <td class="product-unit" x-text="item.unit || '-'"></td>
                            <td>
                                <template x-if="settings.allow_price_modification">
                                    <input type="number" class="form-control form-control-sm" style="width: 85px; padding: 4px 6px; font-size: 1rem; font-weight: bold;" x-model.number="item.price" @change="calculateTotal()" @focus="$event.target.select()" min="0" step="0.01">
                                </template>
                                <template x-if="!settings.allow_price_modification">
                                    <span x-text="Number(item.price).toFixed(2)"></span>
                                </template>
                            </td>
                            <td>
                                <div class="qty-control">
                                    <button class="qty-btn minus" @click="updateQty(item.cart_key, item.qty - 1)">-</button>
                                    <input type="number" class="qty-input" x-model="item.qty" @change="updateQty(item.cart_key, parseInt($event.target.value))" @focus="$event.target.select()" min="1" />
                                    <button class="qty-btn plus" @click="updateQty(item.cart_key, item.qty + 1)">+</button>
                                </div>
                            </td>
                            <td class="item-total" x-text="(item.price * item.qty).toFixed(2)"></td>
                            <td>
                                <button class="remove-btn btn-remove" @click="removeItem(item.cart_key)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <div class="cart-footer">
        <div class="discount-container mb-3" x-show="settings.allow_discount_modification">
            <label>{{ __('pos::pos.discount') }}</label>
            <div class="input-group">
                <input type="number" class="form-control" x-model.number="localDiscount" @change="updateDiscount" @focus="$event.target.select()" min="0" step="0.01" />
                <select class="form-select" style="max-width: 100px;" x-model="discountType" @change="updateDiscount">
                    <option value="amount">{{ __('invoices::models/sales_invoices.fields.discount_type_fixed') }}</option>
                    <option value="percentage">%</option>
                </select>
            </div>
        </div>

        <div class="totals" :class="{ 'border border-danger border-2': isReturn }">
            <div class="total-row">
                <span>{{ __('pos::pos.subtotal') }}</span>
                <span x-text="subtotal.toFixed(2) + ' SAR'"></span>
            </div>
            <div class="total-row text-danger" x-show="discountAmount > 0" style="display: none;">
                <span>{{ __('pos::pos.discount') }}</span>
                <span x-text="'-' + discountAmount.toFixed(2) + ' SAR'"></span>
            </div>
            <div class="total-row">
                <span>{{ __('pos::pos.vat') }}</span>
                <span x-text="vat.toFixed(2) + ' SAR'"></span>
            </div>

            <div class="total-row grand-total" :class="isReturn ? 'text-danger' : 'text-primary'">
                <span>{{ __('pos::pos.final_total') }}</span>
                <span x-text="finalTotal.toFixed(2) + ' SAR'"></span>
            </div>
        </div>
        
        <button class="checkout-btn" :class="{ 'btn-return': isReturn }" :disabled="cartItems.length === 0" @click="handleCheckoutClick">
            <span x-text="isReturn ? '{{ __('pos::pos.confirm_return') }}' : '{{ __('pos::pos.proceed_checkout') }}'"></span>
            <i class="fas fa-arrow-left"></i>
        </button>
    </div>
</div>
