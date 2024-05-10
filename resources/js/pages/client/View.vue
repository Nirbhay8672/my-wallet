<template>
    <Modal ref="client_view" :id="'client_view'" :size="'extra_large'">
        <template #modal_title>
            <span>Client Info : {{ client_obj ? client_obj.name : '' }}</span>
        </template>

        <div class="row gy-3" v-if="client_obj">
            <div class="col-xxl-6">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-6">
                        <b>Name</b>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <span>{{ client_obj.name }}</span>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12 col-md-6 col-lg-6">
                        <b>Phone Number</b>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <span>{{ client_obj.phone_number }}</span>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12 col-md-6 col-lg-6">
                        <b>Email</b>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <span>{{ client_obj.email }}</span>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12 col-md-6 col-lg-6">
                        <b>Joining Date</b>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <span>{{ client_obj.joining_date_format }}</span>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-6">
                        <b>Total Pay Amount</b>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <span>{{ client_obj.total_pay_amount }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4" v-if="client_obj">
            <div class="col">
                <h5 class="text-center">Transactions</h5>
                <div class="row mt-3 mb-2">
                    <div class="col-12 col-lg-4 col-md-4">
                        <select
                            name="website_filter"
                            id="website_filter"
                            class="form-control form-select"
                            v-model="website_filter"
                            @change="getPaymentDetails()"
                        >
                            <option value="">-- Select Website --</option>
                            <option :value="website.id" v-for="(website , index) in client_obj.websites" :key="`webiste_${index}`">{{ website.website_name }}</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="table-responsive">
                        <table
                            class="table"
                            style="
                                border-collapse: separate;
                                border-spacing: 0 10px;
                            "
                        >
                            <thead>
                                <tr
                                    style="
                                        background-color: rgb(
                                            201 201 201
                                        );
                                    "
                                >
                                    <th>Sr No.</th>
                                    <th>Website Name</th>
                                    <th>Payment Date</th>
                                    <th>Payment Time</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="payments.length > 0">
                                    <tr v-for="(payment, index) in payments"
                                        :key="`payment_${index}`">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ payment.website_name ?? '-' }}</td>
                                        <td>{{ payment.payment_date }}</td>
                                        <td>{{ payment.payment_time }}</td>
                                        <td>{{ payment.amount }}</td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr
                                        style="width: 100%"
                                        class="text-center"
                                    >
                                        <td colspan="6">
                                            <img
                                                alt=""
                                                :src="`${$page.props.url}/images/no_found.png`"
                                                style="width: 200px"
                                            />
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import Modal from "../../components/Modal.vue";
import { clientRoutes } from "../../routes/ClientRoutes";

let client_view = ref(null);

let client_obj = ref(null);
let payments = ref([]);

let website_filter = ref('');

function openModal(client) {
    client_view.value.open();
    client_obj.value = client;
    website_filter.value = '';
    getPaymentDetails();
}

function getPaymentDetails() {
    axios.post(clientRoutes.payments,{
        'client_id' : client_obj.value.id,
        'website_id' : website_filter.value ? website_filter.value : null, 
    })
    .then((response) => {
        payments.value = response.data.payments;
    })
    .catch(function (error) {
        if (error.response.status === 422) {
            console.log("somthing went wrong");
        }
    });
}

defineExpose({
    openModal,
});

</script>
