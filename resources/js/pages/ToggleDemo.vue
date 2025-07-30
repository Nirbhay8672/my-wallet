<template>
    <inertia-head title="Toggle Button Demo" />
    <main-page>
        <div class="row justify-content-between gy-3 mb-1">
            <div class="col-md-auto me-auto">
                <div class="pagetitle">
                    <h4>Toggle Button Demo</h4>
                    <p class="text-muted">Attractive toggle buttons that match your theme</p>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Toggle Button Variations</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Different Sizes -->
                                <div class="col-md-6 mb-4">
                                    <h6 class="mb-3">Different Sizes</h6>
                                    <div class="d-flex align-items-center gap-4">
                                        <div>
                                            <label class="form-label">Small</label>
                                            <ToggleButton v-model="toggles.small" size="small" />
                                        </div>
                                        <div>
                                            <label class="form-label">Medium</label>
                                            <ToggleButton v-model="toggles.medium" size="medium" />
                                        </div>
                                        <div>
                                            <label class="form-label">Large</label>
                                            <ToggleButton v-model="toggles.large" size="large" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Different Colors -->
                                <div class="col-md-6 mb-4">
                                    <h6 class="mb-3">Different Colors</h6>
                                    <div class="d-flex align-items-center gap-4">
                                        <div>
                                            <label class="form-label">Primary</label>
                                            <ToggleButton v-model="toggles.primary" color="primary" />
                                        </div>
                                        <div>
                                            <label class="form-label">Success</label>
                                            <ToggleButton v-model="toggles.success" color="success" />
                                        </div>
                                        <div>
                                            <label class="form-label">Warning</label>
                                            <ToggleButton v-model="toggles.warning" color="warning" />
                                        </div>
                                        <div>
                                            <label class="form-label">Danger</label>
                                            <ToggleButton v-model="toggles.danger" color="danger" />
                                        </div>
                                        <div>
                                            <label class="form-label">Info</label>
                                            <ToggleButton v-model="toggles.info" color="info" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Disabled State -->
                                <div class="col-md-6 mb-4">
                                    <h6 class="mb-3">Disabled State</h6>
                                    <div class="d-flex align-items-center gap-4">
                                        <div>
                                            <label class="form-label">Disabled (Off)</label>
                                            <ToggleButton v-model="toggles.disabledOff" :disabled="true" />
                                        </div>
                                        <div>
                                            <label class="form-label">Disabled (On)</label>
                                            <ToggleButton v-model="toggles.disabledOn" :disabled="true" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Interactive Demo -->
                                <div class="col-md-6 mb-4">
                                    <h6 class="mb-3">Interactive Demo</h6>
                                    <div class="d-flex align-items-center gap-4">
                                        <div>
                                            <label class="form-label">Status</label>
                                            <ToggleButton
                                                v-model="toggles.interactive"
                                                color="success"
                                                @change="handleToggleChange"
                                            />
                                        </div>
                                        <div class="mt-4">
                                            <span class="badge" :class="toggles.interactive ? 'bg-success' : 'bg-secondary'">
                                                {{ toggles.interactive ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Usage Examples -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="mb-3">Usage Examples</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Feature</th>
                                                    <th>Toggle</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>User Status</td>
                                                    <td>
                                                        <ToggleButton
                                                            v-model="toggles.userStatus"
                                                            color="success"
                                                            size="small"
                                                        />
                                                    </td>
                                                    <td>Toggle user active/inactive status</td>
                                                </tr>
                                                <tr>
                                                    <td>Bank Status</td>
                                                    <td>
                                                        <ToggleButton
                                                            v-model="toggles.bankStatus"
                                                            color="primary"
                                                            size="small"
                                                        />
                                                    </td>
                                                    <td>Toggle bank active/inactive status</td>
                                                </tr>
                                                <tr>
                                                    <td>Notifications</td>
                                                    <td>
                                                        <ToggleButton
                                                            v-model="toggles.notifications"
                                                            color="info"
                                                            size="small"
                                                        />
                                                    </td>
                                                    <td>Enable/disable notifications</td>
                                                </tr>
                                                <tr>
                                                    <td>Dark Mode</td>
                                                    <td>
                                                        <ToggleButton
                                                            v-model="toggles.darkMode"
                                                            color="warning"
                                                            size="small"
                                                        />
                                                    </td>
                                                    <td>Switch between light/dark theme</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main-page>
</template>

<script setup>
import { reactive } from 'vue';
import ToggleButton from '../components/ToggleButton.vue';
import { toastAlert } from '../helpers/alert';

const toggles = reactive({
    small: false,
    medium: true,
    large: false,
    primary: true,
    success: false,
    warning: true,
    danger: false,
    info: true,
    disabledOff: false,
    disabledOn: true,
    interactive: true,
    userStatus: true,
    bankStatus: false,
    notifications: true,
    darkMode: false
});

const handleToggleChange = (newValue) => {
    toastAlert({
        title: `Toggle changed to: ${newValue ? 'ON' : 'OFF'}`,
        icon: newValue ? 'success' : 'info'
    });
};
</script>

<style scoped>
.gap-4 {
    gap: 1.5rem;
}
</style>
