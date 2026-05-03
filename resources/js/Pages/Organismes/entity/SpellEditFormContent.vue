<script setup>
/**
 * Corps de la fiche d’édition d’un sort (toolbar, formulaire, effets).
 *
 * @description
 * Partagé entre la page {@link Pages/entity/spell/Edit} et {@link SpellEditModal}.
 * — Barre d’options en haut (fiche, suppression, retour liste).
 * — Formulaire en grille responsive + pied d’actions fixe via {@link EntityEditForm} (lecture / écriture dans la carte Métadonnées ; effets enregistrés sur le même « Mettre à jour »).
 * — Les classes liées au sort se gèrent depuis la fiche classe (pas depuis ici).
 */
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import { Spell } from "@/Models/Entity/Spell";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import EntityEditForm from "@/Pages/Organismes/entity/EntityEditForm.vue";
import SpellEffectsUnifiedSection from "@/Pages/Organismes/entity/SpellEffectsUnifiedSection.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import {
    buildSpellFormFieldsConfig,
    SPELL_FORM_FIELD_SECTIONS_EDIT,
    mergeSpellTypesFieldIntoSpellFormConfig,
} from "@/Entities/spell/spell-form-config";

const props = defineProps({
    spell: { type: Object, required: true },
    availableSpellTypes: { type: Array, default: () => [] },
    availableEffects: { type: Array, default: () => [] },
    effectEntityType: { type: String, default: "spell" },
    effectFormOptions: { type: Object, default: () => ({}) },
    spellEffectGroups: { type: Array, default: () => [] },
    /** Quand true : annulation sans redirection vers la fiche lecture. */
    embeddedInModal: { type: Boolean, default: false },
    /**
     * Redirection après PATCH réussi : `edit` = rester sur l’éditeur (page fiche) ; `index` = liste (modal).
     * @type {"stay"|"index"|"show"|"edit"|null}
     */
    redirectAfterUpdate: { type: String, default: "edit" },
});

const emit = defineEmits(["cancel", "saved"]);

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
            class="sticky top-0 z-20 px-3 py-1 bg-base-100/80 backdrop-blur-md border-glass-b-md sm:px-4"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-md font-bold text-base-content sm:text-lg">
                        {{ spellModel.name || "Sort sans nom" }}
                    </h1>
                    <p class="text-xs text-base-content/60">
                        Édition · ID {{ spellModel.id }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <Route v-if="!embeddedInModal" route="entities.spells.index">
                        <Btn color="neutral" variant="ghost" size="xs" type="button" class="gap-1.5">
                            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                            Liste
                        </Btn>
                    </Route>
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
                </div>
            </div>
        </div>

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
        />

        <div class="space-y-6">
            <SpellEffectsUnifiedSection
                ref="spellEffectsSectionRef"
                hide-effect-group-submit-button
                :available-effects="availableEffects"
                :effect-form-options="effectFormOptions"
                :spell-effect-groups="spellEffectGroups"
                :entity-type="effectEntityType"
                :entity-id="spellModel.id"
                :embedded-in-modal="embeddedInModal"
            />
        </div>
    </div>
</template>
