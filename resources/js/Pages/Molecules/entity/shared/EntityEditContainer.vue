<script setup>
/**
 * Conteneur thématique d’édition d’entité (titre, icône, repli optionnel).
 *
 * @example
 * <EntityEditContainer title="Description" icon="fa-solid fa-align-left" span="1">
 *   …
 * </EntityEditContainer>
 */
import { ref, watch } from 'vue';

const props = defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
    icon: { type: String, default: '' },
    /** Nombre de colonnes de grille à couvrir (1–3). */
    span: { type: [Number, String], default: 1 },
    collapsible: { type: Boolean, default: false },
    defaultOpen: { type: Boolean, default: true },
    /** Classes utilitaires additionnelles sur le root. */
    rootClass: { type: String, default: '' },
});

const open = ref(props.defaultOpen);
watch(
    () => props.defaultOpen,
    (v) => {
        open.value = v;
    },
);

const spanClass = {
    1: '',
    2: 'lg:col-span-2',
    3: 'lg:col-span-2 2xl:col-span-3',
};
</script>

<template>
    <section
        class="entity-edit-container rounded-box border border-base-300 bg-base-100 min-w-0"
        :class="[spanClass[Number(span)] || '', rootClass]"
        data-cy="entity-edit-container"
    >
        <header
            class="flex items-start gap-2 px-3 py-2 border-b border-base-300"
            :class="collapsible ? 'cursor-pointer select-none' : ''"
            @click="collapsible ? (open = !open) : undefined"
        >
            <i
                v-if="icon"
                :class="icon"
                class="mt-0.5 text-sm text-base-content/70 shrink-0"
                aria-hidden="true"
            />
            <div class="min-w-0 flex-1">
                <h3 class="text-sm font-semibold text-base-content leading-tight">{{ title }}</h3>
                <p v-if="subtitle" class="text-xs text-base-content/70 mt-0.5 leading-snug">
                    {{ subtitle }}
                </p>
            </div>
            <slot name="actions" />
            <button
                v-if="collapsible"
                type="button"
                class="btn btn-ghost btn-xs btn-square shrink-0"
                :aria-expanded="open"
                @click.stop="open = !open"
            >
                <i
                    class="fa-solid text-xs"
                    :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"
                    aria-hidden="true"
                />
            </button>
        </header>
        <div v-show="!collapsible || open" class="p-3">
            <slot />
        </div>
    </section>
</template>
