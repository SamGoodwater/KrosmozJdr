<script setup>
/**
 * Rappel commande Artisan + lien planning cron sur les pages admin thématiques.
 */
import { Link } from '@inertiajs/vue3';

defineProps({
    signature: { type: String, required: true },
    cronKey: { type: String, default: null },
    cronCommand: { type: String, default: null },
});
</script>

<template>
    <p class="mt-2 text-xs text-base-content/60">
        Commande :
        <code class="rounded bg-base-300 px-1">{{ signature }}</code>
        <template v-if="cronKey">
            — cron
            <code class="rounded bg-base-300 px-1">{{ cronKey }}</code>
            <template v-if="cronCommand">
                (<code class="rounded bg-base-300 px-1">{{ cronCommand }}</code
                >)
            </template>
            ·
            <Link :href="route('admin.project-schedule.index')" class="link link-hover">Planning</Link>
        </template>
    </p>
</template>
