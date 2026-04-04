<script setup>
/**
 * Une ligne du menu secondaire SidebarNav (lien ou bouton, accent glass, icônes, sous-texte).
 *
 * @example
 * <SidebarNavItem
 *   :item="c"
 *   accent-glass-class="glass-border-l-md"
 *   :resolve-href="(i) => route('admin.x.show', i.id)"
 *   :is-item-active="(i) => selected?.id === i.id"
 *   :get-item-label="(i) => i.name"
 * />
 */
import { Link } from '@inertiajs/vue3';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { computed } from 'vue';
import {
    splitItemVisualClasses,
    accentStripStyleFromItem,
} from '@/Pages/Organismes/layout/sidebarNavItemVisuals';

const props = defineProps({
    item: { type: Object, required: true },
    accentGlassClass: { type: String, required: true },
    resolveHref: { type: Function, required: true },
    isItemActive: { type: Function, required: true },
    getItemClick: { type: Function, default: null },
    getItemLabel: { type: Function, required: true },
    getItemLabelSecondary: { type: Function, required: true },
    getItemIcon: { type: Function, required: true },
    getItemIconUrl: { type: Function, required: true },
    getItemColor: { type: Function, required: true },
    getItemCssClasses: { type: Function, required: true },
    iconBasePath: { type: String, default: '/storage/images/icons/caracteristics' },
    /** Afficher le title natif sur le sous-texte (liste dense groupée) */
    secondaryTitle: { type: Boolean, default: false },
});

function resolveIconUrl() {
    const url = props.getItemIconUrl(props.item);
    if (url) return url;
    const icon = props.getItemIcon(props.item);
    if (!icon || typeof icon !== 'string') return null;
    if (icon.startsWith('fa-') || icon.startsWith('http')) return null;
    const base = props.iconBasePath || '/storage/images/icons/caracteristics';
    return `${base}/${icon.includes('/') ? icon.split('/').pop() : icon}`;
}

function isFaIcon() {
    const icon = props.getItemIcon(props.item);
    return icon && typeof icon === 'string' && icon.startsWith('fa-');
}

const rowVisual = computed(() =>
    splitItemVisualClasses(props.item, props.getItemCssClasses)
);
const rowStripStyle = computed(() =>
    accentStripStyleFromItem(props.item, props.getItemColor)
);
</script>

<template>
    <component
        :is="getItemClick && !resolveHref(item) ? 'button' : Link"
        :href="getItemClick && !resolveHref(item) ? undefined : resolveHref(item)"
        :type="getItemClick && !resolveHref(item) ? 'button' : undefined"
        :class="[
            'sidebar-nav-item flex w-full items-stretch overflow-hidden rounded-box text-left text-sm transition-colors',
            rowVisual.body,
            rowVisual.accent,
            isItemActive(item) && 'sidebar-nav-item-active',
        ]"
        :style="rowStripStyle"
        @click="getItemClick && !resolveHref(item) ? getItemClick(item) : undefined"
    >
        <span
            class="sidebar-nav-item-accent w-1.5 shrink-0 self-stretch rounded-l-box"
            :class="accentGlassClass"
            aria-hidden="true"
        />
        <span class="flex min-w-0 flex-1 items-center gap-2 py-2 pr-2 pl-1.5">
            <img
                v-if="resolveIconUrl()"
                :src="resolveIconUrl()"
                :alt="getItemLabel(item)"
                class="h-5 w-5 shrink-0 object-contain"
                @error="($e) => ($e.target.style.display = 'none')"
            />
            <Icon
                v-else-if="isFaIcon()"
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
                <span class="truncate leading-tight">{{ getItemLabel(item) }}</span>
                <span
                    v-if="getItemLabelSecondary(item)"
                    class="truncate text-xs leading-snug text-base-content/65 not-italic"
                    :title="secondaryTitle ? getItemLabelSecondary(item) : undefined"
                >
                    {{ getItemLabelSecondary(item) }}
                </span>
            </span>
            <slot name="item-suffix" :item="item" />
        </span>
    </component>
</template>

<style scoped lang="scss">
.sidebar-nav-item {
    &:hover {
        background: color-mix(in srgb, var(--color-base-300) 60%, transparent);
    }
}

.sidebar-nav-item-active {
    background: color-mix(
        in srgb,
        var(--color, var(--color-primary)) 18%,
        var(--color-base-100)
    );
    color: var(--color-base-content);
}
</style>
