<script setup>
/**
 * SidebarNav Organism
 *
 * @description
 * Sous-menu latéral réutilisable pour pages type liste + détail.
 * - Scroll sur la zone nav si contenu long
 * - Recherche optionnelle (filtre items, conserve groupes visibles)
 * - Modes : plat, ou groupé (collapse)
 * - Items : couleur optionnelle (getItemCssClasses avec color-{key}-500, shadow-from-color ou shadow-box-shadow-glass-* ; getItemColor pour hex ; --color)
 * - Icônes optionnelles : FontAwesome (getItemIcon), image (getItemIconUrl)
 * - Mode lien ou bouton : getItemClick + href vide → <button>
 *
 * @example
 * <SidebarNav
 *   title="Caractéristiques"
 *   :items-by-group="{ creature: [...], object: [...] }"
 *   :group-labels="{ creature: 'Créature', object: 'Objet' }"
 *   searchable
 *   groups-mode="collapse"
 *   :get-item-href="c => route('admin.characteristics.show', c.id)"
 *   :is-active="c => selected?.id === c.id"
 *   :get-item-css-classes="c => c.color && !c.color.startsWith('#') ? `color-${c.color}-500 box-shadow-glass-xs` : ''"
 * />
 */
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';

const props = defineProps({
    /** Titre du panneau */
    title: { type: String, default: '' },
    /** Description courte sous le titre */
    description: { type: String, default: '' },
    /** Items groupés : { groupKey: [items] } */
    itemsByGroup: { type: Object, default: () => ({}) },
    /** Labels des groupes : { groupKey: label } */
    groupLabels: { type: Object, default: () => ({}) },
    /** Items plats (si pas de groups). Priorité sur itemsByGroup si les deux fournis et groupsMode=false */
    items: { type: Array, default: () => [] },
    /** Mode groupes: 'collapse' | 'flat' | false. false = liste plate */
    groupsMode: { type: [String, Boolean], default: false },
    /** Afficher le champ de recherche */
    searchable: { type: Boolean, default: false },
    /** Placeholder du champ recherche */
    searchPlaceholder: { type: String, default: 'Rechercher…' },
    /** Clés pour filtrer (ex: ['name', 'key']). Si vide, utilise label */
    searchKeys: { type: Array, default: () => ['name', 'label', 'key', 'id'] },
    /** Fonction (item) => href ou string (nom de champ) */
    getItemHref: { type: [Function, String], default: null },
    /** Fonction (item) => boolean */
    isItemActive: { type: Function, default: () => false },
    /** Fonction (item) => string - classes CSS (ex: color-breed-500 box-shadow-glass-xs) */
    getItemCssClasses: { type: Function, default: () => '' },
    /** Fonction (item) => string - couleur hex pour bordure inline */
    getItemColor: { type: Function, default: () => null },
    /** Fonction (item) => string - icône FontAwesome (fa-xxx) ou null */
    getItemIcon: { type: Function, default: () => null },
    /** Fonction (item) => string - URL image pour icône (si pas FontAwesome) */
    getItemIconUrl: { type: Function, default: () => null },
    /** Fonction (item) => string - label principal */
    getItemLabel: { type: Function, default: (item) => item.name || item.label || item.id || '' },
    /** Fonction (item) => string - label secondaire (sous le principal) */
    getItemLabelSecondary: { type: Function, default: () => null },
    /** Fonction (item) => string - clé unique */
    getItemKey: { type: Function, default: (item) => item.id ?? item.key ?? JSON.stringify(item) },
    /** Fonction (item) => void - si fournie et href vide, rend un bouton au lieu d'un lien */
    getItemClick: { type: Function, default: null },
    /** Chemin de base pour les icônes image */
    iconBasePath: { type: String, default: '/storage/images/icons/caracteristics' },
});

const searchQuery = ref('');

const hasGroups = computed(() => {
    if (props.groupsMode === false && props.items?.length) return false;
    const keys = Object.keys(props.itemsByGroup || {});
    return keys.some((k) => (props.itemsByGroup[k] || []).length > 0);
});

