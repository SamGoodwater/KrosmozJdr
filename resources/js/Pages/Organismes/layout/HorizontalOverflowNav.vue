<script setup>
/**
 * Barre de navigation horizontale : boutons glass alignés à droite, overflow dans un menu,
 * et sous le breakpoint `md` (useDevice) tout regroupé dans un seul dropdown.
 *
 * Item attendu : `{ title, href, icon, path? }` — `href` = nom de route Ziggy, `path` = préfixe URL pour l’état actif.
 *
 * @example
 * <HorizontalOverflowNav
 *   :items="navItems"
 *   :is-item-active="(item) => page.url.startsWith(item.path)"
 *   aria-label="Navigation"
 * />
 */
import {
    computed,
    nextTick,
    onMounted,
    onUnmounted,
    ref,
    watch,
} from 'vue';
import { Link } from '@inertiajs/vue3';
import Route from '@/Pages/Atoms/action/Route.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Dropdown from '@/Pages/Atoms/action/Dropdown.vue';
import { useDevice } from '@/Composables/layout/useDevice';

const props = defineProps({
    /** @type {{ title: string, href: string, icon: string, path?: string }[]} */
    items: { type: Array, required: true },
    /** (item) => boolean */
    isItemActive: { type: Function, required: true },
    /** aria-label du `<nav>` */
    ariaLabel: { type: String, default: 'Navigation' },
    /** Libellé du bouton menu mobile */
    mobileTriggerLabel: { type: String, default: 'Menu' },
    /** aria-label du dropdown mobile */
    mobileMenuAriaLabel: { type: String, default: '' },
    /** aria-label du dropdown overflow (desktop) */
    overflowMenuAriaLabel: { type: String, default: 'Autres liens' },
});

const { isMobile } = useDevice();

const visibleCount = ref(999);

const safeItems = computed(() =>
    Array.isArray(props.items) ? props.items.filter(Boolean) : []
);

const visibleItems = computed(() =>
    safeItems.value.slice(0, visibleCount.value)
);
const overflowItems = computed(() =>
    safeItems.value.slice(visibleCount.value)
);

const overflowHasActive = computed(() =>
    overflowItems.value.some((item) => props.isItemActive(item))
);

const mobileMenuHasActive = computed(() =>
    safeItems.value.some((item) => props.isItemActive(item))
);

const mobileDropdownAria = computed(
    () => props.mobileMenuAriaLabel || props.ariaLabel
);

const itemsRowRef = ref(null);
const measureRef = ref(null);

const GAP_FALLBACK_PX = 12;

const navBtnClass =
    'gap-2.5 min-h-9 min-w-0 px-2.5 sm:min-h-10 sm:px-3 motion-safe:transition-[transform,box-shadow] duration-200 ease-out motion-safe:hover:-translate-y-0.5 motion-safe:hover:shadow-[0_6px_24px_rgba(0,0,0,0.22)] motion-safe:focus-visible:-translate-y-0.5 motion-safe:active:translate-y-0';

const navMenuBtnClass =
    'motion-safe:transition-[transform,box-shadow] duration-200 ease-out motion-safe:hover:-translate-y-0.5 motion-safe:hover:shadow-[0_6px_24px_rgba(0,0,0,0.22)] motion-safe:focus-visible:-translate-y-0.5 motion-safe:active:translate-y-0';

/** Classes communes des entrées `Link` dans les menus déroulants (mobile + overflow). */
const DROPDOWN_MENU_LINK_BASE =
    'flex items-center gap-2 rounded-lg no-underline! transition-[background-color,box-shadow,transform] duration-200 motion-safe:hover:-translate-y-px motion-safe:hover:shadow-md motion-safe:active:translate-y-0';

/**
 * @param {{ title?: string, href?: string }} item
 * @returns {string[]}
 */
function dropdownMenuLinkClass(item) {
    return [
        DROPDOWN_MENU_LINK_BASE,
        props.isItemActive(item)
            ? 'bg-primary/15 text-primary shadow-sm ring-1 ring-primary/20'
            : 'hover:bg-base-200/80',
    ];
}

/**
 * @param {HTMLElement | null} el
 * @returns {number}
 */
function readGapPx(el) {
    if (!el || typeof window === 'undefined') {
        return GAP_FALLBACK_PX;
    }
    const g = getComputedStyle(el).gap || getComputedStyle(el).columnGap;
    const parsed = parseFloat(g);
    return Number.isFinite(parsed) ? parsed : GAP_FALLBACK_PX;
}

function updateVisibleCount() {
    const row = itemsRowRef.value;
    const measure = measureRef.value;
    const list = safeItems.value;
    const n = list.length;

    if (!row || !measure || n === 0) {
        visibleCount.value = n;
        return;
    }

    const kids = Array.from(measure.children);
    if (kids.length < 2) {
        visibleCount.value = n;
        return;
    }

    const moreEl = kids[kids.length - 1];
    const itemEls = kids.slice(0, -1);
    if (itemEls.length !== n) {
        visibleCount.value = n;
        return;
    }

    const widths = itemEls.map((el) => el.getBoundingClientRect().width);
    const moreW = moreEl.getBoundingClientRect().width;
    const gap = readGapPx(measure);
    const available = row.getBoundingClientRect().width;

    let best = n;
    for (let k = n; k >= 0; k--) {
        let sum = 0;
        for (let i = 0; i < k; i++) {
            sum += widths[i];
            if (i < k - 1) {
                sum += gap;
            }
        }
        const needMore = k < n;
        const total = sum + (needMore ? gap + moreW : 0);
        if (total <= available + 0.5) {
            best = k;
            break;
        }
    }

    visibleCount.value = best;
}

