<script setup>
/**
* EntityCard Molecule (Atomic Design, DaisyUI)
*
* @description
* Carte d'entité affichant une image, un titre, une description, des propriétés et des actions.
* - Utilise les atoms Card, Image, Badge, Btn, Tooltip, Icon
* - Actions avec tooltip et icône atomique
* - Slots pour titre, propriétés, contenu, hoverContent
*
* @props {String} title - Titre de l'entité (requis)
* @props {String} image - URL de l'image (optionnel)
* @props {String} description - Description (optionnel)
* @props {Object} type - { name, color } (optionnel)
* @props {Array} actions - Actions disponibles (ex: ['pin', 'favorite', 'view', 'edit', 'share'])
*
* @slot title - Titre custom
* @slot properties - Propriétés custom
* @slot content - Contenu custom
* @slot hoverContent - Contenu au survol
*
* @see Card, Image, Badge, Btn, Tooltip, Icon
*/
import { computed } from "vue";
import Card from "@/Pages/Atoms/data-display/Card.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";

const props = defineProps({
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
    type: {
        type: Object,
        default: () => ({ name: '', color: '' }),
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

const handleAction = (action) => {
    emit('action', action);
};

const buildCardClasses = () => [
    'w-70',
    'transition-all',
    'duration-200',
    'hover:scale-[1.02]',
    'hover:shadow-lg'
].join(' ');
</script>

<template>
    <Card :class="buildCardClasses()">
        <!-- Badge de type -->
        <Badge v-if="type.name" size="sm" class="absolute top-[-16px] left-[7px] uppercase z-10" :color="type.color">
            {{ type.name }}
        </Badge>

        <div class="flex gap-4 z-2">
            <!-- Image à gauche -->
            <Image v-if="image" :src="image" :alt="`${title} image`" fit="cover" position="center" rounded="lg"
                size="lg" class="transition-transform duration-200 hover:scale-105" />

            <!-- Contenu à droite -->
            <div class="flex flex-col flex-1 justify-between">
                <!-- Ligne 1: Boutons actions -->
                <div class="flex gap-2 justify-end">
                    <Tooltip v-for="action in actions" :key="action" :content="actionIcons[action].label"
                        placement="bottom">
                        <Btn variant="ghost" size="xs" circle
                            @click="handleAction(action)">
                            <Icon :source="actionIcons[action].icon" :alt="actionIcons[action].label" size="sm" />
                        </Btn>
                    </Tooltip>
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
