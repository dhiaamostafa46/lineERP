<!-- Transaction Modal -->
<div class="modal-backdrop" x-show="showTransactionModal" @click.self="showTransactionModal = false" style="display: none;" x-transition>
    <div class="modal-content" @click.stop>
        <div class="modal-header">
            <h2>{{ __('pos::pos.register_cash_movement') }}</h2>
            <button class="btn-close" @click="showTransactionModal = false">&times;</button>
        </div>

        <div class="modal-body">
            <form @submit.prevent="submitTransaction">
                <div class="form-group">
                    <label>{{ __('pos::pos.transaction_type') }}</label>
                    <select x-model="transactionType" class="form-control" required>
                        <option value="withdrawal">{{ __('pos::pos.cash_withdrawal') }}</option>
                        <option value="deposit">{{ __('pos::pos.cash_deposit') }}</option>
                    </select>
                </div>

                <div class="form-group mt-3">
                    <label>{{ __('pos::pos.amount') }}</label>
                    <input type="number" x-model.number="transactionAmount" class="form-control" step="0.01" min="0.01" required />
                </div>

                <div class="form-group mt-3">
                    <label>{{ __('pos::pos.notes') }}</label>
                    <textarea x-model="transactionNotes" class="form-control" rows="3" required></textarea>
                </div>

                <div class="modal-footer mt-4">
                    <button type="button" class="btn btn-secondary" @click="showTransactionModal = false">{{ __('pos::pos.cancel') }}</button>
                    <button type="submit" class="btn btn-primary" :disabled="loadingTransaction">
                        <span x-text="loadingTransaction ? '{{ __('pos::pos.processing') }}' : '{{ __('pos::pos.confirm') }}'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
