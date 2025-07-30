<template>
    <inertia-head title="Banks" />
    <main-page :page_name="'Banks'">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <div class="col-md-auto me-auto">
                <div class="pagetitle">
                    <h4>Banks</h4>
                </div>
            </div>
            <button class="btn btn-primary btn-sm" @click="editBank(null)">
                <i class="fa fa-plus"></i>
            </button>
        </div>

        <div class="card">
            <div
                class="card-body p-4"
                style="height: 200px"
                v-if="loader"
            >
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
            <div class="card-body" v-else>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Logo</th>
                                <th>Name</th>
                                <th class="text-center align-middle">Status</th>
                                <th class="text-center align-middle">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="bank in banks" :key="bank.id">
                                <td style="min-width: 200px">
                                    <img :src="bank.logo" :alt="bank.name" class="avtar"
                                        style="width: 50px; height: 50px; object-fit: contain;">
                                </td>
                                <td style="min-width: 100px">{{ bank.name }}</td>
                                <td style="min-width: 100px" class="text-center align-middle">
                                    <div class="d-flex justify-content-center">
                                        <ToggleButton
                                            v-model="bank.active"
                                            color="success"
                                            size="small"
                                            @change="toggleBankStatus(bank)"
                                        />
                                    </div>
                                </td>
                                <td style="min-width: 100px;" class="text-center align-middle">
                                    <button class="btn btn-sm btn-primary me-2" @click="editBank(bank)">
                                        <i
                                            class="fa fa-pencil"
                                        ></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" @click="deleteBank(bank)">
                                        <i
                                            class="fa fa-trash"
                                        ></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!banks.length">
                                <td colspan="4" class="text-center align-middle">No banks found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <BankForm ref="bankForm" modal-id="bankModal" :bank="selectedBank" :is-edit="!!selectedBank" @reload="handleBankSaved" />
    </main-page>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import BankForm from './Form.vue';
import ToggleButton from '../../components/ToggleButton.vue';
import { confirmAlert, toastAlert } from '../../helpers/alert';

const props = defineProps({
    banks: Array,
});



const selectedBank = ref(null);
const bankForm = ref(null);
const loader = ref(true);

const editBank = (bank) => {
    selectedBank.value = bank;
    // Call openModal on the BankForm component
    if (bankForm.value) {
        bankForm.value.openModal(bank);
    }
};

const toggleBankStatus = (bank) => {
    axios.post(`/banks/${bank.id}/toggle-status`, {
        active: bank.active
    })
    .then((response) => {
        toastAlert({ title: response.data.message });
    })
    .catch(function (error) {
        // Revert the toggle if there's an error
        bank.active = !bank.active;
        if (error.response && error.response.status === 422) {
            toastAlert({
                title: error.response.data.message,
                icon: "error",
            });
        } else {
            toastAlert({
                title: "Something went wrong while updating the bank status.",
                icon: "error",
            });
        }
    });
};

const deleteBank = (bank) => {
    confirmAlert({
        title: "Delete Bank",
        icon: "question",
        html: `Are you sure, you want to delete <strong>${bank.name}</strong> bank?`,
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(`/banks/${bank.id}`)
                .then((response) => {
                    toastAlert({ title: response.data.message });
                    // Reload the page to refresh the banks list
                    window.location.reload();
                })
                .catch(function (error) {
                    if (error.response && error.response.status === 422) {
                        toastAlert({
                            title: error.response.data.message,
                            icon: "error",
                        });
                    } else {
                        toastAlert({
                            title: "Something went wrong while deleting the bank.",
                            icon: "error",
                        });
                    }
                });
        }
    });
};

onMounted(() => {
    setTimeout(function () {
        loader.value = false;
    }, 1000);
});

const handleBankSaved = () => {
    selectedBank.value = null;
    window.location.reload();
};
</script>
