<script setup>
/**
 * SelectSearchField Molecule (DaisyUI, Atomic Design)
 * 
 * @description
 * Molecule pour champ de sélection avec recherche, utilisant un Dropdown avec InputCore pour la recherche.
 * Idéal pour les listes longues d'options (ex: types de ressources).
 * 
 * @example
 * // Label simple
 * <SelectSearchField label="Type de ressource" v-model="resourceType" :options="resourceTypes" />
 * 
 * // Avec validation
 * <SelectSearchField 
 *   label="Catégorie" 
 *   v-model="category"
 *   :validation="{ state: 'error', message: 'Catégorie requise' }"
 * />
 */
import { computed, ref, useAttrs } from 'vue';
import Dropdown from '@/Pages/Atoms/action/Dropdown.vue';
import InputCore from '@/Pages/Atoms/data-input/InputCore.vue';
import FieldTemplate from '@/Pages/Molecules/data-input/FieldTemplate.vue';
import useInputField from '@/Composables/form/useInputField';
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper';

// ------------------------------------------
// 🔧 Définition des props et des events
// ------------------------------------------
const props = defineProps({
    ...getInputPropsDefinition('select', 'field'),
    /**
     * Options du select (array de strings ou objets {value, label, disabled})
     */
    options: {
        type: Array,
        default: () => [],
    },
});
const emit = defineEmits(['update:modelValue']);
const $attrs = useAttrs();

// ------------------------------------------
// 🎯 Utilisation du composable unifié
// ------------------------------------------
const {
    // V-model et actions
    currentValue,
    actionsToDisplay,
    inputRef,
    focus,
    isReadonly,
    
    // Attributs et événements
    inputAttrs,
    listeners,
    
    // Labels
    labelConfig,
    
    // Validation
    validationState,
    validationMessage,
    validate,
    resetValidation,
    hasError,
    
    // Méthodes de contrôle de validation
    enableValidation,
    disableValidation,
    
    // Style
    styleProperties,
    containerClasses,
} = useInputField({
    modelValue: props.modelValue,
    type: 'select',
    mode: 'field',
    props,
    attrs: $attrs,
    emit
});

// Recherche interne
const searchQuery = ref('');

// Normaliser le texte pour la recherche (supprimer accents, minuscules)
const normalize = (text) => {
    return String(text ?? '').toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu, '');
};

// Options filtrées selon la recherche
const filteredOptions = computed(() => {
    const opts = props.options || [];
    const q = normalize(searchQuery.value);
    if (!q) return opts;
    return opts.filter((opt) => {
        const label = opt?.label ?? opt?.value ?? opt;
        const value = opt?.value ?? opt;
        return normalize(label).includes(q) || normalize(String(value)).includes(q);
    });
});

// Valeur affichée : on s'appuie sur la prop pour rester synchronisé avec le parent (v-model)
const displayedValue = computed(() => props.modelValue ?? currentValue.value);

// Label de l'option sélectionnée (basé sur la prop pour mise à jour fiable)
const selectedLabel = computed(() => {
    const val = displayedValue.value;
    if (val === null || val === undefined || val === '') {
        return props.placeholder || 'Choisir...';
    }
    const opt = (props.options || []).find((o) => {
        const optValue = o?.value ?? o;
        return String(optValue) === String(val);
    });
    return opt?.label ?? opt?.value ?? val ?? props.placeholder ?? 'Choisir...';
});

// Indique si une option est sélectionnée (pour le style de fond, sans radio)
const isOptionSelected = (optionValue) => {
    const val = displayedValue.value;
    if (val === null || val === undefined) return optionValue == null;
    return String(optionValue ?? '') === String(val);
};

// Gérer la sélection
const handleSelect = (value) => {
    emit('update:modelValue', value);
    searchQuery.value = ''; // Réinitialiser la recherche après sélection
};

// Réinitialiser la recherche quand le dropdown se ferme
const handleDropdownClose = () => {
    searchQuery.value = '';
};

// Exposer les méthodes pour contrôle externe
defineExpose({
    enableValidation,
    disableValidation,
    resetValidation,
    focus,
    validate
});
</script>

