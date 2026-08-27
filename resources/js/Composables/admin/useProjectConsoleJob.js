import { computed, onBeforeUnmount, ref, watch } from "vue";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";

/**
 * Suivi live d’un job console admin : poll JSON, toast (fermable) + barre %, log sur la page.
 *
 * Le poll continue après un changement de page tant que le job n’est pas terminé.
 *
 * @param {{ consoleJob?: Record<string, any>|null }} props
 * @param {{ title: string }} options
 *
 * @example
 * const { liveJob, busy, pollError, cancelJob } = useProjectConsoleJob(props, { title: "Review" });
 */

const ACTIVE_STATUSES = new Set(["queued", "running"]);

/** @type {Map<string, { timer: number|null, toastId: number|null, title: string, failCount: number, listeners: Set<(job: Record<string, any>) => void> }>} */
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
            cancelled: "Annulé",
        }[status] || status
    );
}

/**
 * @returns {Record<string, string>}
 */
function jsonHeaders() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";
    return {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": token,
    };
}

/**
 * @param {Record<string, any>} job
 * @returns {string}
 */
function queuedWorkerHint(job) {
    if (String(job.status) !== "queued") {
        return "";
    }
    const created = job.created_at ? Date.parse(job.created_at) : 0;
    if (!created || Date.now() - created < 8000) {
        return "";
    }
    return " — la file n’avance pas (worker inactif ? `php artisan queue:listen`)";
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
    if (job.status === "cancelled") {
        return `${title} annulé`;
    }
    return `${title} — ${phase} (${percent} %)${queuedWorkerHint(job)}`;
}

/**
 * @param {Record<string, any>} job
 */
function toastType(job) {
    if (job.status === "success") return "success";
    if (job.status === "failed") return "error";
    if (job.status === "cancelled") return "warning";
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
        dismissible: true,
        icon: "fa-terminal",
        actions: active
            ? [
                  {
                      content: "Annuler",
                      color: "error",
                      onClick: () => {
                          cancelConsoleJob(jobId).catch(() => {});
                      },
                  },
              ]
            : undefined,
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
        headers: jsonHeaders(),
        credentials: "same-origin",
    });
    const json = await res.json();
    if (!res.ok || !json.success) {
        throw new Error(json.message || "Statut indisponible");
    }
    return json.data;
}

/**
 * Annule un job encore en file / en cours.
 *
 * @param {string} jobId
 * @returns {Promise<Record<string, any>>}
 */
export async function cancelConsoleJob(jobId) {
    const res = await fetch(route("admin.console-jobs.cancel", jobId), {
        method: "POST",
        headers: jsonHeaders(),
        credentials: "same-origin",
    });
    const json = await res.json().catch(() => ({}));
    if (!res.ok || !json.success) {
        throw new Error(json.message || "Annulation impossible");
    }
    const data = json.data;
    const entry = polls.get(jobId);
    if (entry && data) {
        pushJobToListeners(jobId, data, entry.title);
        stopPollTimer(jobId);
    }
    return data;
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
    const entry = { timer: null, toastId: null, title, failCount: 0, listeners: new Set() };
    polls.set(jobId, entry);

    const tick = async () => {
        try {
            const data = await fetchConsoleJob(jobId);
            entry.failCount = 0;
            pushJobToListeners(jobId, data, title);
            if (!isConsoleJobActive(data.status)) {
                stopPollTimer(jobId);
            }
        } catch (e) {
            entry.failCount += 1;
            if (entry.failCount >= 3) {
                const message = e instanceof Error ? e.message : "Statut indisponible";
                entry.listeners.forEach((fn) => fn({ ...(polls.get(jobId) ? {} : {}), _pollError: message }));
            }
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
    const cancelling = ref(false);
    let subscribedId = null;

    const busy = computed(() => isConsoleJobActive(liveJob.value?.status));

    const onUpdate = (job) => {
        if (job?._pollError) {
            pollError.value = String(job._pollError);
            return;
        }
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

    async function cancelJob() {
        const id = liveJob.value?.id;
        if (!id || !busy.value || cancelling.value) {
            return;
        }
        cancelling.value = true;
        try {
            const data = await cancelConsoleJob(id);
            if (data) {
                liveJob.value = data;
            }
        } catch (e) {
            pollError.value = e instanceof Error ? e.message : "Annulation impossible";
        } finally {
            cancelling.value = false;
        }
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
        cancelling,
        cancelJob,
        isActive: isConsoleJobActive,
        statusLabel: consoleJobStatusLabel,
    };
}
