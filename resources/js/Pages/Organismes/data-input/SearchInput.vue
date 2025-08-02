<script setup>
/**
 * SearchInput Molecule (Atomic Design, DaisyUI)
 *
 * @description
 * Champ de recherche atomique pour le layout, utilisant InputField (atom), Icon (atom) et Kbd (atom).
 * - Affiche un champ de recherche avec icône et raccourci clavier (ex: alt+k)
 * - Utilise InputField pour l'accessibilité, l'API et la cohérence
 * - Icône de recherche via slot icon (atom Icon)
 * - Raccourci clavier affiché à droite via atom Kbd
 * - Focus automatique sur le champ via le raccourci
 *
 * @props {String} placeholder - Placeholder du champ (défaut: 'Rechercher')
 * @props {String} shortcut - Raccourci clavier pour focus (défaut: 'alt+k')
 * @emits update:modelValue
 *
 * @see InputField, Icon, Kbd
 */
import { ref, onMounted, onUnmounted } from "vue";
import { getCommonProps } from "@/Utils/atomic-design/uiHelper";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Kbd from "@/Pages/Atoms/data-display/Kbd.vue";

const props = defineProps({
    ...getCommonProps(),
    placeholder: {
        type: String,
        default: "Rechercher",
    },
    shortcut: {
        type: String,
        default: "alt+k",
    },
    searchTypes: {
        type: Array,
        default: () => [
            { name: "pages", color: "page-800" },
            { name: "classes", color: "classe-800" },
            { name: "items", color: "item-800" },
            { name: "resources", color: "resource-800" },
            { name: "consommables", color: "consumable-800" },
            { name: "campagnes", color: "campaign-800" },
        ],
    },
});

const emit = defineEmits(["update:modelValue"]);
// Génération d'un ID unique et robuste
const searchBarId = ref(`searchBar-${Date.now()}-${Math.random().toString(36).substring(2, 11)}`);

const handleKeydown = (event) => {
    const [modifier, key] = props.shortcut.split("+");
    if (
        event[`${modifier}Key`] &&
        event.key.toLowerCase() === key.toLowerCase()
    ) {
        document.getElementById(searchBarId.value)?.focus();
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
    <div class="w-full flex items-center gap-2">
        <InputField
            :id="searchBarId"
            :placeholder="placeholder"
            @update:modelValue="(value) => emit('update:modelValue', value)"
            class="w-full"
        >
            <template #labelInEnd>
                <Icon
                    source="fa-magnifying-glass"
                    alt="Rechercher"
                    size="md"
                    pack="solid"
                    class="opacity-70"
                />
            </template>
        </InputField>
        <Kbd size="sm" class="ml-2">{{ props.shortcut }}</Kbd>
    </div>
</template>
