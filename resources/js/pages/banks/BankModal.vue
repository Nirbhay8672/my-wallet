<template>
    <div class="modal fade" :id="modalId" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ isEdit ? 'Edit Bank' : 'Add Bank' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="handleSubmit">
                        <div class="mb-3">
                            <label for="name" class="form-label">Bank Name</label>
                            <input type="text" class="form-control" id="name" v-model="form.name" required>
                        </div>
                        <div class="mb-3">
                            <label for="logo" class="form-label">Bank Logo</label>
                            <input type="file" class="form-control" id="logo" @change="handleFileUpload"
                                accept="image/*" :required="!isEdit">
                            <div v-if="isEdit && form.logo" class="mt-2">
                                <img :src="form.logo" alt="Current Logo" class="img-thumbnail" style="height: 50px">
                                <span class="ms-2">Current Logo</span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" :disabled="loading">
                                {{ loading ? 'Saving...' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

export default {
    name: 'BankModal',
    props: {
        modalId: {
            type: String,
            required: true
        },
        bank: {
            type: Object,
            default: null
        },
        isEdit: {
            type: Boolean,
            default: false
        }
    },
    emits: ['bankSaved'],
    setup(props, { emit }) {
        const loading = ref(false);
        const form = useForm({
            name: '',
            logo: null
        });

        watch(() => props.bank, (newBank) => {
            if (newBank) {
                form.name = newBank.name;
                form.logo = newBank.logo;
            } else {
                form.reset();
            }
        }, { immediate: true });

        const handleFileUpload = (e) => {
            const file = e.target.files[0];
            if (file) {
                form.logo = file;
            }
        };

        const handleSubmit = () => {
            loading.value = true;
            const url = props.isEdit ? `/banks/update/${props.bank.id}` : '/banks/create';

            if (props.isEdit && !(form.logo instanceof File)) {
                delete form.logo;
            }

            form.submit(props.isEdit ? 'put' : 'post', url, {
                preserveScroll: true,
                onSuccess: () => {
                    loading.value = false;
                    emit('bankSaved');
                    bootstrap.Modal.getInstance(document.getElementById(props.modalId)).hide();
                    form.reset();
                },
                onError: () => {
                    loading.value = false;
                }
            });
        };

        return {
            form,
            loading,
            handleSubmit,
            handleFileUpload
        };
    }
};
</script>

<style scoped>
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1050;
}

.modal-dialog {
    max-width: 400px;
    width: 100%;
}
</style>
