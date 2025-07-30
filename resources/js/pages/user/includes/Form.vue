<template>
    <Modal ref="user_form" :id="'user_form'" :size="'large'">
        <template #modal_title>
            <span>{{ title_text }}</span>
        </template>

        <form>
            <div class="row">
                <div class="text-center col-lg-12">
                    <div class="card-img-actions d-inline-block">
                        <div
                            style="
                                position: center;
                                overflow: hidden;
                                border-radius: 50%;
                            "
                        >
                            <img
                                id="profile_image_file"
                                :src="
                                    fields.profile_path
                                        ? fields.profile_path
                                        : '/images/profile.png'
                                "
                                class="rounded"
                                style="width: 120px; height: 120px"
                            />
                        </div>
                        <button
                            class="btn btn-primary btn-sm mt-2"
                            @click="trigger"
                            type="button"
                        >
                            Upload Image
                        </button>
                    </div>
                    <input
                        type="file"
                        id="profile_image"
                        ref="my_profile"
                        @change="previewFiles"
                        class="form-control d-none"
                        accept="image/png, image/jpeg, image/jpg"
                        :class="{
                            'is-invalid':
                                formValidation.hasError('profile_image'),
                        }"
                    />
                    <span
                        :class="{
                            'is-invalid':
                                formValidation.hasError('profile_image'),
                        }"
                    ></span>
                    <div
                        class="invalid-feedback"
                        v-if="formValidation.hasError('profile_image')"
                    >
                        <span>{{
                            formValidation.getError("profile_image")[0]
                        }}</span>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-6 mb-2">
                    <Field
                        v-model="fields.name"
                        label="Name"
                        label-class="required"
                        type="text"
                        id="name"
                        field="name"
                        :errors="formValidation.errors"
                    ></Field>
                </div>
                <div class="col-lg-6 mb-2">
                    <Field
                        v-model="fields.email"
                        label="Email"
                        label-class="required"
                        type="text"
                        id="email"
                        field="email"
                        :errors="formValidation.errors"
                    ></Field>
                </div>
                <div class="col-lg-6 mb-2">
                    <Field
                        v-model="fields.first_name"
                        label="First Name"
                        label-class="required"
                        type="text"
                        id="first_name"
                        field="first_name"
                        :errors="formValidation.errors"
                    ></Field>
                </div>
                <div class="col-lg-6 mb-2">
                    <Field
                        v-model="fields.last_name"
                        label="Last Name"
                        label-class="required"
                        type="text"
                        id="last_name"
                        field="last_name"
                        :errors="formValidation.errors"
                    ></Field>
                </div>
                <div class="col-lg-6 mb-2">
                    <Field
                        v-model="fields.password"
                        label="Password"
                        type="password"
                        id="password"
                        field="password"
                        autocomplete="off"
                        :errors="formValidation.errors"
                    ></Field>
                </div>
                <div class="col-lg-6 mb-2">
                    <Field
                        v-model="fields.confirm_password"
                        label="Confirm Password"
                        type="text"
                        id="confirm_password"
                        field="confirm_password"
                        autocomplete="off"
                        :errors="formValidation.errors"
                    ></Field>
                </div>
                <div class="col-lg-6 mb-2">
                    <Field
                        field="role_id"
                        id="role_id"
                        label-class="required"
                        :errors="formValidation.errors"
                        :no-label="true"
                        no-input
                    >
                        <template #input="{ hasError }">
                            <select
                                class="form-control form-control-solid form-select"
                                id="role_id"
                                v-model="fields.role_id"
                                :class="{ 'is-invalid': hasError }"
                            >
                                <option value="">-- Select Role --</option>
                                <template v-for="(role, index) in roles" :key="`role_${index}`">
                                    <option :value="role.id">{{ role.display_name }}</option>
                                </template>
                            </select>
                        </template>
                    </Field>
                </div>
                <div class="col-lg-6 mb-2" v-if="!isUserSuperAdmin">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <div class="d-flex align-items-center">
                            <ToggleButton
                                v-model="fields.active"
                                :disabled="fields.id == 1"
                                color="success"
                                size="medium"
                                @change="handleStatusChange"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <template #modal_footer>
            <button
                class="btn bg-gradient-primary"
                type="button"
                @click="handleSubmit"
            >
                {{ button_text }}
            </button>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive, computed } from "vue";
import Modal from "../../../components/Modal.vue";
import ToggleButton from "../../../components/ToggleButton.vue";
import {
    FormValidation,
    withParamsAndMessage,
    withParams,
} from "../../../helpers/Validation";
import { resetObjectKeys } from "../../../helpers/utils";
import Field from "../../../helpers/Field.vue";
import axios from "axios";
import { userRoutes } from "../../../routes/UserRoutes";
import { toastAlert } from "../../../helpers/alert";

