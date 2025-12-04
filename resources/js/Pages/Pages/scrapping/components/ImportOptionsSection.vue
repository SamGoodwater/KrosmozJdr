<script setup>
/**
 * Section des options d'import globales
 */
import { ref } from 'vue';
import Card from '@/Pages/Atoms/data-display/Card.vue';
import CheckboxField from '@/Pages/Molecules/data-input/CheckboxField.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';

const props = defineProps({
    modelValue: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['update:modelValue']);

const options = ref({
    skipCache: props.modelValue.skipCache ?? false,
    forceUpdate: props.modelValue.forceUpdate ?? false,
    dryRun: props.modelValue.dryRun ?? false,
    validateOnly: props.modelValue.validateOnly ?? false,
    includeRelations: props.modelValue.includeRelations ?? true, // Par défaut, les relations sont importées
});

const updateOption = (key, value) => {
    options.value[key] = value;
    emit('update:modelValue', { ...options.value });
};
</script>

<template>
    <details class="collapse collapse-arrow border border-base-300 bg-base-200/40">
        <summary class="collapse-title text-sm font-medium text-primary-100 py-2 px-4 min-h-0">
            <div class="flex items-center gap-2">
                <Icon source="fa-solid fa-gear" alt="Options" pack="solid" class="text-primary-300 text-xs" />
                <span>Options d'import</span>
            </div>
        </summary>
        <div class="collapse-content p-4 pt-2">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <CheckboxField
                    :model-value="options.skipCache"
                    @update:model-value="updateOption('skipCache', $event)"
                    label="Ignorer le cache"
                >
                    <template #helper>
                        <Tooltip content="Force la récupération depuis DofusDB sans utiliser le cache">
                            <Icon source="fa-solid fa-circle-question" alt="Aide" pack="solid" class="text-primary-300 text-xs" />
                        </Tooltip>
                    </template>
                </CheckboxField>

                <CheckboxField
                    :model-value="options.forceUpdate"
                    @update:model-value="updateOption('forceUpdate', $event)"
                    label="Forcer la mise à jour"
                >
                    <template #helper>
                        <Tooltip content="Met à jour l'entité même si elle existe déjà">
                            <Icon source="fa-solid fa-circle-question" alt="Aide" pack="solid" class="text-primary-300 text-xs" />
                        </Tooltip>
                    </template>
                </CheckboxField>

                <CheckboxField
                    :model-value="options.dryRun"
                    @update:model-value="updateOption('dryRun', $event)"
                    label="Mode simulation"
                >
                    <template #helper>
                        <Tooltip content="Simule l'import sans sauvegarder en base de données">
                            <Icon source="fa-solid fa-circle-question" alt="Aide" pack="solid" class="text-primary-300 text-xs" />
                        </Tooltip>
                    </template>
                </CheckboxField>

                <CheckboxField
                    :model-value="options.validateOnly"
                    @update:model-value="updateOption('validateOnly', $event)"
                    label="Validation uniquement"
                >
                    <template #helper>
                        <Tooltip content="Valide les données sans les importer">
                            <Icon source="fa-solid fa-circle-question" alt="Aide" pack="solid" class="text-primary-300 text-xs" />
                        </Tooltip>
                    </template>
                </CheckboxField>

                <CheckboxField
                    :model-value="options.includeRelations"
                    @update:model-value="updateOption('includeRelations', $event)"
                    label="Importer les relations"
                >
                    <template #helper>
                        <Tooltip content="Importe automatiquement les entités liées (sorts d'une classe, ressources d'un monstre, recettes d'un objet, etc.)">
                            <Icon source="fa-solid fa-circle-question" alt="Aide" pack="solid" class="text-primary-300 text-xs" />
                        </Tooltip>
                    </template>
                </CheckboxField>
            </div>
        </div>
    </details>
</template>

