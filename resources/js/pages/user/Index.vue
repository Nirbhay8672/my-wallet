<template>
    <inertia-head title="Users" />
    <main-page>
        <div class="row justify-content-between gy-3 mb-1">
            <div class="col-md-auto me-auto">
                <div class="pagetitle">
                    <h4>Users</h4>
                </div>
            </div>
            <div class="col-md-auto ms-auto">
                <button
                    class="btn btn-primary btn-sm"
                    @click="openForm()"
                    v-if="hasPermission('add_user')"
                >
                    <i class="fa fa-plus"></i>
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
                            <div class="pre-loader" v-if="loader">
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
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Profile</th>
                                                <th>Username</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th class="text-center align-middle">Status</th>
                                                <th class="text-center align-middle">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-if="users.length > 0">
                                                <tr
                                                    v-for="(
                                                        user, index
                                                    ) in users"
                                                    :key="`user_${index}`"
                                                >
                                                    <td
                                                        style="min-width: 200px"
                                                        class="align-middle"
                                                    >
                                                        <img
                                                            :src="
                                                                user.profile_path
                                                                    ? `${user.profile_path}`
                                                                    : ''
                                                            "
                                                            class="rounded-circle avatar"
                                                            alt="profile image"
                                                            style="height: 33px; width: auto;"
                                                        />
                                                    </td>
                                                    <td
                                                        style="min-width: 100px"
                                                        class="align-middle"
                                                    >
                                                        {{ user.name }}
                                                    </td>
                                                    <td
                                                        style="min-width: 100px"
                                                        class="align-middle"
                                                    >
                                                        {{
                                                            `${user.first_name} ${user.last_name}`
                                                        }}
                                                    </td>
                                                    <td
                                                        style="min-width: 100px"
                                                        class="align-middle"
                                                    >
                                                        {{ user.email }}
                                                    </td>
                                                    <td
                                                        style="min-width: 100px"
                                                        class="align-middle"
                                                    >
                                                        {{
                                                            user.roles[0][
                                                                "display_name"
                                                            ]
                                                        }}
                                                    </td>
                                                    <td
                                                        style="min-width: 100px"
                                                        class="text-center align-middle"
                                                    >
                                                        <div class="d-flex justify-content-center">
                                                            <ToggleButton
                                                                v-model="user.active"
                                                                :disabled="user.id == 1"
                                                                color="success"
                                                                size="small"
                                                                @change="toggleUserStatus(user)"
                                                                v-if="hasPermission('update_user') && !isUserSuperAdmin(user)"
                                                            />
                                                        </div>
                                                    </td>
                                                    <td
                                                        style="min-width: 200px"
                                                        class="text-center align-middle"
                                                    >
                                                        <button
                                                            class="btn bg-gradient-info btn-sm"
                                                            @click="
                                                                openForm(user)
                                                            "
                                                            v-if="
                                                                hasPermission(
                                                                    'update_user'
                                                                )
                                                            "
                                                        >
                                                            <i
                                                                class="fa fa-pencil"
                                                            ></i>
                                                        </button>
                                                        <button
                                                            class="btn bg-gradient-danger btn-sm ms-3"
                                                            :class="user.id == 1 ? 'd-none' : ''"
                                                            @click="
                                                                deleteUser(user)
                                                            "
                                                            v-if="hasPermission('delete_user')"
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
                                                    <td colspan="7" class="align-middle">
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
                            <div class="row gy-3" v-if="users.length > 0">
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
                                            <li
                                                class="page-item"
                                                @click="prev()"
                                            >
                                                <span class="page-link"
                                                    ><i
                                                        class="fa fa-angle-left"
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
                                                        page_number ===
                                                        fields.page
                                                            ? 'active'
                                                            : ''
                                                    "
                                                    @click="
                                                        setPage(page_number)
                                                    "
                                                >
                                                    <span
                                                        class="page-link cursor-pointer text-white"
                                                        >{{ page_number }}</span
                                                    >
                                                </li>
                                            </template>

                                            <li
                                                class="page-item"
                                                @click="next()"
                                            >
                                                <span class="page-link"
                                                    ><i
                                                        class="fa fa-angle-right"
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
        </section>

        <teleport to="body">
            <user-form
                ref="user_form"
                :roles="roles"
                :auth="auth"
                @reload="reloadTable()"
            ></user-form>
        </teleport>
    </main-page>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from "vue";
import axios from "axios";
import { userRoutes } from "../../routes/UserRoutes";
import { toastAlert, confirmAlert } from "../../helpers/alert";
import UserForm from "./includes/Form.vue";
import ToggleButton from "../../components/ToggleButton.vue";

const props = defineProps({
    auth: {
        type: Object,
        required: true,
    },
    roles: {
        type: Array,
        required: true,
    },
});

// Check if current user is super admin
const isSuperAdmin = computed(() => {
    if (!props.auth?.user?.roles) return false;
    return props.auth.user.roles.some(role => role.name === 'super_admin');
});

// Check if a specific user has super admin role
const isUserSuperAdmin = (user) => {
    if (!user?.roles) return false;
    return user.roles.some(role => role.name === 'super_admin');
};

let users = ref([]);
let loader = ref(true);

let user_form = ref(null);

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

function openForm(user = null) {
    user_form.value.openModal(user);
}

function reloadTable() {
    axios
        .post(userRoutes.datatable, fields)
        .then((response) => {
            users.value = response.data.users;
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

function toggleUserStatus(user) {
    axios
        .post(`/users/${user.id}/toggle-status`, {
            active: user.active
        })
        .then((response) => {
            toastAlert({ title: response.data.message });
        })
        .catch(function (error) {
            // Revert the toggle if there's an error
            user.active = !user.active;
            if (error.response && error.response.status === 422) {
                toastAlert({
                    title: error.response.data.message,
                    icon: "error",
                });
            } else {
                toastAlert({
                    title: "Something went wrong while updating user status.",
                    icon: "error",
                });
            }
        });
}

function deleteUser(user) {
    confirmAlert({
        title: "Delete",
        icon: "question",
        html: `Are you sure, you want to delete <strong> ${user.name} </strong> user ?`,
    }).then((result) => {
        if (result.isConfirmed) {
            axios
                .get(userRoutes.deleteUser(user.id))
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
