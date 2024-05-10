<template>
    <Modal ref="website_form" :id="'website_form'">
        <template #modal_title>
            <span>Add Website</span>
        </template>

        <div class="row mb-3 gy-3">
            <div class="col-12">
                <Field
                    v-model="fields.url"
                    label="Website Url"
                    label-class="required"
                    type="text"
                    id="url"
                    field="url"
                    placeholder="Enter website"
                    :errors="formValidation.errors"
                ></Field>
            </div>
            <div class="col-12">
                <Field
                    v-model="fields.username"
                    label="Username"
                    label-class="required"
                    type="text"
                    id="username"
                    field="username"
                    placeholder="Enter wordpress username"
                    :errors="formValidation.errors"
                ></Field>
            </div>
            <div class="col-12">
                <Field
                    v-model="fields.password"
                    label="Password"
                    label-class="required"
                    type="password"
                    id="password"
                    field="password"
                    placeholder="Enter password"
                    :errors="formValidation.errors"
                ></Field>
            </div>
        </div>

        <template #modal_footer>
            <button class="btn btn-success btn-sm" @click="handleSubmit">
                Find
            </button>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive } from "vue";
import Modal from "../../../components/Modal.vue";
import { FormValidation } from "../../../helpers/Validation";
import Field from "../../../helpers/Field.vue";

let website_form = ref(null);

const emits = defineEmits(["reload"]);

let fields = reactive({
    url: "",
    username: "",
    password: "",
});

function openModal(user) {
    clearFormData();
    website_form.value.open();
}

function clearFormData() {
    formValidation.reset();
    fields.url = "";
    fields.username = "";
    fields.password = "";
}

function handleSubmit() {
    formValidation.validate();
    if (formValidation.isValid()) {

    }
}

defineExpose({
    openModal,
});

let formValidation = reactive(
    new FormValidation(fields, {
        url: {
            required: "The website field is required.",
        },
        username: {
            required: "The username field is required.",
        },
        password: {
            required: "The password field is required.",
        },
    })
);
</script>
