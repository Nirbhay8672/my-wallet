<template>
    <inertia-head title="Income Types" />
    <main-page :page_name="'Income Types'">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <div class="col-md-auto me-auto">
                <div class="pagetitle">
                    <h4>Income Types</h4>
                </div>
            </div>
            <button class="btn btn-primary btn-sm" @click="editIncomeType(null)">
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
                            <tr v-for="incomeType in incomeTypes" :key="incomeType.id">
                                <td style="min-width: 200px">
                                    <img :src="incomeType.icon" :alt="incomeType.name" class="avtar"
                                        style="width: 50px; height: 50px; object-fit: contain;">
                                </td>
                                <td style="min-width: 100px">{{ incomeType.name }}</td>
                                <td style="min-width: 100px" class="text-center align-middle">
                                    <div class="d-flex justify-content-center">
                                        <ToggleButton
                                            v-model="incomeType.active"
                                            color="success"
                                            size="small"
                                            @change="toggleIncomeTypeStatus(incomeType)"
                                        />
                                    </div>
                                </td>
                                <td style="min-width: 100px;" class="text-center align-middle">
                                    <button class="btn btn-sm btn-primary me-2" @click="editIncomeType(incomeType)">
                                        <i
                                            class="fa fa-pencil"
                                        ></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" @click="deleteIncomeType(incomeType)">
                                        <i
                                            class="fa fa-trash"
                                        ></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!incomeTypes.length">
                                <td colspan="4" class="text-center align-middle">No income types found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <IncomeTypeForm ref="incomeTypeForm" modal-id="incomeTypeModal" :income-type="selectedIncomeType" :is-edit="!!selectedIncomeType" @reload="handleIncomeTypeSaved" />
    </main-page>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import IncomeTypeForm from './Form.vue';
import ToggleButton from '../../components/ToggleButton.vue';
import { confirmAlert, toastAlert } from '../../helpers/alert';

const props = defineProps({
    incomeTypes: Array,
});

const selectedIncomeType = ref(null);
const incomeTypeForm = ref(null);
const loader = ref(true);

const editIncomeType = (incomeType) => {
    selectedIncomeType.value = incomeType;
    // Call openModal on the IncomeTypeForm component
    if (incomeTypeForm.value) {
        incomeTypeForm.value.openModal(incomeType);
    }
};

const toggleIncomeTypeStatus = (incomeType) => {
    axios.post(`/income-types/${incomeType.id}/toggle-status`, {
        active: incomeType.active
    })
    .then((response) => {
        toastAlert({ title: response.data.message });
    })
    .catch(function (error) {
        // Revert the toggle if there's an error
        incomeType.active = !incomeType.active;
        if (error.response && error.response.status === 422) {
            toastAlert({
                title: error.response.data.message,
                icon: "error",
            });
        } else {
            toastAlert({
                title: "Something went wrong while updating the income type status.",
                icon: "error",
            });
        }
    });
};

const deleteIncomeType = (incomeType) => {
    confirmAlert({
        title: "Delete Income Type",
        icon: "question",
        html: `Are you sure, you want to delete <strong>${incomeType.name}</strong> income type?`,
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(`/income-types/${incomeType.id}`)
                .then((response) => {
                    toastAlert({ title: response.data.message });
                    // Reload the page to refresh the income types list
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
                            title: "Something went wrong while deleting the income type.",
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

const handleIncomeTypeSaved = () => {
    selectedIncomeType.value = null;
    window.location.reload();
};
</script>
