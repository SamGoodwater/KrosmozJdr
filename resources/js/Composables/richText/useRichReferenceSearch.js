import { buildCharacteristicKeySuggestionsFromStore } from "@/Composables/characteristic/useCharacteristicKeySuggestions";
import { resolveEntityRouteHref } from "@/Composables/entity/entityRouteRegistry";
import { KREF_ENTITY_CONFIGS } from "@/Composables/richText/krefEntityRegistry";

function buildEntityTableUrl(entityType, searchText, limit) {
    try {
        return route(`api.tables.${entityType}`, {
            format: "entities",
            search: searchText,
            limit,
        });
    } catch {
        return null;
    }
}

function filterByQuery(items, q) {
    const s = String(q || "").trim().toLowerCase();
    if (s.length < 2) return [];
    return items.filter((it) => {
        const a = String(it.label || "").toLowerCase();
        const b = String(it.subtitle || "").toLowerCase();
        const c = String(it.searchText || "").toLowerCase();
        return a.includes(s) || b.includes(s) || c.includes(s);
    });
}

function withAbortSignal(signal) {
    if (!signal) return {};
    return { signal };
}

async function fetchEntityHits(q, perTypeLimit, opts = {}) {
    const { signal, entityType = null } = opts;
    const targets = entityType
        ? KREF_ENTITY_CONFIGS.filter((cfg) => cfg.entityType === entityType)
        : KREF_ENTITY_CONFIGS;
    const promises = targets.map(async (cfg) => {
        const url = buildEntityTableUrl(cfg.entityType, q, perTypeLimit);
        if (!url) return [];
        try {
            const res = await fetch(url, {
                headers: { Accept: "application/json" },
                ...withAbortSignal(signal),
            });
            if (!res.ok) return [];
            const data = await res.json();
            const list = Array.isArray(data?.entities) ? data.entities : [];
            return list.map((raw) => {
                const id = raw.id;
                const title =
                    raw.name || raw.creature?.name || raw.title || raw.slug || `#${id}`;
                const slugOrId = raw.slug || id;
                let href = "";
                try {
                    href = resolveEntityRouteHref(cfg.entityType, "show", slugOrId);
                } catch {
                    href = "";
                }
                const label = String(title);
                return {
                    key: `entity:${cfg.entityType}:${id}`,
                    kind: "entity",
                    label,
                    subtitle: cfg.label,
                    icon: cfg.icon,
                    iconUrl: cfg.iconUrl || "",
                    searchText: `${cfg.entityType} ${id} ${label}`,
                    krefType: "entity",
                    krefPayload: { entityType: cfg.entityType, id },
                    href: href || null,
                };
            });
        } catch (error) {
            if (error?.name === "AbortError") throw error;
            return [];
        }
    });
    const chunks = await Promise.all(promises);
    return chunks.flat();
}

async function fetchCmsHits(q, opts = {}) {
    const { signal, sectionsOnly = false, maxResults = 30 } = opts;
    try {
        const url = route("api.cms.page-section-picker", { q, limit: maxResults });
        const res = await fetch(url, {
            headers: { Accept: "application/json" },
            ...withAbortSignal(signal),
        });
        if (!res.ok) return [];
        const data = await res.json();
        const pages = Array.isArray(data?.pages) ? data.pages : [];
        const sections = Array.isArray(data?.sections) ? data.sections : [];
        const out = [];
        if (!sectionsOnly) {
            for (const p of pages) {
                out.push({
                    key: `page:${p.pageSlug}`,
                    kind: "page",
                    label: String(p.title || p.pageSlug),
                    subtitle: "Page",
                    icon: "fa-solid fa-file-lines",
                    searchText: `page ${p.pageSlug} ${p.title}`,
                    krefType: "page",
                    krefPayload: { pageSlug: p.pageSlug },
                    href: p.href || null,
                });
            }
        }
        for (const s of sections) {
            out.push({
                key: `pageSection:${s.sectionId}`,
                kind: "pageSection",
                label: String(s.sectionTitle || `Section #${s.sectionId}`),
                subtitle: String(s.pageTitle || s.pageSlug || ""),
                icon: "fa-solid fa-anchor",
                searchText: `section ${s.sectionId} ${s.sectionTitle} ${s.pageTitle}`,
                krefType: "pageSection",
                krefPayload: { pageSlug: s.pageSlug, sectionId: s.sectionId },
                href: s.href || null,
                previewUrl:
                    typeof s.sectionId === "number" || (s.sectionId != null && `${s.sectionId}`.match(/^\d+$/))
                        ? route("api.cms.sections.preview-snippet", { section: s.sectionId })
                        : null,
            });
        }
        return out;
    } catch (error) {
        if (error?.name === "AbortError") throw error;
        return [];
    }
}

/**
 * Recherche unifiée pour mentions @ (caractéristiques, entités tables, pages/sections).
 *
 * @param {string} query
 * @param {{ perTypeEntityLimit?: number, maxResults?: number, mode?: "all"|"characteristic"|"section"|"entityType", entityType?: string|null, signal?: AbortSignal|null }} [opts]
 * @returns {Promise<Array<{ key: string, kind: string, label: string, subtitle?: string, icon?: string|null, href?: string|null, previewUrl?: string|null, krefType: string, krefPayload: object }>>}
 */
export async function searchRichReferenceItems(query, opts = {}) {
    const q = String(query || "").trim();
    const perTypeEntityLimit = opts.perTypeEntityLimit ?? 4;
    const maxResults = opts.maxResults ?? 12;
    const mode = opts.mode ?? "all";
    const entityType = opts.entityType ?? null;
    const signal = opts.signal ?? null;

    if (q.length < 2) return [];

    const charSuggestions = buildCharacteristicKeySuggestionsFromStore().map((c) => ({
        key: `characteristic:${c.id}`,
        kind: "characteristic",
        label: String(c.short_name || c.name || c.id),
        subtitle: String(c.name && c.short_name && c.name !== c.short_name ? c.name : "Caractéristique"),
        icon: "fa-solid fa-chart-simple",
        searchText: `${c.id} ${c.name || ""} ${c.short_name || ""}`,
        krefType: "characteristic",
        krefPayload: { key: c.id },
        href: null,
        previewUrl: null,
    }));

    const charFiltered = filterByQuery(charSuggestions, q);
    if (mode === "characteristic") {
        return charFiltered.slice(0, maxResults);
    }

    if (mode === "entityType" && entityType) {
        const entities = await fetchEntityHits(q, perTypeEntityLimit, { signal, entityType });
        return filterByQuery(entities, q).slice(0, maxResults);
    }

    if (mode === "section") {
        const cms = await fetchCmsHits(q, { signal, sectionsOnly: true, maxResults });
        return filterByQuery(cms, q).slice(0, maxResults);
    }

    const [entities, cms] = await Promise.all([
        fetchEntityHits(q, perTypeEntityLimit, { signal }),
        fetchCmsHits(q, { signal, maxResults: Math.max(24, maxResults * 2) }),
    ]);

    const cmsHits = filterByQuery(cms, q);
    const sectionHits = cmsHits.filter((item) => item.kind === "pageSection");
    const pageHits = cmsHits.filter((item) => item.kind === "page");
    const entityHits = filterByQuery(entities, q);

    /** Décision Q13–Q14 / Phase D : caractéristiques → sections → pages → entités (plafond global en dernier). */
    return [...charFiltered, ...sectionHits, ...pageHits, ...entityHits].slice(0, maxResults);
}
