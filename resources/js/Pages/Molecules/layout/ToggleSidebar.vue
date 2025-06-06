<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import BaseTooltip from "@/Pages/Atoms/feedback/BaseTooltip.vue";
import { useSidebar } from "@/Composables/layout/useSidebar";

const { toggleSidebar, isSidebarOpen } = useSidebar();

const props = defineProps({
    size: {
        type: String,
        default: "md",
        validator: (value) => ["xs", "sm", "md", "lg", "xl", "2xl"].includes(value),
    },
    shortcut: {
        type: String,
        default: "alt+g"
    }
});

const getSize = computed(() => {
    switch (props.size) {
        case "xs": return 16;
        case "sm": return 24;
        case "md": return 32;
        case "lg": return 48;
        case "xl": return 64;
        case "2xl": return 96;
        default: return 32;
    }
});

const handleKeydown = (event) => {
    const [modifier, key] = props.shortcut.split("+");
    if (event[`${modifier}Key`] && event.key.toLowerCase() === key.toLowerCase()) {
        toggleSidebar();
    }
};

onMounted(() => {
    window.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener("keydown", handleKeydown);
});
</script>

<template>
    <div>
        <BaseTooltip :tooltip="{ custom: true }" tooltip-position="bottom-start">
            <button @click="toggleSidebar"
                class="btn btn-circle dark:bg-secondary-800/10 bg-secondary-200/10 backdrop-blur-md border-none">
                <svg v-if="!isSidebarOpen" :class="['fill-current', 'hover:opacity-50']"
                    xmlns="http://www.w3.org/2000/svg" :width="getSize" :height="getSize" viewBox="0 -960 960 960">
                    <path d="M120-240v-60h720v60H120Zm0-210v-60h720v60H120Zm0-210v-60h720v60H120Z" />
                </svg>

                <svg v-if="isSidebarOpen" :class="['fill-current', 'hover:opacity-50']"
                    xmlns="http://www.w3.org/2000/svg" :width="getSize" :height="getSize" viewBox="0 -960 960 960">
                    <path
                        d="m249-207-42-42 231-231-231-231 42-42 231 231 231-231 42 42-231 231 231 231-42 42-231-231-231 231Z" />
                </svg>
            </button>

            <template #tooltip>
                <div class="flex flex-col gap-2">
                    <p>Masquer ou afficher le menu</p>
                    <div class="flex flex-nowrap align-items-center gap-1">
                        <kbd class="kbd kbd-sm">{{ props.shortcut.split("+")[0] }}</kbd>
                        <span>+</span>
                        <kbd class="kbd kbd-sm">{{ props.shortcut.split("+")[1] }}</kbd>
                    </div>
                </div>
            </template>
        </BaseTooltip>
    </div>
</template>

<style scoped></style>
