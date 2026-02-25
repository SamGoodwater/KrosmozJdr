/**
 * Composable : Simuler / Importer (batch ou par pages).
 * Construit le payload et exécute simulate/import ; met à jour les statuts via callbacks.
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_REFONTE_UI_SCRAPPING.md
 */

import { computed, ref } from "vue";
import { postJson } from "@/utils/scrapping/api";
import { parsePageRange } from "@/utils/scrapping/parsePageRange";

function parseCommaList(str) {
    if (typeof str !== "string") return [];
    return str.split(",").map((s) => s.trim()).filter(Boolean);
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
 *   optReplaceMode: import('vue').Ref<string>,
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
 *   runSearch: () => Promise<void>,
 *   onBatchErrors?: () => void
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
        optReplaceMode,
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
    } = options;

    const importing = ref(false);
    const lastBatchResults = ref(null);
    const importByPagesProgress = ref(null);

    const selectedCount = computed(() => selectedIdsRef.value?.size ?? 0);

    function buildBatchPayload(dryRun, scope = "auto") {
        const ids =
            scope === "all"
                ? (rawItemsRef.value || []).map((it) => Number(it?.id)).filter((n) => Number.isFinite(n))
                : selectedCount.value
                    ? Array.from(selectedIdsRef.value)
                    : (visibleItemsRef.value || []).map((it) => Number(it?.id)).filter((n) => Number.isFinite(n));

        const entities = ids.map((id) => ({ type: entityTypeRef.value, id }));

        const replaceMode = optReplaceMode.value;
        const excludeFromUpdate = parseCommaList(optPropertyBlacklist.value);
        const propertyWhitelist = parseCommaList(optPropertyWhitelist.value);

        return {
            entities,
            skip_cache: !!optSkipCache.value,
            dry_run: !!dryRun,
            validate_only: !!optManualChoice.value,
            include_relations: !!optIncludeRelations.value,
            replace_mode: replaceMode && ["never", "draft_raw_only", "always"].includes(replaceMode) ? replaceMode : "draft_raw_only",
            exclude_from_update: excludeFromUpdate,
            property_whitelist: propertyWhitelist,
        };
    }

    const lastBatchErrorResults = computed(() => {
        const list = lastBatchResults.value;
        if (!Array.isArray(list)) return [];
        return list.filter((r) => r && r.success === false);
    });

    async function runBatch(mode, scope = "auto") {
        const hasItems = scope === "all" ? (rawItemsRef.value?.length ?? 0) > 0 : (visibleItemsRef.value?.length ?? 0) > 0;
        if (!hasItems) {
            notifyError(scope === "all" ? "Aucun résultat chargé." : "Aucun résultat à traiter.");
            return;
        }

        const csrf = getCsrfToken();
        if (!csrf) {
            notifyError("Token CSRF introuvable. Veuillez recharger la page.");
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
        setStatusForEntities(payload.entities, dryRun ? "simulation en cours" : "importation en cours");

        try {
            const result = await postJson("/api/scrapping/import/batch", payload, {
                headers: { "X-CSRF-TOKEN": csrf },
            });

            if (result.ok && result.data) {
                const data = result.data;
                setStatusFromBatchResults(data.results ?? [], dryRun);
                const nextRel = { ...lastBatchRelationsByKeyRef.value };
                for (const r of data.results ?? []) {
                    if (r?.relations?.length) nextRel[`${r.type}-${r.id}`] = r.relations;
                }
                lastBatchRelationsByKeyRef.value = nextRel;
                const s = data.summary || {};
                const errCount = s.errors ?? 0;
                lastBatchResults.value = errCount > 0 ? (data.results ?? []) : null;
                if (errCount > 0) {
                    const failed = (data.results ?? []).filter((r) => r && r.success === false);
                    const firstError = failed[0]?.error || failed[0]?.validation_errors?.[0] || "Erreur inconnue";
                    notifyError(`${label} : ${errCount} erreur(s). ${typeof firstError === "string" ? firstError : JSON.stringify(firstError)}. Voir Options & historique pour le détail.`);
                    pushHistory(`→ ${label.toUpperCase()} OK: ${s.success ?? 0}/${s.total ?? targetCount} (erreurs: ${errCount})`);
                    onBatchErrors();
                } else {
                    notifySuccess(`${label}: ${s.success ?? 0}/${s.total ?? targetCount}`);
                    pushHistory(`→ ${label.toUpperCase()} OK: ${s.success ?? 0}/${s.total ?? targetCount}`);
                }
            } else {
                setStatusForEntities(payload.entities, "erreur", result.error || "batch");
                lastBatchResults.value = null;
                notifyError(result.error || `Erreur ${label.toLowerCase()}`);
                pushHistory(`→ ${label.toUpperCase()} ERREUR: ${result.error || "batch"}`);
            }
        } catch (e) {
            setStatusForEntities(payload.entities, "erreur", e?.message);
            notifyError(`Erreur ${label.toLowerCase()} : ` + (e?.message ?? "erreur"));
            pushHistory(`→ ${label.toUpperCase()} ERREUR: ${e?.message}`);
            lastBatchResults.value = null;
        } finally {
            importing.value = false;
        }
    }

    async function runImportByPages(simulate = false) {
        const pages = parsePageRange(pageRangeRef.value || "");
        if (pages.length === 0) {
            notifyError("Saisis une plage de pages (ex: 1-6 ou 4,5).");
            return;
        }
        const csrf = getCsrfToken();
        if (!csrf) {
            notifyError("Token CSRF introuvable. Veuillez recharger la page.");
            return;
        }

        const label = simulate ? "Simulation" : "Import";
        pushHistory(`${label} par pages (${entityTypeRef.value}) : pages ${pages.join(", ")}.`);
        importing.value = true;
        importByPagesProgress.value = `0/${pages.length}`;
        let totalSuccess = 0;
        let totalErrors = 0;
        let totalEntities = 0;
        const accumulatedErrorResults = [];
        const savedPageNumber = pageNumberRef.value;

        try {
            for (let i = 0; i < pages.length; i++) {
                const p = pages[i];
                importByPagesProgress.value = `${i + 1}/${pages.length}`;
                pageNumberRef.value = p;
                await runSearch();

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

                const result = await postJson("/api/scrapping/import/batch", payload, {
                    headers: { "X-CSRF-TOKEN": csrf },
                });

                if (result.ok && result.data) {
                    const data = result.data;
                    setStatusFromBatchResults(data.results ?? [], simulate);
                    const nextRel = { ...lastBatchRelationsByKeyRef.value };
                    for (const r of data.results ?? []) {
                        if (r?.relations?.length) nextRel[`${r.type}-${r.id}`] = r.relations;
                    }
                    lastBatchRelationsByKeyRef.value = nextRel;
                    const s = data.summary || {};
                    const ok = s.success ?? 0;
                    const err = s.errors ?? 0;
                    totalSuccess += ok;
                    totalErrors += err;
                    (data.results ?? []).filter((r) => r && r.success === false).forEach((r) => accumulatedErrorResults.push(r));
                    pushHistory(`→ Page ${p} : ${ok}/${s.total ?? payload.entities.length} (erreurs: ${err})`);
                } else {
                    setStatusForEntities(payload.entities, "erreur", result.error || "batch");
                    totalErrors += payload.entities.length;
                    payload.entities.forEach((ent) => accumulatedErrorResults.push({ type: ent.type, id: ent.id, success: false, error: result.error || "batch" }));
                    pushHistory(`→ Page ${p} ERREUR: ${result.error || "batch"}`);
                }
            }
            lastBatchResults.value = accumulatedErrorResults.length > 0 ? accumulatedErrorResults : null;
            if (totalErrors > 0) {
                const firstFailed = accumulatedErrorResults[0];
                const firstError = firstFailed?.error || firstFailed?.validation_errors?.[0] || "Erreur inconnue";
                notifyError(`${label} par pages : ${totalErrors} erreur(s). ${typeof firstError === "string" ? firstError : JSON.stringify(firstError)}. Voir Options & historique.`);
                onBatchErrors();
            } else {
                notifySuccess(`${label} par pages terminé : ${totalSuccess}/${totalEntities}`);
            }
            pushHistory(`→ ${label.toUpperCase()} PAR PAGES OK: ${totalSuccess}/${totalEntities} (erreurs: ${totalErrors})`);
        } catch (e) {
            notifyError(`${label} par pages : ` + (e?.message ?? "erreur"));
            pushHistory(`→ ${label.toUpperCase()} PAR PAGES ERREUR: ${e?.message}`);
            lastBatchResults.value = null;
        } finally {
            importing.value = false;
            importByPagesProgress.value = null;
            pageNumberRef.value = savedPageNumber;
            await runSearch();
        }
    }

    /** Import/simulation sur toutes les pages (1 à totalPages), une par une. */
    async function runImportAllPages(simulate = false) {
        const totalPages = getTotalPages();
        const total = Math.max(1, Math.floor(Number(totalPages)));
        const pages = Array.from({ length: total }, (_, i) => i + 1);
        const csrf = getCsrfToken();
        if (!csrf) {
            notifyError("Token CSRF introuvable. Veuillez recharger la page.");
            return;
        }

        const label = simulate ? "Simulation" : "Import";
        pushHistory(`${label} « Tous » (${entityTypeRef.value}) : ${pages.length} page(s).`);
        importing.value = true;
        importByPagesProgress.value = `0/${pages.length}`;
        let totalSuccess = 0;
        let totalErrors = 0;
        let totalEntities = 0;
        const accumulatedErrorResults = [];
        const savedPageNumber = pageNumberRef.value;

        try {
            for (let i = 0; i < pages.length; i++) {
                const p = pages[i];
                importByPagesProgress.value = `${i + 1}/${pages.length}`;
                pageNumberRef.value = p;
                await runSearch();

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

                const result = await postJson("/api/scrapping/import/batch", payload, {
                    headers: { "X-CSRF-TOKEN": csrf },
                });

                if (result.ok && result.data) {
                    const data = result.data;
                    setStatusFromBatchResults(data.results ?? [], simulate);
                    const nextRel = { ...lastBatchRelationsByKeyRef.value };
                    for (const r of data.results ?? []) {
                        if (r?.relations?.length) nextRel[`${r.type}-${r.id}`] = r.relations;
                    }
                    lastBatchRelationsByKeyRef.value = nextRel;
                    const s = data.summary || {};
                    const ok = s.success ?? 0;
                    const err = s.errors ?? 0;
                    totalSuccess += ok;
                    totalErrors += err;
                    (data.results ?? []).filter((r) => r && r.success === false).forEach((r) => accumulatedErrorResults.push(r));
                    pushHistory(`→ Page ${p} : ${ok}/${s.total ?? payload.entities.length} (erreurs: ${err})`);
                } else {
                    setStatusForEntities(payload.entities, "erreur", result.error || "batch");
                    totalErrors += payload.entities.length;
                    payload.entities.forEach((ent) => accumulatedErrorResults.push({ type: ent.type, id: ent.id, success: false, error: result.error || "batch" }));
                    pushHistory(`→ Page ${p} ERREUR: ${result.error || "batch"}`);
                }
            }
            lastBatchResults.value = accumulatedErrorResults.length > 0 ? accumulatedErrorResults : null;
            if (totalErrors > 0) {
                const firstFailed = accumulatedErrorResults[0];
                const firstError = firstFailed?.error || firstFailed?.validation_errors?.[0] || "Erreur inconnue";
                notifyError(`${label} « Tous » : ${totalErrors} erreur(s). ${typeof firstError === "string" ? firstError : JSON.stringify(firstError)}. Voir Options & historique.`);
                onBatchErrors();
            } else {
                notifySuccess(`${label} « Tous » terminé : ${totalSuccess}/${totalEntities}`);
            }
            pushHistory(`→ ${label.toUpperCase()} TOUS OK: ${totalSuccess}/${totalEntities} (erreurs: ${totalErrors})`);
        } catch (e) {
            notifyError(`${label} « Tous » : ` + (e?.message ?? "erreur"));
            pushHistory(`→ ${label.toUpperCase()} TOUS ERREUR: ${e?.message}`);
            lastBatchResults.value = null;
        } finally {
            importing.value = false;
            importByPagesProgress.value = null;
            pageNumberRef.value = savedPageNumber;
            await runSearch();
        }
    }

    async function runBatchOrByPages(mode) {
        const dryRun = mode === "simulate";
        if (batchScopeRef.value === "pages") {
            await runImportByPages(dryRun);
            return;
        }
        if (batchScopeRef.value === "all") {
            await runImportAllPages(dryRun);
            return;
        }
        await runBatch(mode, "auto");
    }

    function clearBatchErrors() {
        lastBatchResults.value = null;
    }

    return {
        importing,
        importByPagesProgress,
        lastBatchResults,
        lastBatchErrorResults,
        selectedCount,
        buildBatchPayload,
        runBatch,
        runImportByPages,
        runBatchOrByPages,
        clearBatchErrors,
    };
}
