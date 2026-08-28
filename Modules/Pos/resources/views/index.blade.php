<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Evix POS') }} - Point of Sale</title>

    <!-- System Styles -->
    @include('layouts.partials._styles')

    <!-- POS Specific Styles -->
    <style>
        /* POS Global Layout */
        body, html { height: 100vh; overflow: hidden; background-color: var(--bs-body-bg); font-family: 'Inter', sans-serif; }
        .pos-container { height: 100vh; overflow: hidden; }

        /* Products Grid Area */
        .product-list-container { padding: 1.5rem; background-color: var(--bs-gray-100); }
        .search-bar { position: relative; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-radius: 0.75rem; }
        .search-bar i { position: absolute; right: 1.25rem; top: 50%; transform: translateY(-50%); color: var(--bs-primary); font-size: 1.1rem; }
        .search-bar input { width: 100%; padding: 1rem 3rem 1rem 1.5rem; border-radius: 0.75rem; border: 1px solid var(--bs-gray-200); background-color: var(--bs-body-bg); color: var(--bs-body-color); outline: none; transition: all 0.3s ease; font-size: 1rem; }
        .search-bar input:focus { border-color: var(--bs-primary); box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15); }

        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.5rem; padding-right: 0.5rem; padding-bottom: 2rem; }
        .product-card { background: var(--bs-body-bg); border: 1px solid var(--bs-gray-200); border-radius: 1rem; overflow: hidden; cursor: pointer; transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); display: flex; flex-direction: column; box-shadow: 0 4px 6px rgba(0,0,0,0.03); }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 12px 20px -5px rgba(0,0,0,0.1); border-color: var(--bs-primary); }
        .product-image { height: 160px; background: var(--bs-gray-100); display: flex; align-items: center; justify-content: center; overflow: hidden; color: var(--bs-gray-400); font-size: 3rem; }
        .product-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .product-card:hover .product-image img { transform: scale(1.05); }
        .product-info { padding: 1.25rem; display: flex; flex-direction: column; flex-grow: 1; }
        .product-info h3 { font-size: 1.05rem; font-weight: 700; margin: 0 0 0.5rem 0; color: var(--bs-text-dark); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .product-info .barcode { font-size: 0.8rem; color: var(--bs-gray-500); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.35rem; }
        .product-price { margin-top: auto; font-size: 1.25rem; font-weight: 800; color: var(--bs-primary); }

        .loading-state, .empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--bs-gray-500); height: 100%; font-size: 1.2rem; }
        .loading-state i, .empty-state i { font-size: 3rem; margin-bottom: 1rem; color: var(--bs-gray-400); }

        /* Cart Area */
        .cart-sidebar { background: var(--bs-body-bg); border-right: 1px solid var(--bs-gray-200); display: flex; flex-direction: column; box-shadow: -5px 0 25px rgba(0,0,0,0.05); z-index: 10; min-width: 550px; width: 550px; max-width: 600px; }
        .cart-header { padding: 1.5rem; border-bottom: 1px solid var(--bs-gray-200); display: flex; justify-content: space-between; align-items: center; background: var(--bs-body-bg); }
        .cart-header h2 { margin: 0; font-size: 1.5rem; font-weight: 800; color: var(--bs-text-dark); display: flex; align-items: center; gap: 0.5rem; }
        .cart-header h2::before { content: "\f07a"; font-family: "Font Awesome 5 Free"; font-weight: 900; color: var(--bs-primary); }
        .items-count { background: rgba(var(--bs-primary-rgb), 0.1); color: var(--bs-primary); padding: 0.35rem 1rem; border-radius: 2rem; font-size: 0.9rem; font-weight: 700; }

        .cart-items { flex-grow: 1; overflow-y: auto; padding: 0; background: var(--bs-body-bg); }
        .empty-cart { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: var(--bs-gray-400); }
        .empty-cart i { font-size: 4rem; margin-bottom: 1rem; opacity: 0.5; }

        .cart-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .cart-table th { padding: 1rem 0.5rem; background: var(--bs-gray-100); color: var(--bs-gray-600); font-size: 0.9rem; font-weight: 700; text-align: right; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--bs-gray-200); position: sticky; top: 0; z-index: 5; }
        .cart-table td { padding: 1.25rem 0.5rem; border-bottom: 1px dashed var(--bs-gray-200); vertical-align: middle; }
        .cart-table tr:hover td { background-color: rgba(var(--bs-primary-rgb), 0.02); }
        .product-name { font-weight: 700; color: var(--bs-text-dark); max-width: 180px; white-space: normal; line-height: 1.4; font-size: 1.05rem; }
        
        .qty-control { display: inline-flex; align-items: center; background: var(--bs-gray-100); border-radius: 0.5rem; padding: 0.25rem; border: 1px solid var(--bs-gray-200); }
        .qty-btn { width: 30px; height: 30px; border: none; background: var(--bs-body-bg); border-radius: 0.35rem; display: flex; align-items: center; justify-content: center; font-weight: bold; color: var(--bs-text-dark); cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .qty-btn:hover { background: var(--bs-primary); color: white; border-color: var(--bs-primary); }
        .qty-input { width: 45px; text-align: center; border: none; background: transparent; font-weight: 700; padding: 0; margin: 0; -moz-appearance: textfield; outline: none; color: var(--bs-text-dark); font-size: 1rem; }
        .qty-input::-webkit-outer-spin-button, .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

        .item-total { font-weight: 700; color: var(--bs-primary); font-size: 1.05rem; }
        .remove-btn { border: none; background: rgba(var(--bs-danger-rgb), 0.1); color: var(--bs-danger); width: 36px; height: 36px; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
        .remove-btn:hover { background: var(--bs-danger); color: white; transform: scale(1.05); }

        .cart-footer { padding: 1.5rem; background: var(--bs-body-bg); border-top: 1px solid var(--bs-gray-200); box-shadow: 0 -5px 20px rgba(0,0,0,0.03); }
        .discount-container { background: var(--bs-gray-100); padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.25rem; border: 1px dashed var(--bs-gray-300); }
        .discount-container label { display: block; font-size: 0.9rem; font-weight: 700; color: var(--bs-gray-700); margin-bottom: 0.75rem; }
        .discount-container .input-group { box-shadow: 0 2px 5px rgba(0,0,0,0.02); border-radius: 0.5rem; overflow: hidden; }
        .discount-container input, .discount-container select { border: 1px solid var(--bs-gray-300); padding: 0.75rem 1rem; font-weight: 600; }
        .discount-container input:focus, .discount-container select:focus { border-color: var(--bs-primary); box-shadow: none; outline: none; background: var(--bs-body-bg); }

        .totals { background: var(--bs-gray-100); padding: 1.25rem; border-radius: 0.75rem; margin-bottom: 1.5rem; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 1rem; font-weight: 600; color: var(--bs-gray-600); }
        .total-row:last-child { margin-bottom: 0; border-top: 2px dashed var(--bs-gray-300); padding-top: 1rem; margin-top: 0.5rem; }
        .grand-total { font-size: 1.5rem !important; font-weight: 900; color: var(--bs-primary) !important; align-items: center; }

        .checkout-btn { width: 100%; padding: 1.25rem; border: none; background: var(--bs-primary); color: white; border-radius: 0.75rem; font-size: 1.25rem; font-weight: 800; display: flex; justify-content: center; align-items: center; gap: 1rem; cursor: pointer; transition: all 0.3s; box-shadow: 0 8px 15px rgba(var(--bs-primary-rgb), 0.25); text-transform: uppercase; letter-spacing: 0.5px; }
        .checkout-btn:hover:not(:disabled) { background: var(--bs-primary-active, #0056b3); transform: translateY(-3px); box-shadow: 0 12px 20px rgba(var(--bs-primary-rgb), 0.3); }
        .checkout-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; box-shadow: none; background: var(--bs-gray-400); }
        
        .btn-return { background: var(--bs-danger); box-shadow: 0 8px 15px rgba(var(--bs-danger-rgb), 0.25); }
        .btn-return:hover:not(:disabled) { background: #dc3545; box-shadow: 0 12px 20px rgba(var(--bs-danger-rgb), 0.3); }
        
        /* Modals & Overlays */
        .modal-overlay, .modal-backdrop { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(5px); z-index: 1050; display: flex; align-items: center; justify-content: center; }
        .checkout-modal-premium, .session-modal { background: var(--bs-body-bg); border-radius: 1.5rem; width: 95%; max-width: 900px; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); display: flex; flex-direction: column; }
        .session-modal { max-width: 500px; }
        .modal-content { background: var(--bs-body-bg); border-radius: 1.5rem; width: 95%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); display: flex; flex-direction: column; }
        
        /* Modal Header */
        .modal-header-premium, .modal-header { padding: 1.5rem 2rem; border-bottom: 1px solid var(--bs-gray-200); display: flex; justify-content: space-between; align-items: center; background: var(--bs-gray-100); border-radius: 1.5rem 1.5rem 0 0; }
        .header-title { display: flex; align-items: center; gap: 1rem; }
        .icon-circle { width: 48px; height: 48px; background: rgba(var(--bs-primary-rgb), 0.1); color: var(--bs-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
        .header-title h2, .modal-header h2 { margin: 0; font-size: 1.5rem; font-weight: 800; color: var(--bs-text-dark); }
        .close-btn, .btn-close { border: none; background: transparent; font-size: 1.5rem; color: var(--bs-gray-500); cursor: pointer; transition: color 0.2s; padding: 0.5rem; }
        .close-btn:hover, .btn-close:hover { color: var(--bs-danger); }
        
        /* Modal Body Premium */
        .modal-body-premium { display: flex; flex-direction: row; gap: 2rem; padding: 2rem; }
        .modal-body { padding: 2rem; display: flex; flex-direction: column; }
        @media (max-width: 768px) { .modal-body-premium { flex-direction: column; } }
        
        /* Payment Section */
        .payment-section { flex: 1.5; display: flex; flex-direction: column; gap: 1.5rem; }
        .section-title { font-size: 1.1rem; font-weight: 700; color: var(--bs-gray-700); margin-bottom: 0.5rem; }
        
        .methods-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 1rem; }
        .payment-card { background: var(--bs-body-bg); border: 2px solid var(--bs-gray-200); border-radius: 1rem; padding: 1.25rem; display: flex; flex-direction: column; align-items: center; gap: 0.75rem; cursor: pointer; transition: all 0.3s ease; position: relative; }
        .payment-card:hover { border-color: rgba(var(--bs-primary-rgb), 0.5); transform: translateY(-2px); }
        .payment-card.active { border-color: var(--bs-primary); background: rgba(var(--bs-primary-rgb), 0.05); }
        .card-icon { font-size: 2rem; }
        .card-name { font-weight: 600; color: var(--bs-text-dark); font-size: 0.95rem; text-align: center; }
        .active-indicator { position: absolute; top: -10px; right: -10px; background: var(--bs-primary); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; border: 2px solid var(--bs-body-bg); }
        
        /* Amount Entry */
        .amount-entry-premium, .amount-entry { background: var(--bs-gray-100); padding: 1.5rem; border-radius: 1rem; margin-top: 1rem; }
        .amount-entry label { display: block; font-weight: 600; color: var(--bs-gray-700); margin-bottom: 0.5rem; }
        .input-wrapper label { display: block; font-weight: 600; color: var(--bs-gray-600); margin-bottom: 0.75rem; }
        .huge-input-container { display: flex; align-items: center; background: var(--bs-body-bg); border: 2px solid var(--bs-gray-300); border-radius: 0.75rem; padding: 0.5rem 1rem; transition: border-color 0.3s; }
        .huge-input-container:focus-within { border-color: var(--bs-primary); box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15); }
        .huge-input-container.has-error { border-color: var(--bs-danger); }
        .currency { font-weight: 700; color: var(--bs-gray-500); margin-right: 0.5rem; font-size: 1.25rem; }
        .huge-input { border: none; background: transparent; width: 100%; font-size: 2rem; font-weight: 800; color: var(--bs-text-dark); outline: none; }
        .error-text { color: var(--bs-danger); font-size: 0.85rem; font-weight: 600; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.35rem; }
        
        .quick-cash-chips { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 1rem; }
        .chip { padding: 0.5rem 1rem; border: 1px solid var(--bs-gray-300); background: var(--bs-body-bg); border-radius: 2rem; font-weight: 600; color: var(--bs-text-dark); cursor: pointer; transition: all 0.2s; }
        .chip:hover { background: var(--bs-primary); color: white; border-color: var(--bs-primary); }
        .chip.exact { background: rgba(var(--bs-primary-rgb), 0.1); color: var(--bs-primary); border-color: var(--bs-primary); }
        
        /* Receipt Section */
        .receipt-section { flex: 1; display: flex; flex-direction: column; }
        .receipt-card { background: var(--bs-gray-100); border-radius: 1rem; padding: 1.5rem; border: 1px dashed var(--bs-gray-300); }
        .receipt-header { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; color: var(--bs-gray-700); }
        .receipt-header i { font-size: 1.5rem; }
        .receipt-header h3 { margin: 0; font-size: 1.1rem; font-weight: 700; }
        
        .receipt-details { display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem; }
        .receipt-line { display: flex; justify-content: space-between; font-size: 1rem; }
        
        .receipt-divider { position: relative; height: 1px; margin: 1.5rem 0; }
        .dash-line { border-top: 2px dashed var(--bs-gray-300); width: 100%; position: absolute; top: 50%; transform: translateY(-50%); }
        .notch { width: 20px; height: 20px; background: var(--bs-body-bg); border-radius: 50%; position: absolute; top: 50%; transform: translateY(-50%); z-index: 1; }
        .notch.left { left: -25px; box-shadow: inset -2px 0 3px rgba(0,0,0,0.05); }
        .notch.right { right: -25px; box-shadow: inset 2px 0 3px rgba(0,0,0,0.05); }
        
        .receipt-grand-total { display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 1.5rem; background: var(--bs-body-bg); padding: 1rem; border-radius: 0.75rem; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .receipt-grand-total .label { font-size: 0.9rem; font-weight: 700; color: var(--bs-gray-500); margin-bottom: 0.25rem; }
        .amount-wrapper { display: flex; align-items: baseline; gap: 0.25rem; }
        .amount-wrapper .amount { font-size: 2.5rem; font-weight: 900; }
        
        .receipt-change { display: flex; align-items: center; gap: 1rem; padding: 1rem; border-radius: 0.75rem; background: var(--bs-body-bg); border: 1px solid var(--bs-gray-200); }
        .receipt-change.positive { background: rgba(var(--bs-success-rgb), 0.1); border-color: rgba(var(--bs-success-rgb), 0.2); }
        .receipt-change.positive .change-icon { color: var(--bs-success); }
        .change-icon { font-size: 1.5rem; color: var(--bs-gray-400); }
        .change-info { display: flex; flex-direction: column; }
        .change-info .label { font-size: 0.85rem; font-weight: 600; color: var(--bs-gray-600); }
        .change-info .amount { font-size: 1.25rem; font-weight: 800; color: var(--bs-text-dark); }
        
        .premium-confirm-btn { padding: 1.25rem; border: none; border-radius: 0.75rem; background: var(--bs-gray-300); color: var(--bs-gray-600); font-size: 1.15rem; font-weight: 800; cursor: not-allowed; transition: all 0.3s; }
        .premium-confirm-btn.ready { background: var(--bs-primary); color: white; cursor: pointer; box-shadow: 0 8px 15px rgba(var(--bs-primary-rgb), 0.25); }
        .premium-confirm-btn.ready:hover { background: var(--bs-primary-active, #0056b3); transform: translateY(-2px); box-shadow: 0 12px 20px rgba(var(--bs-primary-rgb), 0.3); }
        .btn-content { display: flex; justify-content: center; align-items: center; gap: 0.75rem; }
        
        /* Form controls inside modals */
        .modal-body .form-group { margin-bottom: 1rem; }
        .modal-body label { font-weight: 600; color: var(--bs-gray-700); margin-bottom: 0.5rem; display: block; }
        .tendered-input, .form-control { width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--bs-gray-300); border-radius: 0.5rem; font-size: 1rem; transition: border-color 0.2s; outline: none; }
        .tendered-input:focus, .form-control:focus { border-color: var(--bs-primary); box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.15); }
        .modal-footer { border-top: 1px solid var(--bs-gray-200); padding: 1.5rem; display: flex; justify-content: flex-end; gap: 1rem; }
        
        /* Modal Transitions */
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="app-default" x-data="posApp('{{ $device->uuid }}', {{ json_encode($device->only(['allow_price_modification', 'allow_discount_modification', 'show_available_qty', 'enable_pos_returns', 'print_copies_count', 'allow_negative_stock', 'auto_print_receipt', 'prices_include_tax'])) }})" @keydown.window="handleGlobalKeydown($event)">
    @include('layouts.partials._loader')
    @include('layouts.partials._script_dark_mode')

    <!-- Login Overlay -->
    <div class="modal-overlay" x-show="!isAuthenticated" x-transition x-cloak style="z-index: 9999; background: var(--bs-body-bg);">
        <div class="modal-content" style="max-width: 400px; padding: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.1); position: relative;">
            

            <a href="{{ route('switchLang', app()->getLocale() == 'ar' ? 'en' : 'ar') }}" class="btn btn-sm btn-light position-absolute" style="top: 1rem; right: 1rem; border-radius: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none;" title="{{ __('pos::pos.switch_lang') }}">
                <i class="fas fa-globe me-1"></i> {{ __('pos::pos.switch_lang') }}
            </a>

            <div class="text-center mb-4">
                <i class="fas fa-cash-register text-primary mb-3" style="font-size: 3rem;"></i>
                <h3 class="fw-bold">{{ __('pos::pos.pos_login') }}</h3>
                <p class="text-muted">{{ __('pos::pos.pos_login_desc') }}</p>
            </div>
            
            <form @submit.prevent="performLogin">
                <div class="form-group mb-3">
                    <label>{{ __('pos::pos.login_id') }}</label>
                    <input type="text" class="form-control" x-model="loginData.login_id" placeholder="{{ __('pos::pos.login_id') }}" required>
                </div>
                
                <div class="form-group mb-4">
                    <label>{{ __('pos::pos.password') }}</label>
                    <input type="password" class="form-control" x-model="loginData.password" placeholder="{{ __('pos::pos.password') }}" required autocomplete="current-password">
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" :disabled="loadingLogin">
                    <span x-show="!loadingLogin">{{ __('pos::pos.login_btn') }}</span>
                    <span x-show="loadingLogin"><i class="fas fa-spinner fa-spin"></i> {{ __('pos::pos.logging_in') }}</span>
                </button>
            </form>
        </div>
    </div>

    <div class="pos-container d-flex flex-row h-100" x-show="isAuthenticated" x-cloak>
        <!-- Products Grid Area -->
        <div class="p-3 overflow-auto" style="flex: 2;" x-show="sessionId !== null" x-transition>
            @include('pos::pos.partials._products_grid')
        </div>

        <div class="p-3 align-items-center justify-content-center flex-column" :class="sessionId === null ? 'd-flex' : 'd-none'" style="flex: 2; position: relative;">

            <i class="fas fa-cash-register mb-3 text-muted" style="font-size: 4rem;"></i>
            <h3 class="text-muted">{{ __('pos::pos.please_open_shift') }}</h3>
            <button class="btn btn-primary mt-3 px-4 py-2 fw-bold" @click="showSessionModal = true; isClosingSession = false;">
                <i class="fas fa-play-circle me-2"></i> {{ __('pos::pos.open_shift_btn') }}
            </button>
            <button class="btn btn-outline-danger mt-3 px-4 py-2 fw-bold" @click="logout()">
                <i class="fas fa-sign-out-alt me-2"></i> {{ __('pos::pos.logout') }}
            </button>
        </div>

        <!-- Cart Area -->
        <div class="cart-sidebar bg-white border-start p-3 d-flex flex-column shadow-sm" style="flex: 1;" x-show="sessionId !== null" x-transition>
            @include('pos::pos.partials._cart')
        </div>
    </div>

    <!-- Modals -->
    @include('pos::pos.partials._checkout_modal')
    @include('pos::pos.partials._session_modal')
    @include('pos::pos.partials._transaction_modal')
    @include('pos::pos.partials._invoices_modal')
    @include('pos::pos.partials._held_carts_modal')
    @include('pos::pos.partials._customer_modal')

    <!-- System Scripts -->
    @include('layouts.partials._scripts')

    <!-- Alpine.js (Already loaded by Livewire in _scripts) -->

    <script>
        function posApp(deviceUuid, settings) {
            return {
                deviceUuid: deviceUuid,
                settings: Object.assign({
                    allow_price_modification: true,
                    allow_discount_modification: true,
                    show_available_qty: true,
                    enable_pos_returns: true,
                    print_copies_count: 1,
                    allow_negative_stock: true,
                    auto_print_receipt: true,
                    prices_include_tax: false
                }, settings),
                isAuthenticated: !!localStorage.getItem('pos_token_' + deviceUuid),
                bearerToken: localStorage.getItem('pos_token_' + deviceUuid) || '',
                loginData: {
                    email: '',
                    password: ''
                },
                loadingLogin: false,
                configHash: '',

                sessionToken: '',
                sessionId: null,

                showCustomerModal: false,
                loadingCustomer: false,
                newCustomer: {
                    name: '',
                    phone: ''
                },

                // Barcode State
                barcodeBuffer: '',
                barcodeTimer: null,

                init() {
                    if (this.isAuthenticated) {
                        this.bootstrapSystem();
                    }
                },

                handleGlobalKeydown(e) {
                    // 1. Feature: Enter acts as Tab inside any input (across the whole POS)
                    if (e.key === 'Enter' && ['INPUT', 'SELECT'].includes(e.target.tagName)) {
                        // Allow search barcode submission
                        if (e.target.getAttribute('x-model') === 'search' && this.search.trim().length > 0) {
                            e.preventDefault();
                            this.processBarcode(this.search.trim());
                            this.search = '';
                            return;
                        }
                        
                        e.preventDefault();

                        if (this.showCheckoutModal) {
                            if (!this.isMultiPayment && this.tenderedAmount >= this.finalTotal) {
                                this.confirmPayment();
                                return;
                            }
                            if (this.isMultiPayment && this.totalMultiPaid >= this.finalTotal && e.target.tagName !== 'INPUT') {
                                this.confirmPayment();
                                return;
                            }
                        }
                        
                        let container = document.body;
                        if (this.showCheckoutModal) container = document.querySelector('.checkout-modal-premium') || container;
                        else if (this.showSessionModal) container = document.querySelector('#sessionModal') || container;
                        else if (this.showTransactionModal) container = document.querySelector('#transactionModal') || container;
                        
                        // Select focusable elements in logical flow
                        const focusableElements = Array.from(container.querySelectorAll('input:not([disabled]):not([readonly]), button:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'))
                            .filter(el => el.offsetParent !== null && window.getComputedStyle(el).visibility !== 'hidden');
                        
                        const index = focusableElements.indexOf(e.target);
                        if (index > -1 && index < focusableElements.length - 1) {
                            const nextEl = focusableElements[index + 1];
                            nextEl.focus();
                            if (nextEl.tagName === 'INPUT') nextEl.select();
                        } else if (index === focusableElements.length - 1 && focusableElements.length > 0) {
                            if (this.showCheckoutModal && this.isMultiPayment && this.totalMultiPaid >= this.finalTotal) {
                                this.confirmPayment();
                                return;
                            }
                            focusableElements[0].focus();
                            if (focusableElements[0].tagName === 'INPUT') focusableElements[0].select();
                        }
                        return;
                    }

                    // Avoid modals or non-authenticated state for the global barcode scanner
                    if (!this.isAuthenticated || this.showSessionModal || this.showCheckoutModal || this.showTransactionModal || this.showInvoicesModal || this.showHeldCartsModal) {
                        return;
                    }

                    // Ignore if focused on an input element (barcode scanner shouldn't capture typing)
                    if (['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName)) {
                        return;
                    }

                    // Ignore modifier keys
                    if (e.key === 'Shift' || e.key === 'Control' || e.key === 'Alt' || e.key === 'Meta') return;

                    if (e.key === 'Enter') {
                        if (this.barcodeBuffer.length > 1) {
                            e.preventDefault();
                            this.processBarcode(this.barcodeBuffer);
                        }
                        this.barcodeBuffer = '';
                        return;
                    }

                    // Collect character
                    if (e.key.length === 1) {
                        this.barcodeBuffer += e.key;
                    }

                    // Reset buffer if typing is too slow (manual typing vs scanner speed)
                    clearTimeout(this.barcodeTimer);
                    this.barcodeTimer = setTimeout(() => {
                        this.barcodeBuffer = '';
                    }, 200); // Increased timeout for slower scanners
                },

                // Products State
                products: [],
                search: '',
                loadingProducts: false,

                // Customer State
                customerId: null,

                // Cart State
                cartItems: [],
                subtotal: 0,
                vat: 0,
                total: 0,
                _finalTotal: 0,
                localDiscount: 0,
                discountType: 'amount',
                discountAmount: 0,
                isReturn: false,
                parentInvoiceId: null,

                // Checkout State
                showCheckoutModal: false,
                loadingCheckout: false,
                paymentMethods: @json($device->paymentMethods->map(fn($method) => [
                    'id' => $method->id,
                    'name' => $method->name,
                    'type' => $method->type
                ])->toArray()),
                selectedMethod: null,
                tenderedAmount: 0,
                isMultiPayment: false,
                multiAmounts: {},
                
                get totalMultiPaid() {
                    return Object.values(this.multiAmounts).reduce((sum, val) => sum + (parseFloat(val) || 0), 0);
                },
                
                // Session State
                showSessionModal: false,
                isClosingSession: false,
                sessionAmount: null,
                sessionNotes: '',
                transferCashToMainSafe: true,
                
                // Transaction State
                showTransactionModal: false,
                transactionType: 'withdrawal',
                transactionAmount: '',
                transactionNotes: '',
                loadingTransaction: false,

                // Invoices State
                showInvoicesModal: false,
                shiftInvoices: [],
                loadingInvoices: false,
                invoiceSearch: '',

                // Held Carts State
                showHeldCartsModal: false,
                heldCarts: [],

                get finalTotal() {
                    return this._finalTotal || 0;
                },

                get changeAmount() {
                    return Math.max(0, this.tenderedAmount - this.finalTotal);
                },

                // Init
                init() {
                    if (this.isAuthenticated) {
                        this.bootstrapSystem();
                    }
                },

                // Auth
                performLogin() {
                    if (!this.loginData.login_id || !this.loginData.password) {
                        toastr.error("{{ __('pos::pos.login_validation_error') }}");
                        return;
                    }
                    
                    this.loadingLogin = true;
                    fetch('/api/pos/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            login_id: this.loginData.login_id,
                            password: this.loginData.password,
                            device_uuid: this.deviceUuid
                        })
                    })
                    .then(res => res.json().then(data => ({ status: res.status, body: data })))
                    .then(res => {
                        if (res.status === 200) {
                            this.bearerToken = res.body.access_token;
                            localStorage.setItem('pos_token_' + this.deviceUuid, this.bearerToken);
                            this.isAuthenticated = true;
                            toastr.success("{{ __('pos::pos.login_success') }}");
                            this.bootstrapSystem();
                        } else if (res.status === 422 && res.body.errors) {
                            console.log(res.body);
                            let errorMsgs = Object.values(res.body.errors).flat().join('<br>');
                            toastr.error(errorMsgs || "{{ __('pos::pos.invalid_credentials') }}");
                        } else {
                            console.log(res.body);
                            toastr.error(res.body.message || "{{ __('pos::pos.invalid_credentials') }}");
                        }
                        this.loadingLogin = false;
                    })
                    .catch(err => {
                        console.error(err);
                        toastr.error("{{ __('pos::pos.login_error') }}");
                        this.loadingLogin = false;
                    });
                },

                bootstrapSystem() {
                    fetch('/api/pos/bootstrap', {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': 'Bearer ' + this.bearerToken,
                            'X-Device-UUID': this.deviceUuid
                        }
                    })
                    .then(res => {
                        if (res.status === 401) {
                            toastr.error('Authentication failed. Status: 401. Token might be invalid or expired.');
                            setTimeout(() => {
                                this.logout();
                            }, 3000);
                            throw new Error('Unauthorized');
                        }
                        return res.json();
                    })
                    .then(data => {
                        this.configHash = data.config_hash;
                        if (data.data.current_shift) {
                            this.sessionId = data.data.current_shift.id;
                            this.fetchProducts();
                        } else {
                            this.showSessionModal = true;
                        }
                    })
                    .catch(err => console.error('Bootstrap failed:', err));
                },

                logout() {
                    if (this.bearerToken) {
                        fetch('/api/pos/logout', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Authorization': 'Bearer ' + this.bearerToken,
                                'X-Device-UUID': this.deviceUuid
                            }
                        }).finally(() => {
                            this.bearerToken = '';
                            localStorage.removeItem('pos_token_' + this.deviceUuid);
                            this.isAuthenticated = false;
                        });
                    }
                },

                initSession() {
                    fetch(`/api/pos/session/status?device_uuid=${this.deviceUuid}`, {
                        headers: {
                            'Authorization': 'Bearer ' + this.bearerToken
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status && data.has_active_session) {
                                this.sessionToken = data.token;
                                this.sessionId = data.session.id;
                                this.fetchProducts();
                            } else {
                                this.showSessionModal = true;
                            }
                        })
                        .catch(err => console.error('Failed to load session:', err));
                },

                fetchProducts() {
                    this.loadingProducts = true;
                    const lang = '{{ app()->getLocale() }}';
                    const timestamp = new Date().getTime();
                    const searchParam = this.search ? `?search=${encodeURIComponent(this.search)}&is_sale=1&lang=${lang}&_t=${timestamp}` : `?is_sale=1&lang=${lang}&_t=${timestamp}`;
                    fetch(`/api/pos/products${searchParam}`, {
                        headers: {
                            'Authorization': 'Bearer ' + this.bearerToken,
                            'X-Device-UUID': this.deviceUuid,
                            'Accept': 'application/json',
                            'Accept-Language': lang
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.status && data.data && data.data.data) {
                                this.products = data.data.data.map(p => ({
                                    id: p.id,
                                    name: p.name,
                                    price: Math.round((parseFloat(p.price) || 0) * 100) / 100,
                                    barcode: p.barcode,
                                    img: p.img,
                                    unit: p.unit || '-',
                                    unit_id: p.unit_id,
                                    have_sizes: p.have_sizes || false,
                                    quantity: p.quantity || 0,
                                    vat: p.vat || 15,
                                    tax_id: p.tax_id || null
                                }));
                            } else {
                                this.products = [];
                            }
                            this.loadingProducts = false;
                        })
                        .catch(err => {
                            console.error(err);
                            this.loadingProducts = false;
                        });
                },

                processBarcode(barcode) {
                    // Search in already loaded products first
                    const product = this.products.find(p => p.barcode === barcode);
                    if (product) {
                        this.addToCart(product);
                        return;
                    }

                    // If not found in loaded products, fetch from API
                    const lang = '{{ app()->getLocale() }}';
                    fetch(`/api/pos/products?search=${encodeURIComponent(barcode)}&is_sale=1&lang=${lang}`, {
                        headers: {
                            'Authorization': 'Bearer ' + this.bearerToken,
                            'X-Device-UUID': this.deviceUuid,
                            'Accept': 'application/json',
                            'Accept-Language': lang
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.status && data.data && data.data.data && data.data.data.length > 0) {
                                // Find exact match by barcode, or just take the first result
                                const p = data.data.data.find(res => res.barcode === barcode) || data.data.data[0];
                                const productObj = {
                                    id: p.id,
                                    name: p.name,
                                    price: Math.round((parseFloat(p.price) || 0) * 100) / 100,
                                    barcode: p.barcode,
                                    img: p.img,
                                    unit: p.unit || '-',
                                    unit_id: p.unit_id,
                                    have_sizes: p.have_sizes || false,
                                    quantity: p.quantity || 0,
                                    vat: p.vat || 15,
                                    tax_id: p.tax_id || null
                                };
                                this.addToCart(productObj);
                            } else {
                                toastr.warning("{{ __('pos::pos.product_not_found') }}");
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            toastr.error("{{ __('pos::pos.product_not_found') }}");
                        });
                },

                fetchShiftInvoices() {
                    this.loadingInvoices = true;
                    const searchParam = this.invoiceSearch ? `&search=${encodeURIComponent(this.invoiceSearch)}` : '';
                    fetch(`/api/pos/session/invoices?session_id=${this.sessionId}${searchParam}`, {
                        headers: {
                            'Authorization': 'Bearer ' + this.bearerToken,
                            'X-Device-UUID': this.deviceUuid
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status) {
                            this.shiftInvoices = data.data;
                        }
                        this.loadingInvoices = false;
                    })
                    .catch(() => {
                        this.loadingInvoices = false;
                        this.shiftInvoices = [];
                    });
                },

                holdCart() {
                    if (this.cartItems.length === 0) return;
                    this.heldCarts.push({
                        time: new Date().toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' }),
                        items: [...this.cartItems],
                        total: this.total,
                        customerId: this.customerId
                    });
                    this.cartItems = [];
                    this.customerId = null;
                    this.calculateTotal();
                    toastr.success("{{ __('pos::pos.cart_held_success') }}");
                },

                resumeCart(index) {
                    if (this.cartItems.length > 0) {
                        Swal.fire({
                            title: "{{ __('pos::pos.confirm_resume') }}",
                            text: "{{ __('pos::pos.cart_not_empty_resume') }}",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: "{{ __('pos::pos.yes_resume') }}",
                            cancelButtonText: "{{ __('pos::pos.cancel') }}"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.processResumeCart(index);
                            }
                        });
                        return;
                    }
                    this.processResumeCart(index);
                },

                processResumeCart(index) {
                    const cart = this.heldCarts[index];
                    this.cartItems = [...cart.items];
                    this.customerId = cart.customerId;
                    this.calculateTotal();
                    this.heldCarts.splice(index, 1);
                    this.showHeldCartsModal = false;
                    toastr.success("{{ __('pos::pos.cart_resumed_success') }}");
                },

                removeHeldCart(index) {
                    Swal.fire({
                        title: "{{ __('pos::pos.confirm_delete') }}",
                        text: "{{ __('pos::pos.confirm_delete_held_cart') }}",
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: "{{ __('pos::pos.yes_delete') }}",
                        cancelButtonText: "{{ __('pos::pos.cancel') }}"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.heldCarts.splice(index, 1);
                            toastr.info("{{ __('pos::pos.held_cart_deleted') }}");
                        }
                    });
                },

                toggleReturnMode() {
                    this.isReturn = !this.isReturn;
                    this.parentInvoiceId = null;
                    this.cartItems = [];
                    this.calculateTotal();
                },

                loadInvoiceForReturn(invoiceId) {
                    fetch(`/api/pos/invoice/${invoiceId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': 'Bearer ' + this.bearerToken,
                            'X-Device-UUID': this.deviceUuid
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status) {
                                this.isReturn = true;
                                this.parentInvoiceId = invoiceId;
                                this.cartItems = data.data.items.map(i => ({...i, price: Math.round((parseFloat(i.price) || 0) * 100) / 100, cart_key: i.id + '_' + (i.have_sizes ? 1 : 0)}));
                                this.customerId = data.data.customer_id;
                                this.discountType = data.data.discount_type == 1 ? 'percentage' : 'amount';
                                this.localDiscount = data.data.discount_amount || 0;
                                this.calculateTotal();
                                this.showInvoicesModal = false;
                                toastr.success("{{ __('pos::pos.return_invoice_loaded') }}");
                            } else {
                                toastr.error(data.message || "{{ __('pos::pos.failed_load_invoice') }}");
                            }
                        })
                        .catch(() => toastr.error("{{ __('pos::pos.connection_error') }}"));
                },

                addToCart(product) {
                    if (!this.settings.allow_negative_stock && product.quantity < 1) {
                        toastr.error("{{ __('pos::pos.qty_not_available') }}");
                        return;
                    }
                    const cartKey = product.id + '_' + (product.have_sizes ? 1 : 0);
                    const existingIndex = this.cartItems.findIndex(i => i.cart_key === cartKey);
                    
                    if (existingIndex !== -1) {
                        const existing = this.cartItems[existingIndex];
                        if (!this.settings.allow_negative_stock && (existing.qty + 1) > product.quantity) {
                            toastr.error("{{ __('pos::pos.qty_exceeds_available') }}");
                            return;
                        }
                        existing.qty++;
                        // Move item to the bottom of the list
                        this.cartItems.splice(existingIndex, 1);
                        this.cartItems.push(existing);
                    } else {
                        this.cartItems.push({...product, qty: 1, cart_key: cartKey});
                    }
                    this.calculateTotal();
                    this.scrollToBottomCart();
                },

                updateQty(cartKey, qty) {
                    if (qty < 1) return;
                    const item = this.cartItems.find(i => i.cart_key === cartKey);
                    if (item) {
                        const product = this.products.find(p => (p.id + '_' + (p.have_sizes ? 1 : 0)) === cartKey);
                        if (product && !this.settings.allow_negative_stock && qty > product.quantity) {
                            toastr.error("{{ __('pos::pos.qty_exceeds_available') }}");
                            return;
                        }
                        item.qty = qty;
                        this.calculateTotal();
                    }
                },

                removeItem(cartKey) {
                    this.cartItems = this.cartItems.filter(i => i.cart_key !== cartKey);
                    this.calculateTotal();
                },

                scrollToBottomCart() {
                    this.$nextTick(() => {
                        const container = document.querySelector('.cart-items');
                        if (container) {
                            // Smooth scroll behavior can be nice, but instant is better for POS speed
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                },

                updateDiscount() {
                    if (this.localDiscount < 0) this.localDiscount = 0;
                    if (this.discountType === 'percentage' && this.localDiscount > 100) this.localDiscount = 100;
                    this.calculateTotal();
                },

                calculateTotal() {
                    let pricesIncludeVat = this.settings.prices_include_tax;
                    let globalDiscountType = this.discountType === 'percentage' ? 1 : 2;
                    let globalDiscountVal = parseFloat(this.localDiscount) || 0;
                    
                    let totalNetEntered = 0;
                    let lines = this.cartItems.map(item => {
                        let qty = parseFloat(item.qty) || 0;
                        // Force rounding on item price
                        item.price = Math.round((parseFloat(item.price) || 0) * 100) / 100;
                        let price = item.price;
                        let vatRate = parseFloat(item.vat || 15);
                        
                        let rowGrossEntered = qty * price;
                        let lineDiscEntered = 0; // Item level discount not implemented in UI yet
                        let netLineEntered = rowGrossEntered - lineDiscEntered;
                        
                        totalNetEntered += netLineEntered;
                        return { item, vatRate, rowGrossEntered, lineDiscEntered, netLineEntered };
                    });

                    let globalDiscEntered = globalDiscountType === 1 
                        ? totalNetEntered * (globalDiscountVal / 100)
                        : globalDiscountVal;
                        
                    if (globalDiscEntered > totalNetEntered) {
                        globalDiscEntered = totalNetEntered;
                    }
                    this.discountAmount = Math.round(globalDiscEntered * 100) / 100;

                    let netInvoiceEntered = totalNetEntered - globalDiscEntered;
                    let globalDiscountFactor = totalNetEntered > 0 ? (netInvoiceEntered / totalNetEntered) : 1;

                    let sumBaseExclusive = 0;
                    let sumVat = 0;
                    let finalInvoiceTotal = 0;

                    lines.forEach(data => {
                        let finalNetEntered = data.netLineEntered * globalDiscountFactor;
                        
                        let baseExclusive = 0;
                        let finalNetExclusive = 0;
                        let vatAmount = 0;
                        
                        if (pricesIncludeVat && data.vatRate > 0) {
                            let divFactor = 1 + (data.vatRate / 100);
                            baseExclusive = data.rowGrossEntered / divFactor;
                            finalNetExclusive = finalNetEntered / divFactor;
                            vatAmount = finalNetEntered - finalNetExclusive;
                        } else {
                            baseExclusive = data.rowGrossEntered;
                            finalNetExclusive = finalNetEntered;
                            vatAmount = finalNetExclusive * (data.vatRate / 100);
                        }
                        
                        let finalSubtotalWithVat = finalNetExclusive + vatAmount;
                        
                        sumBaseExclusive += baseExclusive;
                        sumVat += vatAmount;
                        finalInvoiceTotal += finalSubtotalWithVat;
                    });

                    this.subtotal = Math.round(sumBaseExclusive * 100) / 100;
                    this.vat = Math.round(sumVat * 100) / 100;
                    this.total = Math.round(totalNetEntered * 100) / 100; // For UI display purposes (subtotal before global discount)
                    this._finalTotal = Math.round(finalInvoiceTotal * 100) / 100;
                },
                
                handleCheckoutClick() {
                    this.tenderedAmount = this.finalTotal;
                    this.showCheckoutModal = true;
                    if (this.paymentMethods.length > 0) {
                        this.selectMethod(this.paymentMethods[0]);
                    }
                    setTimeout(() => {
                        const amountInput = document.querySelector('.checkout-modal-premium input[type="number"]:not([disabled])');
                        if (amountInput) {
                            amountInput.focus();
                            amountInput.select();
                        }
                    }, 100);
                },

                selectMethod(method) {
                    this.isMultiPayment = false;
                    this.selectedMethod = method;
                    if (method.type !== 'cash') {
                        this.tenderedAmount = this.finalTotal;
                    }
                },

                selectMultiPayment() {
                    this.isMultiPayment = true;
                    this.selectedMethod = null;
                    this.multiAmounts = {};
                    
                    const cashMethod = this.paymentMethods.find(m => m.type === 'cash');
                    if (cashMethod) {
                        this.multiAmounts[cashMethod.id] = this.finalTotal;
                    }
                },

                adjustMultiPayment(changedMethodId) {
                    const changedMethod = this.paymentMethods.find(m => m.id === changedMethodId);
                    if (!changedMethod || changedMethod.type === 'cash') return;

                    const cashMethod = this.paymentMethods.find(m => m.type === 'cash');
                    if (!cashMethod) return;

                    let sumNonCash = 0;
                    for (let methodId in this.multiAmounts) {
                        if (parseInt(methodId) !== cashMethod.id) {
                            sumNonCash += (parseFloat(this.multiAmounts[methodId]) || 0);
                        }
                    }

                    this.multiAmounts[cashMethod.id] = Math.max(0, this.finalTotal - sumNonCash);
                },

                confirmPayment() {
                    if (!this.isMultiPayment && this.tenderedAmount < this.finalTotal) {
                        alert('{{ __('pos::pos.amount') }} المدفوع أقل من المطلوب!');
                        return;
                    }
                    if (this.isMultiPayment && this.totalMultiPaid < this.finalTotal) {
                        alert('{{ __('pos::pos.amount') }} المدفوع أقل من المطلوب!');
                        return;
                    }
                    
                    this.loadingCheckout = true;
                    let payments = [];
                    if (this.isMultiPayment) {
                        for (let methodId in this.multiAmounts) {
                            let amt = parseFloat(this.multiAmounts[methodId]);
                            if (amt > 0) {
                                payments.push({
                                    method_id: parseInt(methodId),
                                    amount: amt
                                });
                            }
                        }
                    } else {
                        payments.push({
                            method_id: this.selectedMethod.id,
                            amount: this.tenderedAmount
                        });
                    }

                    const payload = {
                        device_id: this.deviceId,
                        customer_id: this.customerId || 1, // Fallback to default/walk-in customer if none selected
                        items: this.cartItems.map(item => ({
                            id: item.id,
                            name: item.name,
                            qty: item.qty,
                            price: item.price,
                            vat: item.vat || 15,
                            unit_id: item.unit_id || null,
                            have_sizes: item.have_sizes || false,
                            tax_id: item.tax_id || null
                        })),
                        payments: payments,
                        total: this.finalTotal,
                        type_discount: this.discountType === 'percentage' ? 1 : 2,
                        number_discount: parseFloat(this.localDiscount) || 0,
                        discount: this.discountAmount,
                        is_return: this.isReturn,
                        parent_id: this.isReturn ? this.parentInvoiceId : null,
                        session_token: this.sessionToken
                    };
                    
                    fetch('/api/pos/checkout', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': 'Bearer ' + this.bearerToken,
                            'X-Device-UUID': this.deviceUuid
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status) {
                            if (this.settings.auto_print_receipt) {
                                // Open invoice in new tab for printing
                                let printUrl = '/pos/print/' + data.invoice_id;
                                if (this.settings.print_copies_count > 1) {
                                    printUrl += '?copies=' + this.settings.print_copies_count;
                                }
                                window.open(printUrl, '_blank');
                            }
                            
                            // Reset cart
                            this.cartItems = [];
                            this.isReturn = false;
                            this.localDiscount = 0;
                            this.discountType = 'amount';
                            this.calculateTotal();
                            this.showCheckoutModal = false;
                            this.loadingCheckout = false;
                            
                            // Refresh products to update available quantities
                            this.fetchProducts();
                        } else {
                            toastr.error(data.message || "{{ __('pos::pos.error_saving') }}");
                            this.loadingCheckout = false;
                        }
                    })
                    .catch(err => {
                        toastr.error("{{ __('pos::pos.server_connection_error') }}");
                        console.error(err);
                        this.loadingCheckout = false;
                    });
                },

                submitSession() {
                    if (this.sessionAmount === null || this.sessionAmount < 0) return;
                    
                    const endpoint = this.isClosingSession ? '/api/pos/session/close' : '/api/pos/session/open';
                    const payload = {
                        device_uuid: this.deviceUuid,
                        session_id: this.sessionId,
                        opening_balance: this.isClosingSession ? undefined : this.sessionAmount,
                        closing_balance: this.isClosingSession ? this.sessionAmount : undefined,
                        notes: this.sessionNotes,
                        transfer_cash: this.transferCashToMainSafe
                    };
                    
                    fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer ' + this.bearerToken,
                            'X-Device-UUID': this.deviceUuid
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status) {
                            if (!this.isClosingSession) {
                                this.sessionToken = data.token;
                                this.sessionId = data.session.id;
                                toastr.success("{{ __('pos::pos.shift_opened_success') }}");
                                this.showSessionModal = false;
                                this.fetchProducts();
                            } else {
                                toastr.info("{{ __('pos::pos.shift_closed_success') }}");
                                if (data.session_id || this.sessionId) {
                                     window.open('/pos/session/print/' + (data.session_id || this.sessionId), '_blank');
                                }
                                this.sessionToken = '';
                                this.sessionId = null;
                                this.showSessionModal = false;
                            }
                        } else {
                            toastr.error(data.message || "{{ __('pos::pos.error_occurred') }}");
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        toastr.error("{{ __('pos::pos.server_error') }}");
                    });
                },

                submitTransaction() {
                    if (!this.transactionAmount || this.transactionAmount <= 0) {
                        toastr.warning("{{ __('pos::pos.enter_valid_amount') }}");
                        return;
                    }
                    
                    this.loadingTransaction = true;
                    
                    const payload = {
                        session_id: this.sessionId,
                        type: this.transactionType,
                        amount: this.transactionAmount,
                        notes: this.transactionNotes || '-'
                    };

                    fetch('/api/pos/session/transaction', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': 'Bearer ' + this.bearerToken,
                            'X-Device-UUID': this.deviceUuid
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status) {
                            toastr.success("{{ __('pos::pos.transaction_recorded_success') }}");
                            this.showTransactionModal = false;
                            this.transactionAmount = '';
                            this.transactionNotes = '';
                        } else {
                            toastr.error(data.message || "{{ __('pos::pos.transaction_failed') }}");
                        }
                        this.loadingTransaction = false;
                    })
                    .catch(err => {
                        console.error(err);
                        toastr.error("{{ __('pos::pos.server_error') }}");
                        this.loadingTransaction = false;
                    });
                },

                submitCustomer() {
                    if (!this.newCustomer.name) {
                        toastr.warning("{{ __('pos::pos.customer_name_required') ?? 'اسم العميل مطلوب' }}");
                        return;
                    }

                    this.loadingCustomer = true;
                    fetch('/api/pos/customer', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': 'Bearer ' + this.bearerToken,
                            'X-Device-UUID': this.deviceUuid
                        },
                        body: JSON.stringify(this.newCustomer)
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.loadingCustomer = false;
                        if (data.status) {
                            toastr.success(data.message || 'تم حفظ العميل بنجاح');
                            this.showCustomerModal = false;
                            
                            // Add to select2 and select it
                            const newOption = new Option(data.data.name + ' - ' + (data.data.phone || ''), data.data.id, true, true);
                            $(this.$refs.customerSelect).append(newOption).trigger('change');
                            this.customerId = data.data.id;
                            
                            // Reset form
                            this.newCustomer.name = '';
                            this.newCustomer.phone = '';
                        } else {
                            toastr.error(data.message || 'حدث خطأ أثناء حفظ العميل');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        toastr.error('خطأ في الاتصال بالخادم');
                        this.loadingCustomer = false;
                    });
                }
            }
        }
    </script>
</body>
</html>
