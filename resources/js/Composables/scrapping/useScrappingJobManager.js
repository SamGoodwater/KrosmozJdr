import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import { getCsrfToken } from "@/utils/scrapping/api";
import {
    createIdleScrappingJobState,
    isTerminalScrappingJobStatus,
} from "@/Composables/scrapping/scrappingJobContract";

const THROTTLE_MS = 300;
const PERSIST_SYNC_THROTTLE_MS = 1200;

const state = ref(createIdleScrappingJobState());
const persistentNotificationIdRef = ref(null);
const cancelHandlerRef = ref(null);
const historyLines = ref([]);
let lastNotificationUpdateAt = 0;
let lastPersistentSyncAt = 0;

function now() {
    return Date.now();
}

function getScrappingHref() {
    if (typeof route === "function") {
        return route("scrapping.index");
    }
    return "/scrapping";
}

function createJobId() {
    return `scrapping-job-${Date.now()}-${Math.floor(Math.random() * 10000)}`;
}

function toPercent(done, total) {
    if (!Number.isFinite(done) || !Number.isFinite(total) || total <= 0) return 0;
    return Math.max(0, Math.min(100, Math.round((done / total) * 100)));
}

function renderMessage() {
    const s = state.value;
    const runSuffix = s.runId ? ` · run_id=${s.runId}` : "";
    const p = s.progress;

    if (s.status === "running" || s.status === "cancelling") {
        const progressText = p.total > 0 ? ` (${p.done}/${p.total} · ${p.percent}%)` : "";
        const phaseText = p.label || s.label || "Traitement";
        const suffix = s.status === "cancelling" ? " · annulation..." : "";
        return `${phaseText}${progressText}${suffix}${runSuffix}`;
    }

    if (s.status === "success") {
        return `Scrapping termine${runSuffix}`;
    }
    if (s.status === "cancelled") {
        return `Scrapping annule${runSuffix}`;
    }
    if (s.status === "error") {
        const err = String(s.error || "Erreur inconnue");
        return `Erreur scrapping: ${err}${runSuffix}`;
    }
    return "Scrapping";
}

function appendHistory(line) {
    const ts = new Date().toLocaleString("fr-FR");
    historyLines.value.unshift(`[${ts}] ${line}`);
}

function navigateToScrapping() {
    router.visit(getScrappingHref());
}

async function persistNotification(action, force = false) {
    const ts = now();
    if (!force && ts - lastPersistentSyncAt < PERSIST_SYNC_THROTTLE_MS) return;
    lastPersistentSyncAt = ts;

    const s = state.value;
    if (!s.id || s.status === "idle") return;

    const payload = {
        message: renderMessage(),
        url: getScrappingHref(),
        status: s.status,
        progress: {
            phase: s.progress.phase,
            done: s.progress.done,
            total: s.progress.total,
            percent: s.progress.percent,
            label: s.progress.label,
        },
        run_id: s.runId,
        error: s.error,
        meta: {
            kind: s.kind,
            job_id: s.backendJobId,
            started_at: s.startedAt,
            ended_at: s.endedAt,
        },
    };
    const token = getCsrfToken();
    const headers = {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-CSRF-TOKEN": token || "",
        "X-Requested-With": "XMLHttpRequest",
    };

    try {
        if (action === "start" || !persistentNotificationIdRef.value) {
            const res = await fetch(route("notifications.scrapping.start"), {
                method: "POST",
                headers,
                credentials: "same-origin",
                body: JSON.stringify({
                    ...payload,
                    job_key: s.id,
                }),
            });
            const json = await res.json();
            if (json?.success && json?.data?.id) {
                persistentNotificationIdRef.value = String(json.data.id);
            }
            return;
        }

        await fetch(route("notifications.scrapping.update", { id: persistentNotificationIdRef.value }), {
            method: "PATCH",
            headers,
            credentials: "same-origin",
            body: JSON.stringify(payload),
        });
    } catch {
        // Le suivi persistant ne doit pas interrompre le job.
    }
}

