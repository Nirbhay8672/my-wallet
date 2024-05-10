<template>
    <Modal ref="client_form" :id="'client_form'">
        <template #modal_title>
            <span>{{ title_text }}</span>
        </template>

        <div class="row gy-2">
            <div class="col-lg-6 mb-2">
                <Field
                    v-model="fields.name"
                    label="Name"
                    label-class="required"
                    type="text"
                    id="name"
                    field="name"
                    placeholder="Enter name"
                    :errors="formValidation.errors"
                    autocomplete="off"
                ></Field>
            </div>
            <div class="col-lg-6 mb-2">
                <Field
                    v-model="fields.phone_number"
                    label="Phone Number"
                    label-class="required"
                    type="text"
                    id="phone_number"
                    field="phone_number"
                    placeholder="Enter phone number"
                    :errors="formValidation.errors"
                    autocomplete="off"
                ></Field>
            </div>
            <div class="col-lg-6 mb-2">
                <Field
                    v-model="fields.email"
                    label="Email"
                    label-class="required"
                    type="text"
                    id="email"
                    field="email"
                    placeholder="Enter email"
                    :errors="formValidation.errors"
                    autocomplete="off"
                ></Field>
            </div>
            <div class="col-lg-6 mb-2">
                <Field
                    v-model="fields.joining_date"
                    label="Joining Date"
                    label-class="required"
                    type="date"
                    id="joining_date"
                    field="joining_date"
                    :errors="formValidation.errors"
                    autocomplete="off"
                ></Field>
            </div>
        </div>

        <template #modal_footer>
            <button
                class="btn btn-success btn-sm"
                type="button"
                @click="handleSubmit"
            >
                {{ button_text }}
            </button>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive } from "vue";
import Modal from "../../components/Modal.vue";
import { FormValidation } from "../../helpers/Validation";
import { resetObjectKeys } from "../../helpers/utils";
import Field from "../../helpers/Field.vue";
import axios from "axios";
import { clientRoutes } from "../../routes/ClientRoutes";
import { toastAlert } from "../../helpers/alert";

let client_form = ref(null);

const emits = defineEmits(["reload"]);

let title_text = ref('');
let button_text = ref('');

let fields = reactive({
    id: "",
    name: "",
    phone_number: "",
    email: "",
    joining_date: "",
});

function openModal(client) {
    clearFormData();
    client_form.value.open();

    if (client) {
        fields.id = client.id;
        fields.name = client.name;
        fields.phone_number = client.phone_number;
        fields.email = client.email;
        fields.joining_date = client.joining_date;

        title_text.value = `Update client : ${client.name}`;
        button_text.value = 'Update';
    } else {
        title_text.value = "Create client";
        button_text.value = 'Submit';
    }
}


function clearFormData() {
    formValidation.reset();
    resetObjectKeys(fields);
}

function handleSubmit() {
    formValidation.validate();

    if (formValidation.isValid()) {

        axios
            .post(clientRoutes.createOrUpdateClient(fields.id ?? null), fields)
            .then((response) => {
                client_form.value.close();
                emits("reload", response.data.user_details);
                toastAlert({ title: response.data.message });
            })
            .catch(function (error) {
                if (error.response.status === 422) {
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
        name: {
            required: "Name field is required.",
        },
        phone_number: {
            required: "Phone number field is required.",
        },
        email: {
            required: "Email field is required.",
        },
        joining_date: {
            required: "Joining date field is required.",
        },
    })
);
</script>
