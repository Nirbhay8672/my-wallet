<template>
    <Modal ref="payment_form" :id="'payment_form'">
        <template #modal_title>
            <span>Create Payment</span>
        </template>

        <div class="row gy-2">
            <div class="col-lg-6 mb-2">
                <Field
                    label="Client"
                    label-class="required"
                    field="client_id"
                    id="client_id"
                    :errors="formValidation.errors"
                    no-input
                >
                    <template #input="{ hasError }">
                        <select
                            class="form-control form-control-solid form-select"
                            id="client_id"
                            v-model="fields.client_id"
                            @change="setWebsites()"
                            :class="{ 'is-invalid': hasError }"
                        >
                            <option value="">-- Select Client --</option>
                            <template v-for="(client, index) in clients" :key="`client_${index}`">
                                <option :value="client.id">{{ client.name }}</option>
                            </template>
                        </select>
                    </template>
                </Field>
            </div>
            <div class="col-lg-6 mb-2">
                <Field
                    label="Website"
                    field="website_id"
                    id="website_id"
                    :errors="formValidation.errors"
                    no-input
                >
                    <template #input="{ hasError }">
                        <select
                            class="form-control form-control-solid form-select"
                            id="website_id"
                            v-model="fields.website_id"
                            :class="{ 'is-invalid': hasError }"
                            :disabled="fields.client_id ? false : true"
                        >
                            <option value="">-- Select Website --</option>
                            <template v-for="(website, index) in websites" :key="`website_${index}`">
                                <option :value="website.id">{{ website.website_name }}</option>
                            </template>
                        </select>
                    </template>
                </Field>
            </div>
            <div class="col-lg-6 mb-2">
                <Field
                    v-model="fields.payment_date"
                    label="Payment Date"
                    label-class="required"
                    type="date"
                    id="payment_date"
                    field="payment_date"
                    :errors="formValidation.errors"
                    autocomplete="off"
                ></Field>
            </div>
            <div class="col-lg-6 mb-2">
                <Field
                    v-model="fields.payment_time"
                    label="Payment Time"
                    label-class="required"
                    type="time"
                    id="payment_time"
                    field="payment_time"
                    :errors="formValidation.errors"
                    autocomplete="off"
                ></Field>
            </div>
            <div class="col-lg-6 mb-2">
                <Field
                    label="Package Type"
                    label-class="required"
                    field="package_type"
                    id="package_type"
                    :errors="formValidation.errors"
                    no-input
                >
                    <template #input="{ hasError }">
                        <select
                            class="form-control form-control-solid form-select"
                            id="package_type"
                            v-model="fields.package_type"
                            :class="{ 'is-invalid': hasError }"
                        >
                            <option value="">-- Select Type --</option>
                            <option value="Small">Small</option>
                            <option value="Medium">Medium</option>
                            <option value="Big">Big</option>
                        </select>
                    </template>
                </Field>
            </div>
            <div class="col-lg-6 mb-2">
                <Field
                    v-model="fields.amount"
                    label="Amount"
                    label-class="required"
                    type="text"
                    id="amount"
                    field="amount"
                    placeholder="Enter Amount"
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
                Submit
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
import { toastAlert } from "../../helpers/alert";
import { paymentRoutes } from "../../routes/PaymentRoutes";

let props = defineProps({
    clients : {
        required : true,
        type : Array,
        default : []
    }
});

let payment_form = ref(null);
let websites = ref([]);

const emits = defineEmits(["reload"]);

let fields = reactive({
    client_id: "",
    website_id: "",
    payment_date: "",
    payment_time: "",
    package_type: "",
    amount: "",
});

function openModal() {
    clearFormData();
    payment_form.value.open();
}

function setWebsites() {
    websites.value = [];
    fields.website_id = '';
    if(fields.client_id > 0) {
        let client_obj = props.clients.find((client) => client.id == fields.client_id);
        websites.value = client_obj.websites;
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
            .post(paymentRoutes.createPayment, fields)
            .then((response) => {
                payment_form.value.close();
                emits("reload");
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
        client_id: {
            required: "Client field is required.",
        },
        payment_date: {
            required: "Payment date field is required.",
        },
        payment_time: {
            required: "Payment time field is required.",
        },
        package_type: {
            required: "Package type field is required.",
        },
        amount: {
            required: "Amount field is required.",
        },
    })
);
</script>
