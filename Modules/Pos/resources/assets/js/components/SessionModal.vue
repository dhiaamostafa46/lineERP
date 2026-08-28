<template>
  <div class="modal-overlay">
    <div class="session-modal">
      <div class="modal-header">
        <h2>{{ isClosing ? 'Close Shift (Z-Report)' : 'Open Shift' }}</h2>
      </div>

      <div class="modal-body">
        
        <!-- Opening Session -->
        <div v-if="!isClosing">
          <p>Please enter the initial cash amount (Opening Balance) in the drawer.</p>
          <div class="amount-entry">
            <label>Opening Cash (SAR)</label>
            <input 
              type="number" 
              v-model.number="amount" 
              class="tendered-input" 
              step="0.01" 
              placeholder="0.00"
            />
          </div>
        </div>

        <!-- Closing Session -->
        <div v-else>
          <p>Please count the actual cash in the drawer and enter it below.</p>
          <div class="amount-entry">
            <label>Actual Cash Counted (SAR)</label>
            <input 
              type="number" 
              v-model.number="amount" 
              class="tendered-input" 
              step="0.01" 
              placeholder="0.00"
            />
          </div>
          
          <div class="amount-entry" style="margin-top: 15px;">
            <label>Closing Notes (Optional)</label>
            <textarea 
              v-model="notes" 
              class="tendered-input" 
              style="font-size: 16px; min-height: 80px;"
              placeholder="Any variance explanations..."
            ></textarea>
          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" @click="cancel" v-if="isClosing">Cancel</button>
        <button class="btn btn-secondary" @click="cancel" v-else>Go Back to Dashboard</button>
        
        <button 
          class="btn btn-primary" 
          :disabled="amount === null || amount < 0"
          @click="submit"
        >
          {{ isClosing ? 'Close Shift & Print Report' : 'Open Shift' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  isClosing: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['submit', 'cancel']);

const amount = ref(0.00);
const notes = ref('');

const submit = () => {
  emit('submit', { amount: amount.value, notes: notes.value });
};

const cancel = () => {
  emit('cancel');
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.8);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.session-modal {
  background: #fff;
  width: 90%;
  max-width: 450px;
  border-radius: 20px;
  box-shadow: 0 20px 50px rgba(0,0,0,0.3);
  overflow: hidden;
  animation: scaleIn 0.3s ease-out;
}

.dark-mode .session-modal { background: #222; color: #fff; }

@keyframes scaleIn {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}

.modal-header {
  padding: 20px;
  background: linear-gradient(45deg, #007bff, #00c6ff);
  color: white;
  text-align: center;
}
.modal-header h2 { margin: 0; font-size: 22px; font-weight: 700; }

.modal-body { padding: 30px 20px; }
.modal-body p { margin-top: 0; color: #666; text-align: center; font-size: 15px;}
.dark-mode .modal-body p { color: #bbb; }

.amount-entry label { font-weight: bold; margin-bottom: 8px; display: block; color: #444;}
.dark-mode .amount-entry label { color: #ccc; }

.tendered-input {
  width: 100%;
  font-size: 28px;
  font-weight: bold;
  text-align: center;
  padding: 15px;
  border-radius: 12px;
  border: 2px solid #ddd;
  background: #f8f9fa;
  outline: none;
}
.tendered-input:focus { border-color: #007bff; }
.dark-mode .tendered-input { background: #333; color: #fff; border-color: #555; }
.dark-mode .tendered-input:focus { border-color: #00c6ff; }

.modal-footer {
  padding: 20px;
  border-top: 1px solid #eee;
  display: flex;
  gap: 15px;
}
.dark-mode .modal-footer { border-top-color: #444; }

.btn {
  flex: 1;
  padding: 15px;
  border-radius: 12px;
  border: none;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: transform 0.1s;
}
.btn:active { transform: scale(0.98); }
.btn-secondary { background: #eee; color: #555; }
.dark-mode .btn-secondary { background: #444; color: #ddd; }
.btn-primary { background: #007bff; color: #fff; }
.btn-primary:disabled { background: #ccc; cursor: not-allowed; }
</style>
