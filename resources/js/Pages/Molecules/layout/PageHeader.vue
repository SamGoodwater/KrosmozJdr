<script setup>
/**
 * PageHeader — En-tête universel d’une page (titre, sous-titre, retour, actions, enregistrement).
 *
 * @description
 * À placer en haut des pages hors CMS et hors fiches d’entités. Synchronise le titre de
 * l’onglet et masque le titre du header global (pas de doublon). Collant par défaut.
 *
 * Règles d’emplacement :
 * - un seul « Enregistrer » par page, ici (via `forms` ou le slot `primary`) ;
 * - actions secondaires dans le slot `actions` (lien, outil, suppression…) ;
 * - sous-navigation (onglets, registres) dans le slot `tabs`.
 *
 * @example
 * <PageHeader title="Langues" subtitle="Langues parlées par les créatures" :forms="pageForms">
 *   <template #actions>
 *     <Btn size="sm" variant="ghost" @click="refresh">Actualiser</Btn>
 *   </template>
 * </PageHeader>
 *
 * @props {String} title - Titre affiché (h1) et titre de l’onglet
 * @props {String} subtitle - Sous-titre facultatif
 * @props {String} backHref - URL de retour
 * @props {String} backRoute - Nom de route Ziggy de retour (prioritaire sur backHref)
 * @props {Object|Array|Number|String} backRouteParams - Paramètres de backRoute
 * @props {String} backLabel - Libellé du retour (défaut : « Retour »)
 * @props {Boolean} sticky - Reste visible au défilement (défaut : true)
 * @props {Object} forms - Retour de `usePageForms()` : affiche « Enregistrer » / « Annuler les modifications »
 * @props {String} saveLabel - Libellé du bouton principal (défaut : « Enregistrer »)
 * @props {Boolean} saveDisabled - Bloque l’enregistrement
 * @slot actions - Actions secondaires (à gauche du bloc d’enregistrement)
 * @slot primary - Remplace le bloc d’enregistrement (ex. « Créer », « Lancer »)
 * @slot subtitle - Sous-titre riche
 * @slot meta - Badges / état sous le titre
 * @slot tabs - Sous-navigation sous l’en-tête
 */
import { computed, onBeforeUnmount, useSlots, watch } from 'vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Route from '@/Pages/Atoms/action/Route.vue';
import ConfirmModal from '@/Pages/Molecules/action/ConfirmModal.vue';
import PageSaveActions from '@/Pages/Molecules/action/PageSaveActions.vue';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { ACTION, UNSAVED } from '@/Utils/atomic-design/actionLabels';

const props = defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
    backHref: { type: String, default: '' },
    backRoute: { type: String, default: '' },
    backRouteParams: { type: [Object, Array, Number, String], default: () => ({}) },
    backLabel: { type: String, default: ACTION.back.label },
    sticky: { type: Boolean, default: true },
    forms: { type: Object, default: null },
    saveLabel: { type: String, default: ACTION.save.label },
    saveDisabled: { type: Boolean, default: false },
});

const slots = useSlots();
const { setPageTitle, registerPageHeader } = usePageTitle();

watch(
    () => props.title,
    (title) => setPageTitle(title),
    { immediate: true },
);
const unregisterHeader = registerPageHeader();
onBeforeUnmount(unregisterHeader);

const backUrl = computed(() => {
    if (props.backRoute) {
        try {
            return route(props.backRoute, props.backRouteParams);
        } catch {
            return props.backHref;
        }
    }
    return props.backHref;
});

const formsDirty = computed(() => Boolean(props.forms?.isDirty?.value));
const formsProcessing = computed(() => Boolean(props.forms?.processing?.value));
const formsStatus = computed(() => props.forms?.status?.value ?? 'idle');
const leaveOpen = computed(() => Boolean(props.forms?.pendingLeave?.value));

// `top-0` : le padding haut du `<main>` défilant (place du header global) est déjà appliqué.
const rootClasses = computed(() => [
    'page-header mb-4 rounded-xl border border-base-content/10 bg-base-100/85 px-3 py-3 backdrop-blur sm:px-4',
    props.sticky ? 'sticky top-0 z-30 shadow-sm' : 'relative',
]);

const hasActions = computed(() => Boolean(slots.actions || slots.primary || props.forms));
</script>

<template>
    <header :class="rootClasses" data-testid="page-header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="flex min-w-[min(100%,18rem)] flex-1 basis-80 items-start gap-2">
                <Route v-if="backUrl" :href="backUrl" :aria-label="backLabel" class="shrink-0 no-underline">
                    <Btn variant="ghost" size="sm" type="button" class="gap-1.5" data-testid="page-header-back">
                        <i :class="ACTION.back.icon" aria-hidden="true"></i>
                        <span class="hidden sm:inline">{{ backLabel }}</span>
                    </Btn>
                </Route>
                <div class="min-w-0">
                    <h1 class="text-xl font-semibold leading-tight text-base-content sm:text-2xl">
                        {{ title }}
                    </h1>
                    <p v-if="subtitle || slots.subtitle" class="mt-0.5 max-w-3xl text-sm text-base-content/70 max-md:line-clamp-2">
                        <slot name="subtitle">{{ subtitle }}</slot>
                    </p>
                    <div v-if="slots.meta" class="mt-1.5 flex flex-wrap items-center gap-2">
                        <slot name="meta" />
                    </div>
                </div>
            </div>

            <div v-if="hasActions" class="ml-auto flex flex-wrap items-center justify-end gap-2">
                <slot name="actions" />
                <slot name="primary">
                    <PageSaveActions
                        v-if="forms"
                        :dirty="formsDirty"
                        :processing="formsProcessing"
                        :status="formsStatus"
                        :disabled="saveDisabled"
                        :save-label="saveLabel"
                        @save="forms.saveAll()"
                        @discard="forms.discardAll()"
                    />
                </slot>
            </div>
        </div>

        <div v-if="slots.tabs" class="mt-3 min-w-0">
            <slot name="tabs" />
        </div>

        <ConfirmModal
            v-if="forms"
            :open="leaveOpen"
            :title="UNSAVED.leaveTitle"
            :message="UNSAVED.leaveMessage"
            :confirm-label="UNSAVED.leaveConfirm"
            :cancel-label="UNSAVED.leaveCancel"
            confirm-color="warning"
            confirm-icon="fa-solid fa-right-from-bracket"
            @confirm="forms.confirmLeave()"
            @cancel="forms.cancelLeave()"
            @close="forms.cancelLeave()"
        />
    </header>
</template>
