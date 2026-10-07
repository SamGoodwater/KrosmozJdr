<script setup>
/**
 * Onglets de degrés + sélecteur de source des propriétés.
 */
defineProps({
    degrees: { type: Array, default: () => [] },
    activeIndex: { type: Number, default: 0 },
    /** ids des degrés dirty */
    dirtyIds: { type: Object, default: () => new Set() },
    propertiesSource: { type: String, default: 'own' },
    saving: { type: Boolean, default: false },
    showPreviousSource: { type: Boolean, default: true },
});

const emit = defineEmits([
    'select',
    'add',
    'update:propertiesSource',
]);

function degreeTitle(deg) {
    const lvl = Number(deg?.required_level);
    if (!Number.isNaN(lvl) && lvl > 0) return `Niveau ${lvl}`;
    return `Degré ${deg?.position ?? '?'}`;
}

const sources = [
    { value: 'own', label: 'Propres' },
    { value: 'previous', label: 'Degré précédent' },
    { value: 'spell', label: 'Sort de base' },
];
</script>

<template>
    <div class="space-y-2" data-cy="spell-degree-tabs">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div role="tablist" class="tabs tabs-boxed flex-wrap gap-1 bg-base-200/60 p-1">
                <button
                    v-for="(deg, idx) in degrees"
                    :key="deg.id"
                    type="button"
                    role="tab"
                    class="tab tab-sm gap-1"
                    :class="{ 'tab-active': activeIndex === idx }"
                    @click="emit('select', idx)"
                >
                    {{ degreeTitle(deg) }}
                    <span
                        v-if="dirtyIds.has?.(Number(deg.id)) || dirtyIds.has?.(deg.id)"
                        class="inline-block h-1.5 w-1.5 rounded-full bg-warning"
                        title="Modifié"
                    />
                </button>
            </div>
            <button
                type="button"
                class="btn btn-sm btn-primary"
                :disabled="saving"
                data-cy="spell-degree-add"
                @click="emit('add')"
            >
                + Degré
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs text-base-content/70 shrink-0">Propriétés :</span>
            <div class="join">
                <button
                    v-for="src in sources"
                    :key="src.value"
                    type="button"
                    class="btn btn-xs join-item"
                    :class="
                        propertiesSource === src.value
                            ? 'btn-neutral'
                            : 'btn-ghost border border-base-300'
                    "
                    :disabled="saving || (src.value === 'previous' && !showPreviousSource)"
                    @click="emit('update:propertiesSource', src.value)"
                >
                    {{ src.label }}
                </button>
            </div>
        </div>
    </div>
</template>
