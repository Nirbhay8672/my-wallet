<template>
  <inertia-head title="Accounts" />
  <main-page :page_name="'Accounts'">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Accounts</h2>
      <button class="btn btn-primary" @click="openModal()">Add Bank Account</button>
    </div>
    <div class="row" v-if="loader">
      <div class="col-12">
        <div class="card">
          <div class="card-body p-4" style="height: 200px">
            <div class="pre-loader">
              <div class="circle-line">
                <img
                  class="loader-icon-image circle-one"
                  :src="`${$page.props.url}/images/coin.png`"
                  alt="coin"
                />
                <img
                  class="loader-icon-image circle-two"
                  :src="`${$page.props.url}/images/coin.png`"
                  alt="coin"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row" v-else>
      <div class="col-md-4 mb-4" v-for="account in accounts" :key="account.id">
        <div class="card h-100 text-center">
          <img v-if="account.type === 'cash'" :src="`${$page.props.url}/images/coin.png`" alt="cash" class="card-img-top mx-auto mt-3" style="width: 80px; height: 80px; object-fit: contain;" />
          <img v-else :src="bankLogo(account.bank_id)" alt="logo" class="card-img-top mx-auto mt-3" style="width: 80px; height: 80px; object-fit: contain;" />
          <div class="card-body">
            <h5 class="card-title">{{ account.name }}</h5>
            <div v-if="account.type === 'bank' && account.bank">
              <span class="badge bg-secondary mb-2">{{ account.bank.name }}</span>
            </div>
            <p class="mb-1">Balance: <b>{{ account.balance }}</b></p>
            <p class="mb-1" v-if="account.account_number">A/C: {{ account.account_number }}</p>
            <button class="btn btn-sm btn-info me-2" @click="openModal(account)">Edit</button>
            <button v-if="account.type === 'bank'" class="btn btn-sm btn-danger" @click="deleteAccount(account)">Delete</button>
          </div>
        </div>
      </div>
    </div>
    <AccountModal v-if="showModal" :show="showModal" :account="editingAccount" :banks="banks" @close="closeModal" />
  </main-page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AccountModal from './AccountModal.vue';

const props = defineProps({
  accounts: Array,
  banks: Array,
});

const showModal = ref(false);
const editingAccount = ref(null);
const loader = ref(true);

function openModal(account = null) {
  editingAccount.value = account;
  showModal.value = true;
}
function closeModal() {
  showModal.value = false;
  editingAccount.value = null;
}
function deleteAccount(account) {
  if (confirm('Are you sure you want to delete this account?')) {
    router.get(`/accounts/delete/${account.id}`);
  }
}
onMounted(() => {
  setTimeout(function () {
    loader.value = false;
  }, 1000);
});

function bankLogo(bank_id) {
  const bank = props.banks.find(b => b.id === bank_id);
  return bank ? `/images/banks/${bank.logo}` : '';
}
</script>
