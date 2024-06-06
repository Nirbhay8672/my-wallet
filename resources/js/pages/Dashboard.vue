<template>
    <inertia-head title="Dashboard" />
    <main-page>
        <div class="pagetitle">
            <h1>Dashboard</h1>
        </div>
        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-xxl-6 col-md-6">
                            <a
                                class="card info-card sales-card"
                                v-if="hasPermission('view_users')"
                                :href="`${$page.props.url}/users/index`"
                            >
                                <div class="card-body">
                                    <h5 class="card-title">Users</h5>

                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                                        >
                                            <i class="bi bi-people-fill"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>
                                                {{ $page.props.total_users }}
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <audio ref="audioPlayer" autoplay>
                <source :src="`${$page.props.url}/voice/welcome.mp3`" type="audio/mp3">
            </audio>
        </section>
    </main-page>
</template>

<script setup>

const props = defineProps({
    auth: {
        type: Object,
        required: true,
    },
});

function hasPermission(permission_name) {
    let permission_obj = props.auth.user.roles[0].permissions.find(
        (permission) => permission.name == permission_name
    );

    return permission_obj ? true : false;
}
</script>
