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
import { useGlobalEntitySearch } from "@/Composables/entity/useGlobalEntitySearch";
import { router } from "@inertiajs/vue3";

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

// Recherche globale (moteur d'entités)
const {
    query,
    results,
    loading,
    isOpen,
    hasResults,
    setQuery,
    close,
} = useGlobalEntitySearch();

const handleInput = (value) => {
    emit("update:modelValue", value);
    setQuery(value);
};

const handleSelectResult = (result) => {
    if (result?.href) {
        router.visit(result.href);
    }
    close();
};

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
    <div class="w-full flex items-center gap-2 relative">
        <div class="w-full">
            <InputField
                :id="searchBarId"
                :placeholder="placeholder"
                @update:modelValue="handleInput"
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

            <!-- Dropdown résultats de recherche globale -->
            <div
                v-if="isOpen && hasResults"
                class="absolute left-0 right-0 mt-2 z-30"
            >
                <div class="bg-base-100/95 backdrop-blur border border-base-300 rounded-xl shadow-xl max-h-96 overflow-y-auto">
                    <ul class="divide-y divide-base-200">
                        <li
                            v-for="result in results"
                            :key="`${result.entityType}-${result.id}`"
                        >
                            <button
                                type="button"
                                class="w-full flex items-start gap-3 px-3 py-2 text-sm hover:bg-base-200/70 transition-colors text-left"
                                @click="handleSelectResult(result)"
                            >
                                <span class="mt-0.5">
                                    <Icon
                                        v-if="result.icon"
                                        :source="result.icon.replace('fa ', '')"
                                        pack="custom"
                                        size="sm"
                                        alt=""
                                    />
                                </span>
                                <span class="flex-1 min-w-0">
                                    <span class="block font-medium truncate">
                                        {{ result.title }}
                                    </span>
                                    <span v-if="result.subtitle" class="block text-xs text-base-content/70 truncate">
                                        {{ result.subtitle }}
                                    </span>
                                    <span class="block text-[11px] uppercase tracking-wide text-base-content/50 mt-0.5">
                                        {{ result.group }}
                                    </span>
                                </span>
                            </button>
                        </li>
                    </ul>

                    <div v-if="loading" class="px-3 py-2 text-xs text-base-content/60">
                        Chargement…
                    </div>
                </div>
            </div>
        </div>
        <Kbd size="sm" class="ml-2">{{ props.shortcut }}</Kbd>
    </div>
</template>
