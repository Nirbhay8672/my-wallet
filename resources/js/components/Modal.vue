<template>
    <div
        :id="id"
        class="modal fade"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
    >
        <div
            class="modal-dialog animate__animated animate__slideInDown"
            :class="modalSize"
            :style="customeStyle"
        >
            <div class="modal-content" style="border-radius: 10px !important">
                <div class="modal-header">
                    <h6 class="modal-title font-weight-normal" id="exampleModalLabel"><slot name="modal_title">Modal Title</slot></h6>
                    <button type="button" class="btn bg-gradient-primary btn-sm mt-2" @click="close()">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                    </button>
                </div>

                <div class="modal-body">
                    <slot />
                </div>

                <div class="modal-footer" v-if="!noFooter" :class="footerClass">
                    <slot name="modal_footer"></slot>
                    <button
                        type="button"
                        class="btn bg-gradient-secondary"
                        @click="close()"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, computed, watch } from "vue";

let bootstrapModal = null;

const props = defineProps({
    size: {
        type: String,
        default: "medium",
    },
    id: {
        type: String,
        default: "basic_modal",
    },
    noFooter: {
        type: Boolean,
        default: false,
    },
    focusElement: {
        type: Element,
        default: document.getElementById("basic_modal"),
    },
    headerClass: {
        type: String,
        default: "",
    },
    footerClass: {
        type: String,
        default: "",
    },
    customeStyle: {
        type: String,
        default: "",
    },
});

const emits = defineEmits(["opening", "open", "closing", "close"]);

const modalSize = computed(() => {
    let sizes = {
        small: "modal-sm",
        medium: "modal-md",
        large: "modal-lg",
        extra_large: "modal-xl",
        modal_fullscreen: "modal-fullscreen",
    };

    return sizes[props.size];
});

onMounted(() => {
    let modalElement = document.getElementById(props.id);
    bootstrapModal = new bootstrap.Modal(modalElement);

    modalElement.addEventListener("show.bs.modal", function (event) {
        emits("opening");
    });

    modalElement.addEventListener("shown.bs.modal", function (event) {
        emits("open");
    });

    modalElement.addEventListener("hide.bs.modal", function (event) {
        emits("closing");
    });

    modalElement.addEventListener("hidden.bs.modal", function (event) {
        emits("close");
    });
});

watch(
    () => props.focusElement,
    (newVal, oldVal) => {
        setFocusTrap(newVal);
    }
);

function open() {
    bootstrapModal.show();
}

function close() {
    bootstrapModal.hide();
}

function setFocusTrap(element = null) {
    if (element === null) {
        element = document.getElementById(props.id);
    }

    bootstrapModal._focustrap._config.trapElement = element;
}

defineExpose({
    open,
    close,
});
</script>
