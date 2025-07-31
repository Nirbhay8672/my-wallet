<template>
    <inertia-head title="Accounts" />
    <main-page :page_name="'Accounts'">
        <div class="row justify-content-between gy-3 mb-1">
            <div class="col-md-auto me-auto">
                <div class="pagetitle">
                    <h4>My Accounts</h4>
                </div>
            </div>
            <div class="col-md-auto ms-auto">
                <button class="btn btn-primary btn-sm" @click="openModal()" v-if="hasPermission('add_account')">
                    <i class="fa fa-plus me-2"></i>Add Bank Account
                </button>
            </div>
        </div>
        <div class="pre-loader" v-if="loader" style="height: 200px">
            <div class="circle-line">
                <img class="loader-icon-image circle-one" :src="`${$page.props.url}/images/coin.png`"
                    alt="coin" />
                <img class="loader-icon-image circle-two" :src="`${$page.props.url}/images/coin.png`"
                    alt="coin" />
            </div>
        </div>
        <div class="row" v-else>
            <div class="col-md-4 mb-4" v-for="account in accounts" :key="account.id">
                <div class="card h-100 text-center">
                    <div class="card-header bg-gradient-primary text-white">
                        <img v-if="account.type === 'cash'" :src="`${$page.props.url}/images/coin.png`" alt="cash"
                            class="mx-auto mt-2" style="width: 60px; height: 60px; object-fit: contain;" />
                        <img v-else :src="bankLogo(account.bank_id)" alt="logo" class="mx-auto mt-2"
                            style="height: 60px; width: auto; object-fit: cover;border-radius: 5%;border: 2px solid #fff;" />
                        <h5 class="card-title text-white mb-0 mt-2">{{ account.name }}</h5>
                    </div>
                    <div class="card-body">
                        <div v-if="account.type === 'bank' && account.bank">
                            <span class="badge bg-secondary mb-2">{{ account.bank.name }}</span>
                        </div>
                        <div v-else>
                            <span class="badge bg-success mb-2">Cash Account</span>
                        </div>
                        <div class="mb-3">
                            <h4 class="text-primary mb-0">₹{{ formatNumber(account.balance) }}</h4>
                            <small class="text-muted">Current Balance</small>
                        </div>
                        <p class="mb-1" v-if="account.account_number">
                            <small class="text-muted">A/C: {{ account.account_number }}</small>
                        </p>
                        <div class="mt-3">
                            <button class="btn btn-sm btn-info me-2" @click="openModal(account)"
                                v-if="hasPermission('edit_account')">
                                <i class="fa fa-edit me-1"></i>Edit
                            </button>
                            <button v-if="account.type === 'bank' && hasPermission('delete_account')"
                                class="btn btn-sm btn-danger" @click="deleteAccount(account)">
                                <i class="fa fa-trash me-1"></i>Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div class="col-12" v-if="accounts.length === 0">
                <div class="card text-center">
                    <div class="card-body py-5">
                        <img :src="`${$page.props.url}/images/coin.png`" alt="no accounts"
                            style="width: 80px; height: 80px; opacity: 0.5;" class="mb-3" />
                        <h5 class="text-muted">No accounts found</h5>
                        <p class="text-muted">You don't have any accounts yet. Add your first bank account to get
                            started.</p>
                        <button class="btn btn-primary" @click="openModal()" v-if="hasPermission('add_account')">
                            <i class="fa fa-plus me-2"></i>Add Your First Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <Form
            ref="form"
            :modal-id="'accountForm'"
            :account="editingAccount"
            :banks="banks"
            :is-edit="isEdit"
            :url="page.props.url"
            @reload="reloadData"
        />
    </main-page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import Form from './Form.vue';

const page = usePage();

const props = defineProps({
    accounts: Array,
    banks: Array,
    auth: {
        type: Object,
        required: true,
    },
    url: {
        type: String,
        default: ""
    }
});

const form = ref(null);
const editingAccount = ref(null);
const isEdit = ref(false);
const loader = ref(true);

function openModal(account = null) {
    console.log('Opening modal for account:', account);
    editingAccount.value = account;
    isEdit.value = !!account;

    // Open the modal using the ref
    if (form.value) {
        console.log('Form ref found, calling openModal');
        form.value.openModal(account);
    } else {
        console.log('Form ref not found');
    }
}

function reloadData() {
    // Reload the page to get updated data
    router.reload();
}

function deleteAccount(account) {
    if (account.type === 'cash') {
        alert('Cash accounts cannot be deleted.');
        return;
    }

    if (confirm('Are you sure you want to delete this account?')) {
        router.delete(`/accounts/delete/${account.id}`);
    }
}

function formatNumber(number) {
    return parseFloat(number).toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function hasPermission(permission_name) {
    if (!props.auth || !props.auth.user || !props.auth.user.roles || !props.auth.user.roles[0]) {
        return false;
    }

    let permission_obj = props.auth.user.roles[0].permissions.find(
        (permission) => permission.name == permission_name
    );
    return permission_obj ? true : false;
}

onMounted(() => {
    setTimeout(function () {
        loader.value = false;
    }, 1000);
});

function bankLogo(bank_id) {
    const bank = props.banks.find(b => b.id === bank_id);
    return bank ? `${props.url}${bank.logo}` : '';
}
</script>