let props = defineProps({
    roles : {
        required : true,
        type : Array,
        default : []
    },
    auth: {
        type: Object,
        required: true,
    }
});

// Check if current user is super admin
const isSuperAdmin = computed(() => {
    if (!props.auth?.user?.roles) return false;
    return props.auth.user.roles.some(role => role.name === 'super_admin');
});

// Check if the user being edited has super admin role
const isUserSuperAdmin = computed(() => {
    if (!fields.role_id) return false;
    const userRole = props.roles.find(role => role.id === fields.role_id);
    return userRole?.name === 'super_admin';
});

let user_form = ref(null);
let my_profile = ref("");
let title_text = ref("");
let button_text = ref("");

const emits = defineEmits(["reload"]);

let fields = reactive({
    id: "",
    profile_path: "",
    profile_image: "",
    name: "",
    email: "",
    first_name: "",
    last_name: "",
    password: "",
    confirm_password: "",
    role_id: "",
    active: true,
});

function openModal(user) {
    clearFormData();
    user_form.value.open();

    title_text.value = user ? `Update user : ${user.name}` : "Create User";
    button_text.value = user ? "Update" : "Submit";

    if (user) {
        Object.assign(fields, user);
        fields.profile_path = user.profile_path;
        fields.profile_image = user.profile_path;
        fields.role_id = user.roles[0].id;
        fields.active = user.active;

        formValidation.addFields(fields, {
            password: {
                requiredIf: withParamsAndMessage(
                    withParams([fields, "confirm_password"]),
                    "Password field is required."
                ),
            },
            confirm_password: {
                requiredIf: withParamsAndMessage(
                    withParams([fields, "password"]),
                    "Confirm password field is required."
                ),
                same: withParamsAndMessage(
                    withParams([fields, "password"]),
                    "Confirm password dose not match."
                ),
            },
        });
    } else {
        formValidation.addFields(fields, {
            password: {
                required: "Password field is required.",
            },
            confirm_password: {
                required: "Confirm password field is required.",
                same: withParamsAndMessage(
                    withParams([fields, "password"]),
                    "Confirm password dose not match."
                ),
            },
            profile_image: {
                required: "Profile picture is required.",
            },
        });
    }
}

function trigger() {
    my_profile.value.click();
}

function previewFiles(event) {
    fields.profile_image = event.target.files[0].name;

    var image = document.getElementById("profile_image_file");
    image.src = URL.createObjectURL(event.target.files[0]);
}

function clearFormData() {
    formValidation.reset();
    title_text.value = "";
    button_text.value = "";
    formValidation.reset();
    formValidation.removeFields("password");
    formValidation.removeFields("confirm_password");
    resetObjectKeys(fields);
}

function handleSubmit() {
    formValidation.validate();

    let form_data = new FormData();
    let profile_image = document.getElementById("profile_image");

    if (profile_image && profile_image.files.length > 0) {
        let file = profile_image.files[0];
        form_data.set("profile_image", file, file.name);
    }

    form_data.set("id", fields.id);
    form_data.set("name", fields.name);
    form_data.set("first_name", fields.first_name);
    form_data.set("last_name", fields.last_name);
    form_data.set("email", fields.email);
    form_data.set("password", fields.password);
    form_data.set("confirm_password", fields.confirm_password);
    form_data.set("role_id", fields.role_id);

    let settings = { headers: { "content-type": "multipart/form-data" } };

    if (formValidation.isValid()) {
        axios
            .post(userRoutes.createOrUpdate(fields.id), form_data, settings)
            .then((response) => {
                user_form.value.close();
                emits("reload");
                toastAlert({ title: response.data.message });
                clearFormData();
            })
            .catch(function (error) {
                if (error.response.status === 422) {
                    formValidation.setServerSideErrors(
                        error.response.data.errors
                    );
                    toastAlert({
                        title: "Something went wrong. Please try again.",
                        icon: "error"
                    });
                }
            });
    } else {
        toastAlert({
            title: "Something went wrong. Please try again.",
            icon: "error"
        });
    }
}

function handleStatusChange(newValue) {
    // Optional: Add any additional logic when status changes
    console.log('Status changed to:', newValue);
}

defineExpose({
    openModal,
});

let formValidation = reactive(
    new FormValidation(fields, {
        profile_image: {
            required: "Profile picture is required.",
        },
        name: {
            required: "Name field is required.",
        },
        email: {
            required: "Email field is required.",
        },
        first_name: {
            required: "First name field is required.",
        },
        last_name: {
            required: "Last name field is required.",
        },
        role_id: {
            required: "Role field is required.",
        },
    })
);
</script>