const groups = computed(() => {
    if (!hasGroups.value) return [];
    return Object.keys(props.itemsByGroup || {});
});

function itemMatchesSearch(item, query) {
    if (!query || !query.trim()) return true;
    const q = query.trim().toLowerCase();
    const keys = props.searchKeys.length ? props.searchKeys : ['name', 'label', 'key', 'id'];
    const fromKeys = keys.some((k) => {
        const v = item?.[k];
        return v != null && String(v).toLowerCase().includes(q);
    });
    if (fromKeys) return true;
    const label = props.getItemLabel(item);
    return label != null && String(label).toLowerCase().includes(q);
}

const filteredItemsByGroup = computed(() => {
    const q = searchQuery.value?.trim() || '';
    const byGroup = props.itemsByGroup || {};
    if (!q) return byGroup;
    const result = {};
    for (const groupKey of Object.keys(byGroup)) {
        const items = (byGroup[groupKey] || []).filter((item) => itemMatchesSearch(item, q));
        if (items.length > 0) result[groupKey] = items;
    }
    return result;
});

const filteredFlatItems = computed(() => {
    const q = searchQuery.value?.trim() || '';
    const list = props.items || [];
    if (!q) return list;
    return list.filter((item) => itemMatchesSearch(item, q));
});

function resolveHref(item) {
    if (typeof props.getItemHref === 'function') return props.getItemHref(item);
    if (typeof props.getItemHref === 'string') return item[props.getItemHref];
    return item.href ?? null;
}

function resolveCssClasses(item) {
    const base = 'sidebar-nav-item';
    const extra = props.getItemCssClasses(item);
    return extra ? `${base} ${extra}`.trim() : base;
}

function resolveIconUrl(item) {
    const url = props.getItemIconUrl(item);
    if (url) return url;
    const icon = props.getItemIcon(item);
    if (!icon || typeof icon !== 'string') return null;
    if (icon.startsWith('fa-') || icon.startsWith('http')) return null;
    const base = props.iconBasePath || '/storage/images/icons/caracteristics';
    return `${base}/${icon.includes('/') ? icon.split('/').pop() : icon}`;
}

function isFaIcon(item) {
    const icon = props.getItemIcon(item);
    return icon && typeof icon === 'string' && icon.startsWith('fa-');
}
</script>

