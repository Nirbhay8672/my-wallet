<template>
    <inertia-head title="Role Permissions" />
    <main-page>
        <div class="row justify-content-between gy-3 mb-1">
            <div class="col-md-auto me-auto">
                <div class="pagetitle">
                    <h4>Role Permission</h4>
                </div>
            </div>
            <div class="col-md-auto ms-auto" v-if="!allow_update">
                <button
                    class="btn btn-primary btn-sm"
                    @click="allowUpdate"
                    v-if="hasPermission('update_permission')"
                >
                    <i class="fa fa-pencil"></i>
                    <span class="ms-2">Update Permissions</span>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
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
                        <div class="row mb-3">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle" style="border-radius: 100px !important;" v-for="(permissions, category_name) in grouped_permissions" :key="category_name">
                                    <tbody>
                                        <tr>
                                            <th class="p-3 bg-light align-middle" :colspan="roles.length + 1">{{ category_name }}</th>
                                        </tr>
                                        <tr>
                                            <th style="padding-left: 20px" class="align-middle">
                                                Permissions
                                            </th>
                                            <th
                                                v-for="role in roles"
                                                class="text-center align-middle"
                                                style="min-width: 100px;"
                                            >
                                                {{ role.display_name }}
                                            </th>
                                        </tr>
                                        <tr v-for="permission in permissions">
                                            <td
                                                style="
                                                    min-width: 250px;
                                                    padding-left: 20px;
                                                "
                                                class="align-middle"
                                            >
                                                {{ permission.display_name }}
                                            </td>
                                            <td
                                                v-for="(
                                                    role, index
                                                ) in permission.roles"
                                                :key="index"
                                                class="align-middle"
                                            >
                                                <template v-if="allow_update">
                                                    <div class="form-check text-center">
                                                        <input
                                                            class="form-check-input permission-checkbox"
                                                            type="checkbox"
                                                            value=""
                                                            :id="`role_${role.id}_permission_${permission.id}`"
                                                            :checked="
                                                                role.has_permission
                                                            "
                                                        />
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <div class="text-center">
                                                        <i
                                                            v-if="
                                                                role.has_permission
                                                            "
                                                            class="fa fa-check fs-5 text-success"
                                                        ></i>
                                                        <i
                                                            v-else
                                                            class="fa fa-times fs-5 text-danger"
                                                        ></i>
                                                    </div>
                                                </template>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="text-center">
                            <button
                                type="button"
                                class="btn btn-primary"
                                v-if="allow_update && hasPermission('update_permission')"
                                @click="updatePermissions"
                            >
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main-page>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { toastAlert } from "../../helpers/alert";
import { permissionRoutes } from "../../routes/PermissionRoutes";

const props = defineProps({
    auth: {
        type: Object,
        required: true,
    },
});

let loader = ref(true);

let grouped_permissions = ref([]);
let roles = ref([]);
let allow_update = ref(false);

onMounted(() => {
    setTimeout(function () {
        reloadData();
    }, 1000);
});

function reloadData() {
    axios.get(permissionRoutes.getRolePermissions).then((response) => {
        grouped_permissions.value = response.data.grouped_permissions;
        roles.value = response.data.roles;
        loader.value = false;
    }).catch(function (error) {
        if (error.response.status === 422) {
            toastAlert({
                title: "somthing went wrong.",
                icon: "error",
            });
        }
    });
}

function allowUpdate() {
    allow_update.value = !allow_update.value;
}

function updatePermissions() {
    let role_permission_obj = {};

    var checked = document.querySelectorAll(".permission-checkbox:checked");

    roles.value.forEach((role) => {
        role_permission_obj[role.id] = {};
    });

    checked.forEach((element) => {
        let role_id = element.id.split("_")[1];
        let permission_id = element.id.split("_")[3];

        if (role_permission_obj.hasOwnProperty(role_id)) {
            role_permission_obj[role_id][permission_id] = permission_id;
        }
    });

    axios.post(permissionRoutes.updateRolePermission, {
            permissions_by_roles: role_permission_obj,
        })
        .then((response) => {
            toastAlert({ title: response.data.message });
            allow_update.value = false;
            reloadData();
        });
}

function hasPermission(permission_name) {
    let permission_obj = props.auth.user.roles[0].permissions.find(
        (permission) => permission.name == permission_name
    );

    return permission_obj ? true : false;
}

</script>
