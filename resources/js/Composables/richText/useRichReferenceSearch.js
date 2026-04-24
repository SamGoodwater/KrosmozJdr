import { buildCharacteristicKeySuggestionsFromStore } from "@/Composables/characteristic/useCharacteristicKeySuggestions";
import { resolveEntityRouteHref } from "@/Composables/entity/entityRouteRegistry";

const ENTITY_TABLE_TYPES = [
    { entityType: "campaigns", label: "Campagnes", icon: "fa-solid fa-flag" },
    { entityType: "scenarios", label: "Scénarios", icon: "fa-solid fa-scroll" },
    { entityType: "spells", label: "Sorts", icon: "fa-solid fa-wand-magic-sparkles" },
    { entityType: "items", label: "Objets", icon: "fa-solid fa-sack-dollar" },
    { entityType: "resources", label: "Ressources", icon: "fa-solid fa-box" },
    { entityType: "consumables", label: "Consommables", icon: "fa-solid fa-flask" },
    { entityType: "monsters", label: "Monstres", icon: "fa-solid fa-dragon" },
    { entityType: "npcs", label: "PNJ", icon: "fa-solid fa-user" },
    { entityType: "panoplies", label: "Panoplies", icon: "fa-solid fa-shirt" },
    { entityType: "capabilities", label: "Capacités", icon: "fa-solid fa-bolt" },
    { entityType: "creatures", label: "Créatures", icon: "fa-solid fa-paw" },
];

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

async function fetchEntityHits(q, perTypeLimit) {
    const promises = ENTITY_TABLE_TYPES.map(async (cfg) => {
        const url = buildEntityTableUrl(cfg.entityType, q, perTypeLimit);
        if (!url) return [];
        try {
            const res = await fetch(url, { headers: { Accept: "application/json" } });
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
                    searchText: `${cfg.entityType} ${id} ${label}`,
                    krefType: "entity",
                    krefPayload: { entityType: cfg.entityType, id },
                    href: href || null,
                };
            });
        } catch {
            return [];
        }
    });
    const chunks = await Promise.all(promises);
    return chunks.flat();
}

async function fetchCmsHits(q) {
    try {
        const url = route("api.cms.page-section-picker", { q, limit: 30 });
        const res = await fetch(url, { headers: { Accept: "application/json" } });
        if (!res.ok) return [];
        const data = await res.json();
        const pages = Array.isArray(data?.pages) ? data.pages : [];
        const sections = Array.isArray(data?.sections) ? data.sections : [];
        const out = [];
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
    } catch {
        return [];
    }
}

/**
 * Recherche unifiée pour mentions @ (caractéristiques, entités tables, pages/sections).
 *
 * @param {string} query
 * @param {{ perTypeEntityLimit?: number, maxResults?: number }} [opts]
 * @returns {Promise<Array<{ key: string, kind: string, label: string, subtitle?: string, icon?: string|null, href?: string|null, previewUrl?: string|null, krefType: string, krefPayload: object }>>}
 */
export async function searchRichReferenceItems(query, opts = {}) {
    const q = String(query || "").trim();
    const perTypeEntityLimit = opts.perTypeEntityLimit ?? 4;
    const maxResults = opts.maxResults ?? 40;

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

    const [entities, cms] = await Promise.all([
        fetchEntityHits(q, perTypeEntityLimit),
        fetchCmsHits(q),
    ]);

    const charHits = filterByQuery(charSuggestions, q).slice(0, maxResults);
    const entityHits = filterByQuery(entities, q).slice(0, maxResults);
    const cmsHits = filterByQuery(cms, q).slice(0, maxResults);

    const merged = [...charHits, ...entityHits, ...cmsHits];
    return merged.slice(0, maxResults);
}