<template>
    <aside class="sidebar-nav flex w-64 shrink-0 flex-col border-r border-base-300 bg-base-200/50 overflow-hidden">
        <div class="sidebar-nav-header shrink-0 p-3">
            <h2 v-if="title" class="font-semibold text-base-content">{{ title }}</h2>
            <p v-if="description" class="mt-1 text-xs text-base-content/70">{{ description }}</p>
            <div v-if="searchable" class="mt-2">
                <InputField
                    v-model="searchQuery"
                    type="search"
                    :placeholder="searchPlaceholder"
                    size="sm"
                    class="input-sm"
                />
            </div>
        </div>

        <nav class="sidebar-nav-list flex-1 min-h-0 overflow-y-auto p-2">
            <slot name="nav-before" />

            <!-- Liste plate -->
            <template v-if="!hasGroups">
                <div v-if="filteredFlatItems.length === 0" class="px-3 py-4 text-sm text-base-content/70">
                    <slot name="empty">Aucun élément.</slot>
                </div>
                <template v-else>
                    <component
                        v-for="item in filteredFlatItems"
                        :key="getItemKey(item)"
                        :is="getItemClick && !resolveHref(item) ? 'button' : Link"
                        :href="getItemClick && !resolveHref(item) ? undefined : resolveHref(item)"
                        :type="getItemClick && !resolveHref(item) ? 'button' : undefined"
                        :class="[
                            'sidebar-nav-item flex items-center gap-2 rounded-lg border-l-4 border-transparent px-3 py-2 text-left text-sm transition-colors w-full',
                            resolveCssClasses(item),
                            isItemActive(item) && 'sidebar-nav-item-active'
                        ]"
                        :style="getItemColor(item) ? { borderLeftColor: getItemColor(item) } : {}"
                        @click="getItemClick && !resolveHref(item) ? getItemClick(item) : undefined"
                    >
                        <img
                            v-if="resolveIconUrl(item)"
                            :src="resolveIconUrl(item)"
                            :alt="getItemLabel(item)"
                            class="h-5 w-5 shrink-0 object-contain"
                            @error="($e) => ($e.target.style.display = 'none')"
                        />
                        <Icon
                            v-else-if="isFaIcon(item)"
                            :source="getItemIcon(item)"
                            :alt="getItemLabel(item)"
                            size="sm"
                            class="shrink-0"
                        />
                        <span
                            v-else-if="getItemColor(item)"
                            class="h-2.5 w-2.5 shrink-0 rounded-full"
                            :style="{ backgroundColor: getItemColor(item) }"
                        />
                        <span class="min-w-0 flex flex-col flex-1 truncate">
                            <span class="truncate">{{ getItemLabel(item) }}</span>
                            <span
                                v-if="getItemLabelSecondary(item)"
                                class="truncate text-xs italic opacity-70"
                            >
                                {{ getItemLabelSecondary(item) }}
                            </span>
                        </span>
                        <slot name="item-suffix" :item="item" />
                    </component>
                </template>
            </template>

            <!-- Liste groupée -->
            <template v-else>
                <div
                    v-if="Object.keys(filteredItemsByGroup).every((g) => !(filteredItemsByGroup[g] || []).length)"
                    class="px-3 py-4 text-sm text-base-content/70"
                >
                    <slot name="empty">Aucun résultat.</slot>
                </div>
                <template v-else>
                    <div
                        v-for="groupKey in groups"
                        :key="groupKey"
                        v-show="(filteredItemsByGroup[groupKey] || []).length > 0"
                        :class="[
                            'sidebar-nav-group',
                            groupsMode === 'collapse' && 'collapse collapse-arrow rounded-lg border border-base-300 bg-base-100'
                        ]"
                    >
                        <template v-if="groupsMode === 'collapse'">
                            <input
                                type="checkbox"
                                :checked="(filteredItemsByGroup[groupKey] || []).some((i) => isItemActive(i))"
                                class="peer"
                            />
                            <div class="collapse-title min-h-0 py-2 font-medium peer-checked:min-h-0">
                                {{ groupLabels[groupKey] || groupKey }}
                            </div>
                            <div class="collapse-content">
                                <div class="flex flex-col gap-0.5 pb-2">
                                    <component
                                        v-for="item in (filteredItemsByGroup[groupKey] || [])"
                                        :key="getItemKey(item)"
                                        :is="getItemClick && !resolveHref(item) ? 'button' : Link"
                                        :href="getItemClick && !resolveHref(item) ? undefined : resolveHref(item)"
                                        :type="getItemClick && !resolveHref(item) ? 'button' : undefined"
                                        :class="[
                                            'flex items-center gap-2 rounded-lg border-l-4 border-transparent px-3 py-2 text-left text-sm transition-colors w-full',
                                            resolveCssClasses(item),
                                            isItemActive(item) && 'sidebar-nav-item-active'
                                        ]"
                                        :style="getItemColor(item) ? { borderLeftColor: getItemColor(item) } : {}"
                                        @click="getItemClick && !resolveHref(item) ? getItemClick(item) : undefined"
                                    >
                                        <img
                                            v-if="resolveIconUrl(item)"
                                            :src="resolveIconUrl(item)"
                                            :alt="getItemLabel(item)"
                                            class="h-5 w-5 shrink-0 object-contain"
                                            @error="($e) => ($e.target.style.display = 'none')"
                                        />
                                        <Icon
                                            v-else-if="isFaIcon(item)"
                                            :source="getItemIcon(item)"
                                            :alt="getItemLabel(item)"
                                            size="sm"
                                            class="shrink-0"
                                        />
                                        <span
                                            v-else-if="getItemColor(item)"
                                            class="h-2.5 w-2.5 shrink-0 rounded-full"
                                            :style="{ backgroundColor: getItemColor(item) }"
                                        />
                                        <span class="min-w-0 flex flex-col flex-1 truncate">
                                            <span class="truncate">{{ getItemLabel(item) }}</span>
                                            <span
                                                v-if="getItemLabelSecondary(item)"
                                                class="truncate text-xs italic opacity-70"
                                                :title="getItemLabelSecondary(item)"
                                            >
                                                {{ getItemLabelSecondary(item) }}
                                            </span>
                                        </span>
                                        <slot name="item-suffix" :item="item" />
                                    </component>
                                    <slot :name="`group-${groupKey}`" :group="groupKey" :items="filteredItemsByGroup[groupKey]" />
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="sidebar-nav-group-title py-1.5 font-medium text-base-content/80">
                                {{ groupLabels[groupKey] || groupKey }}
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <component
                                    v-for="item in (filteredItemsByGroup[groupKey] || [])"
                                    :key="getItemKey(item)"
                                    :is="getItemClick && !resolveHref(item) ? 'button' : Link"
                                    :href="getItemClick && !resolveHref(item) ? undefined : resolveHref(item)"
                                    :type="getItemClick && !resolveHref(item) ? 'button' : undefined"
                                    :class="[
                                        'flex items-center gap-2 rounded-lg border-l-4 border-transparent px-3 py-2 text-left text-sm transition-colors w-full',
                                        resolveCssClasses(item),
                                        isItemActive(item) && 'sidebar-nav-item-active'
                                    ]"
                                    :style="getItemColor(item) ? { borderLeftColor: getItemColor(item) } : {}"
                                    @click="getItemClick && !resolveHref(item) ? getItemClick(item) : undefined"
                                >
                                    <img
                                        v-if="resolveIconUrl(item)"
                                        :src="resolveIconUrl(item)"
                                        :alt="getItemLabel(item)"
                                        class="h-5 w-5 shrink-0 object-contain"
                                        @error="($e) => ($e.target.style.display = 'none')"
                                    />
                                    <Icon
                                        v-else-if="isFaIcon(item)"
                                        :source="getItemIcon(item)"
                                        :alt="getItemLabel(item)"
                                        size="sm"
                                        class="shrink-0"
                                    />
                                    <span
                                        v-else-if="getItemColor(item)"
                                        class="h-2.5 w-2.5 shrink-0 rounded-full"
                                        :style="{ backgroundColor: getItemColor(item) }"
                                    />
                                    <span class="min-w-0 flex flex-col flex-1 truncate">
                                        <span class="truncate">{{ getItemLabel(item) }}</span>
                                        <span
                                            v-if="getItemLabelSecondary(item)"
                                            class="truncate text-xs italic opacity-70"
                                        >
                                            {{ getItemLabelSecondary(item) }}
                                        </span>
                                    </span>
                                    <slot name="item-suffix" :item="item" />
                                </component>
                                <slot :name="`group-${groupKey}`" :group="groupKey" :items="filteredItemsByGroup[groupKey]" />
                            </div>
                        </template>
                    </div>
                </template>
            </template>

            <slot name="nav-after" />
        </nav>
    </aside>
</template>

<style scoped lang="scss">
.sidebar-nav {
    display: flex;
    flex-direction: column;
}

.sidebar-nav-list {
    scrollbar-width: thin;
}

.sidebar-nav-item {
    &:hover {
        background: color-mix(in srgb, var(--color-base-300) 60%, transparent);
    }
}

.sidebar-nav-item-active {
    background: color-mix(in srgb, var(--color, var(--color-primary)) 18%, var(--color-base-100));
    color: var(--color-base-content);
}

.sidebar-nav-group-title {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Collapse DaisyUI : éviter hauteur minimale excessive */
:deep(.collapse-title) {
    min-height: 0;
}
</style>
