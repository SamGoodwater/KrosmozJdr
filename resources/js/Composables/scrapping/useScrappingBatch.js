/**
 * Composable : Simuler / Importer (batch ou par pages).
 * Construit le payload et exécute simulate/import ; met à jour les statuts via callbacks.
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_REFONTE_UI_SCRAPPING.md
 */

import { computed, ref } from "vue";
import { getJson, postJson } from "@/utils/scrapping/api";
import { parsePageRange } from "@/utils/scrapping/parsePageRange";

const JOB_POLL_INTERVAL_MS = 1500;
/** Timeout pour la création du job (si queue=sync, le POST bloque). Après ce délai, on bascule en fallback synchrone. */
const CREATE_JOB_TIMEOUT_MS = 15000;
/** Si le job reste "queued" plus longtemps (worker non lancé), on bascule en fallback synchrone. */
const JOB_QUEUED_MAX_MS = 60000;

function parseCommaList(str) {
    if (typeof str !== "string") return [];
    return str.split(",").map((s) => s.trim()).filter(Boolean);
}

function abortError() {
    return new DOMException("Aborted", "AbortError");
}

/**
 * Normalise la réponse du batch synchrone au format attendu par runBatch.
 * @param {{ ok: boolean, data?: any, aborted?: boolean }} syncResult
 * @param {number} entityCount
 * @returns {{ ok: boolean, data?: object } | { ok: false, error?: string, aborted?: boolean }}
 */
function normalizeSyncBatchResult(syncResult, entityCount) {
    if (syncResult.aborted) return { ok: false, aborted: true };
    if (!syncResult.ok) return syncResult;
    return {
        ok: true,
        data: {
            run_id: syncResult.data?.run_id ?? null,
            summary: syncResult.data?.summary ?? { total: entityCount, success: 0, errors: 0 },
            results: Array.isArray(syncResult.data?.results) ? syncResult.data.results : [],
            debug: syncResult.data?.debug ?? null,
        },
    };
}

function delay(ms, signal) {
    return new Promise((resolve, reject) => {
        const timer = setTimeout(() => {
            cleanup();
            resolve();
        }, ms);

        const onAbort = () => {
            cleanup();
            reject(abortError());
        };

        const cleanup = () => {
            clearTimeout(timer);
            signal?.removeEventListener?.("abort", onAbort);
        };

        if (signal) {
            if (signal.aborted) {
                cleanup();
                reject(abortError());
                return;
            }
            signal.addEventListener("abort", onAbort, { once: true });
        }
    });
}

/**
 * Combine un signal utilisateur avec un timeout. Aborte dès que l'un des deux se déclenche.
 * @param {AbortSignal | null} userSignal
 * @param {number} timeoutMs
 * @returns {{ signal: AbortSignal, cleanup: () => void }}
 */
function signalWithTimeout(userSignal, timeoutMs) {
    const ctrl = new AbortController();
    const timer = setTimeout(() => ctrl.abort(), timeoutMs);
    const onAbort = () => {
        clearTimeout(timer);
        ctrl.abort();
    };
    if (userSignal) {
        if (userSignal.aborted) {
            clearTimeout(timer);
            ctrl.abort();
        } else {
            userSignal.addEventListener("abort", onAbort, { once: true });
        }
    }
    return {
        signal: ctrl.signal,
        cleanup: () => {
            clearTimeout(timer);
            userSignal?.removeEventListener?.("abort", onAbort);
        },
    };
}

