<template>
    <inertia-head title="Banks" />
    <main-page>
        <div class="row justify-content-between gy-3 mb-3">
            <div class="col-md-auto me-auto">
                <div class="pagetitle">
                    <h4>Banks</h4>
                </div>
            </div>
            <div class="col-md-auto ms-auto">
                <button
                    class="btn btn-primary btn-sm"
                    @click="openForm()"
                    v-if="hasPermission('add_bank')"
                >
                    <i class="fa fa-plus"></i>
                    <span class="ms-2">Add Bank</span>
                </button>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div
                            class="card-body p-4"
                            style="height: 200px"
                            v-if="loader"
                        >
                            <div class="pre-loader">
                                <div class="circle-line">
                                    <img
                                        class="loader-icon-image circle-one"
                                        :src="`${$page.props.url}/images/coin.png`"
                                        alt="coin"
                                    />
                                    <img
                                        class="loader-icon-image circle-two"
                                        :src="`${$page.props.url}/images/coin.png`"
                                        alt="coin"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4" v-else>
                            <div class="row justify-content-between gy-3 mb-3">
                                <div class="col-md-auto me-auto">
                                    <select
                                        class="form-select form-control"
                                        id="per_page"
                                        v-model="fields.per_page"
                                        @change="changeMainFilter()"
                                    >
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="15">15</option>
                                        <option value="20">20</option>
                                    </select>
                                </div>
                                <div class="col-md-auto ms-auto">
                                    <input
                                        type="text"
                                        id="search_input"
                                        placeholder="Search..."
                                        class="form-control"
                                        v-model="fields.search"
                                        @keyup="changeMainFilter()"
                                    />
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Logo</th>
                                                <th>Name</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-if="banks.length > 0">
                                                <tr v-for="(bank, index) in banks" :key="`bank_${index}`">
                                                    <td style="min-width: 100px">
                                                        <img
                                                            :src="bank.logo ? `${$page.props.url}/${bank.logo}` : ''"
                                                            class="bank-logo-preview"
                                                            alt="bank logo"
                                                            style="width: 50px; height: 50px; object-fit: contain; border-radius: 5px;"
                                                        />
                                                    </td>
                                                    <td style="min-width: 100px">
                                                        {{ bank.name }}
                                                    </td>
                                                    <td style="min-width: 200px" class="text-center">
                                                        <button
                                                            class="btn bg-gradient-info btn-sm"
                                                            @click="openForm(bank)"
                                                            v-if="hasPermission('edit_bank')"
                                                        >
                                                            <i class="fa fa-pencil"></i>
                                                        </button>
                                                        <button
                                                            class="btn bg-gradient-danger btn-sm ms-3"
                                                            @click="deleteBank(bank)"
                                                            v-if="hasPermission('delete_bank')"
                                                        >
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                            <template v-else>
                                                <tr class="text-center">
                                                    <td colspan="3">
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
                            <div class="row gy-3" v-if="banks.length > 0">
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
                                                <span class="page-link"><i class="fa fa-angle-left"></i></span>
                                            </li>
                                            <template v-for="page_number in fields.total_pages" :key="`page_number_${page_number}`">
                                                <li
                                                    class="page-item"
                                                    :class="page_number === fields.page ? 'active' : ''"
                                                    @click="setPage(page_number)"
                                                >
                                                    <span class="page-link cursor-pointer text-white">{{ page_number }}</span>
                                                </li>
                                            </template>
                                            <li class="page-item" @click="next()">
                                                <span class="page-link"><i class="fa fa-angle-right"></i></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <teleport to="body">
            <BankModal
                ref="bank_form"
                :modal-id="'bankModal'"
                :bank="selectedBank"
                :is-edit="!!selectedBank"
                @bank-saved="reloadTable()"
            />
        </teleport>
    </main-page>
</template>

<script setup>
import { ref, onMounted, reactive } from "vue";
import axios from "axios";
import { toastAlert, confirmAlert } from "../../helpers/alert";
import BankModal from "./BankModal.vue";

const props = defineProps({
    auth: {
        type: Object,
        required: true,
    },
});

let banks = ref([]);
let loader = ref(true);
let bank_form = ref(null);
let selectedBank = ref(null);

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
    }, 500);
});

function changeMainFilter() {
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

function openForm(bank = null) {
    selectedBank.value = bank;
    if (bank_form.value && bank_form.value.openModal) {
        bank_form.value.openModal(bank);
    }
}

function reloadTable() {
    loader.value = true;
    axios
        .post("/banks/datatable", fields)
        .then((response) => {
            banks.value = response.data.banks;
            fields.total_record = response.data.total;
            fields.total_pages = response.data.total_pages;
            fields.start_index = response.data.start_index;
            fields.end_index = response.data.end_index;
            loader.value = false;
        })
        .catch(function (error) {
            loader.value = false;
            toastAlert({
                title: "Something went wrong.",
                icon: "error",
            });
        });
}

function deleteBank(bank) {
    confirmAlert({
        title: "Delete",
        icon: "question",
        html: `Are you sure, you want to delete <strong> ${bank.name} </strong> bank?`,
    }).then((result) => {
        if (result.isConfirmed) {
            axios
                .get(`/banks/delete/${bank.id}`)
                .then((response) => {
                    toastAlert({ title: response.data.message });
                    reloadTable();
                })
                .catch(function (error) {
                    toastAlert({
                        title: error.response?.data?.message || "Something went wrong.",
                        icon: "error",
                    });
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

<style scoped>
.bank-logo-preview {
  border-radius: 5px !important;
}
</style> 
