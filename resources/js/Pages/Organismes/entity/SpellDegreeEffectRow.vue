<script setup>
/**
 * @deprecated Préférer {@link SpellEffectEditorRow}. Conservé pour compatibilité des imports.
 */
import SpellEffectEditorRow from '@/Pages/Organismes/entity/SpellEffectEditorRow.vue';

defineProps({
    row: { type: Object, required: true },
    index: { type: Number, required: true },
    options: { type: Object, default: () => ({}) },
});

defineEmits(['dirty', 'duplicate', 'remove']);
</script>

<template>
    <SpellEffectEditorRow
        :row="row"
        :index="index"
        :options="options"
        @dirty="$emit('dirty')"
        @duplicate="$emit('duplicate')"
        @remove="$emit('remove')"
    />
</template>