/**
 * @param {{
 *   entityTypeRef: import('vue').Ref<string>,
 *   rawItemsRef: import('vue').Ref<Array<{ id?: number }>>,
 *   visibleItemsRef: import('vue').Ref<Array<{ id?: number }>>,
 *   selectedIdsRef: import('vue').Ref<Set<number>>,
 *   batchScopeRef: import('vue').Ref<string>,
 *   pageRangeRef: import('vue').Ref<string>,
 *   pageNumberRef: import('vue').Ref<number>,
 *   getTotalPages?: () => number - nombre total de pages pour scope "all"
 *   optUpdateMode: import('vue').Ref<string>,
 *   optIncludeRelations: import('vue').Ref<boolean>,
 *   optPropertyWhitelist: import('vue').Ref<string>,
 *   optPropertyBlacklist: import('vue').Ref<string>,
 *   optSkipCache: import('vue').Ref<boolean>,
 *   optManualChoice: import('vue').Ref<boolean>,
 *   getCsrfToken: () => string | null,
 *   setStatusForEntities: (entities: Array<{ type: string, id: number }>, status: string, error?: string | null) => void,
 *   setStatusFromBatchResults: (results: any[], isSimulate: boolean) => void,
 *   lastBatchRelationsByKeyRef: import('vue').Ref<Record<string, Array<{ type: string, id: number }>>>,
 *   notifyError: (msg: string) => void,
 *   notifySuccess: (msg: string) => void,
 *   notifyInfo?: (msg: string, opts?: any) => void,
 *   pushHistory?: (line: string) => void,
 *   runSearch: (options?: { signal?: AbortSignal, silentJob?: boolean }) => Promise<void>,
 *   onBatchErrors?: () => void,
 *   onProgress?: (payload: { phase: string, done: number, total: number, label: string }) => void,
 *   onRunMeta?: (payload: { runId?: string|null, unknownCharacteristics?: Record<string, any>|null }) => void
 * }} options
 */
