<script setup>
import { ref, computed, onMounted } from 'vue';
import { extractTheme } from "@/Utils/extractTheme";
import Btn from '@/Pages/Atoms/actions/Btn.vue';
import Route from '@/Pages/Atoms/text/Route.vue';

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
    delay: {
        type: Number,
        default: 3000,
        validator: (value) => value >= 0
    },
    message: {
        type: String,
        required: true
    },
    route: {
        type: String,
        default: ""
    }
});

const emit = defineEmits(['close']);

const isVisible = ref(true);

const buildNotificationClasses = (themeProps, props) => {
    const classes = ['custom-notification', 'min-w-[200px]', 'max-w-[400px]'];

    // Si une route est définie, ajouter le curseur pointer
    if (props.route) {
        classes.push('cursor-pointer');
    }

    return classes.join(' ');
};

const buildAlertClasses = (themeProps) => {
    const classes = ['alert', 'relative', 'shadow-lg'];

    // Background color and opacity
    const bgColor = themeProps.bgColor || 'secondary-800';
    const opacity = themeProps.opacity || '90';
    classes.push(`bg-${bgColor}/${opacity}`);

    // Blur
    const blur = themeProps.blur || '3';
    classes.push(`backdrop-blur-${blur}`);

    return classes.join(' ');
};

const themeProps = computed(() => extractTheme(props.theme));
const notificationClasses = computed(() => buildNotificationClasses(themeProps.value, props));
const alertClasses = computed(() => buildAlertClasses(themeProps.value));

const close = (e) => {
    if (e) {
        e.stopPropagation();
    }
    isVisible.value = false;
    emit('close');
};

onMounted(() => {
    if (props.delay > 0) {
        setTimeout(() => {
            close();
        }, props.delay);
    }
});
</script>

<template>
    <Transition name="fade">
        <div v-if="isVisible" :class="notificationClasses">
            <Route v-if="route" :route="route">
                <div :class="[alertClasses, 'is-link']">
                    <span class="flex-1">{{ message }}</span>
                    <Btn
                        @click="close"
                        theme="xs link"
                        color="slate-600"
                        class="absolute top-2 right-2"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </Btn>
                </div>
            </Route>
            <div v-else :class="alertClasses">
                <span class="flex-1">{{ message }}</span>
                <Btn
                    @click="close"
                    theme="xs link"
                    color="slate-600"
                    class="absolute top-2 right-2"
                >
                    <i class="fa-solid fa-xmark"></i>
                </Btn>
            </div>
        </div>
    </Transition>
</template>

<style scoped lang="scss">
.fade-enter-active,
.fade-leave-active {
    transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(30px);
}

.custom-notification {
    @apply pointer-events-auto;
    display: flex;
    min-width: fit-content;
    white-space: nowrap;
}

.is-link {
    &:hover {
        box-shadow:
        0 0 1px 1px rgba(255, 255, 255, 0.10),
        0 0 2px 4px rgba(255, 255, 255, 0.05),
        0 0 3px 6px rgba(255, 255, 255, 0.01),
        inset 0 0 2px 4px rgba(255, 255, 255, 0.05),
        inset 0 0 3px 6px rgba(255, 255, 255, 0.01);
    }
}
</style>
