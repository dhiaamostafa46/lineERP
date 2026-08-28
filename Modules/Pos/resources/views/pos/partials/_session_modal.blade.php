<!-- Session Modal -->
<div class="modal-overlay glass-overlay" x-show="showSessionModal" @click.self="if(sessionId) showSessionModal = false" style="display: none;" x-transition>
    <div class="session-modal slide-up" @click.stop>
        <div class="modal-header">
            <h2 x-text="isClosingSession ? '{{ __('pos::pos.close_shift_btn') }} (Z-Report)' : '{{ __('pos::pos.open_shift_btn') }}'"></h2>
        </div>

        <div class="modal-body">
            
            <!-- Opening Session -->
            <div x-show="!isClosingSession">
                <p>{{ __('pos::pos.enter_opening_cash') }}</p>
                <div class="amount-entry">
                    <label>{{ __('pos::pos.opening_cash') }}</label>
                    <input type="number" x-model.number="sessionAmount" class="tendered-input" step="0.01" placeholder="0.00" />
                </div>
            </div>

            <!-- Closing Session -->
            <div x-show="isClosingSession">
                <p>{{ __('pos::pos.enter_closing_cash') }}</p>
                <div class="amount-entry">
                    <label>{{ __('pos::pos.actual_counted_cash') }}</label>
                    <input type="number" x-model.number="sessionAmount" class="tendered-input" step="0.01" placeholder="0.00" />
                </div>
                
                <div class="amount-entry" style="margin-top: 15px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" x-model="transferCashToMainSafe" style="width: 18px; height: 18px;" />
                        توريد النقدية المتبقية للصندوق الرئيسي
                    </label>
                </div>
                
                <div class="amount-entry" style="margin-top: 15px;">
                    <label>{{ __('pos::pos.closing_notes') }}</label>
                    <textarea x-model="sessionNotes" class="tendered-input" style="font-size: 16px; min-height: 80px;" placeholder="{{ __('pos::pos.difference_notes_placeholder') }}"></textarea>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" @click="showSessionModal = false" x-show="isClosingSession">{{ __('pos::pos.cancel') }}</button>

            <button class="btn btn-primary" :disabled="sessionAmount === null || sessionAmount < 0" @click="submitSession">
                <span x-text="isClosingSession ? '{{ __('pos::pos.close_shift_and_print') }}' : '{{ __('pos::pos.open_shift_btn') }}'"></span>
            </button>
        </div>
    </div>
</div>
