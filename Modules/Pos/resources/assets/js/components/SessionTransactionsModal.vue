<template>
  <div class="modal-backdrop">
    <div class="modal-content">
      <div class="modal-header">
        <h2>{{ __('Session Transaction') }}</h2>
        <button class="btn-close" @click="$emit('close')">&times;</button>
      </div>

      <div class="modal-body">
        <form @submit.prevent="submitTransaction">
          <div class="form-group">
            <label>{{ __('Transaction Type') }}</label>
            <select v-model="type" class="form-control" required>
              <option value="withdrawal">{{ __('Cash Withdrawal') }}</option>
              <option value="deposit">{{ __('Cash Deposit') }}</option>
            </select>
          </div>

          <div class="form-group mt-3">
            <label>{{ __('Amount') }}</label>
            <input type="number" v-model="amount" class="form-control" step="0.01" min="0.01" required />
          </div>

          <div class="form-group mt-3">
            <label>{{ __('Notes') }}</label>
            <textarea v-model="notes" class="form-control" rows="3" required></textarea>
          </div>

          <div class="modal-footer mt-4">
            <button type="button" class="btn btn-secondary" @click="$emit('close')">{{ __('Cancel') }}</button>
            <button type="submit" class="btn btn-primary" :disabled="loading">
              {{ loading ? __('Processing...') : __('Submit') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
  sessionId: {
    type: Number,
    required: true
  }
});

const emit = defineEmits(['close', 'success']);

const type = ref('withdrawal');
const amount = ref('');
const notes = ref('');
const loading = ref(false);

// We define a simple helper here to mimic Laravel translation if needed, or just return the text
const __ = (text) => text;

const submitTransaction = async () => {
  if (amount.value <= 0) return;
  
  loading.value = true;
  try {
    const res = await axios.post('/api/pos/session/transaction', {
      session_id: props.sessionId,
      type: type.value,
      amount: amount.value,
      notes: notes.value
    });
    
    if (res.data.status) {
      alert(res.data.message);
      emit('success');
      emit('close');
    }
  } catch (error) {
    alert(error.response?.data?.message || 'Error recording transaction');
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}
.modal-content {
  background: #fff;
  border-radius: 12px;
  width: 400px;
  max-width: 90%;
  padding: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.dark-mode .modal-content {
  background: #1e1e1e;
  color: #fff;
}
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}
.modal-header h2 {
  margin: 0;
  font-size: 1.5rem;
}
.btn-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
}
.dark-mode .btn-close { color: #fff; }
.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
}
.form-control {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 8px;
}
.dark-mode .form-control {
  background: #2c2c2c;
  border-color: #444;
  color: #fff;
}
.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}
.btn {
  padding: 10px 20px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  font-weight: 600;
}
.btn-primary { background: #007bff; color: white; }
.btn-secondary { background: #6c757d; color: white; }
</style>
