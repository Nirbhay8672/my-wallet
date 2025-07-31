<template>
    <Modal :id="modalId" @close="handleClose">
        <template #modal_title>
            {{ isEdit ? 'Edit Expense Entry' : 'Add Expense Entry' }}
        </template>

        <form @submit.prevent="handleSubmit">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <Field
                            v-model="fields.title"
                            label="Title"
                            label-class="required"
                            type="text"
                            id="title"
                            field="title"
                            :errors="formValidation.errors"
                        ></Field>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <Field
                            field="account_id"
                            id="account_id"
                            label="Account"
                            label-class="required"
                            :errors="formValidation.errors"
                            :noLabel="true"
                            no-input
                        >
                            <template #input="{ hasError }">
                                <select 
                                    class="form-control form-control-solid form-select" 
                                    id="account_id" 
                                    v-model="fields.account_id"
                                    :class="{ 'is-invalid': hasError }"
                                    required
                                >
                                    <option value="">-- Select Account --</option>
                                    <option v-for="account in accounts" :key="account.id" :value="account.id">
                                        {{ account.name }} (₹{{ formatNumber(account.balance) }})
                                    </option>
                                </select>
                            </template>
                        </Field>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <Field
                            field="expense_type_id"
                            id="expense_type_id"
                            label="Expense Type"
                            label-class="required"
                            :errors="formValidation.errors"
                            :noLabel="true"
                            no-input
                        >
                            <template #input="{ hasError }">
                                <select 
                                    class="form-control form-control-solid form-select" 
                                    id="expense_type_id" 
                                    v-model="fields.expense_type_id"
                                    :class="{ 'is-invalid': hasError }"
                                    required
                                >
                                    <option value="">-- Select Expense Type --</option>
                                    <option v-for="type in expenseTypes" :key="type.id" :value="type.id">
                                        {{ type.name }}
                                    </option>
                                </select>
                            </template>
                        </Field>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <Field
                            v-model="fields.amount"
                            label="Amount"
                            label-class="required"
                            type="number"
                            id="amount"
                            field="amount"
                            :errors="formValidation.errors"
                            step="0.01"
                            min="0.01"
                            required
                        ></Field>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <Field
                            v-model="fields.date"
                            label="Date"
                            label-class="required"
                            type="date"
                            id="date"
                            field="date"
                            :errors="formValidation.errors"
                            :max="maxDate"
                        ></Field>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <Field
                            v-model="fields.time"
                            label="Time"
                            label-class="required"
                            type="time"
                            id="time"
                            field="time"
                            :errors="formValidation.errors"
                            :max="maxTime"
                        ></Field>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <Field
                    field="proof"
                    id="proof"
                    label="Proof (Optional)"
                    :errors="formValidation.errors"
                    :noLabel="true"
                    no-input
                >
                    <template #input="{ hasError }">
                        <div class="file-upload-wrapper">
                            <input 
                                type="file" 
                                class="form-control" 
                                id="proof" 
                                @change="handleFileUpload"
                                :class="{ 'is-invalid': hasError }"
                                accept=".jpg,.jpeg,.png,.pdf"
                            >
                            <div class="form-text mt-1">Allowed file types: JPG, PNG, PDF (Max: 2MB)</div>
                            <div v-if="fields.proof && !isFileUpload" class="mt-2">
                                <a :href="`${url}/${fields.proof}`" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="fa fa-file me-1"></i>View Current Proof
                                </a>
                            </div>
                        </div>
                    </template>
                </Field>
            </div>

            <div class="mb-3">
                <Field
                    field="description"
                    id="description"
                    label="Description (Optional)"
                    :errors="formValidation.errors"
                    :noLabel="true"
                    no-input
                >
                    <template #input="{ hasError }">
                        <textarea
                            class="form-control"
                            id="description"
                            v-model="fields.description"
                            :class="{ 'is-invalid': hasError }"
                            rows="3"
                            placeholder="Enter description..."
                        ></textarea>
                    </template>
                </Field>
            </div>
        </form>

        <template #modal_footer>
            <button type="submit" class="btn bg-gradient-primary" @click="handleSubmit">Save</button>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive, computed } from "vue";
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
    entry: {
        type: Object,
        default: null
    },
    accounts: {
        type: Array,
        required: true
    },
    expenseTypes: {
        type: Array,
        required: true
    },
    isEdit: {
        type: Boolean,
        default: false
    },
    url: {
        type: String,
        required: true
    }
});

const emits = defineEmits(["reload"]);

let fields = reactive({
    id: "",
    title: "",
    account_id: "",
    expense_type_id: "",
    amount: "",
    proof: null,
    proof_path: "",
    description: "",
    date: "",
    time: "",
});

let isFileUpload = ref(false);

// Computed properties for date/time validation
const maxDate = computed(() => {
    return new Date().toISOString().split('T')[0];
});

