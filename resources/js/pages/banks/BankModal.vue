<template>
    <div class="modal fade" :id="modalId" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ isEdit ? 'Edit Bank' : 'Add Bank' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="handleSubmit">
                        <div class="mb-3">
                            <Field
                                v-model="fields.name"
                                label="Bank Name"
                                label-class="required"
                                type="text"
                                id="name"
                                field="name"
                                :errors="formValidation.errors"
                            ></Field>
                        </div>
                        <div class="mb-3 text-center">
                            <div class="card-img-actions d-inline-block">
                                <div
                                    style="overflow: hidden; border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin: 0 auto;"
                                >
                                    <img
                                        id="bank_logo_file"
                                        :src="logoPreview"
                                        class="rounded"
                                        style="width: 80px; height: 80px; object-fit: contain;"
                                    />
                                </div>
                                <button
                                    class="btn btn-primary btn-sm mt-2"
                                    @click.prevent="triggerLogoUpload"
                                    type="button"
                                >
                                    Upload Logo
                                </button>
                            </div>
                            <Field
                                v-model="fields.logo"
                                label="Bank Logo"
                                label-class="required"
                                type="file"
                                id="logo"
                                field="logo"
                                :errors="formValidation.errors"
                                no-label
                                no-input
                            >
                                <template #input="{ hasError }">
                                    <input
                                        type="file"
                                        id="logo"
                                        ref="logoInput"
                                        class="form-control d-none"
                                        accept="image/*"
                                        @change="handleFileUpload"
                                        :class="{ 'is-invalid': hasError }"
                                    />
                                </template>
                            </Field>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" :disabled="loading">
                                {{ loading ? 'Saving...' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from "vue";
import Field from "../../helpers/Field.vue";
import {
    FormValidation,
    withParamsAndMessage,
    withParams,
} from "../../helpers/Validation";
import { resetObjectKeys } from "../../helpers/utils";
import axios from "axios";
import { toastAlert } from "../../helpers/alert";

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

const emits = defineEmits(["bankSaved"]);

let bank_modal = ref(null);
let logoInput = ref(null);
let title_text = ref("");
let button_text = ref("");

let fields = reactive({
    id: "",
    name: "",
    logo: "",
    logo_path: ""
});

function openModal(bank) {
    clearFormData();
    // Open modal (Bootstrap)
    const modalEl = document.getElementById(props.modalId);
    if (modalEl) {
        const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
    title_text.value = bank ? `Update Bank : ${bank.name}` : "Create Bank";
    button_text.value = bank ? "Update" : "Submit";
    if (bank) {
        Object.assign(fields, bank);
        fields.logo_path = bank.logo;
        fields.logo = "";
    }
}

function triggerLogoUpload() {
    logoInput.value && logoInput.value.click();
}

function previewFiles(event) {
    fields.logo = event.target.files[0].name;
    var image = document.getElementById("logo");
    image.src = URL.createObjectURL(event.target.files[0]);
}

function clearFormData() {
    formValidation.reset();
    title_text.value = "";
    button_text.value = "";
    resetObjectKeys(fields);
    fields.logo_path = "";
}

function handleSubmit() {
    formValidation.validate();
    let form_data = new FormData();
    if (fields.logo && fields.logo instanceof File) {
        form_data.set("logo", fields.logo, fields.logo.name);
    }
    form_data.set("id", fields.id || "");
    form_data.set("name", fields.name);
    let url = props.isEdit ? `/banks/update/${fields.id}` : "/banks/create";
    let method = props.isEdit ? "post" : "post";
    let settings = { headers: { "content-type": "multipart/form-data" } };
    if (formValidation.isValid()) {
        axios[method](url, form_data, settings)
            .then((response) => {
                // Close modal
                const modalEl = document.getElementById(props.modalId);
                if (modalEl) {
                    const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.hide();
                }
                emits("bankSaved");
                toastAlert({ title: response.data.message });
                clearFormData();
            })
            .catch(function (error) {
                if (error.response && error.response.status === 422) {
                    formValidation.setServerSideErrors(
                        error.response.data.errors
                    );
                }
            });
    }
}

defineExpose({
    openModal,
});

let formValidation = reactive(
    new FormValidation(fields, {
        logo: {
            required: "Bank logo is required.",
        },
        name: {
            required: "Bank name is required.",
        },
    })
);
</script>
