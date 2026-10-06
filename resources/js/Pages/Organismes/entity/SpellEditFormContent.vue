<script setup>
/**
 * Corps de la fiche d’édition d’un sort (toolbar, formulaire, effets).
 *
 * @description
 * Partagé entre la page {@link Pages/entity/spell/Edit} et {@link SpellEditModal}.
 * — Barre d’options en haut (fiche, DofusDB, suppression, retour liste).
 * — Résumé combat + porteurs, puis identité / combat, effets, options avancées.
 * — Les classes liées au sort se gèrent depuis la fiche classe (pas depuis ici).
 */
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import { Spell } from "@/Models/Entity/Spell";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import EntityEditForm from "@/Pages/Organismes/entity/EntityEditForm.vue";
import SpellEffectsUnifiedSection from "@/Pages/Organismes/entity/SpellEffectsUnifiedSection.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import EntityListBackButton from "@/Pages/Atoms/action/EntityListBackButton.vue";
import EntityEditOptionsAnchor from "@/Pages/Molecules/entity/shared/EntityEditOptionsAnchor.vue";
import Collapse from "@/Pages/Atoms/data-display/Collapse.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import {
    buildSpellFormFieldsConfig,
    SPELL_FORM_FIELD_SECTIONS_EDIT,
    mergeSpellTypesFieldIntoSpellFormConfig,
} from "@/Entities/spell/spell-form-config";
import {
    buildDofusDbEntityUrl,
    getEntityDofusDbId,
} from "@/Utils/dofusdb/buildDofusDbEntityUrl";
import { useDofusDbReferenceStore } from "@/Composables/store/useDofusDbReferenceStore";
import { getElementLabel, getElementIcon } from "@/Utils/Entity/Elements";
import { formatPoRangeDisplay } from "@/Composables/entity/useCharacteristicDisplay";
import { getEntityStateDisplayLabel } from "@/Utils/Entity/SharedConstants";

/** Icône action DofusDB (`public/images/logos/dofus.png`). */
const DOFUSDB_ACTION_ICON = "/images/logos/dofus.png";

const props = defineProps({
    spell: { type: Object, required: true },
    availableSpellTypes: { type: Array, default: () => [] },
    availableEffects: { type: Array, default: () => [] },
    effectEntityType: { type: String, default: "spell" },
    effectFormOptions: { type: Object, default: () => ({}) },
    spellEffectGroups: { type: Array, default: () => [] },
    /**
     * Monstres, PNJ et classes qui référencent ce sort.
     * @type {{ monsters?: Array, npcs?: Array, breeds?: Array }}
     */
    spellHolders: { type: Object, default: () => ({ monsters: [], npcs: [], breeds: [] }) },
    /** Quand true : annulation sans redirection vers la fiche lecture. */
    embeddedInModal: { type: Boolean, default: false },
    /**
     * Redirection après PATCH réussi : `edit` = rester sur l’éditeur (page fiche) ; `index` = liste (modal).
     * @type {"stay"|"index"|"show"|"edit"|null}
     */
    redirectAfterUpdate: { type: String, default: "edit" },
});

const emit = defineEmits(["cancel", "saved", "effects-changed"]);

const holderGroups = computed(() => {
    const holders = props.spellHolders || {};
    return [
        { key: "monsters", label: "Monstres", items: holders.monsters || [] },
        { key: "npcs", label: "PNJ", items: holders.npcs || [] },
        { key: "breeds", label: "Classes", items: holders.breeds || [] },
    ].filter((group) => group.items.length > 0);
});

const holderCount = computed(() =>
    holderGroups.value.reduce((sum, group) => sum + group.items.length, 0),
);

const { canDeleteAny, isAdmin } = usePermissions();
const canDeleteSpell = computed(() => canDeleteAny("spells") || isAdmin.value);

const fieldsConfig = computed(() =>
    mergeSpellTypesFieldIntoSpellFormConfig(
        buildSpellFormFieldsConfig({ includeReadonlyMeta: true }),
        props.availableSpellTypes || [],
    ),
);

