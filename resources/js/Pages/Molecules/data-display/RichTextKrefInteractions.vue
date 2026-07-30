<script setup>
/**
 * Survol type « Wikipédia » + navigation au clic pour les spans `.kref` (références riches).
 * À placer sur un ancêtre du contenu ProseMirror (édition ou lecture).
 */
import { ref, watch, onBeforeUnmount, nextTick } from "vue";
import { router } from "@inertiajs/vue3";
import {
    buildHrefFromKref,
    buildPagePreviewSnippetUrl,
    buildSectionPreviewSnippetUrl,
    getDecodedKrefFromElement,
} from "@/Composables/richText/krefDomUtils";
import {
    getCachedKrefSectionPreview,
    loadKrefSectionPreview,
} from "@/Composables/richText/krefSectionPreviewCache";
import { sanitizeHtml } from "@/Utils/security/sanitizeHtml";

const props = defineProps({
    /** Conteneur qui inclut `.ProseMirror` ou le HTML rendu. */
    rootElement: {
        type: [Object, null],
        default: null,
    },
    enabled: {
        type: Boolean,
        default: true,
    },
});

const popoverVisible = ref(false);
const popoverTop = ref(0);
const popoverLeft = ref(0);
const popoverMaxWidth = ref(320);
const popoverLoading = ref(false);
const popoverTitle = ref("");
const popoverHtml = ref("");
const popoverHint = ref("");
const popoverRef = ref(null);

let showTimer = null;
let hideTimer = null;
let activeAnchor = null;
let abortFetch = null;

const POPOVER_WIDTH = 320;
/** ~10 lignes prose-sm pour l’aperçu section (Phase D). */
const POPOVER_BODY_MAX_PX = 220;
const HOVER_DELAY_MS = 380;
const HIDE_DELAY_MS = 220;

function clearTimers() {
    if (showTimer) {
        clearTimeout(showTimer);
        showTimer = null;
    }
    if (hideTimer) {
        clearTimeout(hideTimer);
        hideTimer = null;
    }
}

function cancelFetch() {
    if (abortFetch) {
        abortFetch.abort();
        abortFetch = null;
    }
}

function positionNear(anchor) {
    const rect = anchor.getBoundingClientRect();
    const vw = window.innerWidth;
    const vh = window.innerHeight;
    const pad = 8;
    let left = rect.left;
    left = Math.min(left, vw - POPOVER_WIDTH - pad);
    left = Math.max(pad, left);
    let top = rect.bottom + 6;
    const maxH = POPOVER_BODY_MAX_PX + 48;
    if (top + maxH > vh - pad) {
        top = Math.max(pad, rect.top - maxH - 6);
    }
    popoverLeft.value = left;
    popoverTop.value = top;
    popoverMaxWidth.value = POPOVER_WIDTH;
}

function resetPopoverContent() {
    popoverTitle.value = "";
    popoverHtml.value = "";
    popoverHint.value = "";
    popoverLoading.value = false;
}

function scheduleHide() {
    clearTimers();
    hideTimer = window.setTimeout(() => {
        hideTimer = null;
        if (popoverRef.value?.matches(":hover")) return;
        popoverVisible.value = false;
        activeAnchor = null;
        cancelFetch();
        resetPopoverContent();
    }, HIDE_DELAY_MS);
}

function cancelHide() {
    if (hideTimer) {
        clearTimeout(hideTimer);
        hideTimer = null;
    }
}

