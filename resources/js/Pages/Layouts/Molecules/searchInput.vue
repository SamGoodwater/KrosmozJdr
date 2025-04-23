<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { commonProps } from "@/Utils/commonProps";
import { extractTheme } from "@/Utils/extractTheme";
import BaseTooltip from "@/Pages/Atoms/feedback/BaseTooltip.vue";
import TextInput from "@/Pages/Atoms/inputs/TextInput.vue";

const props = defineProps({
    ...commonProps,
    placeholder: {
        type: String,
        default: "Rechercher"
    },
    shortcut: {
        type: String,
        default: "alt+k"
    },
    searchTypes: {
        type: Array,
        default: () => [
            { name: "pages", color: "page-800" },
            { name: "classes", color: "classe-800" },
            { name: "items", color: "item-800" },
            { name: "resources", color: "resource-800" },
            { name: "consommables", color: "consumable-800" },
            { name: "campagnes", color: "campaign-800" }
        ]
    }
});

const emit = defineEmits(["update:modelValue"]);
const searchBarId = ref(`searchBar-${Math.random().toString(36).substr(2, 9)}`);
const appName = ref(import.meta.env.VITE_APP_NAME);

const handleKeydown = (event) => {
    const [modifier, key] = props.shortcut.split("+");
    if (event[`${modifier}Key`] && event.key.toLowerCase() === key.toLowerCase()) {
        document.getElementById(searchBarId.value).focus();
    }
};

onMounted(() => {
    window.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener("keydown", handleKeydown);
});

const themeProps = computed(() => extractTheme(props.theme));
</script>

<template>
    <div>
        <BaseTooltip :tooltip="{ custom: true }" tooltip-position="bottom">
            <TextInput
                :id="searchBarId"
                :placeholder="placeholder"
                :theme="theme"
                :size="size"
                :rounded="rounded"
                :blur="blur"
                :shadow="shadow"
                :bgColor="bgColor"
                :textColor="textColor"
                :borderColor="borderColor"
                :opacity="opacity"
                @update:modelValue="(value) => emit('update:modelValue', value)"
            >
                <template #inputLabel>
                    <div class="flex items-center gap-2">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 16 16"
                            fill="currentColor"
                            class="h-4 w-4 opacity-70"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                </template>
            </TextInput>

            <template #tooltip>
                <div class="flex flex-col gap-2">
                    <div class="flex flex-nowrap justify-content-between gap-2">
                        <p class="text-sm">Rechercher sur {{ appName }} :</p>
                        <p>
                            <span class="flex flex-nowrap align-items-center">
                                <kbd class="kbd kbd-sm">{{ props.shortcut.split("+")[0] }}</kbd> +
                                <kbd class="kbd kbd-sm">{{ props.shortcut.split("+")[1] }}</kbd>
                            </span>
                        </p>
                    </div>

                    <div class="flex justify-around gap-1">
                        <ul>
                            <li v-for="type in searchTypes" :key="type.name">
                                <span :class="`badge bg-${type.color} m-0.5`">{{ type.name }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </template>
        </BaseTooltip>
    </div>
</template>
