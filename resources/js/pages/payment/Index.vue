<template>
    <inertia-head title="Payments" />
    <main-page>
        <div class="container-fluid p-0 mb-3">
            <div class="row mb-2 gy-3">
                <div class="col-sm-6">
                    <h5 class="d-inline align-middle">Payments</h5>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-end gy-3">
                        <button
                            class="btn btn-primary btn-sm ms-sm-3 ms-md-3 ms-lg-3 mt-sm-2 mt-3 mt-md-0 mt-lg-0 mt-sm-0"
                            @click="openForm()"
                            v-if="hasPermission('add_payment')"
                        >
                            <i class="fa fa-plus-circle"></i>
                            <span class="ms-2">Add Payment</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div
                        class="card-body p-4"
                        v-if="loader"
                        style="height: 200px"
                    >
                        <div class="overflow dark" id="preload">
                            <div class="circle-line">
                                <div class="circle-red"><b>P</b></div>
                                <div class="circle-blue"><b>M</b></div>
                                <div class="circle-red"><b>S</b></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4" v-else>
                        <div class="row mt-2 justify-content-between gy-3 mb-3">
                            <div class="col-md-auto me-auto">
                                <div class="dt-length">
                                    <select
                                        class="form-select form-control"
                                        id="per_page"
                                        v-model="fields.per_page"
                                        @change="chnageMainFilter()"
                                    >
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="15">15</option>
                                        <option value="20">20</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-auto ms-auto">
                                <div class="dt-search">
                                    <input
                                        type="text"
                                        id="search_input"
                                        placeholder="Search..."
                                        class="form-control"
                                        v-model="fields.search"
                                        @keyup="chnageMainFilter()"
                                    />
                                </div>
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
                                        <tr class="custom-table-heading">
                                            <th>Sr No.</th>
                                            <th>Client Name</th>
                                            <th>Webiste Name</th>
                                            <th>Payment Date Time</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Package Type</th>
                                            <th>Invoice</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-if="payments.length > 0">
                                            <tr
                                                v-for="(
                                                    payment, index
                                                ) in payments"
                                                :key="`payment_${index}`"
                                            >
                                                <td style="min-width: 100px">
                                                    {{ index + 1 }}
                                                </td>
                                                <td style="min-width: 200px">
                                                    {{ payment.client.name }}
                                                </td>
                                                <td style="min-width: 150px">
                                                    {{ payment.website ? payment.website.website_name : '-' }}
                                                </td>
                                                <td style="min-width: 250px">
                                                    {{ payment.payment_date }} {{ payment.payment_time }}
                                                </td>
                                                <td style="min-width: 100px">
                                                    {{ payment.amount }}
                                                </td>
                                                <td style="min-width: 100px" :class="payment.status == 'Pending' ? 'text-danger' : (payment.status == 'Success' ? 'text-success' : '')">
                                                    <b>{{ payment.status ?? '-' }}</b>
                                                </td>
                                                <td style="min-width: 100px" :class="getPackageTypeColor(payment.package_type)">
                                                    <b>{{ payment.package_type ?? '-' }}</b>
                                                </td>
                                                <td style="min-width: 100px">
                                                    <button class="btn btn-outline-danger btn-sm" @click="generateInvoice(payment.id)"><i class="fa fa-file-pdf-o"></i></button>
                                                </td>
                                            </tr>
                                        </template>
                                        <template v-else>
                                            <tr
                                                style="width: 100%"
                                                class="text-center"
                                            >
                                                <td colspan="10">
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
                        <div class="row gy-3" v-if="payments.length > 0">
                            <div class="col-md-auto me-auto">
                                <div>
                                    Showing {{ fields.start_index }} to
                                    {{ fields.end_index }} of
                                    {{ fields.total_record }} Results
                                </div>
                            </div>
                            <div class="col-md-auto ms-auto">
                                <div class="dt-paging paging_full_numbers">
                                    <ul class="pagination">
                                        <li class="page-item" @click="prev()">
                                            <span class="page-link"
                                                ><i
                                                    class="fa fa-angle-double-left"
                                                ></i
                                            ></span>
                                        </li>
                                        <template
                                            v-for="page_number in fields.total_pages"
                                            :key="`page_number_${page_number}`"
                                        >
                                            <li
                                                class="page-item"
                                                :class="
                                                    page_number === fields.page
                                                        ? 'active'
                                                        : ''
                                                "
                                                @click="setPage(page_number)"
                                            >
                                                <span
                                                    class="page-link cursor-pointer"
                                                    >{{ page_number }}</span
                                                >
                                            </li>
                                        </template>

                                        <li class="page-item" @click="next()">
                                            <span class="page-link"
                                                ><i
                                                    class="fa fa-angle-double-right"
                                                ></i
                                            ></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <teleport to="body">
            <payment-form
                ref="payment_form"
                :clients="$page.props.clients"
                @reload="reloadTable"
            ></payment-form>
        </teleport>
    </main-page>
</template>
    
<script setup>
import { ref, onMounted, reactive } from "vue";
import axios from "axios";
import { paymentRoutes } from "../../routes/PaymentRoutes";
import PaymentForm from "./Form.vue";
import { getPackageTypeColor } from "../../helpers/utils";
import { toastAlert } from "../../helpers/alert";

const props = defineProps({
    auth: {
        type: Object,
        required: true,
    },
});

let payments = ref([]);
let loader = ref(true);

let payment_form = ref(null);

let fields = reactive({
    search: "",
    per_page: 10,
    total_record: 0,
    total_pages: 1,
    page: 1,
    start_index: 0,
    end_index: 0,
});

onMounted(() => {
    setTimeout(function () {
        reloadTable();
    }, 1000);
});

function chnageMainFilter() {
    fields.page = 1;
    reloadTable();
}

function setPage(page_number = 1) {
    fields.page = page_number;
    reloadTable();
}

function prev() {
    if (fields.page === 1) {
        return;
    }
    fields.page--;
    reloadTable();
}

function next() {
    if (fields.page === fields.total_pages) {
        return;
    }
    fields.page++;
    reloadTable();
}

function openForm(client = null) {
    payment_form.value.openModal(client);
}

function generateInvoice(payment_id)
{
    axios.get(paymentRoutes.generateInvoice(payment_id)).then((response) => {
        toastAlert({ title: response.data.message });
    }).catch(function (error) {
        if (error.response.status === 500) {
            toastAlert({
                title: "somthing went wrong.",
                icon: "error",
            });
        }
    });
}

function reloadTable() {
    axios
        .post(paymentRoutes.datatable, fields)
        .then((response) => {
            payments.value = response.data.payments;
            fields.total_record = response.data.total;
            fields.total_pages = response.data.total_pages;
            fields.start_index = response.data.start_index;
            fields.end_index = response.data.end_index;
            loader.value = false;
        })
        .catch(function (error) {
            if (error.response.status === 422) {
                toastAlert({
                    title: "somthing went wrong.",
                    icon: "error",
                });
            }
        });
}

function hasPermission(permission_name) {
    let permission_obj = props.auth.user.roles[0].permissions.find(
        (permission) => permission.name == permission_name
    );

    return permission_obj ? true : false;
}

</script>
