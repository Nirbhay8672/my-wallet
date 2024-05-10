<template>
    <inertia-head title="Clients" />
    <main-page>
        <div class="container-fluid p-0 mb-3">
            <div class="row mb-2 gy-3">
                <div class="col-sm-6">
                    <h5 class="d-inline align-middle">Clients</h5>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-end gy-3">
                        <button
                            class="btn btn-primary btn-sm ms-sm-3 ms-md-3 ms-lg-3 mt-sm-2 mt-3 mt-md-0 mt-lg-0 mt-sm-0"
                            @click="openForm()"
                            v-if="hasPermission('add_client')"
                        >
                            <i class="fa fa-plus-circle"></i>
                            <span class="ms-2">Add Client</span>
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
                                            <th>Name</th>
                                            <th>Phone No.</th>
                                            <th>Email</th>
                                            <th>Joining Date</th>
                                            <th>Total Pay Amount</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-if="clients.length > 0">
                                            <tr
                                                v-for="(
                                                    client, index
                                                ) in clients"
                                                :key="`client_${index}`"
                                            >
                                                <td style="min-width: 100px">
                                                    {{ index + 1 }}
                                                </td>
                                                <td style="min-width: 200px">
                                                    {{ client.name }}
                                                </td>
                                                <td style="min-width: 150px">
                                                    {{ client.phone_number }}
                                                </td>
                                                <td style="min-width: 250px">
                                                    {{ client.email }}
                                                </td>
                                                <td style="min-width: 200px">
                                                    {{ client.joining_date }}
                                                </td>
                                                <td style="min-width: 200px">   
                                                    <b>{{ client.total_pay_amount ?? '0' }}</b>
                                                </td>
                                                <td
                                                    class="text-center"
                                                    style="min-width: 200px"
                                                >
                                                    <button
                                                        class="btn btn-outline-info btn-sm"
                                                        v-if="hasPermission('client_details')"
                                                        @click="
                                                            viewClient(client)
                                                        "
                                                    >
                                                        <i
                                                            class="fa fa-eye"
                                                        ></i>
                                                    </button>
                                                    <button
                                                        class="btn btn-outline-primary btn-sm ms-3"
                                                        v-if="hasPermission('update_details')"
                                                        @click="
                                                            openForm(client)
                                                        "
                                                    >
                                                        <i
                                                            class="fa fa-pencil"
                                                        ></i>
                                                    </button>
                                                    <button
                                                        class="btn btn-outline-danger btn-sm ms-3"
                                                        v-if="hasPermission('delete_details')"
                                                        @click="
                                                            deleteClient(client)
                                                        "
                                                    >
                                                        <i
                                                            class="fa fa-trash"
                                                        ></i>
                                                    </button>
                                                </td>
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
                        <div class="row gy-3" v-if="clients.length > 0">
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
            <client-form ref="client_form" @reload="reloadTable"> </client-form>
            <client-view ref="client_view"> </client-view>
        </teleport>
    </main-page>
</template>

<script setup>
import { ref, onMounted, reactive } from "vue";
import axios from "axios";
import { clientRoutes } from "../../routes/ClientRoutes";
import { confirmAlert, toastAlert } from "../../helpers/alert";
import ClientForm from "./Form.vue";
import ClientView from "./View.vue";

const props = defineProps({
    auth: {
        type: Object,
        required: true,
    },
});

let clients = ref([]);
let loader = ref(true);

let client_form = ref(null);
let client_view = ref(null);

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
    client_form.value.openModal(client);
}

function viewClient(client = null) {
    client_view.value.openModal(client);
}

function reloadTable() {
    axios
        .post(clientRoutes.datatable, fields)
        .then((response) => {
            clients.value = response.data.clients;
            fields.total_record = response.data.total;
            fields.total_pages = response.data.total_pages;
            fields.start_index = response.data.start_index;
            fields.end_index = response.data.end_index;
            loader.value = false;
        })
        .catch(function (error) {
            if (error.response.status === 422) {
                console.log("somthing went wrong");
            }
        });
}

function deleteClient(client) {
    confirmAlert({
        title: "Delete",
        icon: "question",
        html: `Are you sure, you want to delete <strong> ${client.name} </strong> client ?`,
    }).then((result) => {
        if (result.isConfirmed) {
            axios
                .get(clientRoutes.delete(client.id))
                .then((response) => {
                    toastAlert({ title: response.data.message });
                    reloadTable();
                })
                .catch(function (error) {
                    if (error.response.status === 422) {
                        toastAlert({
                            title: error.response.data.message,
                            icon: "error",
                        });
                    }
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