export function useScrappingBatch(options) {
    const {
        entityTypeRef,
        rawItemsRef,
        visibleItemsRef,
        selectedIdsRef,
        batchScopeRef,
        pageRangeRef,
        pageNumberRef,
        getTotalPages = () => 1,
        optUpdateMode,
        optIncludeRelations,
        optPropertyWhitelist,
        optPropertyBlacklist,
        optSkipCache,
        optManualChoice,
        getCsrfToken,
        setStatusForEntities,
        setStatusFromBatchResults,
        lastBatchRelationsByKeyRef,
        notifyError,
        notifySuccess,
        notifyInfo = () => {},
        pushHistory = () => {},
        runSearch,
        onBatchErrors = () => {},
        onProgress = () => {},
        onRunMeta = () => {},
    } = options;

    const importing = ref(false);
    const lastBatchResults = ref(null);
    const importByPagesProgress = ref(null);
    const lastRunId = ref(null);
    const lastUnknownCharacteristics = ref(null);

    const selectedCount = computed(() => selectedIdsRef.value?.size ?? 0);
    const shouldStop = (signal) => signal?.aborted === true;

    const mergeUnknown = (target, incoming) => {
        if (!incoming || typeof incoming !== "object") return target;
        const next = target && typeof target === "object"
            ? { ...target, ids: { ...(target.ids || {}) } }
            : { total_occurrences: 0, distinct_ids: 0, ids: {}, contains_id_38: false };
        const ids = incoming.ids && typeof incoming.ids === "object" ? incoming.ids : {};
        for (const [id, count] of Object.entries(ids)) {
            const n = Number(count);
            if (!Number.isFinite(n) || n <= 0) continue;
            next.ids[id] = (Number(next.ids[id] || 0) || 0) + n;
        }
        next.total_occurrences = Object.values(next.ids).reduce((acc, n) => acc + (Number(n) || 0), 0);
        next.distinct_ids = Object.keys(next.ids).length;
        next.contains_id_38 = Boolean(next.ids["38"]) || Boolean(next.contains_id_38);
        return next;
    };

    async function executeBatchViaJob(payload, csrf, label, signal) {
        if (shouldStop(signal)) throw abortError();

        const { signal: createSignal, cleanup: cleanupCreateSignal } = signalWithTimeout(signal, CREATE_JOB_TIMEOUT_MS);
        let createResult;
        try {
            createResult = await postJson("/api/scrapping/jobs", {
                kind: "import_batch",
                entities: payload.entities,
                skip_cache: payload.skip_cache,
                dry_run: payload.dry_run,
                validate_only: payload.validate_only,
                include_relations: payload.include_relations,
                update_mode: payload.update_mode,
                exclude_from_update: payload.exclude_from_update,
                property_whitelist: payload.property_whitelist,
            }, {
                headers: { "X-CSRF-TOKEN": csrf },
                signal: createSignal,
            });
        } finally {
            cleanupCreateSignal();
        }

        // Fallback robuste: si endpoint jobs indisponible ou timeout (queue=sync), on repasse en mode synchrone.
        if (!createResult.ok || !createResult.data?.data?.job_id) {
            onProgress({ phase: "batch", done: 0, total: payload.entities.length, label: `${label} (mode synchrone, peut prendre plusieurs minutes)…` });
            notifyInfo(`${label} en mode synchrone… L'import peut prendre plusieurs minutes pour les classes avec relations.`, { duration: 4000 });
            const syncResult = await postJson("/api/scrapping/import/batch", payload, {
                headers: { "X-CSRF-TOKEN": csrf },
                signal,
            });
            const normalized = normalizeSyncBatchResult(syncResult, payload.entities.length);
            if (normalized.aborted) throw abortError();
            return normalized;
        }

        const jobId = createResult.data.data.job_id;
        onRunMeta({ runId: createResult.data?.run_id ?? null, unknownCharacteristics: null, jobId });
        let statusResult = null;
        let queuedSince = null;
        try {
            // Polling jusqu'à statut terminal.
            while (true) {
                if (shouldStop(signal)) throw abortError();
                statusResult = await getJson(`/api/scrapping/jobs/${encodeURIComponent(jobId)}`, { signal });
                if (!statusResult.ok || !statusResult.data?.data) {
                    throw new Error(statusResult.error || "Statut du job indisponible");
                }

                const data = statusResult.data.data;
                const status = String(data?.status ?? "");
                const total = Number(data?.progress?.total ?? 0);
                const done = Number(data?.progress?.done ?? 0);
                onProgress({ phase: "job", done, total, label });
                onRunMeta({
                    runId: data?.run_id ?? null,
                    unknownCharacteristics: statusResult.data?.debug?.unknown_characteristics ?? null,
                    jobId,
                });

                if (status === "queued" || status === "running") {
                    if (status === "queued") {
                        queuedSince = queuedSince ?? Date.now();
                        if (Date.now() - queuedSince > JOB_QUEUED_MAX_MS) {
                            notifyInfo("Le worker de queue ne semble pas actif. Passage en mode synchrone…", { duration: 5000 });
                            await postJson(`/api/scrapping/jobs/${encodeURIComponent(jobId)}/cancel`, {}, {
                                headers: { "X-CSRF-TOKEN": csrf },
                            });
                            onProgress({ phase: "batch", done: 0, total: payload.entities.length, label: `${label} (mode synchrone)…` });
                            const syncResult = await postJson("/api/scrapping/import/batch", payload, {
                                headers: { "X-CSRF-TOKEN": csrf },
                                signal,
                            });
                            const normalized = normalizeSyncBatchResult(syncResult, payload.entities.length);
                            if (normalized.aborted) throw abortError();
                            return normalized;
                        }
                    } else {
                        queuedSince = null;
                    }
                }

                if (["succeeded", "failed", "cancelled"].includes(status)) {
                    if (status === "cancelled") {
                        throw abortError();
                    }
                    return {
                        ok: true,
                        data: {
                            run_id: data?.run_id ?? null,
                            summary: data?.summary ?? { total, success: done, errors: 0 },
                            results: Array.isArray(data?.results) ? data.results : [],
                            debug: statusResult.data?.debug ?? null,
                        },
                    };
                }
                await delay(JOB_POLL_INTERVAL_MS, signal);
            }
        } catch (e) {
            if (e?.name === "AbortError") {
                await postJson(`/api/scrapping/jobs/${encodeURIComponent(jobId)}/cancel`, {}, {
                    headers: { "X-CSRF-TOKEN": csrf },
                });
            }
            throw e;
        }
    }

    const unknownToInline = (summary) => {
        if (!summary || !summary.ids || typeof summary.ids !== "object") return "";
        const ids = Object.entries(summary.ids)
            .map(([id, c]) => [Number(id), Number(c)])
            .filter(([id, c]) => Number.isFinite(id) && Number.isFinite(c) && c > 0)
            .sort((a, b) => b[1] - a[1])
            .slice(0, 4)
            .map(([id, c]) => `${id}(${c})`);
        if (!ids.length) return "";
        return `unknown characteristic IDs: ${ids.join(", ")}`;
    };

    function buildBatchPayload(dryRun, scope = "auto") {
        const ids =
            scope === "all"
                ? (rawItemsRef.value || []).map((it) => Number(it?.id)).filter((n) => Number.isFinite(n))
                : selectedCount.value
                    ? Array.from(selectedIdsRef.value)
                    : (visibleItemsRef.value || []).map((it) => Number(it?.id)).filter((n) => Number.isFinite(n));

        const entities = ids.map((id) => ({ type: entityTypeRef.value, id }));

        const updateMode = optUpdateMode.value;
        const excludeFromUpdate = parseCommaList(optPropertyBlacklist.value);
        const propertyWhitelist = parseCommaList(optPropertyWhitelist.value);
        const validUpdateModes = ["ignore", "draft_raw_auto_update", "auto_update", "force"];

        return {
            entities,
            skip_cache: !!optSkipCache.value,
            dry_run: !!dryRun,
            validate_only: !!optManualChoice.value,
            include_relations: !!optIncludeRelations.value,
            update_mode: validUpdateModes.includes(updateMode) ? updateMode : "draft_raw_auto_update",
            exclude_from_update: excludeFromUpdate,
            property_whitelist: propertyWhitelist,
        };
    }

    const lastBatchErrorResults = computed(() => {
        const list = lastBatchResults.value;
        if (!Array.isArray(list)) return [];
        return list.filter((r) => r && r.success === false);
    });

    async function runBatch(mode, scope = "auto", options = {}) {
        const signal = options.signal ?? null;
        const hasItems = scope === "all" ? (rawItemsRef.value?.length ?? 0) > 0 : (visibleItemsRef.value?.length ?? 0) > 0;
        if (!hasItems) {
            notifyError(scope === "all" ? "Aucun résultat chargé." : "Aucun résultat à traiter.");
            return;
        }
        if (shouldStop(signal)) return;

        const csrf = getCsrfToken();
        if (!csrf) {
            notifyError("Token CSRF introuvable. Recharge la page.");
            return;
        }

        importing.value = true;
        const dryRun = mode === "simulate";
        const payload = buildBatchPayload(dryRun, scope);
        const targetCount = payload.entities.length;
        if (targetCount < 1) {
            notifyError("Aucune entité sélectionnée.");
            importing.value = false;
            return;
        }

        const label = dryRun ? "Simulation" : "Import";
        pushHistory(`${label} batch (${entityTypeRef.value}) sur ${targetCount} entité(s).`);
        notifyInfo(`${label} en cours…`, { duration: 1500 });
        onProgress({ phase: "batch", done: 0, total: targetCount, label: `${label} batch` });
        setStatusForEntities(payload.entities, dryRun ? "simulation en cours" : "importation en cours");

        try {
            if (shouldStop(signal)) {
                throw new DOMException("Aborted", "AbortError");
            }
            const result = await executeBatchViaJob(payload, csrf, `${label} batch`, signal);
            if (result.aborted || shouldStop(signal)) {
                throw new DOMException("Aborted", "AbortError");
            }

            if (result.ok && result.data) {
                const data = result.data;
                lastRunId.value = data?.run_id ?? null;
                lastUnknownCharacteristics.value = data?.debug?.unknown_characteristics ?? null;
                onRunMeta({ runId: lastRunId.value, unknownCharacteristics: lastUnknownCharacteristics.value, jobId: null });
                setStatusFromBatchResults(data.results ?? [], dryRun);
                const nextRel = { ...lastBatchRelationsByKeyRef.value };
                for (const r of data.results ?? []) {
                    if (r?.relations?.length) nextRel[`${r.type}-${r.id}`] = r.relations;
                }
                lastBatchRelationsByKeyRef.value = nextRel;
                const s = data.summary || {};
                onProgress({
                    phase: "batch",
                    done: Number(s.success ?? 0) + Number(s.errors ?? 0),
                    total: targetCount,
                    label: `${label} batch`,
                });
                const errCount = s.errors ?? 0;
                const runId = data?.run_id ? ` run_id=${data.run_id}` : "";
                lastBatchResults.value = errCount > 0 ? (data.results ?? []) : null;
                if (errCount > 0) {
                    const failed = (data.results ?? []).filter((r) => r && r.success === false);
                    const firstError = failed[0]?.error || failed[0]?.validation_errors?.[0] || "Erreur inconnue";
                    notifyError(`${label} : ${errCount} erreur(s). ${typeof firstError === "string" ? firstError : JSON.stringify(firstError)}. Voir Options & historique pour le détail.`);
                    pushHistory(`→ ${label.toUpperCase()} OK: ${s.success ?? 0}/${s.total ?? targetCount} (erreurs: ${errCount})${runId}`);
                    onBatchErrors();
                } else {
                    notifySuccess(`${label}: ${s.success ?? 0}/${s.total ?? targetCount}`);
                    pushHistory(`→ ${label.toUpperCase()} OK: ${s.success ?? 0}/${s.total ?? targetCount}${runId}`);
                }
                const unknownInfo = unknownToInline(lastUnknownCharacteristics.value);
                if (unknownInfo) pushHistory(`→ DEBUG ${unknownInfo}${runId}`);
            } else {
                setStatusForEntities(payload.entities, "erreur", result.error || "batch");
                lastBatchResults.value = null;
                notifyError(result.error || `Erreur ${label.toLowerCase()}`);
                pushHistory(`→ ${label.toUpperCase()} ERREUR: ${result.error || "batch"}`);
                onProgress({ phase: "batch", done: targetCount, total: targetCount, label: `${label} batch` });
            }
        } catch (e) {
            if (e?.name === "AbortError") return;
            setStatusForEntities(payload.entities, "erreur", e?.message);
            notifyError(`Erreur ${label.toLowerCase()} : ` + (e?.message ?? "erreur"));
            pushHistory(`→ ${label.toUpperCase()} ERREUR: ${e?.message}`);
            lastBatchResults.value = null;
            throw e;
        } finally {
            importing.value = false;
        }
    }

    async function runImportByPages(simulate = false, options = {}) {
        const signal = options.signal ?? null;
        const pages = parsePageRange(pageRangeRef.value || "");
        if (pages.length === 0) {
            notifyError("Saisis une plage de pages (ex: 1-6 ou 4,5).");
            return;
        }
        if (shouldStop(signal)) return;
        const csrf = getCsrfToken();
        if (!csrf) {
            notifyError("Token CSRF introuvable. Recharge la page.");
            return;
        }

        const label = simulate ? "Simulation" : "Import";
        pushHistory(`${label} par pages (${entityTypeRef.value}) : pages ${pages.join(", ")}.`);
        importing.value = true;
        importByPagesProgress.value = `0/${pages.length}`;
        onProgress({ phase: "pages", done: 0, total: pages.length, label: `${label} par pages` });
        let totalSuccess = 0;
        let totalErrors = 0;
        let totalEntities = 0;
        const accumulatedErrorResults = [];
        let unknownSummary = null;
        const savedPageNumber = pageNumberRef.value;

        try {
            for (let i = 0; i < pages.length; i++) {
                if (shouldStop(signal)) {
                    throw new DOMException("Aborted", "AbortError");
                }
                const p = pages[i];
                importByPagesProgress.value = `${i + 1}/${pages.length}`;
                pageNumberRef.value = p;
                await runSearch({ signal, silentJob: true });
                if (shouldStop(signal)) {
                    throw new DOMException("Aborted", "AbortError");
                }

                if (!rawItemsRef.value?.length) {
                    pushHistory(`→ Page ${p} : aucun résultat, ignorée.`);
                    continue;
                }
                const payload = buildBatchPayload(simulate, "all");
                if (payload.entities.length < 1) {
                    pushHistory(`→ Page ${p} : 0 entité, ignorée.`);
                    continue;
                }
                totalEntities += payload.entities.length;
                setStatusForEntities(payload.entities, simulate ? "simulation en cours" : "importation en cours");

                const result = await executeBatchViaJob(payload, csrf, `${label} page ${p}`, signal);
                if (result.aborted || shouldStop(signal)) {
                    throw new DOMException("Aborted", "AbortError");
                }

                if (result.ok && result.data) {
                    const data = result.data;
                    lastRunId.value = data?.run_id ?? lastRunId.value;
                    unknownSummary = mergeUnknown(unknownSummary, data?.debug?.unknown_characteristics ?? null);
                    onRunMeta({ runId: lastRunId.value, unknownCharacteristics: unknownSummary, jobId: null });
                    setStatusFromBatchResults(data.results ?? [], simulate);
                    const nextRel = { ...lastBatchRelationsByKeyRef.value };
                    for (const r of data.results ?? []) {
                        if (r?.relations?.length) nextRel[`${r.type}-${r.id}`] = r.relations;
                    }
                    lastBatchRelationsByKeyRef.value = nextRel;
                    const s = data.summary || {};
                    const ok = s.success ?? 0;
                    const err = s.errors ?? 0;
                    const runId = data?.run_id ? ` run_id=${data.run_id}` : "";
                    totalSuccess += ok;
                    totalErrors += err;
                    (data.results ?? []).filter((r) => r && r.success === false).forEach((r) => accumulatedErrorResults.push(r));
                    pushHistory(`→ Page ${p} : ${ok}/${s.total ?? payload.entities.length} (erreurs: ${err})${runId}`);
                } else {
                    setStatusForEntities(payload.entities, "erreur", result.error || "batch");
                    totalErrors += payload.entities.length;
                    payload.entities.forEach((ent) => accumulatedErrorResults.push({ type: ent.type, id: ent.id, success: false, error: result.error || "batch" }));
                    pushHistory(`→ Page ${p} ERREUR: ${result.error || "batch"}`);
                }
                onProgress({ phase: "pages", done: i + 1, total: pages.length, label: `${label} par pages` });
            }
            lastBatchResults.value = accumulatedErrorResults.length > 0 ? accumulatedErrorResults : null;
            lastUnknownCharacteristics.value = unknownSummary;
            if (totalErrors > 0) {
                const firstFailed = accumulatedErrorResults[0];
                const firstError = firstFailed?.error || firstFailed?.validation_errors?.[0] || "Erreur inconnue";
                notifyError(`${label} par pages : ${totalErrors} erreur(s). ${typeof firstError === "string" ? firstError : JSON.stringify(firstError)}. Voir Options & historique.`);
                onBatchErrors();
            } else {
                notifySuccess(`${label} par pages terminé : ${totalSuccess}/${totalEntities}`);
            }
            pushHistory(`→ ${label.toUpperCase()} PAR PAGES OK: ${totalSuccess}/${totalEntities} (erreurs: ${totalErrors})`);
            const unknownInfo = unknownToInline(unknownSummary);
            if (unknownInfo) pushHistory(`→ DEBUG ${unknownInfo}`);
        } catch (e) {
            if (e?.name === "AbortError") return;
            notifyError(`${label} par pages : ` + (e?.message ?? "erreur"));
            pushHistory(`→ ${label.toUpperCase()} PAR PAGES ERREUR: ${e?.message}`);
            lastBatchResults.value = null;
            throw e;
        } finally {
            importing.value = false;
            importByPagesProgress.value = null;
            pageNumberRef.value = savedPageNumber;
            await runSearch();
        }
    }

    /** Import/simulation sur toutes les pages (1 à totalPages), une par une. */
    async function runImportAllPages(simulate = false, options = {}) {
        const signal = options.signal ?? null;
        const totalPages = getTotalPages();
        const total = Math.max(1, Math.floor(Number(totalPages)));
        const pages = Array.from({ length: total }, (_, i) => i + 1);
        const csrf = getCsrfToken();
        if (!csrf) {
            notifyError("Token CSRF introuvable. Recharge la page.");
            return;
        }

        const label = simulate ? "Simulation" : "Import";
        pushHistory(`${label} « Tous » (${entityTypeRef.value}) : ${pages.length} page(s).`);
        importing.value = true;
        importByPagesProgress.value = `0/${pages.length}`;
        onProgress({ phase: "all-pages", done: 0, total: pages.length, label: `${label} toutes pages` });
        let totalSuccess = 0;
        let totalErrors = 0;
        let totalEntities = 0;
        const accumulatedErrorResults = [];
        let unknownSummary = null;
        const savedPageNumber = pageNumberRef.value;

        try {
            for (let i = 0; i < pages.length; i++) {
                if (shouldStop(signal)) {
                    throw new DOMException("Aborted", "AbortError");
                }
                const p = pages[i];
                importByPagesProgress.value = `${i + 1}/${pages.length}`;
                pageNumberRef.value = p;
                await runSearch({ signal, silentJob: true });
                if (shouldStop(signal)) {
                    throw new DOMException("Aborted", "AbortError");
                }

                if (!rawItemsRef.value?.length) {
                    pushHistory(`→ Page ${p} : aucun résultat, ignorée.`);
                    continue;
                }
                const payload = buildBatchPayload(simulate, "all");
                if (payload.entities.length < 1) {
                    pushHistory(`→ Page ${p} : 0 entité, ignorée.`);
                    continue;
                }
                totalEntities += payload.entities.length;
                setStatusForEntities(payload.entities, simulate ? "simulation en cours" : "importation en cours");

                const result = await executeBatchViaJob(payload, csrf, `${label} page ${p}`, signal);
                if (result.aborted || shouldStop(signal)) {
                    throw new DOMException("Aborted", "AbortError");
                }

                if (result.ok && result.data) {
                    const data = result.data;
                    lastRunId.value = data?.run_id ?? lastRunId.value;
                    unknownSummary = mergeUnknown(unknownSummary, data?.debug?.unknown_characteristics ?? null);
                    onRunMeta({ runId: lastRunId.value, unknownCharacteristics: unknownSummary, jobId: null });
                    setStatusFromBatchResults(data.results ?? [], simulate);
                    const nextRel = { ...lastBatchRelationsByKeyRef.value };
                    for (const r of data.results ?? []) {
                        if (r?.relations?.length) nextRel[`${r.type}-${r.id}`] = r.relations;
                    }
                    lastBatchRelationsByKeyRef.value = nextRel;
                    const s = data.summary || {};
                    const ok = s.success ?? 0;
                    const err = s.errors ?? 0;
                    const runId = data?.run_id ? ` run_id=${data.run_id}` : "";
                    totalSuccess += ok;
                    totalErrors += err;
                    (data.results ?? []).filter((r) => r && r.success === false).forEach((r) => accumulatedErrorResults.push(r));
                    pushHistory(`→ Page ${p} : ${ok}/${s.total ?? payload.entities.length} (erreurs: ${err})${runId}`);
                } else {
                    setStatusForEntities(payload.entities, "erreur", result.error || "batch");
                    totalErrors += payload.entities.length;
                    payload.entities.forEach((ent) => accumulatedErrorResults.push({ type: ent.type, id: ent.id, success: false, error: result.error || "batch" }));
                    pushHistory(`→ Page ${p} ERREUR: ${result.error || "batch"}`);
                }
                onProgress({ phase: "all-pages", done: i + 1, total: pages.length, label: `${label} toutes pages` });
            }
            lastBatchResults.value = accumulatedErrorResults.length > 0 ? accumulatedErrorResults : null;
            lastUnknownCharacteristics.value = unknownSummary;
            if (totalErrors > 0) {
                const firstFailed = accumulatedErrorResults[0];
                const firstError = firstFailed?.error || firstFailed?.validation_errors?.[0] || "Erreur inconnue";
                notifyError(`${label} « Tous » : ${totalErrors} erreur(s). ${typeof firstError === "string" ? firstError : JSON.stringify(firstError)}. Voir Options & historique.`);
                onBatchErrors();
            } else {
                notifySuccess(`${label} « Tous » terminé : ${totalSuccess}/${totalEntities}`);
            }
            pushHistory(`→ ${label.toUpperCase()} TOUS OK: ${totalSuccess}/${totalEntities} (erreurs: ${totalErrors})`);
            const unknownInfo = unknownToInline(unknownSummary);
            if (unknownInfo) pushHistory(`→ DEBUG ${unknownInfo}`);
        } catch (e) {
            if (e?.name === "AbortError") return;
            notifyError(`${label} « Tous » : ` + (e?.message ?? "erreur"));
            pushHistory(`→ ${label.toUpperCase()} TOUS ERREUR: ${e?.message}`);
            lastBatchResults.value = null;
            throw e;
        } finally {
            importing.value = false;
            importByPagesProgress.value = null;
            pageNumberRef.value = savedPageNumber;
            await runSearch();
        }
    }

    async function runBatchOrByPages(mode, options = {}) {
        const dryRun = mode === "simulate";
        if (batchScopeRef.value === "pages") {
            await runImportByPages(dryRun, options);
            return;
        }
        if (batchScopeRef.value === "all") {
            await runImportAllPages(dryRun, options);
            return;
        }
        await runBatch(mode, "auto", options);
    }

    function clearBatchErrors() {
        lastBatchResults.value = null;
    }

    return {
        importing,
        importByPagesProgress,
        lastBatchResults,
        lastBatchErrorResults,
        lastRunId,
        lastUnknownCharacteristics,
        selectedCount,
        buildBatchPayload,
        runBatch,
        runImportByPages,
        runBatchOrByPages,
        clearBatchErrors,
    };
}