async function loadSectionPreview(info) {
    const cached = getCachedKrefSectionPreview(info);
    if (cached) {
        popoverTitle.value = String(cached?.title || info?.label || "Section");
        popoverHtml.value = String(cached?.html || "");
        popoverHint.value = String(cached?.hint || "");
        popoverLoading.value = false;
        return;
    }

    popoverLoading.value = true;
    popoverHtml.value = "";
    popoverHint.value = "";
    cancelFetch();
    const controller = new AbortController();
    abortFetch = controller;
    try {
        const payload = await loadKrefSectionPreview(info, async () => {
            const url = buildSectionPreviewSnippetUrl(info);
            if (!url) {
                return { title: "", html: "", hint: "Aperçu indisponible." };
            }
            const res = await fetch(url, {
                method: "GET",
                signal: controller.signal,
                credentials: "same-origin",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });
            if (res.status === 401) {
                return { title: "", html: "", hint: "Connectez-vous pour voir l’aperçu." };
            }
            if (!res.ok) {
                return { title: "", html: "", hint: "Aperçu indisponible." };
            }
            const data = await res.json();
            const raw = String(data?.html || "");
            let hint = "";
            if (data?.textPreviewOnly) {
                hint = "Aperçu détaillé réservé aux sections texte.";
            }
            if (data?.canView === false) {
                hint = "Contenu non accessible.";
            }
            return {
                title: String(data?.title || "Section"),
                html: raw ? sanitizeHtml(raw) : "",
                hint,
            };
        });
        popoverTitle.value = String(payload?.title || "Section");
        popoverHtml.value = String(payload?.html || "");
        popoverHint.value = String(payload?.hint || "");
    } catch (e) {
        if (e?.name === "AbortError") return;
        popoverHint.value = "Impossible de charger l’aperçu.";
    } finally {
        popoverLoading.value = false;
        abortFetch = null;
    }
}

