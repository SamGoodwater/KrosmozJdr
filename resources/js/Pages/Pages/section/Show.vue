<script setup>
/**
 * Pages/section/Show
 *
 * @description
 * Page Inertia minimale pour l’affichage d’une section.
 * (Nécessaire pour les tests Inertia `->component('Pages/section/Show')`.)
 *
 * @example
 * Inertia::render('Pages/section/Show', ['section' => ...])
 */
defineProps({
    section: { type: Object, required: true },
});
</script>

<template>
    <div class="p-4">
        <div class="text-lg font-semibold">Section</div>
        <div class="text-sm opacity-60">
            Vue dédiée minimale (la section est généralement rendue dans la page parente).
        </div>
    </div>
</template>


