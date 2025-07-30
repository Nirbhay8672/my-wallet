<template>
    <div class="row vh-100">
        <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
            <div class="d-table-cell align-middle">
                <div class="text-center mt-4">
                    <h1 class="h2 text-white">PMS Login</h1>
                    <p class="lead text-white">
                        Sign in to your account to continue
                    </p>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="m-sm-3">
                            <div v-if="incorrect" class="alert-danger">
                                Incorrect username or password.
                            </div>
                            <div v-if="accountInactive" class="alert-danger">
                                Your account is inactive. Please contact administrator.
                            </div>
                            <form>
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input
                                        class="form-control form-control-lg"
                                        type="username"
                                        name="username"
                                        placeholder="Enter your username"
                                        autocomplete="off"
                                        v-model="data.username"
                                        :class="{
                                            'is-invalid':
                                                formValidation.hasError(
                                                    'username'
                                                ),
                                        }"
                                    />
                                    <div
                                        class="invalid-feedback"
                                        v-if="
                                            formValidation.hasError('username')
                                        "
                                    >
                                        <span>{{
                                            formValidation.getError(
                                                "username"
                                            )[0]
                                        }}</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input
                                        class="form-control form-control-lg"
                                        :type="
                                            show_password ? 'text' : 'password'
                                        "
                                        name="password"
                                        placeholder="Enter your password"
                                        autocomplete="off"
                                        v-model="data.password"
                                        :class="{
                                            'is-invalid':
                                                formValidation.hasError(
                                                    'password'
                                                ),
                                        }"
                                    />
                                    <div
                                        class="invalid-feedback"
                                        v-if="
                                            formValidation.hasError('password')
                                        "
                                    >
                                        <span>{{
                                            formValidation.getError(
                                                "password"
                                            )[0]
                                        }}</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="form-check align-items-center">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="show_password"
                                            @change="hideShowPassword"
                                        />
                                        <label
                                            class="form-check-label text-small"
                                            for="show_password"
                                            >Show Password</label
                                        >
                                    </div>
                                </div>
                                <div class="d-grid gap-2 mt-3">
                                    <button
                                        class="btn btn-lg btn-primary"
                                        type="button"
                                        @click="submit()"
                                    >
                                        Sign in
                                    </button>
                                </div>

                                <div class="d-grid gap-2 mt-3">
                                    <a
                                        class="btn btn-lg btn-danger"
                                        :href="`${$page.props.url}/auth/google`"
                                    >
                                        Login With Google
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="text-center mb-3">
                    Don't have an account?
                    <a href="pages-sign-up.html">Sign up</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import GuestLayout from "../../layout/GuestLayout.vue";

export default {
    layout: GuestLayout,
};
</script>

<script setup>
import { reactive, ref } from "vue";
import { FormValidation } from "../../helpers/Validation";
import axios from "axios";

const props = defineProps({
    url: {
        type: String,
        required: true,
    },
    error: {
        type: String,
        default: null,
    },
    success: {
        type: String,
        default: null,
    },
});

let incorrect = ref(false);
let show_password = ref(false);
let accountInactive = ref(false);

const data = reactive({
    username: "",
    password: "",
});

let formValidation = reactive(
    new FormValidation(data, {
        username: {
            required: "Username field is required.",
        },
        password: {
            required: "Password field is required.",
        },
    })
);

function hideShowPassword() {
    show_password.value = !show_password.value;
}

function submit() {
    formValidation.validate();
    incorrect.value = false;
    accountInactive.value = false;

    if (formValidation.isValid()) {
        axios.post(`${props.url}/post-login`, data).then((response) => {
            if (response.data.is_success) {
                window.location.href = `${response.data.url ?? props.url}/`;
            } else {
                // Check if the error is due to inactive account
                if (response.data.message && response.data.message.includes('inactive')) {
                    accountInactive.value = true;
                } else {
                    incorrect.value = true;
                }
            }
        }).catch((error) => {
            if (error.response && error.response.data && error.response.data.message) {
                if (error.response.data.message.includes('inactive')) {
                    accountInactive.value = true;
                } else {
                    incorrect.value = true;
                }
            } else {
                incorrect.value = true;
            }
        });
    }
}

// Check for session messages on component mount
onMounted(() => {
    // Check if there's a session error message about inactive account
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    if (error && error.includes('inactive')) {
        accountInactive.value = true;
    }

    // Check for session flash messages
    if (window.location.search.includes('error=inactive')) {
        accountInactive.value = true;
    }

    // Check for session error prop
    if (props.error === 'inactive') {
        accountInactive.value = true;
    }
});
</script>


