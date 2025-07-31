<template>
    <inertia-head title="Expense Entries" />
    <main-page :page_name="'Expense Entries'">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <div class="col-md-auto me-auto">
                <div class="pagetitle">
                    <h4>Expenses</h4>
                </div>
            </div>
            <button class="btn btn-primary btn-sm" @click="editEntry(null)">
                <i class="fa fa-plus me-1"></i>Add Expense Entry
            </button>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Account</label>
                        <select v-model="filters.account_id" class="form-control form-control-solid form-select" @change="applyFilters">
                            <option value="">All Accounts</option>
                            <option v-for="account in accounts" :key="account.id" :value="account.id">
                                {{ account.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Expense Type</label>
                        <select v-model="filters.expense_type_id" class="form-control form-control-solid form-select" @change="applyFilters">
                            <option value="">All Types</option>
                            <option v-for="type in expenseTypes" :key="type.id" :value="type.id">
                                {{ type.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date Range</label>
                        <select v-model="filters.date_range" class="form-control form-control-solid form-select" @change="applyFilters">
                            <option value="">All Time</option>
                            <option value="this_week">This Week</option>
                            <option value="this_month">This Month</option>
                            <option value="this_year">This Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div class="col-md-3" v-if="filters.date_range === 'custom'">
                        <label class="form-label">Custom Date Range</label>
                        <div class="input-group">
                            <input type="date" v-model="filters.start_date" class="form-control" @change="applyFilters">
                            <span class="input-group-text">to</span>
                            <input type="date" v-model="filters.end_date" class="form-control" @change="applyFilters">
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
            <div class="card-body" v-else>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Title</th>
                                <th>Account</th>
                                <th>Expense Type</th>
                                <th>Amount</th>
                                <th>Proof</th>
                                <th class="text-center align-middle">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="entry in entries.data" :key="entry.id">
                                <td style="min-width: 120px">
                                    <div>{{ formatDate(entry.date) }}</div>
                                    <small class="text-muted">{{ entry.time }}</small>
                                </td>
                                <td style="min-width: 150px">{{ entry.title }}</td>
                                <td style="min-width: 120px">
                                    <span class="badge bg-primary">{{ entry.account.name }}</span>
                                </td>
                                <td style="min-width: 120px">
                                    <span class="badge bg-danger">{{ entry.expense_type.name }}</span>
                                </td>
                                <td style="min-width: 120px">
                                    <span class="text-danger fw-bold">-₹{{ formatNumber(entry.amount) }}</span>
                                </td>
                                <td style="min-width: 100px">
                                    <a v-if="entry.proof" :href="`${$page.props.url}/${entry.proof}`" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="fa fa-file me-1"></i>View
                                    </a>
                                    <span v-else class="text-muted">No proof</span>
                                </td>
                                <td style="min-width: 100px;" class="text-center align-middle">
                                    <button class="btn btn-sm btn-primary me-2" @click="editEntry(entry)">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" @click="deleteEntry(entry)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!entries.data.length">
                                <td colspan="7" class="text-center align-middle">No expense entries found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ entries.from }} to {{ entries.to }} of {{ entries.total }} entries
                    </div>
                    <nav v-if="entries.last_page > 1">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item" :class="{ disabled: !entries.prev_page_url }">
                                <a class="page-link" href="#" @click.prevent="changePage(entries.current_page - 1)">
                                    Previous
                                </a>
                            </li>
                            <li v-for="page in getPageNumbers()" :key="page" class="page-item" :class="{ active: page === entries.current_page }">
                                <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                            </li>
                            <li class="page-item" :class="{ disabled: !entries.next_page_url }">
                                <a class="page-link" href="#" @click.prevent="changePage(entries.current_page + 1)">
                                    Next
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <ExpenseEntryForm ref="expenseEntryForm" modal-id="expenseEntryModal" :entry="selectedEntry" :is-edit="!!selectedEntry" :accounts="accounts" :expense-types="expenseTypes" :url="page.props.url" @reload="handleEntrySaved" />
    </main-page>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';
import ExpenseEntryForm from './Form.vue';
import { confirmAlert, toastAlert } from '../../helpers/alert';

const page = usePage();

const props = defineProps({
    entries: Object,
    accounts: Array,
    expenseTypes: Array,
    filters: Object,
});

const selectedEntry = ref(null);
const expenseEntryForm = ref(null);
const loader = ref(true);

const filters = reactive({
    account_id: props.filters?.account_id || '',
    expense_type_id: props.filters?.expense_type_id || '',
    date_range: props.filters?.date_range || '',
    start_date: props.filters?.start_date || '',
    end_date: props.filters?.end_date || '',
});

const editEntry = (entry) => {
    selectedEntry.value = entry;
    if (expenseEntryForm.value) {
        expenseEntryForm.value.openModal(entry);
    }
};

const deleteEntry = (entry) => {
    confirmAlert({
        title: "Delete Expense Entry",
        icon: "question",
        html: `Are you sure, you want to delete <strong>${entry.title}</strong>?`,
    }).then((result) => {
        if (result.isConfirmed) {
            axios.get(`/expense-entries/delete/${entry.id}`)
                .then((response) => {
                    toastAlert({ title: response.data.message });
                    window.location.reload();
                })
                .catch(function (error) {
                    if (error.response && error.response.status === 422) {
                        toastAlert({
                            title: error.response.data.message,
                            icon: "error",
                        });
                    } else {
                        toastAlert({
                            title: "Something went wrong while deleting the expense entry.",
                            icon: "error",
                        });
                    }
                });
        }
    });
};

const applyFilters = () => {
    const params = {};
    if (filters.account_id) params.account_id = filters.account_id;
    if (filters.expense_type_id) params.expense_type_id = filters.expense_type_id;
    if (filters.date_range) params.date_range = filters.date_range;
    if (filters.start_date) params.start_date = filters.start_date;
    if (filters.end_date) params.end_date = filters.end_date;

    router.get('/expense-entries/index', params);
};

const changePage = (page) => {
    const params = { page, ...filters };
    router.get('/expense-entries/index', params);
};

onMounted(() => {
    setTimeout(function () {
        loader.value = false;
    }, 1000);
});

const handleEntrySaved = () => {
    selectedEntry.value = null;
    window.location.reload();
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-IN');
};

const formatNumber = (number) => {
    return parseFloat(number).toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

const getPageNumbers = () => {
    const current = props.entries.current_page;
    const last = props.entries.last_page;
    const delta = 2;
    const range = [];
    const rangeWithDots = [];

    for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
        range.push(i);
    }

    if (current - delta > 2) {
        rangeWithDots.push(1, '...');
    } else {
        rangeWithDots.push(1);
    }

    rangeWithDots.push(...range);

    if (current + delta < last - 1) {
        rangeWithDots.push('...', last);
    } else {
        rangeWithDots.push(last);
    }

    return rangeWithDots.filter((item, index, array) => array.indexOf(item) === index);
};
</script>
