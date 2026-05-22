<script setup>
/**
 * PageChildIndexCard — Carte lien vers une sous-page (index parent).
 *
 * @props {String} title - Titre affiché
 * @props {String} href - URL de la sous-page
 * @props {String} icon - Icône optionnelle (chemin ou FA)
 * @props {String} iconAlt - Texte alternatif icône
 */
import Route from '@/Pages/Atoms/action/Route.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';

defineProps({
    title: { type: String, required: true },
    href: { type: String, required: true },
    icon: { type: String, default: '' },
    iconAlt: { type: String, default: '' },
});
</script>

<template>
    <Route
        :href="href"
        class="page-child-index-card block h-full no-underline"
        :aria-label="title"
    >
        <span class="page-child-index-card__inner box-glass-sm flex h-full min-h-[4.5rem] flex-col items-start justify-center gap-2 rounded-box px-3 py-2.5 transition-transform duration-200 hover:translate-x-0.5">
            <span v-if="icon" class="page-child-index-card__icon flex items-center justify-center">
                <Icon
                    :source="icon"
                    alt=""
                    size="sm"
                />
            </span>
            <span class="page-child-index-card__title text-sm font-semibold leading-snug text-base-content">
                {{ title }}
            </span>
        </span>
    </Route>
</template>

<style scoped lang="scss">
.page-child-index-card__inner:hover {
    background: color-mix(in srgb, var(--color-base-100) 36%, transparent);
}

.page-child-index-card__icon {
    opacity: 0.85;
}
</style>
