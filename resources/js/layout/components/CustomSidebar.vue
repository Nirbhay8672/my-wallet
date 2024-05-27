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
        </ul>
    </aside>
</template>

<script setup>
import { onMounted, ref, reactive } from "vue";
import { initThemeScript } from "../../theme";

let current_url = ref(null);

const props = defineProps({
    auth: {
        type: Object,
        required: true,
    },
});

onMounted(() => {
    current_url.value = window.location.href;
    initThemeScript();
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
        icon: "bi bi-shield-shaded",
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