const maxTime = computed(() => {
    const now = new Date();
    const currentTime = now.toTimeString().slice(0, 5);
    
    // If date is today, restrict time to current time or earlier
    if (fields.date === maxDate.value) {
        return currentTime;
    }
    
    // If date is in the past, allow any time
    return "23:59";
});

function openModal(entry) {
    // Reset form validation errors
    formValidation.reset();

    // Clear all fields
    clearFormData();

    // Clear file input
    const fileInput = document.getElementById("proof");
    if (fileInput) {
        fileInput.value = "";
    }

    // Populate fields if editing
    if (entry) {
        Object.assign(fields, entry);
        fields.proof_path = entry.proof;
        fields.proof = null;
        isFileUpload.value = false;
    } else {
        // Set default values for new entry
        fields.date = new Date().toISOString().split('T')[0];
        fields.time = new Date().toTimeString().slice(0, 5);
    }

    // Open modal (Bootstrap)
    const modalEl = document.getElementById(props.modalId);
    if (modalEl) {
        const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
}

function handleFileUpload(event) {
    const file = event.target.files[0];
    if (file) {
        // Validate file type - only JPG, PNG, PDF allowed
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
        if (!allowedTypes.includes(file.type)) {
            toastAlert({
                title: "Please select a valid file (JPG, PNG, or PDF only)",
                icon: "error"
            });
            event.target.value = "";
            return;
        }

        // Validate file size (max 2MB)
        const maxSize = 2 * 1024 * 1024; // 2MB
        if (file.size > maxSize) {
            toastAlert({
                title: "File size must be less than 2MB",
                icon: "error"
            });
            event.target.value = "";
            return;
        }

        fields.proof_path = file.name;
        fields.proof = file;
        isFileUpload.value = true;

        // Clear any validation errors for proof
        formValidation.clearError('proof');
    }
}

function clearFormData() {
    formValidation.reset();
    resetObjectKeys(fields);
    fields.proof_path = "";
    fields.proof = null;
    isFileUpload.value = false;

    // Clear file input
    const fileInput = document.getElementById("proof");
    if (fileInput) {
        fileInput.value = "";
    }
}

function handleSubmit() {
    formValidation.validate();

    // Additional validation for date and time
    const selectedDate = new Date(fields.date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (selectedDate > today) {
        toastAlert({
            title: "Date cannot be in the future. Please select today or a past date.",
            icon: "error"
        });
        return;
    }

    // Validate time if date is today
    if (fields.date === maxDate.value) {
        const selectedTime = fields.time;
        const currentTime = new Date().toTimeString().slice(0, 5);
        
        if (selectedTime > currentTime) {
            toastAlert({
                title: "Time cannot be in the future. Please select current time or earlier.",
                icon: "error"
            });
            return;
        }
    }

    // Validate amount
    if (parseFloat(fields.amount) <= 0) {
        toastAlert({
            title: "Amount must be greater than 0.",
            icon: "error"
        });
        return;
    }

    let form_data = new FormData();

    let proof = document.getElementById("proof");

    if (proof && proof.files.length > 0) {
        let file = proof.files[0];
        form_data.set("proof", file, file.name);
    }

    form_data.set("id", fields.id || "");
    form_data.set("title", fields.title);
    form_data.set("account_id", fields.account_id);
    form_data.set("expense_type_id", fields.expense_type_id);
    form_data.set("amount", fields.amount);
    form_data.set("description", fields.description);
    form_data.set("date", fields.date);
    form_data.set("time", fields.time);

    let url = props.isEdit ? `/expense-entries/update/${fields.id}` : "/expense-entries/create";
    let method = "post";
    let settings = { headers: { "content-type": "multipart/form-data" } };

    if (formValidation.isValid()) {
        axios[method](url, form_data, settings)
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
            title: "Please fill all required fields correctly.",
            icon: "error"
        });
    }
}

function handleClose() {
    clearFormData();
}

function formatNumber(number) {
    return parseFloat(number).toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

defineExpose({
    openModal,
});

let formValidation = reactive(
    new FormValidation(fields, {
        title: {
            required: "Title is required.",
        },
        account_id: {
            required: "Account is required.",
        },
        expense_type_id: {
            required: "Expense type is required.",
        },
        amount: {
            required: "Amount is required.",
        },
        date: {
            required: "Date is required.",
        },
        time: {
            required: "Time is required.",
        },
        proof: {
            // Optional field, no validation required
        },
        description: {
            // Optional field, no validation required
        },
    })
);
</script>

<style scoped>
.file-upload-wrapper {
    position: relative;
}

.file-upload-wrapper input[type="file"] {
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    background-color: #fff;
}

.file-upload-wrapper input[type="file"]:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
}

.file-upload-wrapper input[type="file"].is-invalid {
    border-color: #dc3545;
}
</style> 