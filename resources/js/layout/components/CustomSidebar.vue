<template>
    <aside
        class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark"
        id="sidenav-main" :class="$page.props.mini_sidebar ? 'collapsed' : ''">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
                aria-hidden="true" id="iconSidenav">
            </i>
            <a class="navbar-brand m-0" :href="`${$page.props.url}/`">
                <img :src="`${$page.props.url}/images/favicon.png`" class="navbar-brand-img h-100" alt="main_logo">
                <span class="ms-1 font-weight-bold text-white ms-3">My Wallet</span>
            </a>
        </div>
        <hr class="horizontal light mt-0 mb-2">
        <div class="collapse navbar-collapse  w-auto h-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav">
                <li class="nav-item mb-2 mt-0">
                    <a
                        data-bs-toggle="collapse"
                        href="#ProfileNav"
                        class="nav-link text-white"
                        aria-controls="ProfileNav"
                        role="button"
                        :aria-expanded="current_url == `${$page.props.url}/users/profile` ? true : false"
                        :class="current_url == `${$page.props.url}/users/profile` ? '' : 'collapsed'"
                    >
                        <img :src="$page.props.auth.user.profile_path
                                ? $page.props.auth.user.profile_path
                                : `${$page.props.url}/images/profile.png`
                            " class="avatar">
                        <span class="nav-link-text ms-2 ps-1">{{ $page.props.auth.user.first_name }} {{
                            $page.props.auth.user.last_name }}</span>
                    </a>
                    <div class="collapse" id="ProfileNav" :class="current_url == `${$page.props.url}/users/profile` ? 'show' : ''" >
                        <ul class="nav">
                            <li class="nav-item">
                                <a
                                    class="nav-link text-white"
                                    :href="`${$page.props.url}/users/profile`"
                                    :class="current_url == `${$page.props.url}/users/profile` ? 'bg-gradient-primary active' : ''"
                                    id="my_profile_nav_link"
                                >
                                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center" style="margin-left: 9px;">
                                        <i class="material-icons opacity-10 fa fa-user fs-6"></i>
                                    </div>
                                    <span class="sidenav-normal ms-3"> My Profile </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" :href="`${$page.props.url}/logout-auth`">
                                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center" style="margin-left: 9px;">
                                        <i class="material-icons opacity-10 fa fa-sign-out fs-6"></i>
                                    </div>
                                    <span class="sidenav-normal ms-3 ps-1"> Logout </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <hr class="horizontal light mt-0">

                <template v-for="(menu, index) in menuItems">
                    <li class="nav-item" v-if="menu.has_permission && !menu.is_url">
                        <hr class="horizontal light mt-2 mb-2">
                        <span class="text-muted" style="margin-left: 2.1rem;">{{ menu.name }}</span>
                        <hr class="horizontal light mt-2 mb-2">
                    </li>
                    <li class="nav-item" v-if="menu.has_permission && menu.is_url">
                        <a class="nav-link text-white"
                            :class="current_url == `${$page.props.url}/${menu.url}` ? 'bg-gradient-primary active' : ''"
                            :href="`${$page.props.url}/${menu.url}`"
                            :id="`nav-link-${index}`"
                        >
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center" style="margin-left: 7px;">
                                <i class="material-icons opacity-10 fs-6" :class="menu.icon"></i>
                            </div>
                            <span class="nav-link-text ms-1">{{ menu.name }}</span>
                        </a>
                    </li>
                </template>
            </ul>
        </div>
    </aside>
</template>

<script setup>
import { onMounted, ref, reactive } from "vue";
import { initComponent } from "../../theme";

let current_url = ref(null);

const props = defineProps({
    auth: {
        type: Object,
        required: true,
    },
});

onMounted(() => {
    current_url.value = window.location.href;
    initComponent();
});

let menuItems = reactive([
    {
        name: "Dashboard",
        icon: "fa fa-th-large",
        url: "",
        is_url : true,
        has_permission: hasPermission("view_dashboard"),
    },
    {
        name: "Main",
        icon: "",
        url: "",
        is_url : false,
        has_permission: hasPermission("view_banks") || hasPermission("view_accounts") || hasPermission("view_income_types") || hasPermission("view_expense_types"),
    },
    {
        name: "Banks",
        icon: "fa fa-bank",
        url: "banks/index",
        is_url : true,
        has_permission: hasPermission("view_banks"),
    },
    {
        name: "Accounts",
        icon: "fa fa-credit-card",
        url: "accounts/index",
        is_url : true,
        has_permission: hasPermission("view_accounts"),
    },
    {
        name: "Income Types",
        icon: "fa fa-plus-circle",
        url: "income-types/index",
        is_url : true,
        has_permission: hasPermission("view_income_types"),
    },
    {
        name: "Expense Types",
        icon: "fa fa-minus-circle",
        url: "expense-types/index",
        is_url : true,
        has_permission: hasPermission("view_expense_types"),
    },
    {
        name: "User Management",
        icon: "",
        url: "",
        is_url : false,
        has_permission: hasPermission("view_users") || hasPermission("view_permissions"),
    },
    {
        name: "Users",
        icon: "fa fa-users",
        url: "users/index",
        is_url : true,
        has_permission: hasPermission("view_users"),
    },
    {
        name: "Permission",
        icon: "fa fa-shield",
        url: "permissions/index",
        is_url : true,
        has_permission: hasPermission("view_permissions"),
    },
]);

function hasPermission(permission_name) {
    let permission_obj = props.auth.user.roles[0].permissions.find(
        (permission) => permission.name == permission_name
    );

    return permission_obj ? true : false;
}

</script>
