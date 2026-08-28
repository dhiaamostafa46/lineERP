<!-- Products Grid -->
<div class="product-list-container h-100 d-flex flex-column">
    <!-- Header Actions -->
    <div class="d-flex flex-wrap align-items-center mb-3 gap-2">
        <!-- Session Info -->
        <div class="d-flex align-items-center bg-white border rounded px-3" style="height: 42px; font-size: 0.85rem;">
            <i class="fas fa-desktop text-primary me-2"></i>
            <span class="fw-bold">{{ $device->name }}</span>
            <span class="mx-2 text-muted">|</span>
            <i class="fas fa-store text-info me-1"></i>
            <span class="fw-bold">{{ $device->branch?->name ?? '' }}</span>
        </div>

        <div class="search-bar flex-grow-1 mb-0" style="min-width: 250px;">
            <i class="fas fa-search" style="font-size: 0.95rem;"></i>
            <input type="text" placeholder="{{ __('pos::pos.search_product') }}" x-model="search" @input.debounce.500ms="fetchProducts()" style="padding: 0.6rem 2.5rem 0.6rem 1rem; height: 42px;">
        </div>
        
        <!-- Action Buttons -->
        <div class="d-flex gap-2">
            <button class="btn btn-outline-warning btn-sm d-flex align-items-center justify-content-center" style="height: 42px; border-radius: 0.5rem; padding: 0 0.75rem;" @click="showHeldCartsModal = true" title="{{ __('pos::pos.held_carts') }}">
                <i class="fas fa-pause-circle me-1"></i> <span x-text="heldCarts.length" class="badge bg-warning text-dark"></span>
            </button>
            @if($device->enable_cash_movements)
            <button class="btn btn-outline-success btn-sm d-flex align-items-center justify-content-center" style="height: 42px; border-radius: 0.5rem; padding: 0 0.75rem;" @click="showTransactionModal = true" title="{{ __('pos::pos.cash_movement') }}">
                <i class="fas fa-money-bill-wave me-1"></i>{{ __('pos::pos.movement') }}</button>
            @endif
            <button class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center" style="height: 42px; border-radius: 0.5rem; padding: 0 0.75rem;" @click="showInvoicesModal = true; fetchShiftInvoices()" title="{{ __('pos::pos.shift_invoices') }}">
                <i class="fas fa-receipt me-1"></i>{{ __('pos::pos.invoices') }}</button>
            <button class="btn btn-danger btn-sm d-flex align-items-center justify-content-center" style="height: 42px; border-radius: 0.5rem; padding: 0 0.75rem;" @click="showSessionModal = true; isClosingSession = true;" title="{{ __('pos::pos.close_shift') }}">
                <i class="fas fa-lock me-1"></i>{{ __('pos::pos.close_shift') }}</button>

            <a href="{{ route('switchLang', app()->getLocale() == 'ar' ? 'en' : 'ar') }}" class="btn btn-outline-info btn-sm d-flex align-items-center justify-content-center" style="height: 42px; border-radius: 0.5rem; padding: 0 0.75rem;" title="{{ __('pos::pos.switch_lang') }}">
                <i class="fas fa-globe me-1"></i> {{ __('pos::pos.switch_lang_code') }}
            </a>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loadingProducts" class="loading-state flex-grow-1" style="display: none;">
        <i class="fas fa-spinner fa-spin"></i> {{ __('pos::pos.loading_products') }}
    </div>
    
    <!-- Empty State -->
    <div x-show="!loadingProducts && products.length === 0" class="empty-state flex-grow-1" style="display: none;">
        <i class="fas fa-box-open"></i>
        <p>{{ __('pos::pos.no_products') }}.</p>
    </div>

    <!-- Products Grid -->
    <div x-show="!loadingProducts && products.length > 0" class="products-grid overflow-auto" style="display: none;">
        <template x-for="(product, index) in products" :key="product.id ? product.id + '-' + index : index">
            <div class="product-card" :class="{'opacity-50': !settings.allow_negative_stock && product.quantity <= 0}" @click="addToCart(product)" style="position: relative;">
                <div class="product-image">
                    <template x-if="product.img && product.img !== 'placeholder.png' && !product.img.includes('no_img.jpg')">
                        <img :src="product.img" :alt="product.name" />
                    </template>
                    <template x-if="!product.img || product.img === 'placeholder.png' || product.img.includes('no_img.jpg')">
                        <img src="{{ asset('images/default_product.png') }}" :alt="product.name" />
                    </template>
                    <div class="position-absolute top-0 end-0 m-1 text-white fw-bold px-1 rounded shadow-sm" :class="product.quantity > 0 ? 'bg-success' : 'bg-danger'" style="font-size: 0.75rem;" x-show="settings.show_available_qty">
                        <span x-text="product.quantity > 0 ? product.quantity : '{{ __('pos::pos.out_of_stock') ?? 'نفدت الكمية' }}'"></span>
                    </div>
                </div>
                <div class="product-info">
                    <h3 x-text="product.name.replace(/\s*\(\s*(Avl:|OOS).*\)$/, '')"></h3>
                    <span class="barcode text-muted" style="font-size: 12px;">
                        <i class="fas fa-barcode"></i> <span x-text="product.barcode || '{{ __('pos::pos.no_barcode') }}'"></span>
                    </span>
                    <div class="product-price" x-text="Number(product.price).toFixed(2) + ' SAR'"></div>
                </div>
            </div>
        </template>
    </div>
</div>
