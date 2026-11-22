/**
 * Fil d'Ariane, plan et section active d'une page CMS.
 *
 * @example
 * const chrome = usePageSectionChrome(pageModel, sortedSections, {
 *   pageTitle: entityName,
 *   prepareSection: (id) => requestSectionMount(id),
 * });
 */
import { computed, nextTick, onBeforeUnmount, onMounted, ref, toValue, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { useDynamicMenu } from "@/Composables/layout/useDynamicMenu";
import { sectionDisplayLabel } from "@/Utils/section/sectionTitle";
import { publishedSectionPlanLabel } from "@/Utils/section/sectionPlanLabels";

/**
 * @param {string} targetId
 * @param {Array} sections
 * @returns {number|null}
 */
export function resolveSectionIdFromAnchor(targetId, sections = []) {
    const raw = String(targetId || "").replace(/^#/, "").trim();
    if (!raw) {
        return null;
    }
    if (raw.startsWith("section-") && raw.length > "section-".length) {
        const id = Number(raw.slice("section-".length));
        return Number.isNaN(id) || id <= 0 ? null : id;
    }
    if (raw.startsWith("ssec-") && raw.length > "ssec-".length) {
        const rest = raw.slice("ssec-".length);
        for (const section of sections) {
            const slug = String(section?.slug || "").trim();
            if (!slug) {
                continue;
            }
            if (rest === slug || rest.startsWith(`${slug}-`)) {
                const id = Number(section.id);
                return Number.isNaN(id) || id <= 0 ? null : id;
            }
        }
    }
    return null;
}

function sectionHash(section) {
    if (!section) return "";
    const slug = String(section.slug || "").trim();
    if (slug) return `#ssec-${slug}`;
    const sid = section.id;
    return sid ? `#section-${sid}` : "";
}

function parseL4Headings(section) {
    const html = String(section?.data?.content || "").trim();
    if (!html || typeof DOMParser === "undefined") return [];
    try {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, "text/html");
        const used = new Set();
        return Array.from(doc.body.querySelectorAll("h4, h5, h6"))
            .map((el, idx) => {
                const text = String(el.textContent || "").trim();
                if (!text) return null;
                const base = text
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "")
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, "")
                    .trim()
                    .replace(/\s+/g, "-");
                const local = base || `heading-${idx + 1}`;
                const prefix = String(section?.slug || section?.id || "").trim();
                const anchorPrefix = prefix ? `ssec-${prefix}` : `section-${String(section?.id || "")}`;
                let id = `${anchorPrefix}-${local}`;
                let n = 2;
                while (used.has(id)) {
                    id = `${anchorPrefix}-${local}-${n}`;
                    n += 1;
                }
                used.add(id);
                return { id, text, hash: `#${id}` };
            })
            .filter(Boolean);
    } catch {
        return [];
    }
}

/**
 * @param {import('vue').MaybeRefOrGetter} pageSource - Modèle Page
 * @param {import('vue').MaybeRefOrGetter<Array>} sectionsSource
 * @param {{ pageTitle?: import('vue').MaybeRefOrGetter<string>, prepareSection?: (id: number) => void, enabled?: import('vue').MaybeRefOrGetter<boolean> }} [options]
 */
