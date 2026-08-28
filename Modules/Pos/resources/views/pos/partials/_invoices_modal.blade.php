<!-- Invoices Modal -->
<div class="modal-overlay glass-overlay" x-show="showInvoicesModal" @click.self="showInvoicesModal = false" style="display: none;" x-transition>
    <div class="checkout-modal-premium slide-up" @click.stop style="max-width: 800px;">
        <div class="modal-header-premium">
            <div class="header-title">
                <div class="icon-circle">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <h2>{{ __('pos::pos.session_invoices') }}</h2>
            </div>
            <button class="close-btn" @click="showInvoicesModal = false">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body-premium flex-column">
            <!-- Search -->
            <div class="search-bar w-100 mb-3" style="box-shadow: none; border: 1px solid #dee2e6;">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="{{ __('pos::pos.search_invoice_placeholder') }}" x-model="invoiceSearch" @input.debounce.500ms="fetchShiftInvoices()">
            </div>

            <!-- Invoices Table -->
            <div class="table-responsive flex-grow-1" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-hover table-bordered mb-0 text-center align-middle">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>{{ __('pos::pos.invoice_number') }}</th>
                            <th>{{ __('pos::pos.type') }}</th>
                            <th>{{ __('pos::pos.customer_name') }}</th>
                            <th>{{ __('pos::pos.amount') }}</th>
                            <th>{{ __('pos::pos.time') }}</th>
                            <th>{{ __('pos::pos.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loadingInvoices">
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                                    <p>{{ __('pos::pos.loading_invoices') }}</p>
                                </td>
                            </tr>
                        </template>
                        <template x-if="!loadingInvoices && shiftInvoices.length === 0">
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-folder-open fa-2x mb-2"></i>
                                    <p>{{ __('pos::pos.no_invoices') }}</p>
                                </td>
                            </tr>
                        </template>
                        <template x-for="inv in shiftInvoices" :key="inv.id">
                            <tr>
                                <td class="fw-bold text-primary" x-text="inv.invoice_number"></td>
                                <td>
                                    <span class="badge" :class="inv.type === 'مبيعات' ? 'bg-success' : 'bg-danger'" x-text="inv.type"></span>
                                </td>
                                <td x-text="inv.customer_name"></td>
                                <td class="fw-bold" x-text="Number(inv.total).toFixed(2) + ' SAR'"></td>
                                <td x-text="inv.date" class="text-muted small"></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" @click="window.open('/pos/print/' + inv.id, '_blank')" title="{{ __('pos::pos.print') }}">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" x-show="inv.type === 'مبيعات' && settings.enable_pos_returns" @click="loadInvoiceForReturn(inv.id)" title="{{ __('pos::pos.return') }}">
                                        <i class="fas fa-undo"></i>
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