<template>
    <FieldTemplate
        :container-classes="containerClasses"
        :label-config="labelConfig"
        :input-attrs="inputAttrs"
        :listeners="listeners"
        :input-ref="inputRef"
        :actions-to-display="actionsToDisplay"
        :style-properties="styleProperties"
        :validation-state="validationState"
        :validation-message="validationMessage"
        :helper="props.helper"
    >
        <!-- Slot core spécifique pour le Dropdown avec recherche -->
        <template #core>
            <Dropdown
                placement="bottom-start"
                :close-on-content-click="true"
                @close="handleDropdownClose"
            >
                <template #trigger>
                    <button
                        type="button"
                        :class="[
                            'select select-bordered w-full text-left',
                            props.size === 'xs' ? 'select-xs' : props.size === 'sm' ? 'select-sm' : props.size === 'md' ? 'select-md' : props.size === 'lg' ? 'select-lg' : props.size === 'xl' ? 'select-xl' : '',
                            hasError ? 'select-error' : '',
                            (isReadonly || props.disabled) ? 'select-disabled' : '',
                            'select-variant-glass'
                        ]"
                        :disabled="props.disabled || isReadonly"
                        ref="inputRef"
                    >
                        <span :class="{ 'opacity-50': (displayedValue == null || displayedValue === '') }">
                            {{ selectedLabel }}
                        </span>
                    </button>
                </template>
                <template #content>
                    <div class="p-3 w-72 space-y-2">
                        <!-- Champ de recherche -->
                        <InputCore
                            type="search"
                            variant="glass"
                            color="primary"
                            :size="props.size"
                            class="w-full"
                            placeholder="Rechercher…"
                            :model-value="searchQuery"
                            @update:model-value="searchQuery = $event"
                            @click.stop
                        />

                        <!-- Option "Tous" / Vide (sans radio, sélection par fond) -->
                        <button
                            v-if="!props.required"
                            type="button"
                            class="w-full flex items-center rounded p-2 text-left text-sm cursor-pointer transition-colors"
                            :class="isOptionSelected(null) ? 'bg-primary/20 text-primary-content' : 'hover:bg-base-200'"
                            @click.stop="handleSelect(null)"
                        >
                            <span>{{ props.placeholder || 'Aucun' }}</span>
                        </button>

                            <!-- Liste des options filtrées (sans radio, sélection par fond) -->
                            <div class="max-h-64 overflow-y-auto pr-1 space-y-1">
                                <button
                                    v-for="option in filteredOptions"
                                    :key="String(option?.value ?? option)"
                                    type="button"
                                    class="w-full flex items-center rounded p-2 text-left text-sm cursor-pointer transition-colors"
                                    :class="[
                                        option?.disabled ? 'opacity-50 cursor-not-allowed' : '',
                                        isOptionSelected(option?.value ?? option) ? 'bg-primary/20 text-primary-content' : 'hover:bg-base-200'
                                    ]"
                                    :disabled="option?.disabled"
                                    @click.stop="!option?.disabled && handleSelect(option?.value ?? option)"
                                >
                                    <span>{{ option?.label ?? option?.value ?? option }}</span>
                                </button>
                            
                            <!-- Message si aucune option trouvée -->
                            <div v-if="filteredOptions.length === 0" class="text-sm text-base-content/60 text-center py-2">
                                Aucune option trouvée
                            </div>
                        </div>
                    </div>
                </template>
            </Dropdown>
        </template>
        
        <!-- Slots personnalisés -->
        <template v-if="$slots.overStart" #overStart>
            <slot name="overStart" />
        </template>
        <template v-if="$slots.overEnd" #overEnd>
            <slot name="overEnd" />
        </template>
        <template #helper>
            <slot name="helper" />
        </template>
    </FieldTemplate>
</template>

<style scoped lang="scss">
// Styles pour le bouton trigger (simule un select)
.select-variant-glass {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 0.5rem;
    transition: all 0.2s ease-in-out;
    
    &:hover:not(:disabled) {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
    }
    
    &:focus:not(:disabled) {
        outline: 2px solid var(--color-primary, #3b82f6);
        outline-offset: 2px;
    }
    
    &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
}
</style>
