<script setup>
/**
 * Édition de la configuration d'une section CharacteristicNorms.
 * Permet de sélectionner la caractéristique, le groupe et l'entité.
 */
import InputField from '@/Pages/Molecules/data-input/InputField.vue';

defineProps({
    section: { type: Object, required: true },
    data: { type: Object, default: () => ({}) },
    settings: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['update:settings']);

function update(key, value) {
    emit('update:settings', { ...arguments[0]?.settings ?? {}, [key]: value });
}
</script>

<template>
    <div class="space-y-4">
        <InputField
            label="Clé de la caractéristique"
            :model-value="settings.characteristic_key || ''"
            @update:model-value="emit('update:settings', { ...settings, characteristic_key: $event })"
            placeholder="strength_creature"
            helper="Clé unique, visible dans l'administration des caractéristiques."
        />

        <div>
            <label class="label"><span class="label-text">Groupe</span></label>
            <select
                class="select select-bordered select-sm w-full"
                :value="settings.group || 'creature'"
                @change="emit('update:settings', { ...settings, group: $event.target.value })"
            >
                <option value="creature">Créature</option>
                <option value="object">Objet</option>
                <option value="spell">Sort</option>
            </select>
        </div>

        <InputField
            label="Entité"
            :model-value="settings.entity || '*'"
            @update:model-value="emit('update:settings', { ...settings, entity: $event })"
            placeholder="*"
            helper="* = toutes les entités du groupe. Sinon : monster, class, item, spell, etc."
        />
    </div>
</template>
