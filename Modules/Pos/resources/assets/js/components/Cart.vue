<template>
  <div class="cart-container">
    <div class="cart-header">
      <h2>الطلب الحالي</h2>
      <span class="items-count">{{ cartItems.length }} عناصر</span>
    </div>

    <div class="cart-items">
      <div v-if="cartItems.length === 0" class="empty-cart">
        <i class="fas fa-shopping-cart"></i>
        <p>السلة فارغة</p>
      </div>
      
      <div v-else class="cart-table-wrapper">
        <table class="cart-table">
          <thead>
            <tr>
              <th>المنتج</th>
              <th>الوحدة</th>
              <th>السعر</th>
              <th style="width: 100px;">الكمية</th>
              <th>المجموع</th>
              <th style="width: 30px;"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in cartItems" :key="item.id">
              <td class="product-name" :title="item.name">{{ item.name }}</td>
              <td class="product-unit">{{ item.unit || '-' }}</td>
              <td>{{ Number(item.price).toFixed(2) }}</td>
              <td>
                <div class="qty-control">
                  <button class="qty-btn minus" @click="$emit('update-qty', item.id, item.qty - 1)">-</button>
                  <input type="number" :name="'qty_' + item.id" :id="'qty_' + item.id" class="qty-input" :value="item.qty" @change="e => $emit('update-qty', item.id, parseInt(e.target.value))" min="1" />
                  <button class="qty-btn plus" @click="$emit('update-qty', item.id, item.qty + 1)">+</button>
                </div>
              </td>
              <td class="item-total">{{ (item.price * item.qty * (1 + (item.vat || 0) / 100)).toFixed(2) }}</td>
              <td>
                <button class="remove-btn btn-remove" @click="$emit('remove-item', item.id)">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="cart-footer">
      <div class="discount-container mb-3">
        <label>الخصم:</label>
        <div class="input-group">
          <input type="number" name="cart_discount" id="cart_discount" class="form-control" v-model.number="localDiscount" @change="updateDiscount" min="0" step="0.01" />
          <select class="form-select" style="max-width: 100px;" v-model="discountType" @change="updateDiscount">
            <option value="amount">SAR</option>
            <option value="percentage">%</option>
          </select>
        </div>
      </div>

      <div class="totals">
        <div class="total-row">
          <span>الإجمالي (بدون ضريبة)</span>
          <span>{{ subtotal.toFixed(2) }} SAR</span>
        </div>
        <div class="total-row">
          <span>الضريبة</span>
          <span>{{ vat.toFixed(2) }} SAR</span>
        </div>
        <div class="total-row text-primary" style="font-weight: 600; font-size: 16px;">
          <span>الإجمالي</span>
          <span>{{ total.toFixed(2) }} SAR</span>
        </div>
        <div class="total-row text-danger" v-if="discountAmount > 0">
          <span>الخصم</span>
          <span>-{{ discountAmount.toFixed(2) }} SAR</span>
        </div>
        <div class="total-row grand-total text-primary">
          <span>الصافي المطلوب</span>
          <span>{{ Math.max(0, total - discountAmount).toFixed(2) }} SAR</span>
        </div>
      </div>
      
      <button 
        class="checkout-btn" 
        :class="{ 'btn-return': isReturn }"
        :disabled="cartItems.length === 0"
        @click="handleCheckoutClick"
      >
        <span>{{ isReturn ? 'تأكيد الإرجاع' : 'متابعة الدفع' }}</span>
        <i class="fas fa-arrow-left"></i>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';

