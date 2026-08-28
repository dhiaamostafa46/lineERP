<template>
  <div class="modal-overlay glass-overlay" @click.self="$emit('close')">
    <div class="checkout-modal-premium slide-up">
      <div class="modal-header-premium">
        <div class="header-title">
          <div class="icon-circle"><i class="fas fa-shopping-bag"></i></div>
          <h2>إتمام الدفع</h2>
        </div>
        <button class="close-btn" @click="$emit('close')"><i class="fas fa-times"></i></button>
      </div>

      <div class="modal-body-premium">
        <!-- Right Column: Payment Methods & Input -->
        <div class="payment-section">
          <h3 class="section-title">اختر طريقة الدفع</h3>
          <div v-if="paymentMethods.length === 0" class="alert alert-warning rounded-4 shadow-sm">
            <i class="fas fa-exclamation-triangle me-2"></i> لا توجد طرق دفع.
          </div>
          <div v-else class="methods-cards">
            <div 
              v-for="method in paymentMethods" 
              :key="method.id"
              class="payment-card"
              :class="{ 'active': selectedMethod?.id === method.id }"
              @click="selectMethod(method)"
            >
              <div class="card-icon">
                <i class="fas fa-money-bill-wave text-success" v-if="method.type === 'cash'"></i>
                <i class="fas fa-credit-card text-primary" v-else-if="method.type === 'pos' || method.type === 'credit'"></i>
                <i class="fas fa-university text-info" v-else-if="method.type === 'bank_transfer'"></i>
                <i class="fas fa-wallet text-secondary" v-else></i>
              </div>
              <span class="card-name">{{ method.name }}</span>
              <div class="active-indicator" v-if="selectedMethod?.id === method.id">
                <i class="fas fa-check-circle"></i>
              </div>
            </div>
          </div>

          <transition name="fade">
            <div class="amount-entry-premium" v-if="selectedMethod">
              <div class="input-wrapper">
                <label>المبلغ المستلم من العميل</label>
                <div class="huge-input-container" :class="{ 'has-error': tendered < finalTotal }">
                  <span class="currency">SAR</span>
                  <input 
                    type="number" 
                    name="tendered_amount" 
                    id="tendered_amount" 
                    v-model.number="tendered" 
                    class="huge-input" 
                    step="0.01" 
                    @focus="$event.target.select()"
                  />
                </div>
                <div class="error-text" v-if="tendered < finalTotal">
                  <i class="fas fa-exclamation-circle"></i> المبلغ المدفوع أقل من المطلوب!
                </div>
              </div>
              
              <div class="quick-cash-chips" v-if="selectedMethod.type === 'cash'">
                <button class="chip" @click="addQuickCash(5)">+5</button>
                <button class="chip" @click="addQuickCash(10)">+10</button>
                <button class="chip" @click="addQuickCash(50)">+50</button>
                <button class="chip" @click="addQuickCash(100)">+100</button>
                <button class="chip exact" @click="tendered = finalTotal">المبلغ بالضبط</button>
              </div>
            </div>
          </transition>
        </div>

        <!-- Left Column: Order Summary (Receipt Style) -->
        <div class="receipt-section">
          <div class="receipt-card">
            <div class="receipt-header">
              <i class="fas fa-file-invoice-dollar"></i>
              <h3>ملخص الفاتورة</h3>
            </div>
            
            <div class="receipt-details">
              <div class="receipt-line">
                <span class="text-muted">الإجمالي (بدون ضريبة)</span>
                <span class="fw-bold">{{ subtotal.toFixed(2) }}</span>
              </div>
              <div class="receipt-line">
                <span class="text-muted">الضريبة المضافة</span>
                <span class="fw-bold">{{ vat.toFixed(2) }}</span>
              </div>
              <div class="receipt-line text-danger" v-if="discount > 0">
                <span>الخصم المستقطع</span>
                <span class="fw-bold">-{{ discount.toFixed(2) }}</span>
              </div>
            </div>
            
            <div class="receipt-divider">
              <div class="notch left"></div>
              <div class="dash-line"></div>
              <div class="notch right"></div>
            </div>
            
            <div class="receipt-grand-total">
              <span class="label">المطلوب للدفع</span>
              <div class="amount-wrapper text-primary">
                <span class="amount">{{ finalTotal.toFixed(2) }}</span>
                <span class="currency">SAR</span>
              </div>
            </div>

            <div class="receipt-change" :class="{ 'positive': change > 0 }">
              <div class="change-icon">
                <i class="fas fa-hand-holding-usd"></i>
              </div>
              <div class="change-info">
                <span class="label">الباقي للعميل</span>
                <span class="amount">{{ change.toFixed(2) }} SAR</span>
              </div>
            </div>
          </div>
          
          <button 
            class="premium-confirm-btn mt-4" 
            :class="{ 'ready': selectedMethod && tendered >= finalTotal }"
            :disabled="!selectedMethod || tendered < finalTotal"
            @click="confirmPayment"
          >
            <div class="btn-content">
              <span>تأكيد الدفع وإصدار الفاتورة</span>
              <i class="fas fa-arrow-left"></i>
            </div>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  subtotal: {
    type: Number,
    required: true
  },
  vat: {
    type: Number,
    required: true
  },
  total: {
    type: Number,
    required: true
  },
  discount: {
    type: Number,
    default: 0
  },
  paymentMethods: {
    type: Array,
    required: true
  }
});