const fieldSections = SPELL_FORM_FIELD_SECTIONS_EDIT;

const spellModel = computed(() =>
    props.spell instanceof Spell ? props.spell : new Spell(props.spell),
);

const dofusDbStore = useDofusDbReferenceStore();

const dofusDbUrl = computed(() => {
    const id = getEntityDofusDbId(spellModel.value);
    if (!id) return null;
    return buildDofusDbEntityUrl("spells", id);
});

/** Ouvre le panneau modal de référence DofusDB (iframe + « Ouvrir dans une fenêtre »). */
function openDofusDbPanel() {
    if (!dofusDbUrl.value) return;
    dofusDbStore.openPanel("spells", spellModel.value);
}

const summaryElementLabel = computed(() => {
    const el = spellModel.value.element;
    if (el == null || el === "" || Number(el) === 0) return "Aucun";
    return getElementLabel(Number(el)) || "—";
});

const summaryElementIcon = computed(() => {
    const el = spellModel.value.element;
    if (el == null || el === "" || Number(el) === 0) return null;
    return getElementIcon(Number(el));
});

const summaryPa = computed(() => {
    const pa = spellModel.value.pa;
    return pa == null || pa === "" ? "—" : String(pa);
});

const summaryPo = computed(() => {
    const display = formatPoRangeDisplay(spellModel.value.poMin, spellModel.value.poMax);
    return display || "—";
});

const summaryState = computed(() => getEntityStateDisplayLabel(spellModel.value.state) || "—");

/**
 * Le layout décale déjà `<main>` sous la sidebar ; pas de second décalage sur le pied.
 * @see CapabilityEditFormContent — même raison (`sticky` dans la colonne scrollable).
 */
const fixedFooterInsetClass = "left-0 right-0";

const spellEffectsSectionRef = ref(null);

/** PATCH groupe d’effets sélectionné puis le formulaire entité (via {@link EntityEditForm}). */
async function beforeSpellSubmitAsync() {
    const fn = spellEffectsSectionRef.value?.flushEffectGroupSave;
    if (typeof fn !== "function") {
        return true;
    }
    return fn();
}

function goToShow() {
    const id = spellModel.value?.id;
    if (!id) return;
    router.visit(route("entities.spells.show", { spell: id }));
}

function confirmDelete() {
    const id = spellModel.value?.id;
    if (!id) return;
    const ok = window.confirm(
        "Supprimer ce sort ? Il sera placé en corbeille (récupération possible côté admin).",
    );
    if (!ok) return;
    router.delete(route("entities.spells.delete", { spell: id }), {
        onSuccess: () => {
            if (props.embeddedInModal) {
                emit("cancel");
            }
        },
    });
}
</script>

