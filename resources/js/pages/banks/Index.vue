<template>
  <inertia-head title="Banks" />
  <main-page :page_name="'Banks'">
    <div class="container-fluid">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Banks</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bankModal">
          Add Bank
        </button>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th style="width: 80px">Logo</th>
                  <th>Name</th>
                  <th style="width: 150px">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="bank in banks" :key="bank.id">
                  <td>
                    <img :src="bank.logo" :alt="bank.name" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: contain;">
                  </td>
                  <td>{{ bank.name }}</td>
                  <td>
                    <button class="btn btn-sm btn-primary me-2" @click="editBank(bank)" data-bs-toggle="modal" data-bs-target="#bankModal">
                      Edit
                    </button>
                    <button class="btn btn-sm btn-danger" @click="deleteBank(bank)">
                      Delete
                    </button>
                  </td>
                </tr>
                <tr v-if="!banks.length">
                  <td colspan="3" class="text-center">No banks found</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <BankModal 
        modal-id="bankModal"
        :bank="selectedBank"
        :is-edit="!!selectedBank"
        @bank-saved="handleBankSaved"
      />
    </div>
  </main-page>
</template>

<script setup>
import { ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import BankModal from './BankModal.vue';

const props = defineProps({
  banks: Array,
});

const selectedBank = ref(null);

const editBank = (bank) => {
  selectedBank.value = bank;
};

const deleteBank = (bank) => {
  if (confirm('Are you sure you want to delete this bank?')) {
    router.delete(`/banks/${bank.id}`, {
      preserveScroll: true
    });
  }
};

const handleBankSaved = () => {
  selectedBank.value = null;
};
</script> 
