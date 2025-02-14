<script setup>
import { ref, computed } from "vue";
import { extractTheme } from "@/Utils/extractTheme";
import Card from "@/Pages/Atoms/panels/Card.vue";
import Image from "@/Pages/Atoms/images/Image.vue";
import Badge from "@/Pages/Atoms/text/Badge.vue";

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
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
    properties: {
        type: Array,
        default: () => [],
    },
    type: {
        type: Array, // Nom du type de module suivi de la couleur
        default: () => [],
    },
    actions: {
        type: Array,
        default: () => [],
        validator: (value) => value.every(action =>
            ['pin', 'favorite', 'view', 'edit', 'share'].includes(action)
        )
    },
});

const actionIcons = {
    pin: {
        icon: "fa-solid fa-thumbtack",
        label: "Épingler"
    },
    favorite: {
        icon: "fa-regular fa-heart",
        label: "Favoris"
    },
    view: {
        icon: "fa-regular fa-eye",
        label: "Voir"
    },
    edit: {
        icon: "fa-regular fa-pen-to-square",
        label: "Éditer"
    },
    share: {
        icon: "fa-solid fa-link",
        label: "Partager"
    }
};

const themeProps = computed(() => extractTheme(props.theme));
</script>

<template>
    <Card theme="w-70">
        <!-- Badge de type -->
        <Badge
            size="sm"
            class="absolute top-[-14px] uppercase z-[-1]"
            :color="props.type.color"
        >
            {{ props.type.name }}
        </Badge>

        <div class="flex gap-4 z-2">
            <!-- Image à gauche -->
            <Image
                v-if="image"
                :src="image"
                :alt="`${title} image`"
                fit="cover"
                position="center"
                rounded="lg"
                theme="w-16 h-16"
            />

            <!-- Contenu à droite -->
            <div class="flex flex-col flex-1 justify-between">
                <!-- Ligne 1: Boutons actions -->
                <div class="flex gap-2 justify-end">
                    <button
                        v-for="action in actions"
                        :key="action"
                        class="btn btn-circle btn-ghost btn-xs"
                        :title="actionIcons[action].label"
                    >
                        <i :class="actionIcons[action].icon"></i>
                    </button>
                </div>

                <!-- Ligne 2: Titre -->
                <div>
                    <h4 class="font-semibold text-title-light dark:text-title-dark">
                        <slot name="title" />
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
