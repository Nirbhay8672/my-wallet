<template>
    <Modal :id="modalId" @close="handleClose">
        <template #modal_title>
            {{ isEdit ? (isCash ? 'Edit Cash Account' : 'Edit Bank Account') : 'Add Bank Account' }}
        </template>

        <form @submit.prevent="handleSubmit">
            <div class="row">
                <div class="col-lg-6 mb-2">
                    <Field
                        v-model="fields.name"
                        label="Account Name"
                        label-class="required"
                        type="text"
                        id="name"
                        field="name"
                        :errors="formValidation.errors"
                    ></Field>
                </div>
                <div class="col-lg-6 mb-2">
                    <Field
                        v-model="fields.initial_amount"
                        label="Initial Amount"
                        label-class="required"
                        type="number"
                        id="initial_amount"
                        field="initial_amount"
                        :errors="formValidation.errors"
                        step="0.01"
                    ></Field>
                </div>
                <div class="col-lg-6 mb-2" v-if="!isCash">
                    <Field
                        field="bank_id"
                        id="bank_id"
                        label="Bank"
                        label-class="required"
                        :errors="formValidation.errors"
                        :no-label="true"
                        no-input
                    >
                        <template #input="{ hasError }">
                            <select
                                class="form-control form-control-solid form-select"
                                id="bank_id"
                                v-model="fields.bank_id"
                                :class="{ 'is-invalid': hasError }"
                                @change="handleBankChange"
                            >
                                <option value="">-- Select Bank --</option>
                                <template v-for="(bank, index) in banks" :key="`bank_${index}`">
                                    <option :value="bank.id">{{ bank.name }}</option>
                                </template>
                            </select>
                        </template>
                    </Field>
                </div>
                <div class="col-lg-6 mb-2" v-if="!isCash">
                    <Field
                        v-model="fields.account_number"
                        label="Account Number"
                        type="text"
                        id="account_number"
                        field="account_number"
                        :errors="formValidation.errors"
                    ></Field>
                </div>
            </div>

            <!-- Bank Logo Preview -->
            <div class="row" v-if="!isCash && fields.bank_id">
                <div class="col-12 text-center">
                    <div class="card-img-actions d-inline-block">
                        <div style="overflow: hidden; border-radius: 5%; width: 200px; height: 150px; display: flex; align-items: center; justify-content: center;">
                            <img
                                :src="bankLogo(fields.bank_id)"
                                alt="bank logo"
                                class="rounded"
                                style="height: 150px; width: auto; object-fit: contain;"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <template #modal_footer>
            <button type="submit" class="btn bg-gradient-primary" @click="handleSubmit">
                {{ isEdit ? 'Update' : 'Save' }}
            </button>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive, computed, watch } from "vue";
import Field from "../../helpers/Field.vue";
import { FormValidation } from "../../helpers/Validation";
import { resetObjectKeys } from "../../helpers/utils";
import axios from "axios";
import { toastAlert } from "../../helpers/alert";
import Modal from "../../components/Modal.vue";

const props = defineProps({
    modalId: {
        type: String,
        required: true
    },
    account: {
        type: Object,
        default: null
    },
    banks: {
        type: Array,
        default: () => []
    },
    isEdit: {
        type: Boolean,
        default: false
    },
    url: {
        type: String,
        default: ""
    }
});

const emits = defineEmits(["reload"]);

const isCash = computed(() => props.account && props.account.type === 'cash');

let fields = reactive({
    id: "",
    name: "",
    initial_amount: "",
    bank_id: "",
    account_number: ""
});

// Watch for account changes to populate form
watch(() => props.account, (newAccount) => {
    if (newAccount && props.isEdit) {
        populateFields(newAccount);
    }
}, { immediate: true });

function populateFields(account) {
    console.log('Populating fields with account data:', account);

    fields.id = account.id || "";
    fields.name = account.name || "";
    fields.initial_amount = account.initial_amount || "";
    fields.bank_id = account.bank_id || "";
    fields.account_number = account.account_number || "";

    console.log('Fields populated:', fields);

    // For cash accounts, disable bank validation
    if (account.type === 'cash') {
        console.log('Disabling bank validation for cash account');
        formValidation.disableField('bank_id');
        formValidation.disableField('account_number');
    } else {
        console.log('Enabling bank validation for bank account');
        formValidation.enableField('bank_id');
        formValidation.enableField('account_number');
    }
}

function openModal(account) {
    console.log('Form openModal called with account:', account);
    console.log('Props isEdit:', props.isEdit);
    console.log('Props account:', props.account);

    // Reset form validation errors
    formValidation.reset();

    // Clear all fields
    clearFormData();

    // Populate fields if editing
    if (account) {
        console.log('Populating fields for editing account:', account);
        populateFields(account);
    } else {
        console.log('Setting default values for new account');
        // Set default values for new account
        fields.initial_amount = "";
        fields.bank_id = "";
        fields.account_number = "";
        formValidation.enableField('bank_id');
    }

    console.log('Fields after population:', fields);

    // Open modal (Bootstrap)
    const modalEl = document.getElementById(props.modalId);
    console.log('Modal element found:', modalEl);
    if (modalEl) {
        const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
        console.log('Bootstrap modal instance:', modal);
        modal.show();
    } else {
        console.log('Modal element not found with ID:', props.modalId);
    }
}

function clearFormData() {
    formValidation.reset();
    resetObjectKeys(fields);
    fields.initial_amount = "";
    fields.bank_id = "";
    fields.account_number = "";
}

function handleBankChange() {
    // Clear any validation errors for bank_id when user selects a bank
    formValidation.clearError('bank_id');
}

function handleSubmit() {
    formValidation.validate();

    if (formValidation.isValid()) {
        let url = props.isEdit ? `/accounts/update/${fields.id}` : "/accounts/store";
        let method = "post";

        // Prepare data based on account type
        let submitData = {
            name: fields.name,
            initial_amount: fields.initial_amount
        };

        // Only include bank fields for bank accounts
        if (!isCash.value) {
            submitData.bank_id = fields.bank_id;
            submitData.account_number = fields.account_number;
        }

        console.log('Submitting data:', submitData);

        axios[method](url, submitData)
            .then((response) => {
                const modalEl = document.getElementById(props.modalId);
                if (modalEl) {
                    const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.hide();
                }
                emits("reload");
                toastAlert({ title: response.data.message });
                clearFormData();
            })
            .catch(function (error) {
                console.error('Error submitting form:', error);
                if (error.response && error.response.status === 422) {
                    formValidation.setServerSideErrors(
                        error.response.data.errors
                    );
                } else {
                    toastAlert({
                        title: "Something went wrong. Please try again.",
                        icon: "error"
                    });
                }
            });
    } else {
        toastAlert({
            title: "Please fix the validation errors.",
            icon: "error"
        });
    }
}

function handleClose() {
    clearFormData();
}

function bankLogo(bank_id) {
    const bank = props.banks.find(b => b.id === bank_id);
    return bank ? `${props.url}${bank.logo}` : '';
}

defineExpose({
    openModal,
});

let formValidation = reactive(
    new FormValidation(fields, {
        name: {
            required: "Account name is required.",
        },
        initial_amount: {
            required: "Initial amount is required.",
        },
        bank_id: {
            required: "Bank selection is required.",
        },
        account_number: {
            required: "Account number is required.",
        },
    })
);
</script>
