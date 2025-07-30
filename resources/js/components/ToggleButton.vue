<template>
    <div class="custom-toggle-wrapper">
        <div
            class="custom-toggle"
            :class="{ 'active': modelValue }"
            @click="toggleValue"
            :style="toggleStyles"
        >
            <div class="toggle-slider" :style="sliderStyles"></div>
            <div class="toggle-icons">
                <i class="fas fa-check toggle-icon active-icon" v-if="modelValue"></i>
                <i class="fas fa-times toggle-icon inactive-icon" v-else></i>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: 'medium', // small, medium, large
        validator: (value) => ['small', 'medium', 'large'].includes(value)
    },
    color: {
        type: String,
        default: 'primary', // primary, success, warning, danger, info
        validator: (value) => ['primary', 'success', 'warning', 'danger', 'info'].includes(value)
    }
});

const emit = defineEmits(['update:modelValue', 'change']);

const toggleValue = () => {
    if (!props.disabled) {
        const newValue = !props.modelValue;
        emit('update:modelValue', newValue);
        emit('change', newValue);
    }
};

const toggleStyles = computed(() => {
    const sizeMap = {
        small: { width: '40px', height: '20px' },
        medium: { width: '50px', height: '25px' },
        large: { width: '60px', height: '30px' }
    };

    const colorMap = {
        primary: { backgroundColor: '#e9ecef', borderColor: '#5e72e4' },
        success: { backgroundColor: '#e9ecef', borderColor: '#2dce89' },
        warning: { backgroundColor: '#e9ecef', borderColor: '#fb6340' },
        danger: { backgroundColor: '#e9ecef', borderColor: '#f5365c' },
        info: { backgroundColor: '#e9ecef', borderColor: '#11cdef' }
    };

    return {
        ...sizeMap[props.size],
        ...colorMap[props.color],
        opacity: props.disabled ? '0.6' : '1',
        cursor: props.disabled ? 'not-allowed' : 'pointer'
    };
});

const sliderStyles = computed(() => {
    const sizeMap = {
        small: { width: '16px', height: '16px', transform: props.modelValue ? 'translateX(20px)' : 'translateX(2px)' },
        medium: { width: '19px', height: '19px', transform: props.modelValue ? 'translateX(25px)' : 'translateX(3px)' },
        large: { width: '24px', height: '24px', transform: props.modelValue ? 'translateX(30px)' : 'translateX(3px)' }
    };

    const colorMap = {
        primary: { backgroundColor: props.modelValue ? '#5e72e4' : '#adb5bd' },
        success: { backgroundColor: props.modelValue ? '#2dce89' : '#adb5bd' },
        warning: { backgroundColor: props.modelValue ? '#fb6340' : '#adb5bd' },
        danger: { backgroundColor: props.modelValue ? '#f5365c' : '#adb5bd' },
        info: { backgroundColor: props.modelValue ? '#11cdef' : '#adb5bd' }
    };

    return {
        ...sizeMap[props.size],
        ...colorMap[props.color]
    };
});
</script>

<style scoped>
.custom-toggle-wrapper {
    display: inline-block;
}

.custom-toggle {
    position: relative;
    border-radius: 25px;
    border: 2px solid;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 2px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.custom-toggle:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    transform: translateY(-1px);
}

.custom-toggle.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: transparent;
}

.custom-toggle.active.primary {
    background: linear-gradient(135deg, #5e72e4 0%, #825ee4 100%);
}

.custom-toggle.active.success {
    background: linear-gradient(135deg, #2dce89 0%, #2dcecc 100%);
}

.custom-toggle.active.warning {
    background: linear-gradient(135deg, #fb6340 0%, #fbb140 100%);
}

.custom-toggle.active.danger {
    background: linear-gradient(135deg, #f5365c 0%, #f56036 100%);
}

.custom-toggle.active.info {
    background: linear-gradient(135deg, #11cdef 0%, #1171ef 100%);
}

.toggle-slider {
    position: absolute;
    border-radius: 50%;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    z-index: 2;
}

.toggle-icons {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 4px;
    z-index: 1;
}

.toggle-icon {
    font-size: 10px;
    transition: all 0.3s ease;
    opacity: 0.7;
}

.custom-toggle.active .active-icon {
    opacity: 1;
    color: white;
    transform: scale(1.1);
}

.custom-toggle:not(.active) .inactive-icon {
    opacity: 1;
    color: #6c757d;
    transform: scale(1.1);
}

.custom-toggle.active .inactive-icon,
.custom-toggle:not(.active) .active-icon {
    opacity: 0;
    transform: scale(0.8);
}

/* Animation for the toggle effect */
.custom-toggle::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: inherit;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.custom-toggle:hover::before {
    opacity: 1;
}

/* Focus styles for accessibility */
.custom-toggle:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(94, 114, 228, 0.25);
}

/* Disabled state */
.custom-toggle:disabled,
.custom-toggle[disabled] {
    opacity: 0.6;
    cursor: not-allowed;
}

.custom-toggle:disabled:hover,
.custom-toggle[disabled]:hover {
    transform: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>
