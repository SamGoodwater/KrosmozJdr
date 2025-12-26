<script setup>
/**
 * Pages/section/Index
 *
 * @description
 * Page Inertia minimale pour la liste des sections.
 * (Nécessaire pour les tests Inertia `->component('Pages/section/Index')`.)
 *
 * @example
 * Inertia::render('Pages/section/Index', ['sections' => ...])
 */
defineProps({
    sections: { type: Object, required: true },
});
</script>

<template>
    <div class="p-4">
        <div class="text-lg font-semibold">Sections</div>
        <div class="text-sm opacity-60">
            Cette page est principalement utilisée pour l’admin et pour les tests.
        </div>
    </div>
</template>


