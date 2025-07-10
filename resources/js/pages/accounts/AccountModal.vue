<template>
  <div v-if="show" class="modal-backdrop">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ isEdit ? (isCash ? 'Edit Cash Account' : 'Edit Bank Account') : 'Add Bank Account' }}</h5>
          <button type="button" class="btn-close" @click="$emit('close')"></button>
        </div>
        <form @submit.prevent="submit">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Name</label>
              <input v-model="form.name" class="form-control" required />
              <div v-if="errors.name" class="text-danger small">{{ errors.name }}</div>
            </div>
            <div class="mb-3">
              <label class="form-label">Initial Amount</label>
              <input v-model.number="form.initial_amount" type="number" class="form-control" required />
              <div v-if="errors.initial_amount" class="text-danger small">{{ errors.initial_amount }}</div>
            </div>
            <template v-if="!isCash">
              <div class="mb-3">
                <label class="form-label">Bank</label>
                <select v-model="form.bank_id" class="form-control" required>
                  <option value="">Select Bank</option>
                  <option v-for="bank in banks" :key="bank.id" :value="bank.id">{{ bank.name }}</option>
                </select>
                <div v-if="form.bank_id" class="mt-2">
                  <img :src="bankLogo(form.bank_id)" alt="logo" style="width: 60px; height: 60px; object-fit: contain;" />
                </div>
                <div v-if="errors.bank_id" class="text-danger small">{{ errors.bank_id }}</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Account Number</label>
                <input v-model="form.account_number" class="form-control" />
                <div v-if="errors.account_number" class="text-danger small">{{ errors.account_number }}</div>
              </div>
            </template>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="$emit('close')">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
  show: Boolean,
  account: Object,
  banks: Array
});

const isEdit = computed(() => !!props.account);
const isCash = computed(() => props.account && props.account.type === 'cash');
const form = ref({
  name: '',
  initial_amount: 0,
  bank_id: '',
  account_number: ''
});
const errors = ref({});

watch(() => props.account, (val) => {
  if (val) {
    form.value = {
      name: val.name,
      initial_amount: val.initial_amount,
      bank_id: val.bank_id || '',
      account_number: val.account_number || ''
    };
  } else {
    form.value = { name: '', initial_amount: 0, bank_id: '', account_number: '' };
  }
  errors.value = {};
}, { immediate: true });

function submit() {
  errors.value = {};
  if (isEdit.value) {
    router.post(`/accounts/update/${props.account.id}`, form.value, {
      onError: (e) => { errors.value = e; },
      onSuccess: () => emitClose(),
    });
  } else {
    router.post('/accounts/create', form.value, {
      onError: (e) => { errors.value = e; },
      onSuccess: () => emitClose(),
    });
  }
}
function emitClose() {
  form.value = { name: '', initial_amount: 0, bank_id: '', account_number: '' };
  errors.value = {};
  emit('close');
}
function bankLogo(bank_id) {
  const bank = props.banks.find(b => b.id === bank_id);
  return bank ? `/images/banks/${bank.logo}` : '';
}
const emit = defineEmits(['close']);
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
}
.modal-dialog {
  max-width: 400px;
  width: 100%;
}
</style> 