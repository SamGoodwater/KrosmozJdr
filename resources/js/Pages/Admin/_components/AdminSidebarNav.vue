<script setup>
/**
 * Barre de navigation administration : rangée horizontale de liens (boutons glass),
 * séparateur `glass-border-b-sm` en bas. Si la ligne est trop étroite, les derniers
 * liens sont regroupés dans un menu déroulant (icône « hamburger »).
 */
import {
    computed,
    nextTick,
    onMounted,
    onUnmounted,
    ref,
    watch,
} from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Route from '@/Pages/Atoms/action/Route.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Dropdown from '@/Pages/Atoms/action/Dropdown.vue';
import { usePermissions } from '@/Composables/permissions/usePermissions';

const page = usePage();
const { canAccess, isSuperAdmin } = usePermissions();

/** @type {import('vue').Ref<number>} */
const visibleCount = ref(999);

const path = computed(() => {
    const u = page.url.split('?')[0];
    return u.endsWith('/') && u.length > 1 ? u.slice(0, -1) : u;
});

/** @param {{ path: string }} item */
function isNavActive(item) {
    const p = path.value;
    if (item.path === '/admin') {
        return p === '/admin' || p === '/admin/';
    }
    return p === item.path || p.startsWith(`${item.path}/`);
}

const sections = computed(() => {
    const items = [];

    items.push({
        title: 'Vue d’ensemble',
        href: 'admin.dashboard.index',
        path: '/admin',
        icon: 'fa-gauge-high',
        show: true,
    });

    if (isSuperAdmin.value) {
        items.push(
            {
                title: 'Sync données',
                href: 'admin.project-maintenance.index',
                path: '/admin/project-maintenance',
                icon: 'fa-database',
                show: true,
            },
            {
                title: 'Sauvegarde',
                href: 'admin.backup.index',
                path: '/admin/backup',
                icon: 'fa-floppy-disk',
                show: true,
            },
            {
                title: 'Mise à jour stack',
                href: 'admin.project-update.index',
                path: '/admin/project-update',
                icon: 'fa-arrows-rotate',
                show: true,
            }
        );
    }

    if (canAccess('adminPanel')) {
        items.push(
            {
                title: 'Utilisateurs',
                href: 'user.index',
                path: '/user/list',
                icon: 'fa-users',
                show: true,
            },
            {
                title: 'Scrapping',
                href: 'scrapping.index',
                path: '/scrapping',
                icon: 'fa-magnifying-glass',
                show: canAccess('scrapping'),
            },
            {
                title: 'Caractéristiques',
                href: 'admin.characteristics.index',
                path: '/admin/characteristics',
                icon: 'fa-sliders',
                show: true,
            },
            {
                title: 'Mappings scrapping',
                href: 'admin.scrapping-mappings.index',
                path: '/admin/scrapping-mappings',
                icon: 'fa-diagram-project',
                show: true,
            },
            {
                title: 'Mappings effets DofusDB',
                href: 'admin.dofusdb-effect-mappings.index',
                path: '/admin/dofusdb-effect-mappings',
                icon: 'fa-link',
                show: true,
            }
        );
    }

    if (canAccess('effectsAdmin')) {
        items.push(
            {
                title: 'Effets',
                href: 'admin.effects.index',
                path: '/admin/effects',
                icon: 'fa-bolt',
                show: true,
            },
            {
                title: 'Sous-effets',
                href: 'admin.sub-effects.index',
                path: '/admin/sub-effects',
                icon: 'fa-wand-magic-sparkles',
                show: true,
            }
        );
    }

    return items.filter((i) => i.show);
});

const visibleItems = computed(() =>
    sections.value.slice(0, visibleCount.value)
);
const overflowItems = computed(() =>
    sections.value.slice(visibleCount.value)
);

const overflowHasActive = computed(() =>
    overflowItems.value.some((item) => isNavActive(item))
);

const itemsRowRef = ref(null);
const measureRef = ref(null);

/** Correspond à `gap-3` (0.75rem) entre les boutons */
const GAP_FALLBACK_PX = 12;

/** Classes alignées sur la barre visible + la rangée de mesure (largeurs identiques). */
const navBtnClass =
    'gap-2.5 min-h-9 min-w-0 px-2.5 sm:min-h-10 sm:px-3 motion-safe:transition-[transform,box-shadow] duration-200 ease-out motion-safe:hover:-translate-y-0.5 motion-safe:hover:shadow-[0_6px_24px_rgba(0,0,0,0.22)] motion-safe:focus-visible:-translate-y-0.5 motion-safe:active:translate-y-0';

/** Bouton menu (carré) : mêmes animations, sans padding horizontal qui casserait `square`. */
const navMenuBtnClass =
    'motion-safe:transition-[transform,box-shadow] duration-200 ease-out motion-safe:hover:-translate-y-0.5 motion-safe:hover:shadow-[0_6px_24px_rgba(0,0,0,0.22)] motion-safe:focus-visible:-translate-y-0.5 motion-safe:active:translate-y-0';

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
    const list = sections.value;
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
    () => sections.value.map((s) => s.href).join('\0'),
    () => {
        visibleCount.value = sections.value.length;
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
</script>

<template>
    <nav
        class="glass-border-b-sm w-full pb-4 pt-0.5 mb-2"
        aria-label="Administration"
    >
        <!-- Mesure hors écran : mêmes Route+Btn que la barre visible + bouton « plus » -->
        <div
            ref="measureRef"
            class="pointer-events-none fixed left-0 top-0 z-[-1000] flex gap-3 opacity-0"
            aria-hidden="true"
        >
            <div
                v-for="item in sections"
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
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between sm:gap-8"
        >
            <p
                class="shrink-0 text-[0.65rem] font-semibold uppercase tracking-wider text-base-content/55"
            >
                Administration
            </p>
            <div
                ref="itemsRowRef"
                class="flex min-w-0 flex-1 justify-end overflow-hidden"
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
                                :active="isNavActive(item)"
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
                            aria-label="Autres liens d’administration"
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
                                        :key="'overflow-' + item.href"
                                        role="none"
                                    >
                                        <Link
                                            :href="route(item.href)"
                                            role="menuitem"
                                            class="flex items-center gap-2 rounded-lg no-underline! transition-[background-color,box-shadow,transform] duration-200 motion-safe:hover:-translate-y-px motion-safe:hover:shadow-md motion-safe:active:translate-y-0"
                                            :class="
                                                isNavActive(item)
                                                    ? 'bg-primary/15 text-primary shadow-sm ring-1 ring-primary/20'
                                                    : 'hover:bg-base-200/80'
                                            "
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
        </div>
    </nav>
</template>
