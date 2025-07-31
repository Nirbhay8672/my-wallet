<template>
    <inertia-head title="Expense Types" />
    <main-page :page_name="'Expense Types'">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <div class="col-md-auto me-auto">
                <div class="pagetitle">
                    <h4>Expense Types</h4>
                </div>
            </div>
            <button class="btn btn-primary btn-sm" @click="editExpenseType(null)">
                <i class="fa fa-plus"></i>
            </button>
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
                                <th>Icon</th>
                                <th>Name</th>
                                <th class="text-center align-middle">Status</th>
                                <th class="text-center align-middle">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="expenseType in expenseTypes" :key="expenseType.id">
                                <td style="min-width: 200px">
                                    <img :src="expenseType.icon" :alt="expenseType.name" class="avtar"
                                        style="width: 50px; height: 50px; object-fit: contain;">
                                </td>
                                <td style="min-width: 100px">{{ expenseType.name }}</td>
                                <td style="min-width: 100px" class="text-center align-middle">
                                    <div class="d-flex justify-content-center">
                                        <ToggleButton
                                            v-model="expenseType.active"
                                            color="success"
                                            size="small"
                                            @change="toggleExpenseTypeStatus(expenseType)"
                                        />
                                    </div>
                                </td>
                                <td style="min-width: 100px;" class="text-center align-middle">
                                    <button class="btn btn-sm btn-primary me-2" @click="editExpenseType(expenseType)">
                                        <i
                                            class="fa fa-pencil"
                                        ></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" @click="deleteExpenseType(expenseType)">
                                        <i
                                            class="fa fa-trash"
                                        ></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!expenseTypes.length">
                                <td colspan="4" class="text-center align-middle">No expense types found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <ExpenseTypeForm ref="expenseTypeForm" modal-id="expenseTypeModal" :expense-type="selectedExpenseType" :is-edit="!!selectedExpenseType" @reload="handleExpenseTypeSaved" />
    </main-page>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import ExpenseTypeForm from './Form.vue';
import ToggleButton from '../../components/ToggleButton.vue';
import { confirmAlert, toastAlert } from '../../helpers/alert';

const props = defineProps({
    expenseTypes: Array,
});

const selectedExpenseType = ref(null);
const expenseTypeForm = ref(null);
const loader = ref(true);

const editExpenseType = (expenseType) => {
    selectedExpenseType.value = expenseType;
    // Call openModal on the ExpenseTypeForm component
    if (expenseTypeForm.value) {
        expenseTypeForm.value.openModal(expenseType);
    }
};

const toggleExpenseTypeStatus = (expenseType) => {
    axios.post(`/expense-types/${expenseType.id}/toggle-status`, {
        active: expenseType.active
    })
    .then((response) => {
        toastAlert({ title: response.data.message });
    })
    .catch(function (error) {
        // Revert the toggle if there's an error
        expenseType.active = !expenseType.active;
        if (error.response && error.response.status === 422) {
            toastAlert({
                title: error.response.data.message,
                icon: "error",
            });
        } else {
            toastAlert({
                title: "Something went wrong while updating the expense type status.",
                icon: "error",
            });
        }
    });
};

const deleteExpenseType = (expenseType) => {
    confirmAlert({
        title: "Delete Expense Type",
        icon: "question",
        html: `Are you sure, you want to delete <strong>${expenseType.name}</strong> expense type?`,
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(`/expense-types/${expenseType.id}`)
                .then((response) => {
                    toastAlert({ title: response.data.message });
                    // Reload the page to refresh the expense types list
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
                            title: "Something went wrong while deleting the expense type.",
                            icon: "error",
                        });
                    }
                });
        }
    });
};

onMounted(() => {
    setTimeout(function () {
        loader.value = false;
    }, 1000);
});

const handleExpenseTypeSaved = () => {
    selectedExpenseType.value = null;
    window.location.reload();
};
</script>