export function usePageSectionChrome(pageSource, sectionsSource, options = {}) {
    const { menuItems } = useDynamicMenu();
    const activeSectionId = ref(null);
    let sectionObserver = null;

    const enabled = computed(() => toValue(options.enabled) !== false);

    const pageModel = computed(() => toValue(pageSource));

    const sortedSections = computed(() => {
        const sections = toValue(sectionsSource);
        const list = Array.isArray(sections) ? sections : [];
        return list
            .map((section, index) => {
                const raw = section?.order ?? section?.pivot_level ?? section?.pivot?.level ?? section?.level;
                const order = Number(raw);
                return {
                    section,
                    order: Number.isFinite(order) ? order : index + 1,
                };
            })
            .sort((a, b) => {
                if (a.order !== b.order) return a.order - b.order;
                return String(a.section?.title || "").localeCompare(String(b.section?.title || ""), "fr");
            })
            .map((item) => item.section);
    });

    const planSections = computed(() =>
        sortedSections.value.map((section, index) => ({
            id: section.id ?? `section-${index + 1}`,
            title: sectionDisplayLabel(section) || publishedSectionPlanLabel(section.id),
            hash: sectionHash(section),
            l4Headings: parseL4Headings(section),
        })),
    );

    const activeSectionTitle = computed(() => {
        const id = activeSectionId.value;
        const current = id
            ? planSections.value.find((section) => String(section.id) === String(id))
            : planSections.value[0];
        return String(current?.title || "").trim();
    });

    const pageTitle = computed(() => {
        const override = String(toValue(options.pageTitle) || "").trim();
        if (override) return override;
        return String(pageModel.value?.title || "").trim() || "Page";
    });

    const flattenedMenuPages = computed(() => {
        const tree = Array.isArray(menuItems.value) ? menuItems.value : [];
        const currentUrl = String(pageModel.value?.url || "").trim();
        if (!tree.length || !currentUrl) return [];

        const normalize = (url) => String(url || "").replace(/\/+$/, "");
        const current = normalize(currentUrl);
        let siblingBranch = null;

        const visit = (items = []) => {
            if (!Array.isArray(items) || !items.length || siblingBranch) return;
            for (const item of items) {
                const itemUrl = normalize(item?.url);
                if (itemUrl === current) {
                    siblingBranch = items;
                    return;
                }
                const children = Array.isArray(item?.children) ? item.children : [];
                if (children.length) {
                    if (children.some((child) => normalize(child?.url) === current)) {
                        siblingBranch = children;
                        return;
                    }
                    visit(children);
                    if (siblingBranch) return;
                }
            }
        };

        visit(tree);
        return (siblingBranch || [])
            .filter((item) => item?.title && item?.url)
            .map((item) => ({ title: item.title, url: item.url }));
    });

    const l1Pages = computed(() => {
        const tree = Array.isArray(menuItems.value) ? menuItems.value : [];
        const currentUrl = String(pageModel.value?.url || "").trim();
        if (!tree.length || !currentUrl) return [];

        const normalize = (url) => String(url || "").replace(/\/+$/, "");
        const current = normalize(currentUrl);
        let groupChildren = [];

        const findGroupForCurrent = (groups = []) => {
            for (const group of groups) {
                const children = Array.isArray(group?.children) ? group.children : [];
                if (!children.length) continue;
                const hasCurrentInside = children.some((parentItem) => {
                    if (normalize(parentItem?.url) === current) return true;
                    const nested = Array.isArray(parentItem?.children) ? parentItem.children : [];
                    return nested.some((child) => normalize(child?.url) === current);
                });
                if (hasCurrentInside) {
                    groupChildren = children;
                    return true;
                }
            }
            return false;
        };

        findGroupForCurrent(tree);
        return (groupChildren || [])
            .filter((item) => item?.title && item?.url)
            .map((item) => ({ title: item.title, url: item.url }));
    });

    const l1Title = computed(() => {
        const parentTitle = String(pageModel.value?.parent?.title || "").trim();
        if (parentTitle) return parentTitle;
        const menuGroup = String(pageModel.value?.menuGroup || "").trim();
        if (menuGroup) return menuGroup;
        return "Règles";
    });

    const parentPageBreadcrumb = computed(() => {
        const parent = pageModel.value?.parent;
        if (!parent?.title) return null;
        const url = String(parent.url || "").trim();
        if (!url) return null;
        return { title: parent.title, url };
    });

    const currentPageBreadcrumb = computed(() => {
        const title = pageTitle.value;
        const url = String(pageModel.value?.url || "").trim();
        if (!title || !url) return null;
        return { title, url };
    });

    const showBreadcrumb = computed(() => pageModel.value?.showRulesBreadcrumb !== false);

    function prepare(sectionOrId) {
        const id = Number(sectionOrId);
        if (!Number.isNaN(id) && id > 0) {
            options.prepareSection?.(id);
        }
    }

    function scrollToHash(raw) {
        const targetId = String(raw || "").replace(/^#/, "");
        if (!targetId) return;
        const sectionId = resolveSectionIdFromAnchor(targetId, sortedSections.value);
        if (sectionId) prepare(sectionId);
        nextTick(() => {
            requestAnimationFrame(() => {
                const el = document.getElementById(targetId);
                if (el && typeof el.scrollIntoView === "function") {
                    el.scrollIntoView({ behavior: "smooth", block: "start" });
                }
            });
        });
    }

    function navigateToSection(section) {
        if (!section) return;
        const hash = section.hash || "";
        if (!hash || typeof window === "undefined") return;
        prepare(resolveSectionIdFromAnchor(hash, sortedSections.value) ?? Number(section?.id));
        window.location.hash = hash;
        scrollToHash(hash);
    }

    function navigateToPage(item) {
        const url = String(item?.url || "").trim();
        if (!url) return;
        router.visit(url, { preserveScroll: true });
    }

    function scrollToSectionFromHash() {
        if (!enabled.value) return;
        const raw = (typeof window !== "undefined" ? window.location.hash : "").replace(/^#/, "");
        if (!raw) return;
        const isNumericSection = raw.startsWith("section-") && raw !== "section-";
        const isSlugSection = raw.startsWith("ssec-") && raw.length > "ssec-".length;
        if (!isNumericSection && !isSlugSection) return;
        scrollToHash(raw);
    }

    function setupSectionObserver() {
        if (!enabled.value || typeof window === "undefined" || typeof IntersectionObserver === "undefined") {
            return;
        }
        if (sectionObserver) {
            sectionObserver.disconnect();
            sectionObserver = null;
        }
        const nodes = Array.from(document.querySelectorAll("[data-section-id]"));
        if (!nodes.length) return;

        sectionObserver = new IntersectionObserver(
            (entries) => {
                const visible = entries
                    .filter((entry) => entry.isIntersecting)
                    .sort((a, b) => b.intersectionRatio - a.intersectionRatio);
                if (!visible.length) return;
                const top = visible[0].target;
                const rawId = Number(top?.getAttribute("data-section-id"));
                if (!Number.isNaN(rawId) && rawId > 0) {
                    activeSectionId.value = rawId;
                }
            },
            {
                root: null,
                threshold: [0.2, 0.45, 0.7],
                rootMargin: "-18% 0px -58% 0px",
            },
        );

        nodes.forEach((node) => sectionObserver.observe(node));
        const firstId = Number(nodes[0]?.getAttribute("data-section-id"));
        if (!Number.isNaN(firstId) && firstId > 0 && activeSectionId.value == null) {
            activeSectionId.value = firstId;
        }
    }

    function onInertiaFinish() {
        scrollToSectionFromHash();
    }

    onMounted(() => {
        if (!enabled.value || typeof document === "undefined") return;
        scrollToSectionFromHash();
        setupSectionObserver();
        document.addEventListener("inertia:finish", onInertiaFinish);
        window.addEventListener("hashchange", scrollToSectionFromHash);
    });

    onBeforeUnmount(() => {
        if (sectionObserver) {
            sectionObserver.disconnect();
            sectionObserver = null;
        }
        if (typeof document !== "undefined") {
            document.removeEventListener("inertia:finish", onInertiaFinish);
            window.removeEventListener("hashchange", scrollToSectionFromHash);
        }
    });

    watch(
        () => sortedSections.value.map((section) => section?.id).join(","),
        () => {
            if (!enabled.value) return;
            scrollToSectionFromHash();
            nextTick(() => setupSectionObserver());
        },
    );

    watch(enabled, (on) => {
        if (!on && sectionObserver) {
            sectionObserver.disconnect();
            sectionObserver = null;
        }
    });

    return {
        planSections,
        activeSectionTitle,
        pageTitle,
        flattenedMenuPages,
        l1Pages,
        l1Title,
        parentPageBreadcrumb,
        currentPageBreadcrumb,
        showBreadcrumb,
        navigateToSection,
        navigateToPage,
        resolveSectionIdFromAnchor: (targetId) => resolveSectionIdFromAnchor(targetId, sortedSections.value),
    };
}