const emit = defineEmits(['close', 'confirm']);

const finalTotal = computed(() => {
  return Math.max(0, props.total - props.discount);
});

const selectedMethod = ref(null);
const tendered = ref(finalTotal.value);

// Select default method on mount
if (props.paymentMethods && props.paymentMethods.length > 0) {
  const def = props.paymentMethods.find(m => m.is_default) || props.paymentMethods[0];
  selectedMethod.value = def;
}

const change = computed(() => {
  return Math.max(0, tendered.value - finalTotal.value);
});

const selectMethod = (method) => {
  selectedMethod.value = method;
  if (method.type !== 'cash') {
    tendered.value = finalTotal.value; // Non-cash methods usually exact amount
  }
};

const addQuickCash = (amount) => {
  tendered.value += amount;
};

const confirmPayment = () => {
  if (tendered.value < finalTotal.value) {
    alert('المبلغ المدفوع أقل من المطلوب!');
    return;
  }
  
  emit('confirm', {
    method: selectedMethod.value,
    tendered: tendered.value,
    change: change.value
  });
};
</script>

<style scoped>
/* Glassmorphism Overlay */
.glass-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(8px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

/* Animations */
.slide-up {
  animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes slideUp {
  0% { opacity: 0; transform: translateY(40px) scale(0.98); }
  100% { opacity: 1; transform: translateY(0) scale(1); }
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Modal Container */
.checkout-modal-premium {
  background: #ffffff;
  border-radius: 24px;
  width: 100%;
  max-width: 950px;
  box-shadow: 0 25px 50px rgba(0,0,0,0.15), 0 0 0 1px rgba(0,0,0,0.05);
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.dark-mode .checkout-modal-premium {
  background: #1e1e2d;
  box-shadow: 0 25px 50px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.05);
}

/* Header */
.modal-header-premium {
  padding: 20px 30px;
  border-bottom: 1px solid rgba(0,0,0,0.05);
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.dark-mode .modal-header-premium { border-bottom-color: rgba(255,255,255,0.05); }

.header-title {
  display: flex;
  align-items: center;
  gap: 15px;
}
.header-title h2 {
  margin: 0;
  font-size: 22px;
  font-weight: 800;
  color: #181c32;
}
.dark-mode .header-title h2 { color: #ffffff; }

.icon-circle {
  width: 45px; height: 45px;
  border-radius: 12px;
  background: rgba(0, 158, 247, 0.1);
  color: #009ef7;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}

.close-btn {
  background: #f5f8fa;
  border: none;
  width: 40px; height: 40px;
  border-radius: 50%;
  color: #a1a5b7;
  font-size: 18px;
  cursor: pointer;
  transition: all 0.2s;
}
.close-btn:hover { background: #f1416c; color: white; transform: rotate(90deg); }
.dark-mode .close-btn { background: #2b2b40; color: #565674; }
.dark-mode .close-btn:hover { background: #f1416c; color: white; }

/* Body Layout */
.modal-body-premium {
  display: grid;
  grid-template-columns: 1fr 380px;
  background: #fcfcfc;
}
.dark-mode .modal-body-premium { background: #151521; }

@media (max-width: 768px) {
  .modal-body-premium {
    grid-template-columns: 1fr;
    max-height: 80vh;
    overflow-y: auto;
  }
}

/* Left/Right Sections */
.payment-section {
  padding: 30px;
  border-left: 1px solid rgba(0,0,0,0.05);
}
.dark-mode .payment-section { border-left-color: rgba(255,255,255,0.05); }

.receipt-section {
  padding: 30px;
  background: #f5f8fa;
  display: flex;
  flex-direction: column;
}
.dark-mode .receipt-section { background: #1a1a27; }

/* Section Titles */
.section-title {
  font-size: 18px;
  font-weight: 700;
  color: #3f4254;
  margin: 0 0 20px 0;
}
.dark-mode .section-title { color: #e4e6ef; }

/* Payment Cards Grid */
.methods-cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
  gap: 15px;
  margin-bottom: 30px;
}

.payment-card {
  background: #ffffff;
  border: 2px solid #e4e6ef;
  border-radius: 16px;
  padding: 20px 15px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  position: relative;
  overflow: hidden;
}
.dark-mode .payment-card { background: #1e1e2d; border-color: #323248; }

.payment-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.05);
  border-color: #b5b5c3;
}
.dark-mode .payment-card:hover { border-color: #565674; box-shadow: 0 10px 20px rgba(0,0,0,0.2); }

.payment-card.active {
  border-color: #009ef7;
  background: rgba(0, 158, 247, 0.03);
  box-shadow: 0 10px 20px rgba(0, 158, 247, 0.1);
}
.dark-mode .payment-card.active { background: rgba(0, 158, 247, 0.1); }

.card-icon {
  font-size: 32px;
  margin-bottom: 12px;
  transition: transform 0.3s;
}
.payment-card.active .card-icon { transform: scale(1.1); }

.card-name {
  display: block;
  font-weight: 700;
  font-size: 15px;
  color: #3f4254;
}
.dark-mode .card-name { color: #e4e6ef; }
.payment-card.active .card-name { color: #009ef7; }

.active-indicator {
  position: absolute;
  top: 10px; right: 10px;
  color: #009ef7;
  font-size: 18px;
  opacity: 0;
  transform: scale(0);
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.payment-card.active .active-indicator {
  opacity: 1; transform: scale(1);
}

/* Huge Input */
.amount-entry-premium {
  background: #ffffff;
  padding: 25px;
  border-radius: 20px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.03);
  border: 1px solid rgba(0,0,0,0.03);
}
.dark-mode .amount-entry-premium { background: #1e1e2d; border-color: rgba(255,255,255,0.02); }

.input-wrapper label {
  font-size: 14px;
  font-weight: 700;
  color: #a1a5b7;
  margin-bottom: 10px;
  display: block;
}

.huge-input-container {
  position: relative;
  background: #f5f8fa;
  border-radius: 16px;
  padding: 10px 20px;
  display: flex;
  align-items: center;
  border: 2px solid transparent;
  transition: all 0.3s;
}
.dark-mode .huge-input-container { background: #151521; }

.huge-input-container:focus-within {
  border-color: #009ef7;
  background: #ffffff;
  box-shadow: 0 0 0 4px rgba(0, 158, 247, 0.1);
}
.dark-mode .huge-input-container:focus-within { background: #1e1e2d; }

.huge-input-container.has-error {
  border-color: #f1416c;
  background: rgba(241, 65, 108, 0.05);
}

.huge-input {
  background: transparent;
  border: none;
  width: 100%;
  font-size: 40px;
  font-weight: 800;
  color: #181c32;
  text-align: left;
  outline: none;
  padding: 10px 0;
}
.dark-mode .huge-input { color: #ffffff; }

.currency {
  font-size: 20px;
  font-weight: 700;
  color: #a1a5b7;
  margin-right: 15px;
}

.error-text {
  color: #f1416c;
  font-size: 13px;
  font-weight: 600;
  margin-top: 8px;
}

/* Quick Cash Chips */
.quick-cash-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 20px;
}

.chip {
  background: #e1f0ff;
  color: #009ef7;
  border: none;
  padding: 10px 18px;
  border-radius: 30px;
  font-weight: 800;
  font-size: 15px;
  cursor: pointer;
  transition: all 0.2s;
}
.dark-mode .chip { background: rgba(0, 158, 247, 0.1); }
.chip:hover { background: #009ef7; color: white; transform: translateY(-2px); }

.chip.exact {
  background: #e8fff3;
  color: #50cd89;
  flex-grow: 1;
}
.dark-mode .chip.exact { background: rgba(80, 205, 137, 0.1); }
.chip.exact:hover { background: #50cd89; color: white; }

/* Receipt Section */
.receipt-card {
  background: #ffffff;
  border-radius: 20px;
  padding: 25px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
  position: relative;
  overflow: hidden;
  margin-bottom: auto;
}
.dark-mode .receipt-card { background: #1e1e2d; }

.receipt-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 25px;
  color: #a1a5b7;
}
.receipt-header h3 { margin: 0; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
.receipt-header i { font-size: 20px; }

.receipt-details {
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-bottom: 25px;
}
.receipt-line {
  display: flex;
  justify-content: space-between;
  font-size: 15px;
  color: #3f4254;
}
.dark-mode .receipt-line { color: #e4e6ef; }

/* Receipt Divider with Notches */
.receipt-divider {
  position: relative;
  height: 2px;
  margin: 20px 0;
  display: flex;
  align-items: center;
}
.dash-line {
  flex-grow: 1;
  border-top: 2px dashed #e4e6ef;
}
.dark-mode .dash-line { border-top-color: #323248; }

.notch {
  width: 20px; height: 20px;
  background: #f5f8fa;
  border-radius: 50%;
  position: absolute;
}
.dark-mode .notch { background: #1a1a27; }
.notch.left { left: -35px; }
.notch.right { right: -35px; }

/* Grand Total */
.receipt-grand-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}
.receipt-grand-total .label {
  font-size: 18px;
  font-weight: 800;
  color: #181c32;
}
.dark-mode .receipt-grand-total .label { color: #ffffff; }

.amount-wrapper {
  display: flex;
  align-items: baseline;
  gap: 5px;
}
.amount-wrapper .amount { font-size: 32px; font-weight: 900; }
.amount-wrapper .currency { font-size: 14px; font-weight: 700; opacity: 0.7; }

/* Change Box */
.receipt-change {
  background: #f5f8fa;
  border-radius: 12px;
  padding: 15px;
  display: flex;
  align-items: center;
  gap: 15px;
  transition: all 0.3s;
}
.dark-mode .receipt-change { background: #151521; }

.receipt-change.positive {
  background: #e8fff3;
}
.dark-mode .receipt-change.positive { background: rgba(80, 205, 137, 0.1); }

.change-icon {
  width: 40px; height: 40px;
  border-radius: 10px;
  background: #e4e6ef;
  color: #7e8299;
  display: flex; align-items: center; justify-content: center;
  font-size: 18px;
}
.dark-mode .change-icon { background: #323248; color: #a1a5b7; }

.receipt-change.positive .change-icon {
  background: #50cd89; color: white;
}

.change-info {
  display: flex; flex-direction: column;
}
.change-info .label { font-size: 13px; font-weight: 700; color: #a1a5b7; }
.change-info .amount { font-size: 18px; font-weight: 800; color: #3f4254; }
.dark-mode .change-info .amount { color: #e4e6ef; }
.receipt-change.positive .change-info .amount { color: #50cd89; }

/* Confirm Button */
.premium-confirm-btn {
  background: #e4e6ef;
  color: #a1a5b7;
  border: none;
  padding: 22px;
  border-radius: 16px;
  font-size: 18px;
  font-weight: 800;
  cursor: not-allowed;
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  box-shadow: 0 10px 20px rgba(0,0,0,0.05);
}
.dark-mode .premium-confirm-btn { background: #323248; color: #565674; }

.premium-confirm-btn.ready {
  background: linear-gradient(135deg, #009ef7 0%, #007acd 100%);
  color: #ffffff;
  cursor: pointer;
  box-shadow: 0 15px 30px rgba(0, 158, 247, 0.3);
}

.premium-confirm-btn.ready:hover {
  transform: translateY(-3px);
  box-shadow: 0 20px 40px rgba(0, 158, 247, 0.4);
}
.premium-confirm-btn.ready:active {
  transform: translateY(1px);
}

.btn-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.btn-content i {
  font-size: 20px;
  transition: transform 0.3s;
}
.premium-confirm-btn.ready:hover .btn-content i {
  transform: translateX(-5px);
}
</style>
