<!-- Held Carts Modal -->
<div class="modal-overlay glass-overlay" x-show="showHeldCartsModal" @click.self="showHeldCartsModal = false" style="display: none;" x-transition>
    <div class="checkout-modal-premium slide-up" @click.stop style="max-width: 600px;">
        <div class="modal-header-premium">
            <div class="header-title">
                <div class="icon-circle text-warning bg-warning bg-opacity-10">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <h2>{{ __('pos::pos.held_carts') }}</h2>
            </div>
            <button class="close-btn" @click="showHeldCartsModal = false">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body-premium flex-column">
            <div class="table-responsive flex-grow-1" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-hover table-bordered mb-0 text-center align-middle">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>{{ __('pos::pos.time') }}</th>
                            <th>عدد ال{{ __('pos::pos.items_count') }}</th>
                            <th>{{ __('pos::pos.total') }}</th>
                            <th>{{ __('pos::pos.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="heldCarts.length === 0">
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="fas fa-folder-open fa-2x mb-2"></i>
                                    <p>{{ __('pos::pos.no_held_carts') }}</p>
                                </td>
                            </tr>
                        </template>
                        <template x-for="(heldCart, index) in heldCarts" :key="index">
                            <tr>
                                <td x-text="heldCart.time"></td>
                                <td x-text="heldCart.items.length"></td>
                                <td class="fw-bold" x-text="Number(heldCart.total).toFixed(2) + ' SAR'"></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-success" @click="resumeCart(index)" title="{{ __('pos::pos.resume_order') }}">
                                        <i class="fas fa-play"></i>{{ __('pos::pos.resume') }}</button>
                                    <button class="btn btn-sm btn-outline-danger" @click="removeHeldCart(index)" title="{{ __('pos::pos.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
