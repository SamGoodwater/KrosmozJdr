<script setup>
/**
 * Panneau de suivi d’un job Artisan admin : pourcentage + sortie console filtrée.
 */
import { nextTick, ref, watch } from "vue";
import { consoleJobStatusLabel } from "@/Composables/admin/useProjectConsoleJob";

const props = defineProps({
    job: { type: Object, default: null },
    pollError: { type: String, default: "" },
});

const logEl = ref(null);

watch(
    () => props.job?.output,
    async () => {
        await nextTick();
        if (logEl.value) {
            logEl.value.scrollTop = logEl.value.scrollHeight;
        }
    },
);
</script>

<template>
    <section
        v-if="job"
        class="rounded-box border border-base-content/10 bg-base-100/50 p-4 space-y-3"
        aria-live="polite"
    >
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-lg font-medium">Suivi du job</h2>
            <span class="badge badge-outline">{{ consoleJobStatusLabel(job.status) }}</span>
        </div>
        <p class="text-sm text-base-content/80">
            {{ job.progress_label || consoleJobStatusLabel(job.status) }}
            — <strong>{{ job.progress ?? 0 }} %</strong>
        </p>
        <progress class="progress progress-primary w-full" :value="job.progress ?? 0" max="100" />
        <p v-if="job.command" class="text-xs text-base-content/50 font-mono break-all">{{ job.command }}</p>
        <p v-if="job.error" class="text-sm text-error">{{ job.error }}</p>
        <p v-if="pollError" class="text-sm text-warning">{{ pollError }}</p>
        <pre
            ref="logEl"
            class="mt-1 max-h-80 overflow-auto rounded-box bg-neutral text-neutral-content p-3 text-xs font-mono whitespace-pre-wrap break-all"
        >{{ job.output || "En attente de sortie…" }}</pre>
        <p class="text-[11px] text-base-content/50">
            Sortie filtrée (codes ANSI retirés, secrets masqués).
        </p>
    </section>
</template>
