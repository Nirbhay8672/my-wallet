<template>
    <custom-navbar />
    <custom-sidebar :auth="$page.props.auth"></custom-sidebar>
    <main id="main" class="main">
        <slot></slot>
    </main>
    <custom-footer />
</template>

<script setup>
import CustomNavbar from "./components/CustomNavbar.vue";
import CustomSidebar from "./components/CustomSidebar.vue";
import CustomFooter from "./components/CustomFooter.vue";
</script>