<template>
    <div class="spell-edit-form-content space-y-6">
        <div
            class="sticky top-0 z-20 px-3 py-1 bg-glass-3xl backdrop-blur-md border-glass-b-md sm:px-4"
            style="--bg-color: var(--color-base-100)"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-md font-bold text-base-content sm:text-lg">
                        {{ spellModel.name || "Sort sans nom" }}
                    </h1>
                    <p class="text-xs text-base-content/60">
                        Édition · ID {{ spellModel.id }}
                    </p>
                    <ul
                        class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-base-content/80"
                        data-cy="spell-edit-summary"
                    >
                        <li class="inline-flex items-center gap-1.5">
                            <Icon
                                v-if="summaryElementIcon"
                                :source="summaryElementIcon"
                                size="xs"
                                class="opacity-90"
                            />
                            <span>{{ summaryElementLabel }}</span>
                        </li>
                        <li>
                            <span class="text-base-content/50">PA</span>
                            {{ summaryPa }}
                        </li>
                        <li>
                            <span class="text-base-content/50">PO</span>
                            {{ summaryPo }}
                        </li>
                        <li>
                            <span class="text-base-content/50">État</span>
                            {{ summaryState }}
                        </li>
                    </ul>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <EntityListBackButton
                        v-if="!embeddedInModal"
                        route-name="entities.spells.index"
                    />
                    <Btn
                        color="neutral"
                        variant="outline"
                        size="xs"
                        type="button"
                        class="gap-1.5"
                        @click="goToShow"
                    >
                        <i class="fa-solid fa-book-open" aria-hidden="true"></i>
                        Fiche
                    </Btn>
                    <Btn
                        v-if="dofusDbUrl"
                        color="neutral"
                        variant="outline"
                        size="xs"
                        type="button"
                        class="gap-1.5"
                        data-cy="spell-edit-dofusdb"
                        title="Ouvrir la fiche DofusDB dans un panneau"
                        @click="openDofusDbPanel"
                    >
                        <Icon :source="DOFUSDB_ACTION_ICON" size="xs" class="opacity-90" />
                        DofusDB
                    </Btn>
                    <Btn
                        v-if="canDeleteSpell"
                        color="error"
                        variant="outline"
                        size="xs"
                        type="button"
                        class="gap-1.5"
                        @click="confirmDelete"
                    >
                        <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                        Supprimer
                    </Btn>
                    <EntityEditOptionsAnchor />
                </div>
            </div>
        </div>

        <section class="rounded-box border border-base-300 bg-base-100 px-4 py-3" data-cy="spell-holders">
            <h2 class="text-sm font-semibold text-base-content">Qui possède ce sort</h2>
            <p v-if="holderCount === 0" class="mt-1 text-sm text-base-content/70">
                Aucun monstre, PNJ ou classe ne possède ce sort.
            </p>
            <div v-else class="mt-3 space-y-3">
                <div v-for="group in holderGroups" :key="group.key">
                    <p class="text-xs font-medium uppercase tracking-wide text-base-content/60">
                        {{ group.label }}
                    </p>
                    <ul class="mt-1 flex flex-wrap gap-2">
                        <li
                            v-for="item in group.items"
                            :key="group.key + '-' + item.id"
                            class="inline-flex items-center gap-2 rounded-box border border-base-300 bg-base-200/40 px-2 py-1"
                        >
                            <Route :href="item.show_url" color="neutral" hover class="text-sm no-underline">
                                {{ item.name }}
                            </Route>
                            <Route :href="item.edit_url" color="neutral" class="text-xs no-underline">
                                Modifier
                            </Route>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <EntityEditForm
            :entity="spellModel"
            entity-type="spell"
            :fields-config="fieldsConfig"
            :is-updating="true"
            :hidden-field-keys="['dofus_version']"
            :field-sections="fieldSections"
            :show-state-toolbar="true"
            :show-access-levels-in-footer="false"
            characteristics-group="spell"
            layout-profile="spell"
            :fixed-footer-actions="true"
            :fixed-footer-inset-class="fixedFooterInsetClass"
            :embedded-in-modal="embeddedInModal"
            :redirect-after-update="redirectAfterUpdate || undefined"
            :before-submit-async="beforeSpellSubmitAsync"
            @cancel="emit('cancel')"
            @submit="emit('saved')"
        >
            <template #after-primary>
                <Collapse
                    arrow
                    :default-open="true"
                    bg-off="bg-base-100"
                    bg-on="bg-base-100"
                    class="border border-base-300"
                    data-cy="spell-effects-section"
                >
                    <template #title>Effets du sort</template>
                    <template #content>
                        <SpellEffectsUnifiedSection
                            ref="spellEffectsSectionRef"
                            hide-effect-group-submit-button
                            :available-effects="availableEffects"
                            :effect-form-options="effectFormOptions"
                            :spell-effect-groups="spellEffectGroups"
                            :entity-type="effectEntityType"
                            :entity-id="spellModel.id"
                            :suggested-effect-name="spellModel.name || ''"
                            :embedded-in-modal="embeddedInModal"
                            @effects-changed="emit('effects-changed')"
                        />
                    </template>
                </Collapse>
            </template>
        </EntityEditForm>
    </div>
</template>
