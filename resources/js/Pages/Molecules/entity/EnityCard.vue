/**
* ModuleCard component that displays a card with an image, title, description, and actions.
* Utilizes Atoms components for consistent styling and behavior.
*
* Props:
* - theme (String): The theme of the card. Default is an empty string.
* - title (String): The title of the module. Required.
* - image (String): The source URL of the module image. Default is an empty string.
* - description (String): The description of the module. Default is an empty string.
* - hoverContent (String): The content to display on hover. Default is an empty string.
* - type (Object): The type of the module with name and color. Default is { name: '', color: '' }.
* - actions (Array): The actions available for the module. Default is [].
* Valid values are "pin", "favorite", "view", "edit", "share".
*
* Slots:
* - #title: Custom title content
* - #properties: Custom properties content
* - #content: Custom content
* - #hoverContent: Custom hover content
*/
<script setup>
import { computed } from "vue";
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";
import Card from "@/Pages/Atoms/panels/Card.vue";
import Image from "@/Pages/Atoms/images/Image.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import Btn from "@/Pages/Atoms/actions/Btn.vue";
import BaseTooltip from "@/Pages/Atoms/feedback/BaseTooltip.vue";

const props = defineProps({
    ...commonProps,
    title: {
        type: String,
        required: true,
    },
    image: {
        type: String,
        default: "",
    },
    description: {
        type: String,
        default: "",
    },
    hoverContent: {
        type: String,
        default: "",
    },
    type: {
        type: Object,
        default: () => ({
            name: '',
            color: ''
        }),
        validator: (value) => {
            if (!value) return true;
            return typeof value.name === 'string' && typeof value.color === 'string';
        }
    },
    actions: {
        type: Array,
        default: () => [],
        validator: (value) => value.every(action =>
            ['pin', 'favorite', 'view', 'edit', 'share'].includes(action)
        )
    },
});

const emit = defineEmits(['action']);

const actionIcons = {
    pin: {
        icon: "fa-solid fa-thumbtack",
        label: "Épingler",
        theme: "ghost"
    },
    favorite: {
        icon: "fa-regular fa-heart",
        label: "Favoris",
        theme: "ghost"
    },
    view: {
        icon: "fa-regular fa-eye",
        label: "Voir",
        theme: "ghost"
    },
    edit: {
        icon: "fa-regular fa-pen-to-square",
        label: "Éditer",
        theme: "ghost"
    },
    share: {
        icon: "fa-solid fa-link",
        label: "Partager",
        theme: "ghost"
    }
};

const handleAction = (action) => {
    emit('action', action);
};

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));

const buildCardClasses = () => {
    return [
        'w-70',
        'transition-all',
        'duration-200',
        'hover:scale-[1.02]',
        'hover:shadow-lg'
    ].join(' ');
};
</script>

<template>
    <Card :theme="theme" :size="size" :tooltip="tooltip" :tooltip-position="tooltipPosition"
        :class="buildCardClasses()">
        <!-- Badge de type -->
        <Badge v-if="type.name" size="sm" class="absolute top-[-16px] left-[7px] uppercase z-10" :color="type.color"
            :theme="theme">
            {{ type.name }}
        </Badge>

        <div class="flex gap-4 z-2">
            <!-- Image à gauche -->
            <Image v-if="image" :src="image" :alt="`${title} image`" fit="cover" position="center" rounded="lg"
                theme="w-16 h-16" class="transition-transform duration-200 hover:scale-105" />

            <!-- Contenu à droite -->
            <div class="flex flex-col flex-1 justify-between">
                <!-- Ligne 1: Boutons actions -->
                <div class="flex gap-2 justify-end">
                    <BaseTooltip v-for="action in actions" :key="action" :tooltip="actionIcons[action].label"
                        tooltip-position="bottom">
                        <Btn :theme="actionIcons[action].theme" size="xs" class="btn-circle"
                            @click="handleAction(action)">
                            <i :class="actionIcons[action].icon"></i>
                        </Btn>
                    </BaseTooltip>
                </div>

                <!-- Ligne 2: Titre -->
                <div>
                    <h4 class="font-semibold text-title-light dark:text-title-dark">
                        <span v-if="title">
                            {{ title }}
                        </span>
                        <span v-else>
                            <slot name="title" />
                        </span>
                    </h4>
                </div>

                <!-- Ligne 3: Propriétés -->
                <div class="flex gap-2 flex-wrap text-sm text-content-light dark:text-content-dark">
                    <slot name="properties" />
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="mt-4 text-sm text-content-light dark:text-content-dark">
            <slot name="content" />
        </div>

        <!-- Contenu au survol -->
        <template #hover>
            <div class="mt-4 px-2 text-sm text-content-light dark:text-content-dark">
                <slot name="hoverContent" />
            </div>
        </template>
    </Card>
</template>
