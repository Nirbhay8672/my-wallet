<template>
    <Modal :id="modalId" @close="handleClose">
        <template #modal_title>
            {{ isEdit ? 'Edit Income Entry' : 'Add Income Entry' }}
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
                            field="income_type_id"
                            id="income_type_id"
                            label="Income Type"
                            label-class="required"
                            :errors="formValidation.errors"
                            :noLabel="true"
                            no-input
                        >
                            <template #input="{ hasError }">
                                <select
                                    class="form-control form-control-solid form-select"
                                    id="income_type_id"
                                    v-model="fields.income_type_id"
                                    :class="{ 'is-invalid': hasError }"
                                    required
                                >
                                    <option value="">-- Select Income Type --</option>
                                    <option v-for="type in incomeTypes" :key="type.id" :value="type.id">
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
                        <div class="custom-file-upload">
                            <div class="file-upload-area" :class="{ 'is-invalid': hasError }" @click="triggerFileInput">
                                <input
                                    ref="fileInput"
                                    type="file"
                                    class="file-input"
                                    id="proof"
                                    @change="handleFileUpload"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                >
                                <div class="upload-content">
                                    <i class="fa fa-cloud-upload fa-2x text-primary mb-2"></i>
                                    <h5 class="upload-title">Upload any proof</h5>
                                    <p class="upload-text">Click to browse files</p>
                                    <p class="upload-info">Allowed file types: JPG, PNG, PDF (Max: 2MB)</p>
                                </div>
                                <div v-if="selectedFileName" class="selected-file">
                                    <i class="fa fa-file text-success me-2"></i>
                                    <span>{{ selectedFileName }}</span>
                                </div>
                            </div>
                            <div v-if="hasError" class="invalid-feedback d-block">
                                {{ formValidation.getError("proof")[0] }}
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
    incomeTypes: {
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
    },
    selectedAccountId: {
        type: [String, Number],
        default: null
    }
});

const emits = defineEmits(["reload"]);

let fields = reactive({
    id: "",
    title: "",
    account_id: "",
    income_type_id: "",
    amount: "",
    proof: null,
    proof_path: "",
    description: "",
    date: "",
    time: "",
});

let isFileUpload = ref(false);
let selectedFileName = ref("");
let fileInput = ref(null);



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
        selectedFileName.value = "";

        // Ensure date is properly formatted for the input
        if (entry.date) {
            fields.date = new Date(entry.date).toISOString().split('T')[0];
        }
    } else {
        // Set default values for new entry
        fields.date = new Date().toISOString().split('T')[0];
        fields.time = new Date().toTimeString().slice(0, 5);

        // Auto-select account if provided
        if (props.selectedAccountId) {
            fields.account_id = props.selectedAccountId;
        }
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
            selectedFileName.value = "";
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
            selectedFileName.value = "";
            return;
        }

        fields.proof_path = file.name;
        fields.proof = file;
        isFileUpload.value = true;
        selectedFileName.value = file.name;

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
    selectedFileName.value = "";

    // Clear file input
    const fileInput = document.getElementById("proof");
    if (fileInput) {
        fileInput.value = "";
    }
}

function handleSubmit() {
    formValidation.validate();



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
    form_data.set("income_type_id", fields.income_type_id);
    form_data.set("amount", fields.amount);
    form_data.set("description", fields.description);
    form_data.set("date", fields.date);
    form_data.set("time", fields.time);

    let url = props.isEdit ? `/income-entries/update/${fields.id}` : "/income-entries/create";
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

function triggerFileInput() {
    if (fileInput.value) {
        fileInput.value.click();
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
        income_type_id: {
            required: "Income type is required.",
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
.custom-file-upload {
    width: 100%;
}

.file-upload-area {
    position: relative;
    width: 100%;
    min-height: 120px;
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-upload-area:hover {
    border-color: #3b82f6;
    background-color: #f0f8ff;
}

.file-upload-area.is-invalid {
    border-color: #dc3545;
    background-color: #fff5f5;
}

.file-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 1;
}

.upload-content {
    text-align: center;
    padding: 20px;
    pointer-events: none;
}

.upload-title {
    margin: 0;
    color: #374151;
    font-weight: 600;
}

.upload-text {
    margin: 5px 0;
    color: #6b7280;
    font-size: 14px;
}

.upload-info {
    margin: 5px 0 0 0;
    color: #9ca3af;
    font-size: 12px;
}

.selected-file {
    position: absolute;
    top: 10px;
    left: 10px;
    right: 10px;
    background: rgba(34, 197, 94, 0.1);
    border: 1px solid #22c55e;
    border-radius: 4px;
    padding: 8px 12px;
    color: #15803d;
    font-size: 14px;
    display: flex;
    align-items: center;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}
</style>
