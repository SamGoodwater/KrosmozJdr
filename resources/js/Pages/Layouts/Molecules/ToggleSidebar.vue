<script setup>
import { ref, computed } from "vue";
import tooltips from "@/Pages/Atoms/feedback/Tooltip.vue";
import { useSidebar } from "@/Composables/useSidebar";

const { toggleSidebar, isSidebarOpen } = useSidebar();

const props = defineProps({
    size: {
        type: String,
        default: "md",
        validator: (value) =>
            ["", "xs", "sm", "md", "lg", "xl", "2xl"].includes(value),
    },
});

const getSize = computed(() => {
    switch (props.size) {
        case "xs":
            return 16;
        case "sm":
            return 24;
        case "md":
            return 32;
        case "lg":
            return 48;
        case "xl":
            return 64;
        case "2xl":
            return 96;
        default:
            return 32;
    }
});
</script>

<template>
    <div>
        <tooltips bgColor="bg-secondary-900/70" placement="bottom-start">
            <button
                @click="toggleSidebar"
                class="btn btn-circle dark:bg-secondary-800/10 bg-secondary-200/10 backdrop-blur-md border-none"
            >
                <svg
                    v-if="!isSidebarOpen"
                    :class="[
                        'fill-current',
                        'hover:opacity-50',
                        'tooltip',
                        'tooltip-bottom',
                    ]"
                    :data-tip="
                        isSidebarOpen ? 'Masquer le menu' : 'Afficher le menu'
                    "
                    xmlns="http://www.w3.org/2000/svg"
                    :width="getSize"
                    :height="getSize"
                    viewBox="0 -960 960 960"
                >
                    <path
                        d="M120-240v-60h720v60H120Zm0-210v-60h720v60H120Zm0-210v-60h720v60H120Z"
                    />
                </svg>

                <svg
                    v-if="isSidebarOpen"
                    :class="[
                        'fill-current',
                        'hover:opacity-50',
                        'tooltip',
                        'tooltip-bottom',
                    ]"
                    xmlns="http://www.w3.org/2000/svg"
                    :width="getSize"
                    :height="getSize"
                    viewBox="0 -960 960 960"
                >
                    <path
                        d="m249-207-42-42 231-231-231-231 42-42 231 231 231-231 42 42-231 231 231 231-42 42-231-231-231 231Z"
                    />
                </svg>
            </button>
            <template #content>
                <p>
                    Masquer ou afficher le menu
                    <span class="flex flex-nowrap align-items-center"
                        ><kbd class="kbd kbd-sm">alt</kbd> +
                        <kbd class="kbd kbd-sm">g</kbd></span
                    >
                </p>
            </template>
        </tooltips>
    </div>
</template>

<style scoped></style>
