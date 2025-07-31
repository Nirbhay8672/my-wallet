<template>
    <Modal :id="modalId" @close="handleClose">
        <template #modal_title>
            {{ isEdit ? 'Edit Expense Type' : 'Add Expense Type' }}
        </template>

        <form @submit.prevent="handleSubmit">
            <div class="mb-3 text-center">
                <div class="card-img-actions d-inline-block">
                    <div
                        style="overflow: hidden; border-radius: 5%; width: 200px; height: 150px; display: flex; align-items: center; justify-content: center;"
                    >
                        <img
                            id="expense_type_icon_file"
                            src=""
                            class="rounded"
                            style="height: 150px; width: auto; object-fit: contain;"
                        />
                    </div>
                    <button
                        class="btn btn-primary btn-sm mt-3"
                        @click.prevent="triggerIconUpload"
                        type="button"
                    >
                        Upload Icon
                    </button>
                </div>
                <input
                    type="file"
                    id="icon"
                    ref="iconInput"
                    @change="previewFiles"
                    class="form-control d-none"
                    accept="image/png, image/jpeg, image/jpg"
                    :class="{
                        'is-invalid':
                            formValidation.hasError('icon_path'),
                    }"
                />
                <span
                    :class="{
                        'is-invalid':
                            formValidation.hasError('icon_path'),
                    }"
                ></span>
                <div
                    class="invalid-feedback"
                    v-if="formValidation.hasError('icon_path')"
                >
                    <span>{{
                        formValidation.getError("icon_path")[0]
                    }}</span>
                </div>
            </div>
            <div class="mb-3">
                <Field
                    v-model="fields.name"
                    label="Expense Type Name"
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
    expenseType: {
        type: Object,
        default: null
    },
    isEdit: {
        type: Boolean,
        default: false
    }
});

const emits = defineEmits(["reload"]);

let iconInput = ref(null);
let title_text = ref("");
let button_text = ref("");

let fields = reactive({
    id: "",
    name: "",
    icon: "",
    icon_path: "",
    active: true
});

function openModal(expenseType) {
    // Reset form validation errors
    formValidation.reset();

    // Clear all fields
    clearFormData();

    // Reset image preview to default
    const image = document.getElementById("expense_type_icon_file");
    if (image) {
        image.src = "https://cdn-icons-png.flaticon.com/512/5501/5501375.png";
    }

    // Clear file input
    if (iconInput.value) {
        iconInput.value.value = "";
    }

    // Set title and button text
    title_text.value = expenseType ? `Update Expense Type : ${expenseType.name}` : "Create Expense Type";
    button_text.value = expenseType ? "Update" : "Submit";

    // Populate fields if editing
    if (expenseType) {
        Object.assign(fields, expenseType);
        fields.icon_path = expenseType.icon;
        fields.icon = "";
        fields.active = expenseType.active !== undefined ? expenseType.active : true;

        // Disable icon validation when editing (since it's optional)
        formValidation.disableField('icon_path');

        // Update the image preview if icon exists
        if (expenseType.icon) {
            if (image) {
                image.src = expenseType.icon;
            }
        }
    } else {
        // Enable icon validation for new expense types
        formValidation.enableField('icon_path');
        fields.active = true;
    }

    // Open modal (Bootstrap)
    const modalEl = document.getElementById(props.modalId);
    if (modalEl) {
        const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
}

function triggerIconUpload() {
    iconInput.value && iconInput.value.click();
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

        fields.icon_path = file.name;
        fields.icon = file;
        var image = document.getElementById("expense_type_icon_file");
        image.src = URL.createObjectURL(file);

        // Clear any validation errors for icon
        formValidation.clearError('icon_path');
    }
}

function clearFormData() {
    formValidation.reset();
    title_text.value = "";
    button_text.value = "";
    resetObjectKeys(fields);
    fields.icon_path = "";
    fields.icon = "";

    // Reset image preview to default
    const image = document.getElementById("expense_type_icon_file");
    if (image) {
        image.src = "https://www.pngplay.com/wp-content/uploads/6/Expense-Icon-PNG-Image.png";
    }

    // Clear file input
    if (iconInput.value) {
        iconInput.value.value = "";
    }
}

function handleSubmit() {
    formValidation.validate();

    let form_data = new FormData();

    let icon = document.getElementById("icon");

    if (icon && icon.files.length > 0) {
        let file = icon.files[0];
        form_data.set("icon", file, file.name);
    }

    form_data.set("id", fields.id || "");
    form_data.set("name", fields.name);
    form_data.set("active", fields.active ? 1 : 0);
    let url = props.isEdit ? `/expense-types/update/${fields.id}` : "/expense-types/create";
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
    console.log('Expense type status changed to:', newValue);
}

defineExpose({
    openModal,
});

let formValidation = reactive(
    new FormValidation(fields, {
        icon_path: {
            required: "Expense type icon is required.",
        },
        name: {
            required: "Expense type name is required.",
        },
    })
);
</script>