/**
 * @param {{ kind: string, label: string, canCancel?: boolean, cancelHandler?: (() => void)|null }} input
 * @returns {string}
 */
function startJob(input) {
    const jobId = createJobId();
    state.value = {
        id: jobId,
        kind: String(input.kind || "generic"),
        label: String(input.label || "Scrapping"),
        status: "running",
        progress: {
            phase: "start",
            done: 0,
            total: 0,
            percent: 0,
            label: String(input.label || "Demarrage"),
        },
        runId: null,
        backendJobId: null,
        unknownCharacteristics: null,
        error: null,
        canCancel: input.canCancel !== false,
        startedAt: now(),
        endedAt: null,
    };
    cancelHandlerRef.value = typeof input.cancelHandler === "function" ? input.cancelHandler : null;
    appendHistory(`Job ${state.value.kind} demarre`);
    persistNotification("start", true);
    return jobId;
}

/**
 * @param {{ phase?: string, done?: number, total?: number, label?: string }} patch
 */
function updateProgress(patch) {
    if (!state.value.id || state.value.status === "idle") return;
    const done = Number.isFinite(Number(patch.done)) ? Number(patch.done) : state.value.progress.done;
    const total = Number.isFinite(Number(patch.total)) ? Number(patch.total) : state.value.progress.total;
    state.value.progress = {
        phase: String(patch.phase || state.value.progress.phase || "running"),
        done,
        total,
        percent: toPercent(done, total),
        label: String(patch.label || state.value.progress.label || state.value.label),
    };
    const ts = now();
    if (ts - lastNotificationUpdateAt > THROTTLE_MS) {
        lastNotificationUpdateAt = ts;
        persistNotification("update");
    }
}

function setRunMeta(runId, unknownCharacteristics = null, backendJobId = undefined) {
    if (!state.value.id || state.value.status === "idle") return;
    if (runId != null) state.value.runId = String(runId);
    if (backendJobId !== undefined) {
        state.value.backendJobId = backendJobId === null ? null : String(backendJobId);
    }
    if (unknownCharacteristics != null) state.value.unknownCharacteristics = unknownCharacteristics;
    persistNotification("update");
}

function setCancelHandler(handler) {
    cancelHandlerRef.value = typeof handler === "function" ? handler : null;
}

function requestCancel() {
    if (!state.value.id || !state.value.canCancel) return;
    if (state.value.status !== "running") return;
    state.value.status = "cancelling";
    appendHistory(`Annulation demandee (${state.value.kind})`);
    try {
        cancelHandlerRef.value?.();
    } catch {
        // ignore
    }
    persistNotification("update", true);
}

function finishSuccess(label = "") {
    if (!state.value.id) return;
    state.value.status = "success";
    state.value.endedAt = now();
    if (label) state.value.progress.label = label;
    state.value.progress = {
        ...state.value.progress,
        percent: 100,
        done: Math.max(state.value.progress.done, state.value.progress.total),
    };
    appendHistory(`Job ${state.value.kind} termine`);
    persistNotification("update", true);
}

function finishError(errorMessage) {
    if (!state.value.id) return;
    state.value.status = "error";
    state.value.error = String(errorMessage || "Erreur inconnue");
    state.value.endedAt = now();
    appendHistory(`Job ${state.value.kind} en erreur: ${state.value.error}`);
    persistNotification("update", true);
}

function finishCancelled() {
    if (!state.value.id) return;
    state.value.status = "cancelled";
    state.value.endedAt = now();
    appendHistory(`Job ${state.value.kind} annule`);
    persistNotification("update", true);
}

function clearActiveJob() {
    state.value = createIdleScrappingJobState();
    cancelHandlerRef.value = null;
}

export function useScrappingJobManager() {
    return {
        state,
        historyLines,
        hasActiveJob: computed(() => Boolean(state.value.id) && !isTerminalScrappingJobStatus(state.value.status)),
        startJob,
        updateProgress,
        setRunMeta,
        setCancelHandler,
        requestCancel,
        finishSuccess,
        finishError,
        finishCancelled,
        clearActiveJob,
        appendHistory,
        navigateToScrapping,
    };
}
