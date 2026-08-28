<!-- Checkout Modal (Premium) -->
<div class="modal-overlay glass-overlay" x-show="showCheckoutModal" @click.self="showCheckoutModal = false" style="display: none;" x-transition>
    <div class="checkout-modal-premium slide-up" @click.stop>
        <div class="modal-header-premium">
            <div class="header-title">
                <div class="icon-circle"><i class="fas fa-shopping-bag"></i></div>
                <h2>{{ __('pos::pos.complete_payment') }}</h2>
            </div>
            <button class="close-btn" @click="showCheckoutModal = false"><i class="fas fa-times"></i></button>
        </div>

        <div class="modal-body-premium">
            <!-- Right Column: Payment Methods & Input -->
            <div class="payment-section">
                <h3 class="section-title">{{ __('pos::pos.payment_method') }}</h3>
                <div x-show="paymentMethods.length === 0" class="alert alert-warning rounded-4 shadow-sm" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ __('pos::pos.no_payment_methods') }}
                </div>
                
                <div x-show="paymentMethods.length > 0" class="methods-cards" style="display: none;">
                    <template x-for="method in paymentMethods" :key="method.id">
                        <div class="payment-card" :class="{ 'active': method.type === 'installment' ? isMultiPayment : (!isMultiPayment && selectedMethod && selectedMethod.id === method.id) }" @click="if(method.type === 'installment') { selectMultiPayment(); } else { selectMethod(method); }">
                            <div class="card-icon">
                                <i class="fas fa-money-bill-wave text-success" x-show="method.type === 'cash'"></i>
                                <i class="fas fa-credit-card text-primary" x-show="method.type === 'pos' || method.type === 'card'"></i>
                                <i class="fas fa-file-invoice-dollar text-warning" x-show="method.type === 'credit'"></i>
                                <i class="fas fa-university text-info" x-show="method.type === 'bank_transfer' || method.type === 'transfer'"></i>
                                <i class="fas fa-layer-group text-dark" x-show="method.type === 'installment'"></i>
                                <i class="fas fa-wallet text-secondary" x-show="!['cash', 'pos', 'card', 'credit', 'bank_transfer', 'transfer', 'installment'].includes(method.type)"></i>
                            </div>
                            <span class="card-name" x-text="method.name"></span>
                            <div class="active-indicator" x-show="method.type === 'installment' ? isMultiPayment : (!isMultiPayment && selectedMethod && selectedMethod.id === method.id)">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="amount-entry-premium" x-show="selectedMethod && !isMultiPayment" x-transition style="display: none;">
                    <div class="input-wrapper">
                        <label>{{ __('pos::pos.amount_paid') }}</label>
                        <div class="huge-input-container" :class="{ 'has-error': tenderedAmount < finalTotal }">
                            <span class="currency">SAR</span>
                            <input type="number" x-model.number="tenderedAmount" class="huge-input" step="0.01" @focus="$event.target.select()" />
                        </div>
                        <div class="error-text" x-show="tenderedAmount < finalTotal">
                            <i class="fas fa-exclamation-circle"></i> {{ __('pos::pos.amount_less_than_required') }}
                        </div>
                    </div>
                    
                    <div class="quick-cash-chips" x-show="selectedMethod && selectedMethod.type === 'cash'">
                        <button class="chip" @click="tenderedAmount += 5">+5</button>
                        <button class="chip" @click="tenderedAmount += 10">+10</button>
                        <button class="chip" @click="tenderedAmount += 50">+50</button>
                        <button class="chip" @click="tenderedAmount += 100">+100</button>
                        <button class="chip exact" @click="tenderedAmount = finalTotal">{{ __('pos::pos.exact_amount') }}</button>
                    </div>
                </div>

                <div class="amount-entry-premium" x-show="isMultiPayment" x-transition style="display: none;">
                    <div class="d-flex flex-column gap-3">
                        <template x-for="method in paymentMethods.filter(m => m.type !== 'installment')" :key="method.id">
                            <div class="input-wrapper">
                                <label>
                                    <i class="fas fa-money-bill-wave text-success me-2" x-show="method.type === 'cash'"></i>
                                    <i class="fas fa-credit-card text-primary me-2" x-show="method.type === 'pos' || method.type === 'card'"></i>
                                    <i class="fas fa-file-invoice-dollar text-warning me-2" x-show="method.type === 'credit'"></i>
                                    <i class="fas fa-university text-info me-2" x-show="method.type === 'bank_transfer' || method.type === 'transfer'"></i>
                                    <span x-text="method.name"></span>
                                </label>
                                <div class="huge-input-container">
                                    <span class="currency">SAR</span>
                                    <input type="number" x-model.number="multiAmounts[method.id]" class="huge-input" step="0.01" min="0" @focus="$event.target.select()" @input="adjustMultiPayment(method.id)" />
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <div class="error-text mt-3" x-show="totalMultiPaid < finalTotal">
                        <i class="fas fa-exclamation-circle"></i> {{ __('pos::pos.remaining_to_complete') }} <span x-text="(finalTotal - totalMultiPaid).toFixed(2)"></span>
                    </div>
                    <div class="text-success fw-bold mt-2" x-show="totalMultiPaid > finalTotal" style="font-size: 1.1rem;">
                        <i class="fas fa-hand-holding-usd"></i> {{ __('pos::pos.change_from_cash_only') }} <span x-text="(totalMultiPaid - finalTotal).toFixed(2)"></span> SAR
                    </div>
                </div>
            </div>

            <!-- Left Column: Order Summary -->
            <div class="receipt-section">
                <div class="receipt-card">
                    <div class="receipt-header">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <h3>{{ __('pos::pos.invoice_summary') }}</h3>
                    </div>
                    
                    <div class="receipt-details">
                        <div class="receipt-line">
                            <span class="text-muted">{{ __('pos::pos.subtotal') }}</span>
                            <span class="fw-bold" x-text="subtotal.toFixed(2)"></span>
                        </div>
                        <div class="receipt-line">
                            <span class="text-muted">{{ __('pos::pos.vat') }}</span>
                            <span class="fw-bold" x-text="vat.toFixed(2)"></span>
                        </div>
                        <div class="receipt-line text-danger" x-show="discountAmount > 0">
                            <span>{{ __('pos::pos.discount') }}</span>
                            <span class="fw-bold" x-text="'-' + discountAmount.toFixed(2)"></span>
                        </div>
                    </div>
                    
                    <div class="receipt-divider">
                        <div class="notch left"></div>
                        <div class="dash-line"></div>
                        <div class="notch right"></div>
                    </div>
                    
                    <div class="receipt-grand-total">
                        <span class="label">{{ __('pos::pos.amount_required') }}</span>
                        <div class="amount-wrapper text-primary">
                            <span class="amount" x-text="finalTotal.toFixed(2)"></span>
                            <span class="currency">SAR</span>
                        </div>
                    </div>

                    <div class="receipt-change" :class="{ 'positive': changeAmount > 0 }">
                        <div class="change-icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <div class="change-info">
                            <span class="label">{{ __('pos::pos.change_amount') }}</span>
                            <span class="amount" x-text="changeAmount.toFixed(2) + ' SAR'"></span>
                        </div>
                    </div>
                </div>
                
                <button class="premium-confirm-btn mt-4 flex-grow-1" :class="{ 'ready': (isMultiPayment ? totalMultiPaid >= finalTotal : (selectedMethod && tenderedAmount >= finalTotal)) }" :disabled="!(isMultiPayment ? totalMultiPaid >= finalTotal : (selectedMethod && tenderedAmount >= finalTotal))" @click="confirmPayment">
                    <div class="btn-content" x-show="!loadingCheckout">
                        <span>{{ __('pos::pos.confirm_payment_issue_invoice') }}</span>
                        <i class="fas fa-arrow-left"></i>
                    </div>
                    <div class="btn-content" x-show="loadingCheckout" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>{{ __('pos::pos.processing') }}</span>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>
