<template>
    <tr style="background-color: rgb(248 248 248)">
        <td style="min-width: 350px">
            <div class="row">
                <div class="col-3 text-center">
                    <img
                        :src="`${website.website_logo_path}`"
                        class="rounded mt-2"
                        style="height: 35px; width: auto"
                        alt="profile image"
                    />
                </div>
                <div class="col-5">
                    <h6>
                        {{ website.website_name }}
                    </h6>
                    <a
                        :href="website.website_url"
                        style="text-decoration: none"
                        target="_blank"
                        class="mt-1"
                        ><i class="fa fa-external-link fs-8"></i
                        ><small class="text-primary ms-1">View Site</small></a
                    >
                </div>
                <div class="col-4" style="border-left: 2px solid #c9c9c9;">
                    <div>
                        <img
                            :src="`${$page.props.url}/images/google.png`"
                            style="height: 15px; width: auto"
                            alt="profile image"
                        />
                        <small class="ms-2">{{ website.google_rank }} / 100</small>
                    </div>
                    <div>
                        <i class="fa fa-tachometer text-danger fs-8"></i>
                        <small class="ms-2">{{ website.time }} ms</small>
                    </div>
                </div>
            </div>
        </td>
        <td style="min-width: 150px">{{ website.client_name }}</td>
        <td style="min-width: 150px">
            <span
                class="badge rounded-pill p-2"
                style="background-color: rgb(255 213 213)"
            >
                <i class="fa fa-cog fs-6 text-danger"></i
                ><span class="ms-2 text-dark"
                    >{{ website.total_update }} Updates</span
                >
            </span>
        </td>
        <td style="min-width: 150px">
            <i class="fa fa-history text-success fs-5"></i>
            <small class="ms-2">Backup active</small>
        </td>
        <td style="min-width: 150px">
            <i class="fa fa-heart text-success fs-5"></i>
            <small class="ms-2"
                >{{ website.total_site_helth }} Site helth</small
            >
        </td>
        <td style="min-width: 150px">
            <img
                :src="`${$page.props.url}/images/php.png`"
                style="height: 17px; width: auto"
                alt="profile image"
            />
            <small class="ms-2">{{ website.total_php_issue }} Issues</small>
        </td>
        <td style="min-width: 150px">
            <a
                :href="website.wp_admin_url"
                target="_blank"
                class="btn btn-light bg-white border"
            >
                <img
                    :src="`${$page.props.url}/images/wordpress.png`"
                    class="rounded"
                    style="height: 20px; width: auto"
                    alt="wp"
                />
                <small class="ms-2">WP Admin</small>
            </a>
        </td>
        <template v-if="hasPermission('view_website_payment')">
            <td style="min-width: 150px" :class="website.payment_status == 'Pending' ? 'text-danger' : (website.payment_status == 'Success' ? 'text-success' : '')">
                <b>{{ website.payment_status ?? '-' }}</b>
            </td>
            <td style="min-width: 150px" :class="getPackageTypeColor(website.package_type)">
                <b>{{ website.package_type ?? '-' }}</b>
            </td>
        </template>
        <td style="min-width: 150px" v-if="hasPermission('website_details')">
            <button class="btn btn-outline-info btn-sm" @click="emits('openWebiteDetails')"><i class="fa fa-eye"></i></button>
        </td>
    </tr>
</template>

<script setup>
import { getPackageTypeColor } from '../../../helpers/utils';

const props = defineProps({
    website: {
        type: Object,
        required: true,
        default: {},
    },
    auth: {
        type: Object,
        required: true,
    },
});

const emits = defineEmits(['openWebiteDetails']);

function hasPermission(permission_name) {
    let permission_obj = props.auth.user.roles[0].permissions.find(
        (permission) => permission.name == permission_name
    );

    return permission_obj ? true : false;
}

</script>
