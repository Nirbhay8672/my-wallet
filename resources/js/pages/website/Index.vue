<template>
    <inertia-head title="Websites" />
    <main-page>
        <div class="container-fluid p-0 mb-3">
            <div class="row mb-2 gy-3">
                <div class="col-12 col-md-6 col-lg-6">
                    <h5 class="d-inline align-middle">Websites</h5>
                </div>
                <div class="col-12 col-md-6 col-lg-6">
                    <div class="float-sm-end gy-3">
                        <button class="btn btn-secondary btn-sm" v-if="hasPermission('re_sync_website')">
                            <i class="fa fa-refresh"></i>
                            <span class="ms-2">Re-Sync</span>
                        </button>
                        <button
                            class="btn btn-primary btn-sm ms-sm-3 ms-md-3 ms-lg-3 mt-3 mt-md-0 mt-lg-0 col-sm-0"
                            v-if="hasPermission('add_website')"
                            @click="openForm()"
                        >
                            <i class="fa fa-plus-circle"></i>
                            <span class="ms-2">Add Website</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div
                        class="card-body p-4"
                        v-if="loader"
                        style="height: 200px"
                    >
                        <div class="overflow dark" id="preload">
                            <div class="circle-line">
                                <div class="circle-2"><b>P</b></div>
                                <div class="circle-1"><b>M</b></div>
                                <div class="circle-2"><b>S</b></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4" v-else>
                        <div class="row mt-2 justify-content-between gy-3 mb-3">
                            <div class="col-md-auto me-auto">
                                <div class="dt-length">
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
                            </div>
                            <div class="col-md-auto ms-auto">
                                <div class="dt-search">
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
                        </div>
                        <div class="row mb-3 mt-2 gy-2" v-if="hasPermission('view_website_payment')">
                            <div class="col-12 col-lg-2 col-md-3 col-sm-3">
                                <select
                                    name="payment_status"
                                    id="payment_status"
                                    class="form-select"
                                    @change="changeMainFilter()"
                                    v-model="fields.payment_status"
                                >
                                <option value="">Payment Status</option>
                                <option value="Success">Success</option>
                                <option value="Pending">Pending</option>
                                </select>
                            </div>
                            <div class="col-12 col-lg-2 col-md-3 col-sm-3">
                                <select
                                    name="package_type"
                                    id="package_type"
                                    class="form-select"
                                    @change="changeMainFilter()"
                                    v-model="fields.package_type"
                                >
                                <option value="">Package Type</option>
                                <option value="Small">Small</option>
                                <option value="Medium">Medium</option>
                                <option value="Big">Big</option>
                                </select>
                            </div>
                        </div>
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
                                            <th class="text-center">Webiste Info</th>
                                            <th>Client Name</th>
                                            <th>Updates</th>
                                            <th>Backup</th>
                                            <th>Helth</th>
                                            <th>Issues</th>
                                            <th>WP Admin</th>
                                            <template v-if="hasPermission('view_website_payment')">
                                                <th>Payment Status</th>
                                                <th>Package Type</th>
                                            </template>
                                            <th v-if="hasPermission('website_details')">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-if="websites.length > 0">
                                            <template
                                                v-for="(
                                                    website, index
                                                ) in websites"
                                                :key="`website_${index}`"
                                            >
                                                <website-row
                                                    :website="website"
                                                    :auth="auth"
                                                    @openWebiteDetails="openWebsiteDetails(website)"
                                                ></website-row>
                                            </template>
                                        </template>
                                        <template v-else>
                                            <tr
                                                style="width: 100%"
                                                class="text-center"
                                            >
                                                <td colspan="9">
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
                        <div class="row gy-3" v-if="websites.length > 0">
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
                                        <li class="page-item" @click="prev()">
                                            <span class="page-link"
                                                ><i
                                                    class="fa fa-angle-double-left"
                                                ></i
                                            ></span>
                                        </li>
                                        <template
                                            v-for="page in fields.total_pages"
                                            :key="`page_${page}`"
                                        >
                                            <li
                                                class="page-item"
                                                :class="
                                                    page === fields.page
                                                        ? 'active'
                                                        : ''
                                                "
                                                @click="setPage(page)"
                                            >
                                                <span
                                                    class="page-link cursor-pointer"
                                                    >{{ page }}</span
                                                >
                                            </li>
                                        </template>

                                        <li class="page-item" @click="next()">
                                            <span class="page-link"
                                                ><i
                                                    class="fa fa-angle-double-right"
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
        <teleport to="body">
            <website-form ref="website_form" @reload="reloadTable" />
            <website-details ref="website_details" :auth="auth" />
        </teleport>
    </main-page>
</template>

<script setup>
import { ref, onMounted, reactive } from "vue";
import WebsiteRow from "./includes/WebsiteRow.vue";
import axios from "axios";
import { websiteRoutes } from "../../routes/WebsiteRoutes";
import websiteForm from "./includes/Form.vue";
import websiteDetails from "./includes/View.vue";

let websites = ref([]);
let loader = ref(true);

let website_form = ref(null);
let website_details = ref(null);

const props = defineProps({
    auth: {
        type: Object,
        required: true,
    },
});

let fields = reactive({
    search: "",
    payment_status : "",
    package_type : "",
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

function openForm() {
    website_form.value.openModal();
}

function changeMainFilter() {
    fields.page = 1;
    reloadTable();
}

function setPage(page_number) {
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

function openWebsiteDetails(webiste) {
    website_details.value.openModal(webiste);
}

function reloadTable() {
    axios
        .post(websiteRoutes.datatable, fields)
        .then((response) => {
            websites.value = response.data.websites;
            fields.total_record = response.data.total;
            fields.total_pages = response.data.total_pages;
            fields.start_index = response.data.start_index;
            fields.end_index = response.data.end_index;
            loader.value = false;
        })
        .catch(function (error) {
            if (error.response.status === 422) {
                console.log("somthing went wrong");
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
