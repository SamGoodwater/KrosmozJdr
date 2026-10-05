<script setup>
/**
 * Panneau de suivi d’une restauration (fichier local, hors file database).
 */
import { computed, nextTick, ref, watch } from 'vue';

const props = defineProps({
    status: { type: Object, default: null },
});

const logEl = ref(null);

const phases = [
    { key: 'verify', label: 'Vérification' },
    { key: 'safety', label: 'Secours' },
    { key: 'extract', label: 'Extraction' },
    { key: 'maintenance', label: 'Maintenance' },
    { key: 'database', label: 'Base' },
    { key: 'storage', label: 'Storage' },
    { key: 'game', label: 'Game' },
    { key: 'finalize', label: 'Finalisation' },
    { key: 'done', label: 'Terminé' },
];

const phaseOrder = phases.map((p) => p.key);

const busy = computed(() => {
    const state = props.status?.state;
    return state === 'queued' || state === 'running';
});

const progress = computed(() => Number(props.status?.progress ?? (busy.value ? 5 : 0)));

const stateLabel = computed(() => {
    const state = props.status?.state;
    if (state === 'queued') return 'En file';
    if (state === 'running') return 'En cours';
    if (state === 'success') return 'Succès';
    if (state === 'failed') return 'Échec';
    if (state === 'interrupted') return 'Interrompu';
    return state || '—';
});

const badgeClass = computed(() => {
    const state = props.status?.state;
    if (state === 'success') return 'badge-success';
    if (state === 'failed' || state === 'interrupted') return 'badge-error';
    if (state === 'running' || state === 'queued') return 'badge-warning';
    return 'badge-ghost';
});

const elapsedLabel = computed(() => {
    const started = props.status?.started_at;
    if (!started) return null;
    const start = Date.parse(started);
    if (Number.isNaN(start)) return null;
    const end = props.status?.finished_at ? Date.parse(props.status.finished_at) : Date.now();
    if (Number.isNaN(end)) return null;
    const seconds = Math.max(0, Math.round((end - start) / 1000));
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return m > 0 ? `${m} min ${s} s` : `${s} s`;
});

function phaseIndex(key) {
    const idx = phaseOrder.indexOf(key);
    return idx === -1 ? -1 : idx;
}

function phaseState(key) {
    const current = props.status?.phase || 'verify';
    const currentIdx = phaseIndex(current);
    const idx = phaseIndex(key);
    if (
        (props.status?.state === 'failed' || props.status?.state === 'interrupted')
        && idx === currentIdx
    ) {
        return 'error';
    }
    if (props.status?.state === 'success') return 'done';
    if (idx < currentIdx) return 'done';
    if (idx === currentIdx) return 'active';
    return 'pending';
}

watch(
    () => props.status?.log?.length,
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
        v-if="status"
        class="rounded-box border border-base-content/15 bg-base-100/70 p-4 space-y-3"
        aria-live="polite"
        aria-label="Suivi de la restauration"
    >
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-lg font-medium">Suivi restauration</h2>
            <div class="flex items-center gap-2">
                <span class="badge badge-outline" :class="badgeClass">{{ stateLabel }}</span>
                <span v-if="elapsedLabel" class="text-xs text-base-content/60">{{ elapsedLabel }}</span>
            </div>
        </div>

        <p v-if="status.archive" class="text-xs font-mono break-all text-base-content/70">
            Archive : {{ status.archive }}
        </p>
        <p v-if="status.safety_archive" class="text-xs font-mono break-all text-base-content/70">
            Secours : {{ status.safety_archive }}
        </p>

        <p class="text-sm text-base-content/90">
            {{ status.message || 'En attente…' }}
            <strong v-if="busy || status.state === 'success' || status.state === 'failed'">
                — {{ progress }} %
            </strong>
        </p>

        <progress
            class="progress w-full"
            :class="{
                'progress-warning': busy,
                'progress-success': status.state === 'success',
                'progress-error': status.state === 'failed' || status.state === 'interrupted',
                'progress-primary':
                    !busy
                    && status.state !== 'success'
                    && status.state !== 'failed'
                    && status.state !== 'interrupted',
            }"
            :value="progress"
            max="100"
        />

        <ol class="flex flex-wrap gap-2" aria-label="Étapes de restauration">
            <li
                v-for="step in phases.filter((p) => p.key !== 'finalize')"
                :key="step.key"
                class="badge badge-sm"
                :class="{
                    'badge-success': phaseState(step.key) === 'done',
                    'badge-warning': phaseState(step.key) === 'active',
                    'badge-error': phaseState(step.key) === 'error',
                    'badge-ghost opacity-50': phaseState(step.key) === 'pending',
                }"
            >
                {{ step.label }}
            </li>
        </ol>

        <pre
            ref="logEl"
            class="max-h-64 overflow-auto rounded-box border border-base-content/15 bg-base-200 text-base-content p-3 text-xs font-mono whitespace-pre-wrap break-all"
        >{{
            Array.isArray(status.log) && status.log.length
                ? status.log.join('\n')
                : 'En attente de logs…'
        }}</pre>

        <p class="text-[11px] text-base-content/50">
            Suivi fichier local (reste disponible pendant le mode maintenance).
            Les grosses étapes (secours / storage) peuvent prendre plusieurs minutes.
        </p>
    </section>
</template>
