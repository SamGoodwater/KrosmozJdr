import { computed, onBeforeUnmount, ref, watch } from "vue";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";

/**
 * Suivi live d’un job console admin : poll JSON, toast animé (barre %), log sur la page.
 *
 * Le poll continue après un changement de page tant que le job n’est pas terminé.
 *
 * @param {{ consoleJob?: Record<string, any>|null }} props
 * @param {{ title: string }} options
 *
 * @example
 * const { liveJob, busy, pollError } = useProjectConsoleJob(props, { title: "Review" });
 */

const ACTIVE_STATUSES = new Set(["queued", "running"]);

/** @type {Map<string, { timer: number|null, toastId: number|null, title: string, listeners: Set<(job: Record<string, any>) => void> }>} */
const polls = new Map();

/**
 * @param {string|undefined|null} status
 * @returns {boolean}
 */
export function isConsoleJobActive(status) {
    return ACTIVE_STATUSES.has(String(status || ""));
}

/**
 * @param {string} status
 * @returns {string}
 */
export function consoleJobStatusLabel(status) {
    return (
        {
            queued: "En file",
            running: "En cours",
            success: "Terminé",
            failed: "Échec",
        }[status] || status
    );
}

/**
 * @param {Record<string, any>} job
 * @param {string} title
 */
function toastMessage(job, title) {
    const percent = Number(job.progress ?? 0);
    const phase = job.progress_label || consoleJobStatusLabel(job.status);
    if (job.status === "success") {
        return `${title} terminé (${percent} %)`;
    }
    if (job.status === "failed") {
        return `${title} en échec${job.error ? ` : ${job.error}` : ""}`;
    }
    return `${title} — ${phase} (${percent} %)`;
}

/**
 * @param {Record<string, any>} job
 */
function toastType(job) {
    if (job.status === "success") return "success";
    if (job.status === "failed") return "error";
    return "info";
}

/**
 * @param {string} jobId
 * @param {Record<string, any>} job
 * @param {string} title
 */
function pushJobToListeners(jobId, job, title) {
    const entry = polls.get(jobId);
    if (!entry) return;
    const store = useNotificationStore();
    const percent = Number(job.progress ?? 0);
    const active = isConsoleJobActive(job.status);
    const payload = {
        message: toastMessage(job, title),
        type: toastType(job),
        progress: percent,
        duration: active ? 0 : 14000,
        dismissible: !active,
        icon: "fa-terminal",
    };
    if (entry.toastId == null) {
        entry.toastId = store.addNotification(payload);
    } else {
        store.updateNotification(entry.toastId, payload);
        if (!active) {
            store.scheduleDismiss(entry.toastId, 14000);
        }
    }
    entry.listeners.forEach((fn) => fn(job));
}

/**
 * @param {string} jobId
 */
async function fetchConsoleJob(jobId) {
    const res = await fetch(route("admin.console-jobs.show", jobId), {
        headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
        credentials: "same-origin",
    });
    const json = await res.json();
    if (!res.ok || !json.success) {
        throw new Error(json.message || "Statut indisponible");
    }
    return json.data;
}

/**
 * @param {string} jobId
 * @param {string} title
 */
function ensurePoll(jobId, title) {
    if (polls.has(jobId)) {
        const existing = polls.get(jobId);
        existing.title = title;
        return existing;
    }
    const entry = { timer: null, toastId: null, title, listeners: new Set() };
    polls.set(jobId, entry);

    const tick = async () => {
        try {
            const data = await fetchConsoleJob(jobId);
            pushJobToListeners(jobId, data, title);
            if (!isConsoleJobActive(data.status)) {
                stopPollTimer(jobId);
            }
        } catch {
            // Le poll reprend au tick suivant.
        }
    };

    tick();
    entry.timer = window.setInterval(tick, 2000);
    return entry;
}

/**
 * @param {string} jobId
 */
function stopPollTimer(jobId) {
    const entry = polls.get(jobId);
    if (!entry?.timer) return;
    window.clearInterval(entry.timer);
    entry.timer = null;
}

/**
 * @param {{ consoleJob?: Record<string, any>|null }} props
 * @param {{ title: string }} options
 */
export function useProjectConsoleJob(props, options) {
    const title = options.title || "Job";
    const liveJob = ref(props.consoleJob ?? null);
    const pollError = ref("");
    let subscribedId = null;

    const busy = computed(() => isConsoleJobActive(liveJob.value?.status));

    const onUpdate = (job) => {
        liveJob.value = job;
        pollError.value = "";
    };

    function subscribe(job) {
        if (!job?.id || !isConsoleJobActive(job.status)) {
            return;
        }
        if (subscribedId && subscribedId !== job.id) {
            polls.get(subscribedId)?.listeners.delete(onUpdate);
            subscribedId = null;
        }
        const entry = ensurePoll(job.id, title);
        entry.listeners.add(onUpdate);
        subscribedId = job.id;
        pushJobToListeners(job.id, job, title);
    }

    watch(
        () => props.consoleJob,
        (job) => {
            liveJob.value = job ?? liveJob.value;
            if (job?.id && isConsoleJobActive(job.status)) {
                subscribe(job);
            }
        },
        { immediate: true },
    );

    onBeforeUnmount(() => {
        if (!subscribedId) return;
        polls.get(subscribedId)?.listeners.delete(onUpdate);
        subscribedId = null;
    });

    return {
        liveJob,
        pollError,
        busy,
        isActive: isConsoleJobActive,
        statusLabel: consoleJobStatusLabel,
    };
}