const props = defineProps({
  cartItems: {
    type: Array,
    required: true
  },
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
  isReturn: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update-qty', 'remove-item', 'update-discount', 'checkout']);

const localDiscount = ref(0);
const discountType = ref('amount');

const discountAmount = computed(() => {
  if (!localDiscount.value || localDiscount.value < 0) return 0;
  if (discountType.value === 'percentage') {
    return props.total * (localDiscount.value / 100);
  }
  return localDiscount.value;
});

const updateDiscount = () => {
  if (localDiscount.value < 0) localDiscount.value = 0;
  if (discountType.value === 'percentage' && localDiscount.value > 100) localDiscount.value = 100;
  emit('update-discount', discountAmount.value);
};

// Sync prop back to local if parent resets it
watch(() => props.discount, (newVal) => {
  if (newVal === 0) {
    localDiscount.value = 0;
  }
});

const handleCheckoutClick = () => {
  emit('checkout');
};
</script>

<style scoped>
.cart-container {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.cart-header {
  padding: 15px 20px;
  border-bottom: 1px solid rgba(0,0,0,0.05);
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f8f9fa;
}

.dark-mode .cart-header { 
  background: #1e1e1e;
  border-bottom: 1px solid rgba(255,255,255,0.05); 
}

.cart-header h2 {
  margin: 0;
  font-size: 18px;
  font-weight: 700;
  color: #3f4254;
}
.dark-mode .cart-header h2 { color: #eee; }

.items-count {
  background: #009ef7;
  color: #fff;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: bold;
}

.cart-items {
  flex: 1;
  overflow-y: auto;
  padding: 0;
  background: #fff;
}
.dark-mode .cart-items { background: #2c2c2c; }

.empty-cart {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  color: #a1a5b7;
}

.empty-cart i {
  font-size: 40px;
  margin-bottom: 10px;
  opacity: 0.5;
}

.cart-table-wrapper {
  width: 100%;
}

.cart-table {
  width: 100%;
  border-collapse: collapse;
}

.cart-table th {
  background: #f5f8fa;
  color: #5e6278;
  font-size: 12px;
  font-weight: 600;
  text-align: right;
  padding: 10px 8px;
  border-bottom: 1px solid #eff2f5;
}

.dark-mode .cart-table th {
  background: #1a1a1a;
  color: #a1a5b7;
  border-bottom-color: #333;
}

.cart-table td {
  padding: 12px 8px;
  vertical-align: middle;
  border-bottom: 1px dashed #eff2f5;
  font-size: 13px;
  color: #3f4254;
}

.dark-mode .cart-table td {
  border-bottom-color: #333;
  color: #cdcdde;
}

.product-name {
  font-weight: 600;
  max-width: 120px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.product-unit {
  color: #7e8299;
  font-size: 11px;
}
.dark-mode .product-unit { color: #888; }

.product-price {
  font-weight: 600;
}

.item-total {
  font-weight: 600;
}

.qty-control {
  display: flex;
  align-items: center;
  background: #f5f8fa;
  border-radius: 6px;
  overflow: hidden;
  border: 1px solid #e4e6ef;
}

.dark-mode .qty-control { 
  background: #1a1a1a; 
  border-color: #333;
}

.qty-btn {
  background: transparent;
  border: none;
  width: 25px;
  height: 25px;
  font-weight: bold;
  cursor: pointer;
  color: #7e8299;
  display: flex;
  align-items: center;
  justify-content: center;
}
.qty-btn:hover { background: #e4e6ef; color: #3f4254; }
.dark-mode .qty-btn { color: #a1a5b7; }
.dark-mode .qty-btn:hover { background: #333; color: #fff; }

.qty-input {
  width: 35px;
  text-align: center;
  border: none;
  background: transparent;
  font-weight: bold;
  color: inherit;
  font-size: 12px;
  padding: 0;
  -moz-appearance: textfield;
}
.qty-input::-webkit-outer-spin-button,
.qty-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.remove-btn {
  background: transparent;
  border: none;
  color: #f1416c;
  cursor: pointer;
  padding: 5px;
  opacity: 0.6;
  transition: opacity 0.2s;
  border-radius: 4px;
}
.remove-btn:hover { opacity: 1; background: #fff5f8; }
.dark-mode .remove-btn:hover { background: rgba(241, 65, 108, 0.1); }

.cart-footer {
  padding: 20px;
  background: #fff;
  border-top: 1px solid rgba(0,0,0,0.05);
  box-shadow: 0 -4px 20px rgba(0,0,0,0.03);
  z-index: 5;
}

.dark-mode .cart-footer { background: #1e1e1e; border-top-color: #333; }

.discount-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #f5f8fa;
  padding: 10px;
  border-radius: 8px;
}
.dark-mode .discount-container { background: #2a2a2a; }

.discount-container label {
  margin: 0;
  font-weight: 600;
  font-size: 13px;
  color: #5e6278;
}
.dark-mode .discount-container label { color: #a1a5b7; }

.discount-container .form-control {
  width: 100px;
  text-align: left;
  border-color: #e4e6ef;
  font-weight: bold;
}
.dark-mode .discount-container .form-control { background: #1a1a1a; border-color: #444; color: #fff; }

.total-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
  color: #7e8299;
  font-size: 14px;
}
.dark-mode .total-row { color: #a1a5b7; }

.grand-total {
  font-size: 18px;
  font-weight: 800;
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px dashed #e4e6ef;
}
.dark-mode .grand-total { border-top-color: #444; }

.checkout-btn {
  width: 100%;
  padding: 16px;
  background: #009ef7;
  border: none;
  border-radius: 12px;
  color: white;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 15px;
  transition: all 0.2s;
}
.checkout-btn.btn-return {
  background: #f1416c;
}

.checkout-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0, 158, 247, 0.3);
}
.checkout-btn.btn-return:hover:not(:disabled) {
  box-shadow: 0 5px 15px rgba(241, 65, 108, 0.3);
}

.checkout-btn:disabled {
  background: #e4e6ef;
  color: #a1a5b7;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}
.dark-mode .checkout-btn:disabled { background: #333; color: #666; }
</style>
