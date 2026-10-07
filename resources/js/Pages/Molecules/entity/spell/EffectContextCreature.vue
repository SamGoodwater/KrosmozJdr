<script setup>
/**
 * Contexte créature invoquée.
 */
import EntityPickerCore from '@/Pages/Organismes/entity/EntityPickerCore.vue';

defineProps({
    modelValue: { type: [Number, String, null], default: null },
    helper: {
        type: String,
        default: 'Choisissez la créature invoquée par cet effet.',
    },
});

const emit = defineEmits(['update:modelValue']);
</script>

<template>
    <div class="space-y-1 max-w-xl">
        <label class="text-xs font-medium text-base-content/80">Créature invoquée</label>
        <EntityPickerCore
            entity-type="creatures"
            :model-value="modelValue || null"
            variant="compact"
            placeholder="Rechercher une créature…"
            @update:model-value="emit('update:modelValue', $event)"
        />
        <p class="text-xs text-base-content/70">{{ helper }}</p>
    </div>
</template>