function escapeHtml(value) {
    return String(value ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function buildPageSummaryHtml(data) {
    const sections = Array.isArray(data?.sections) ? data.sections : [];
    if (sections.length === 0) {
        return "<p>Aucune section accessible dans cette page.</p>";
    }

    const items = sections.map((section) => {
        const title = escapeHtml(section?.title || `Section #${section?.id || "?"}`);
        const href = escapeHtml(section?.href || "#");
        return `<li><a href="${href}">${title}</a></li>`;
    });

    return `<ol>${items.join("")}</ol>`;
}

async function loadPagePreview(info) {
    popoverLoading.value = true;
    popoverHtml.value = "";
    popoverHint.value = "";
    cancelFetch();
    const controller = new AbortController();
    abortFetch = controller;

    try {
        const url = buildPagePreviewSnippetUrl(info);
        if (!url) {
            popoverHint.value = "Cliquez pour ouvrir la page.";
            return;
        }
        const res = await fetch(url, {
            method: "GET",
            signal: controller.signal,
            credentials: "same-origin",
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
        });
        if (res.status === 401) {
            popoverHint.value = "Connectez-vous pour voir l’aperçu.";
            return;
        }
        if (!res.ok) {
            popoverHint.value = "Cliquez pour ouvrir la page.";
            return;
        }
        const data = await res.json();
        popoverTitle.value = String(data?.title || info?.label || "Page");
        popoverHtml.value = sanitizeHtml(buildPageSummaryHtml(data));
        const count = Number(data?.sectionCount ?? 0);
        if (count > 12) {
            popoverHint.value = `${count - 12} section(s) supplémentaire(s) sur la page.`;
        }
    } catch (e) {
        if (e?.name === "AbortError") return;
        popoverHint.value = "Impossible de charger l’aperçu.";
    } finally {
        popoverLoading.value = false;
        abortFetch = null;
    }
}

function openForAnchor(anchor, info) {
    if (!props.enabled || !anchor) return;
    const t = String(info?.krefType || "");
    /** Infobulles riches gérées dans {@link ReferenceInlineNodeView.vue} (TipTap). */
    if (t === "characteristic" || t === "entity") {
        return;
    }
    activeAnchor = anchor;
    positionNear(anchor);
    popoverVisible.value = true;
    resetPopoverContent();

    const previewUrl = buildSectionPreviewSnippetUrl(info);
    if (previewUrl) {
        popoverTitle.value = info.label || "Section";
        void loadSectionPreview(info);
        return;
    }

    if (info.krefType === "page") {
        popoverTitle.value = info.label || "Page";
        void loadPagePreview(info);
        return;
    }

    popoverTitle.value = info.label || "Référence";
    popoverHint.value = "Aperçu non disponible pour ce type de référence.";
}

function scheduleShow(anchor, info) {
    clearTimers();
    showTimer = window.setTimeout(() => {
        showTimer = null;
        openForAnchor(anchor, info);
    }, HOVER_DELAY_MS);
}

function elementFromEventTarget(t) {
    if (t instanceof Element) return t;
    if (t instanceof Node && t.parentElement) return t.parentElement;
    return null;
}

function onContainerMouseOver(e) {
    if (!props.enabled || !props.rootElement) return;
    const t = elementFromEventTarget(e.target);
    if (!t) return;
    const kref = t.closest("span.kref");
    if (!kref || !props.rootElement.contains(kref)) return;
    const info = getDecodedKrefFromElement(kref);
    if (!info) return;
    if (activeAnchor === kref && popoverVisible.value) return;
    activeAnchor = kref;
    cancelHide();
    scheduleShow(kref, info);
}

function onContainerMouseOut(e) {
    if (!props.enabled || !props.rootElement) return;
    const t = elementFromEventTarget(e.target);
    if (!t) return;
    const kref = t.closest("span.kref");
    if (!kref || !props.rootElement.contains(kref)) return;
    const rel = e.relatedTarget;
    if (rel instanceof Node && kref.contains(rel)) return;
    if (rel instanceof Node && popoverRef.value?.contains(rel)) return;
    scheduleHide();
}

function onContainerClick(e) {
    if (!props.enabled || !props.rootElement) return;
    const t = elementFromEventTarget(e.target);
    if (!t) return;
    const kref = t.closest("span.kref");
    if (!kref || !props.rootElement.contains(kref)) return;
    const info = getDecodedKrefFromElement(kref);
    if (!info) return;
    const href = buildHrefFromKref(info);
    if (!href) return;
    e.preventDefault();
    e.stopPropagation();
    router.visit(href);
}

let rootEl = null;

function detach() {
    clearTimers();
    cancelFetch();
    if (rootEl) {
        rootEl.removeEventListener("mouseover", onContainerMouseOver);
        rootEl.removeEventListener("mouseout", onContainerMouseOut);
        rootEl.removeEventListener("click", onContainerClick, true);
        rootEl = null;
    }
    popoverVisible.value = false;
    activeAnchor = null;
    resetPopoverContent();
}

function attach(el) {
    detach();
    rootEl = el;
    rootEl.addEventListener("mouseover", onContainerMouseOver);
    rootEl.addEventListener("mouseout", onContainerMouseOut);
    rootEl.addEventListener("click", onContainerClick, true);
}

watch(
    () => [props.rootElement, props.enabled],
    () => {
        void nextTick(() => {
            if (!props.enabled || !(props.rootElement instanceof HTMLElement)) {
                detach();
                return;
            }
            attach(props.rootElement);
        });
    },
    { immediate: true },
);

onBeforeUnmount(() => {
    detach();
});

function onPopoverEnter() {
    cancelHide();
}

function onPopoverLeave() {
    scheduleHide();
}
</script>

<template>
    <Teleport to="body">
        <div
            v-show="popoverVisible"
            ref="popoverRef"
            class="kref-preview-popover kref-preview-popover--wiki pointer-events-auto fixed z-9999 rounded-box border border-base-300/80 bg-base-100/95 p-0 text-sm shadow-2xl backdrop-blur-sm"
            :style="{
                top: `${popoverTop}px`,
                left: `${popoverLeft}px`,
                maxWidth: `${popoverMaxWidth}px`,
                maxHeight: `min(48vh, ${POPOVER_BODY_MAX_PX + 56}px)`,
            }"
            role="tooltip"
            @mouseenter="onPopoverEnter"
            @mouseleave="onPopoverLeave"
        >
            <div
                class="kref-preview-popover__title border-b border-base-200/80 bg-base-200/30 px-3 py-2 text-sm font-bold leading-snug text-base-content"
            >
                {{ popoverTitle }}
            </div>
            <div v-if="popoverLoading" class="px-3 py-3 text-base-content/60 italic">Chargement…</div>
            <!-- eslint-disable vue/no-v-html -- HTML issu de l’API + second passage sanitizeHtml() -->
            <div
                v-else-if="popoverHtml"
                class="kref-preview-popover__body kref-rich-preview-panel prose prose-sm max-w-none overflow-x-hidden overflow-y-auto wrap-break-word px-3 py-2 text-base-content"
                v-html="popoverHtml"
            />
            <!-- eslint-enable vue/no-v-html -->
            <p v-if="popoverHint" class="px-3 pb-2 pt-1 text-xs text-base-content/70">
                {{ popoverHint }}
            </p>
        </div>
    </Teleport>
</template>

<style scoped lang="scss">
.kref-preview-popover__title {
    border-left: 3px solid hsl(var(--p) / 0.85);
    padding-left: 0.65rem;
}

.kref-preview-popover__body {
    max-height: 13.75rem;
}

.kref-preview-popover__body :deep(p) {
    margin-bottom: 0.35rem;
}
.kref-preview-popover__body :deep(p:last-child) {
    margin-bottom: 0;
}

.kref-preview-popover__body :deep(img),
.kref-preview-popover__body :deep(table),
.kref-preview-popover__body :deep(pre) {
    max-width: 100%;
}

.kref-rich-preview-panel :deep(table) {
    display: block;
    overflow-x: auto;
}

/* Panneaux d’aperçu kref (tooltips carac./entité + popover section) */
.kref-rich-preview-panel {
    text-align: left;
    line-height: 1.45;
}
</style>

<style lang="scss">
/* Hors scoped : éditeur + lecture (ProseMirror / TipTap) */
.section-rich-editor :deep(.ProseMirror span.kref),
.rich-text-readonly :deep(.ProseMirror span.kref) {
    gap: 0.2em;
}

.section-rich-editor :deep(.ProseMirror span.kref--nav),
.rich-text-readonly :deep(.ProseMirror span.kref--nav) {
    cursor: pointer;
    text-decoration: underline;
    text-decoration-style: dotted;
    text-underline-offset: 2px;
}

.section-rich-editor :deep(.ProseMirror span.kref--type-characteristic),
.rich-text-readonly :deep(.ProseMirror span.kref--type-characteristic) {
    color: var(--color-warning);
    background: color-mix(in oklch, var(--color-warning) 16%, transparent);
    border-color: color-mix(in oklch, var(--color-warning) 35%, transparent);
}

.section-rich-editor :deep(.ProseMirror span.kref--type-entity),
.rich-text-readonly :deep(.ProseMirror span.kref--type-entity) {
    color: var(--color-secondary);
    background: color-mix(in oklch, var(--color-secondary) 16%, transparent);
    border-color: color-mix(in oklch, var(--color-secondary) 35%, transparent);
}

.section-rich-editor :deep(.ProseMirror span.kref--type-page),
.rich-text-readonly :deep(.ProseMirror span.kref--type-page) {
    color: var(--color-info);
    background: color-mix(in oklch, var(--color-info) 16%, transparent);
    border-color: color-mix(in oklch, var(--color-info) 35%, transparent);
}

.section-rich-editor :deep(.ProseMirror span.kref--type-pageSection),
.rich-text-readonly :deep(.ProseMirror span.kref--type-pageSection) {
    color: var(--color-accent);
    background: color-mix(in oklch, var(--color-accent) 16%, transparent);
    border-color: color-mix(in oklch, var(--color-accent) 35%, transparent);
}

.section-rich-editor :deep(.ProseMirror span.kref--invalid),
.rich-text-readonly :deep(.ProseMirror span.kref--invalid) {
    border-style: dashed;
    opacity: 0.95;
    color: var(--color-error);
    background: color-mix(in oklch, var(--color-error) 14%, transparent);
    border-color: color-mix(in oklch, var(--color-error) 38%, transparent);
}
</style>
