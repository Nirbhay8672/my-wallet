<template>
    <Modal ref="website_view" :id="'website_view'" :size="'extra_large'">
        <template #modal_title v-if="website_obj">
            <span>Website Info : {{ website_obj.website_name }} </span>
        </template>

        <div class="row gy-3" v-if="website_obj">
            <div class="col-xxl-6 d-none">
                <h6 class="text-center">Website Details</h6>
                <hr />
                <div class="text-center">
                    <img
                        :src="`${$page.props.url}/${website_obj.website_logo_path}`"
                        alt="website_logo"
                        class="text-center"
                        style="height: 100px; width: auto"
                    />
                </div>
                <div class="row gy-2 mt-3">
                    <div class="col-12 col-md-6 col-lg-6">
                        <b>Website Name</b>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <a
                            :href="website_obj.website_url"
                            style="text-decoration: none"
                            target="_blank"
                            >{{ website_obj.website_name }}</a
                        >
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <b>Google Rank</b>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <span>{{ website_obj.google_rank }}</span>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <b>Time</b>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <span>{{ website_obj.time }}</span>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <b>Total Updates</b>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <span>{{ website_obj.total_update }}</span>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <b>Backup</b>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <span>{{
                            website_obj.is_backup_active ? "Yes" : "No"
                        }}</span>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <b>Total Site Helth</b>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <span>{{ website_obj.total_site_helth }}</span>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <b>Total PHP Issues</b>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <span>{{ website_obj.total_php_issue }}</span>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <b>WP Admin</b>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <a
                            :href="website_obj.wp_admin_url"
                            style="text-decoration: none"
                            target="_blank"
                            >Open</a
                        >
                    </div>
                </div>
            </div>
            <div class="col-xxl-6">
                <h6 class="text-center">Client Details</h6>
                <hr />
                <div class="row gy-2 mt-3">
                    <div class="col-12 col-md-6 col-lg-6">
                        <b>Name</b>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <span>{{ website_obj.client.name }}</span>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <b>Phone Number</b>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <span>{{ website_obj.client.phone_number }}</span>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <b>Email</b>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <span>{{ website_obj.client.email }}</span>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <b>Joining Date</b>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <span>{{ website_obj.client.joining_date }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="row gy-3 mt-3"
            v-if="website_obj && hasPermission('view_website_payment')"
        >
            <div class="row mb-3">
                <div class="table-responsive">
                    <table
                        class="table"
                        style="
                            border-collapse: separate;
                            border-spacing: 0 10px;
                        "
                    >
                        <thead>
                            <tr class="custom-table-heading">
                                <th>Sr No.</th>
                                <th>Payment Date Time</th>
                                <th>Package Type</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-if="website_obj.payments.length > 0">
                                <tr
                                    v-for="(
                                        payment, index
                                    ) in website_obj.payments"
                                    :key="`payment_${index}`"
                                >
                                    <td>{{ index + 1 }}</td>
                                    <td>
                                        {{ payment.payment_date }}
                                        {{ payment.payment_time }}
                                    </td>
                                    <td>{{ payment.package_type }}</td>
                                    <td>{{ payment.amount }}</td>
                                </tr>
                            </template>
                            <tr v-else>
                                <td colspan="4" class="text-center">
                                    <img
                                        alt="no found"
                                        :src="`${$page.props.url}/images/no_found.png`"
                                        style="width: 200px"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import Modal from "../../../components/Modal.vue";
import { websiteRoutes } from "../../../routes/WebsiteRoutes";

const props = defineProps({
    auth: {
        type: Object,
        required: true,
    },
});

let website_view = ref(null);
let website_obj = ref(null);

function openModal(website) {
    axios
        .get(websiteRoutes.details(website.id))
        .then((response) => {
            website_obj.value = response.data.website_details;
        })
        .catch(function (error) {
            if (error.response.status === 422) {
                console.log("somthing went wrong");
            }
        });

    website_view.value.open();
}

function hasPermission(permission_name) {
    let permission_obj = props.auth.user.roles[0].permissions.find(
        (permission) => permission.name == permission_name
    );

    return permission_obj ? true : false;
}

defineExpose({
    openModal,
});
</script>
