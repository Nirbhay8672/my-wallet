<template>
    <inertia-head title="Dashboard" />
    <main-page>
        <div class="row">
            <div class="col-lg-12 position-relative z-index-2">
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-6 mt-sm-0 mt-4" v-if="hasPermission('view_users')">
                        <div class="card mb-2">
                            <div class="card-header p-3 pt-2">
                                <div
                                    class="icon icon-lg icon-shape bg-gradient-primary shadow-primary shadow text-center border-radius-xl mt-n4 position-absolute">
                                    <i class="material-icons opacity-10">leaderboard</i>
                                </div>
                                <div class="text-end pt-1">
                                    <p class="text-sm mb-0 text-capitalize">Users</p>
                                    <h4 class="mb-0">{{ $page.props.total_users }}</h4>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-3 text-center">
                                <a :href="`${$page.props.url}/users/index`" class="btn btn-outline-primary btn-sm mb-0">View</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6 mt-sm-0 mt-4" v-if="hasPermission('view_accounts')">
                        <div class="card mb-2">
                            <div class="card-header p-3 pt-2">
                                <div
                                    class="icon icon-lg icon-shape bg-gradient-success shadow-success shadow text-center border-radius-xl mt-n4 position-absolute">
                                    <i class="material-icons opacity-10">account_balance_wallet</i>
                                </div>
                                <div class="text-end pt-1">
                                    <p class="text-sm mb-0 text-capitalize">My Accounts</p>
                                    <h4 class="mb-0">{{ $page.props.total_accounts || 0 }}</h4>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-3 text-center">
                                <a :href="`${$page.props.url}/accounts/index`" class="btn btn-outline-success btn-sm mb-0">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