/** @type {ResizeObserver | null} */
let rowObserver = null;

function scheduleLayout() {
    nextTick(() => {
        updateVisibleCount();
    });
}

onMounted(() => {
    scheduleLayout();
    if (typeof document !== 'undefined' && document.fonts?.ready) {
        document.fonts.ready.then(() => scheduleLayout());
    }
});

onUnmounted(() => {
    rowObserver?.disconnect();
    rowObserver = null;
});

watch(
    () => safeItems.value.map((s) => s.href).join('\0'),
    () => {
        visibleCount.value = safeItems.value.length;
        scheduleLayout();
    }
);

watch(
    itemsRowRef,
    (el) => {
        rowObserver?.disconnect();
        if (el) {
            if (!rowObserver) {
                rowObserver = new ResizeObserver(() => scheduleLayout());
            }
            rowObserver.observe(el);
        }
        scheduleLayout();
    },
    { immediate: true }
);

function itemKey(item, prefix) {
    return `${prefix}-${item.href}`;
}
</script>

<template>
    <nav class="glass-border-b-sm mb-2 w-full pb-4 pt-0.5" :aria-label="ariaLabel">
        <div v-if="isMobile" class="flex w-full justify-end">
            <Dropdown
                placement="bottom-end"
                variant="glass"
                size="sm"
                :aria-label="mobileDropdownAria"
            >
                <template #trigger>
                    <Btn
                        variant="glass"
                        color="neutral"
                        size="md"
                        :active="mobileMenuHasActive"
                        :class="navBtnClass"
                    >
                        <span
                            class="fa-solid fa-bars opacity-90"
                            aria-hidden="true"
                        />
                        {{ mobileTriggerLabel }}
                    </Btn>
                </template>
                <template #content>
                    <ul
                        class="menu menu-sm w-[min(100vw-2rem,16rem)] min-w-48 max-w-[calc(100vw-2rem)] p-1"
                        role="menu"
                    >
                        <li
                            v-for="item in safeItems"
                            :key="itemKey(item, 'm')"
                            role="none"
                        >
                            <Link
                                :href="route(item.href)"
                                role="menuitem"
                                :class="dropdownMenuLinkClass(item)"
                            >
                                <span
                                    class="fa-solid w-4 shrink-0 text-center opacity-90"
                                    :class="item.icon"
                                    aria-hidden="true"
                                />
                                <span class="truncate">{{ item.title }}</span>
                            </Link>
                        </li>
                    </ul>
                </template>
            </Dropdown>
        </div>

        <template v-else>
            <div
                ref="measureRef"
                class="pointer-events-none fixed top-0 left-0 z-[-1000] flex gap-3 opacity-0"
                aria-hidden="true"
            >
                <div
                    v-for="item in safeItems"
                    :key="'measure-' + item.href"
                    class="inline-flex shrink-0"
                >
                    <Route :route="item.href">
                        <Btn
                            variant="glass"
                            color="neutral"
                            size="md"
                            :class="navBtnClass"
                        >
                            <span
                                class="fa-solid opacity-90"
                                :class="item.icon"
                                aria-hidden="true"
                            />
                            {{ item.title }}
                        </Btn>
                    </Route>
                </div>
                <div class="inline-flex shrink-0">
                    <Btn
                        variant="glass"
                        color="neutral"
                        size="md"
                        square
                        :class="navMenuBtnClass"
                    >
                        <span class="fa-solid fa-bars" aria-hidden="true" />
                    </Btn>
                </div>
            </div>

            <div
                ref="itemsRowRef"
                class="flex min-w-0 w-full justify-end overflow-hidden"
            >
                <ul
                    class="flex min-w-0 flex-1 flex-nowrap items-center justify-end gap-3"
                >
                    <li
                        v-for="item in visibleItems"
                        :key="item.href"
                        class="shrink-0"
                    >
                        <Route :route="item.href" :title="item.title">
                            <Btn
                                variant="glass"
                                color="neutral"
                                size="md"
                                :active="isItemActive(item)"
                                :class="navBtnClass"
                            >
                                <span
                                    class="fa-solid opacity-90"
                                    :class="item.icon"
                                    aria-hidden="true"
                                />
                                {{ item.title }}
                            </Btn>
                        </Route>
                    </li>
                    <li v-if="overflowItems.length" class="shrink-0">
                        <Dropdown
                            placement="bottom-end"
                            variant="glass"
                            size="sm"
                            :aria-label="overflowMenuAriaLabel"
                        >
                            <template #trigger>
                                <Btn
                                    variant="glass"
                                    color="neutral"
                                    size="md"
                                    square
                                    :active="overflowHasActive"
                                    :class="navMenuBtnClass"
                                >
                                    <span
                                        class="fa-solid fa-bars"
                                        aria-hidden="true"
                                    />
                                </Btn>
                            </template>
                            <template #content>
                                <ul
                                    class="menu menu-sm w-56 min-w-48 p-1"
                                    role="menu"
                                >
                                    <li
                                        v-for="item in overflowItems"
                                        :key="itemKey(item, 'o')"
                                        role="none"
                                    >
                                        <Link
                                            :href="route(item.href)"
                                            role="menuitem"
                                            :class="dropdownMenuLinkClass(item)"
                                        >
                                            <span
                                                class="fa-solid w-4 shrink-0 text-center opacity-90"
                                                :class="item.icon"
                                                aria-hidden="true"
                                            />
                                            <span class="truncate">{{
                                                item.title
                                            }}</span>
                                        </Link>
                                    </li>
                                </ul>
                            </template>
                        </Dropdown>
                    </li>
                </ul>
            </div>
        </template>
    </nav>
</template>
