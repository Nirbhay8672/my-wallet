<template>
    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">
            <template
                v-for="(menu, index) in menuItems"
                :key="`menu_item_${index}`"
            >
                <li class="nav-item" v-if="menu.has_permission">
                    <a
                        class="nav-link"
                        :href="`${$page.props.url}/${menu.url}`"
                        :class="
                            current_url == `${$page.props.url}/${menu.url}`
                                ? ''
                                : 'collapsed'
                        "
                    >
                        <i :class="menu.icon"></i>
                        <span>{{ menu.name }}</span>
                    </a>
                </li>
            </template>

            <li class="nav-item">
                <a
                    class="nav-link collapsed"
                    data-bs-target="#components-nav"
                    data-bs-toggle="collapse"
                    href="#"
                >
                    <i class="bi bi-menu-button-wide"></i><span>Components</span
                    ><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul
                    id="components-nav"
                    class="nav-content collapse"
                    data-bs-parent="#sidebar-nav"
                >
                    <li>
                        <a href="components-alerts.html">
                            <i class="bi bi-circle"></i><span>Alerts</span>
                        </a>
                    </li>
                    <li>
                        <a href="components-accordion.html">
                            <i class="bi bi-circle"></i><span>Accordion</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </aside>
</template>

<script setup>
import { onMounted, ref, reactive } from "vue";

let current_url = ref(null);

const props = defineProps({
    auth: {
        type: Object,
        required: true,
    },
});

onMounted(() => {
    current_url.value = window.location.href;
});

let menuItems = reactive([
    {
        name: "Dashboard",
        icon: "bi bi-house-door-fill",
        url: "home",
        has_permission: hasPermission("view_dashboard"),
    },
    {
        name: "Websites",
        icon: "fa fa-list",
        url: "websites/index",
        has_permission: hasPermission("view_websites"),
    },
    {
        name: "Clients",
        icon: "fa fa-users",
        url: "clients/index",
        has_permission: hasPermission("view_clients"),
    },
    {
        name: "Users",
        icon: "bi bi-people-fill",
        url: "users/index",
        has_permission: hasPermission("view_users"),
    },
    {
        name: "Permissions",
        icon: "bi bi-gear-fill",
        url: "permissions/index",
        has_permission: hasPermission("view_permissions"),
    },
    {
        name: "Payments",
        icon: "fa fa-money",
        url: "payments/index",
        has_permission: hasPermission("view_payments"),
    },
]);

function hasPermission(permission_name) {
    let permission_obj = props.auth.user.roles[0].permissions.find(
        (permission) => permission.name == permission_name
    );

    return permission_obj ? true : false;
}
</script>
