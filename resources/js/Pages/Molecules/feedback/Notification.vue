<script setup>
import { ref, computed, onMounted } from 'vue';
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";
import Btn from '@/Pages/Atoms/actions/Btn.vue';
import Route from '@/Pages/Atoms/action/Route.vue';
import BaseTooltip from './BaseTooltip.vue';

const props = defineProps({
    ...commonProps,
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

const buildNotificationClasses = (props) => {
    const classes = ['custom-notification', 'min-w-[200px]', 'max-w-[400px]'];

    // Si une route est définie, ajouter le curseur pointer
    if (props.route) {
        classes.push('cursor-pointer');
    }

    // Ajout des classes communes
    const baseClasses = generateClasses(props);
    if (baseClasses) {
        classes.push(baseClasses);
    }

    return classes.join(' ');
};

const buildAlertClasses = () => {
    const classes = ['alert', 'relative', 'shadow-lg', 'backdrop-blur-3'];

    //theme
    switch (props.theme) {
        case 'info':
            classes.push('bg-info-900/90');
            break;
        case 'success':
            classes.push('bg-success-900/90');
            break;
        case 'error':
            classes.push('bg-error-900/90');
            break;
        case 'warning':
            classes.push('bg-warning-900/90');
            break;
        default:
            classes.push('bg-secondary-900/90');
            break;
    }

    return classes.join(' ');
};

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));
const notificationClasses = computed(() => buildNotificationClasses(combinedProps.value));
const alertClasses = computed(() => buildAlertClasses());

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
        <BaseTooltip v-if="isVisible" :tooltip="tooltip" :tooltip-position="tooltipPosition">
            <div :class="notificationClasses">
                <Route v-if="route" :route="route">
                    <div :class="[alertClasses, 'is-link']">
                        <span class="flex-1">{{ message }}</span>
                        <Btn @click="close" theme="xs link" color="slate-600" class="absolute top-2 right-2">
                            <i class="fa-solid fa-xmark"></i>
                        </Btn>
                    </div>
                </Route>
                <div v-else :class="alertClasses">
                    <span class="flex-1">{{ message }}</span>
                    <Btn @click="close" theme="xs link" color="secondary" class="absolute top-2 right-2">
                        <i class="fa-solid fa-xmark"></i>
                    </Btn>
                </div>
            </div>
            <template v-if="typeof tooltip === 'object'" #tooltip>
                <slot name="tooltip" />
            </template>
        </BaseTooltip>
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
