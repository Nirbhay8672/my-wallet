<template>
    <div class="modal fade" :id="modalId" tabindex="-1" aria-hidden="true" ref="modalRef">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ isEdit ? 'Edit Bank' : 'Add Bank' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="closeModal"></button>
                </div>
                <form @submit.prevent="handleSubmit">
                    <div class="modal-body">
                        <div class="mb-3 text-center">
                            <div class="card-img-actions d-inline-block">
                                <div style="overflow: hidden; border-radius: 5px;">
                                    <img :src="logoPreview || '/images/profile.png'" class="bank-logo-preview"
                                        style="width: 80px; height: 80px; object-fit: contain; border-radius: 5px;" />
                                </div>
                                <button class="btn btn-primary btn-sm mt-2" @click.prevent="triggerFile" type="button">
                                    Upload Logo
                                </button>
                                <input type="file" ref="logoInput" @change="handleFileUpload" class="form-control d-none"
                                    accept="image/png, image/jpeg, image/jpg" />
                                <div v-if="errors.logo" class="invalid-feedback d-block">{{ errors.logo[0] }}</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Bank Name</label>
                            <input type="hidden" v-model="form.id" id="id">
                            <Field
                                v-model="form.name"
                                label="Bank Name"
                                label-class="required"
                                type="text"
                                id="name"
                                field="name"
                                :errors="errors"
                            ></Field>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="closeModal">Close</button>
                        <button type="submit" class="btn btn-success btn-sm" :disabled="loading">
                            {{ loading ? 'Saving...' : (isEdit ? 'Update' : 'Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue';
import axios from 'axios';
import { toastAlert } from '../../helpers/alert';
import Field from '../../helpers/Field.vue';

const props = defineProps({
    modalId: {
        type: String,
        required: true
    },
    bank: {
        type: Object,
        default: null
    },
    isEdit: {
        type: Boolean,
        default: false
    }
});
const emit = defineEmits(['bankSaved']);

const loading = ref(false);
const logoInput = ref(null);
const modalRef = ref(null);
const errors = ref({});
const logoPreview = ref('');

const form = ref({
    id: '',
    name: '',
    logo: null
});

watch(() => props.bank, (newBank) => {
    if (newBank) {
        form.value.name = newBank.name;
        form.value.id = newBank.id;
        form.value.logo = null;
        logoPreview.value = newBank.logo ? `${window.location.origin}/${newBank.logo}` : '';
    } else {
        resetForm();
    }
    errors.value = {};
}, { immediate: true });

function triggerFile() {
    logoInput.value && logoInput.value.click();
}

function handleFileUpload(e) {
    const file = e.target.files[0];
    if (file) {
        form.value.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
}

function closeModal() {
    resetForm();
    errors.value = {};
    emit('bankSaved');
    const modalEl = modalRef.value;
    if (window.bootstrap && window.bootstrap.Modal) {
        window.bootstrap.Modal.getOrCreateInstance(modalEl).hide();
    }
}

function resetForm() {
    form.value.id = null;
    form.value.name = '';
    form.value.logo = null;
    logoPreview.value = '';
    if (logoInput.value) logoInput.value.value = '';
}

function handleSubmit() {
    loading.value = true;
    errors.value = {};
    const url = props.isEdit && props.bank ? `/banks/update/${props.bank.id}` : '/banks/create';
    const method = 'post';
    const formData = new FormData();
    formData.append('id', form.value.id);
    formData.append('name', form.value.name);
    if (form.value.logo) formData.append('logo', form.value.logo);

    axios({
        method,
        url,
        data: formData,
        headers: { 'Content-Type': 'multipart/form-data' },
    })
        .then((response) => {
            loading.value = false;
            toastAlert({ title: response.data.message, icon: 'success' });
            closeModal();
        })
        .catch((error) => {
            loading.value = false;
            if (error.response && error.response.data && error.response.data.errors) {
                errors.value = error.response.data.errors;
            } else {
                toastAlert({ title: 'Something went wrong.', icon: 'error' });
            }
        });
}

function openModal(bank = null) {
    if (bank) {
        form.value.id = bank.id;
        form.value.name = bank.name;
        form.value.logo = null;
        logoPreview.value = bank.logo ? `${window.location.origin}/${bank.logo}` : '';
    } else {
        resetForm();
    }
    errors.value = {};
    nextTick(() => {
        if (modalRef.value) {
            const modalEl = modalRef.value;
            if (window.bootstrap && window.bootstrap.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        }
    });
}

defineExpose({
    openModal
});
</script>
