<template>
    <Modal :id="modalId" @close="handleClose">
        <template #modal_title>
            {{ isEdit ? 'Edit Bank' : 'Add Bank' }}
        </template>

        <form @submit.prevent="handleSubmit">
            <div class="mb-3 text-center">
                <div class="card-img-actions d-inline-block">
                    <div
                        style="overflow: hidden; border-radius: 5%; width: 200px; height: 150px; display: flex; align-items: center; justify-content: center;"
                    >
                        <img
                            id="bank_logo_file"
                            src=""
                            class="rounded"
                            style="height: 150px; width: auto; object-fit: contain;"
                        />
                    </div>
                    <button
                        class="btn btn-primary btn-sm mt-3"
                        @click.prevent="triggerLogoUpload"
                        type="button"
                    >
                        Upload Logo
                    </button>
                </div>
                <input
                    type="file"
                    id="logo"
                    ref="logoInput"
                    @change="previewFiles"
                    class="form-control d-none"
                    accept="image/png, image/jpeg, image/jpg"
                    :class="{
                        'is-invalid':
                            formValidation.hasError('logo_path'),
                    }"
                />
                <span
                    :class="{
                        'is-invalid':
                            formValidation.hasError('logo_path'),
                    }"
                ></span>
                <div
                    class="invalid-feedback"
                    v-if="formValidation.hasError('logo_path')"
                >
                    <span>{{
                        formValidation.getError("logo_path")[0]
                    }}</span>
                </div>
            </div>
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
            <div class="mb-3">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <div class="d-flex align-items-center">
                        <ToggleButton
                            v-model="fields.active"
                            color="success"
                            size="medium"
                            @change="handleStatusChange"
                        />
                    </div>
                </div>
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
import ToggleButton from "../../components/ToggleButton.vue";
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
    bank: {
        type: Object,
        default: null
    },
    isEdit: {
        type: Boolean,
        default: false
    }
});

const emits = defineEmits(["reload"]);

let logoInput = ref(null);
let title_text = ref("");
let button_text = ref("");

let fields = reactive({
    id: "",
    name: "",
    logo: "",
    logo_path: "",
    active: true
});

function openModal(bank) {
    // Reset form validation errors
    formValidation.reset();

    // Clear all fields
    clearFormData();

    // Reset image preview to default
    const image = document.getElementById("bank_logo_file");
    if (image) {
        image.src = "https://www.pngplay.com/wp-content/uploads/6/Bank-Background-PNG-Image.png";
    }

    // Clear file input
    if (logoInput.value) {
        logoInput.value.value = "";
    }

    // Set title and button text
    title_text.value = bank ? `Update Bank : ${bank.name}` : "Create Bank";
    button_text.value = bank ? "Update" : "Submit";

    // Populate fields if editing
    if (bank) {
        Object.assign(fields, bank);
        fields.logo_path = bank.logo;
        fields.logo = "";
        fields.active = bank.active !== undefined ? bank.active : true;

        // Disable logo validation when editing (since it's optional)
        formValidation.disableField('logo_path');

        // Update the image preview if logo exists
        if (bank.logo) {
            if (image) {
                image.src = bank.logo;
            }
        }
    } else {
        // Enable logo validation for new banks
        formValidation.enableField('logo_path');
        fields.active = true;
    }

    // Open modal (Bootstrap)
    const modalEl = document.getElementById(props.modalId);
    if (modalEl) {
        const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
}

function triggerLogoUpload() {
    logoInput.value && logoInput.value.click();
}

function previewFiles(event) {
    const file = event.target.files[0];
    if (file) {
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            toastAlert({
                title: "Please select a valid image file (JPEG, JPG, or PNG)",
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

        fields.logo_path = file.name;
        fields.logo = file;
        var image = document.getElementById("bank_logo_file");
        image.src = URL.createObjectURL(file);

        // Clear any validation errors for logo
        formValidation.clearError('logo_path');
    }
}

function clearFormData() {
    formValidation.reset();
    title_text.value = "";
    button_text.value = "";
    resetObjectKeys(fields);
    fields.logo_path = "";
    fields.logo = "";

    // Reset image preview to default
    const image = document.getElementById("bank_logo_file");
    if (image) {
        image.src = "https://www.pngplay.com/wp-content/uploads/6/Bank-Background-PNG-Image.png";
    }

    // Clear file input
    if (logoInput.value) {
        logoInput.value.value = "";
    }
}

function handleSubmit() {
    formValidation.validate();

    let form_data = new FormData();

    let logo = document.getElementById("logo");

    if (logo && logo.files.length > 0) {
        let file = logo.files[0];
        form_data.set("logo", file, file.name);
    }

    form_data.set("id", fields.id || "");
    form_data.set("name", fields.name);
    form_data.set("active", fields.active ? 1 : 0);
    let url = props.isEdit ? `/banks/update/${fields.id}` : "/banks/create";
    let method = props.isEdit ? "post" : "post";
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
            title: "Something went wrong. Please try again.",
            icon: "error"
        });
    }
}

function handleClose() {
    clearFormData();
}

function handleStatusChange(newValue) {
    // Optional: Add any additional logic when status changes
    console.log('Bank status changed to:', newValue);
}

defineExpose({
    openModal,
});

let formValidation = reactive(
    new FormValidation(fields, {
        logo_path: {
            required: "Bank logo is required.",
        },
        name: {
            required: "Bank name is required.",
        },
    })
);
</script>
