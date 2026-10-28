<script setup>
/**
 * Corps de la fiche d’édition d’un sort (header compact, formulaire, degrés).
 *
 * @description
 * Partagé entre la page {@link Pages/entity/spell/Edit} et l’édition embarquée ({@link EntitySurfaceHost}).
 * Chemin principal : un effet = degrés + sous-effets. Définitions legacy en collapse discret.
 * Enregistrement unique : degrés (bulk) puis formulaire sort.
 */
import { computed, reactive, ref, watch } from "vue";
import { Spell } from "@/Models/Entity/Spell";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useEntityEditEmbed } from "@/Composables/entity/useEntityEditEmbed";
import EntityEditForm from "@/Pages/Organismes/entity/EntityEditForm.vue";
import SpellDegreesEditor from "@/Pages/Organismes/entity/SpellDegreesEditor.vue";
import EntityEditHeader from "@/Pages/Molecules/entity/shared/EntityEditHeader.vue";
import EntityEditContainer from "@/Pages/Molecules/entity/shared/EntityEditContainer.vue";
import SpellHoldersPanel from "@/Pages/Molecules/entity/spell/SpellHoldersPanel.vue";
import SpellViewText from "@/Pages/Molecules/entity/spell/SpellViewText.vue";
import SpellEffectsUnifiedSection from "@/Pages/Organismes/entity/SpellEffectsUnifiedSection.vue";
import Collapse from "@/Pages/Atoms/data-display/Collapse.vue";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import SelectField from "@/Pages/Molecules/data-input/SelectField.vue";
import {
    buildSpellFormFieldsConfig,
    SPELL_FORM_FIELD_SECTIONS_EDIT,
    mergeSpellTypesFieldIntoSpellFormConfig,
} from "@/Entities/spell/spell-form-config";

const EFFECT_TARGET_TYPE_OPTIONS = [
    { value: "direct", label: "Direct" },
    { value: "trap", label: "Piège" },
    { value: "glyph", label: "Glyphe" },
];

const props = defineProps({
    spell: { type: Object, required: true },
    availableSpellTypes: { type: Array, default: () => [] },
    availableEffects: { type: Array, default: () => [] },
    effectEntityType: { type: String, default: "spell" },
    effectFormOptions: { type: Object, default: () => ({}) },
    spellDegrees: { type: Object, default: () => ({ degrees: [], default_degree_id: null }) },
    spellEffectGroups: { type: Array, default: () => [] },
    spellHolders: { type: Object, default: () => ({}) },
    /** Quand true : annulation sans redirection vers la fiche lecture. */
    embeddedInModal: { type: Boolean, default: false },
    /**
     * Redirection après PATCH réussi : `edit` = rester sur l’éditeur (page fiche) ; `index` = liste (modal).
     * @type {"stay"|"index"|"show"|"edit"|null}
     */
    redirectAfterUpdate: { type: String, default: "edit" },
});

const emit = defineEmits(["cancel", "saved", "effects-changed"]);

const {
    embed: entityEditEmbed,
    isEmbeddedInModal,
    resolvedRedirectAfterUpdate,
} = useEntityEditEmbed(props);
const embeddedInModal = isEmbeddedInModal;
const redirectAfterUpdate = resolvedRedirectAfterUpdate;

const { canDeleteAny, isAdmin } = usePermissions();
const canDeleteSpell = computed(() => canDeleteAny("spells") || isAdmin.value);

const fieldsConfig = computed(() =>
    mergeSpellTypesFieldIntoSpellFormConfig(
        buildSpellFormFieldsConfig({ includeReadonlyMeta: true }),
        props.availableSpellTypes || [],
    ),
);

const fieldSections = computed(() =>
    SPELL_FORM_FIELD_SECTIONS_EDIT.map((s) => ({ ...s })),
);

const spellModel = computed(() =>
    props.spell instanceof Spell ? props.spell : new Spell(props.spell),
);

/**
 * Le layout décale déjà `<main>` sous la sidebar ; pas de second décalage sur le pied.
 * @see CapabilityEditFormContent — même raison (`sticky` dans la colonne scrollable).
 */
const fixedFooterInsetClass = "left-0 right-0";

const spellDegreesEditorRef = ref(null);
const spellEffectsSectionRef = ref(null);
const entityEditFormRef = ref(null);

/** Nom / description / type de cible de l’effet global (partagé avec EffectGroupEditorForm). */
const effectCommon = reactive({
    name: "",
    description: "",
    target_type: "direct",
});

function hydrateEffectCommonFromGroups() {
    const groups = Array.isArray(props.spellEffectGroups) ? props.spellEffectGroups : [];
    const firstDeg = groups[0]?.group_effects?.[0];
    if (firstDeg) {
        effectCommon.name = String(firstDeg.name ?? groups[0]?.label ?? "").trim();
        effectCommon.description = String(firstDeg.description ?? "").trim();
        effectCommon.target_type = String(firstDeg.target_type || "direct");
        return;
    }
    if (!effectCommon.name) {
        effectCommon.name = String(spellModel.value?.name || "").trim();
    }
    if (!effectCommon.target_type) {
        effectCommon.target_type = "direct";
    }
}

watch(
    () => props.spellEffectGroups?.[0]?.anchor_effect_id ?? null,
    () => hydrateEffectCommonFromGroups(),
    { immediate: true },
);

/** Enregistre les groupes d’effets du sort puis les degrés avant le PATCH entité. */
async function beforeSpellSubmitAsync() {
    const flushGroups = spellEffectsSectionRef.value?.flushEffectGroupSave;
    if (typeof flushGroups === "function") {
        const groupsOk = await flushGroups();
        if (groupsOk === false) {
            throw new Error("Enregistrement des effets du sort impossible.");
        }
    }

    const editor = spellDegreesEditorRef.value;
    const fn = editor?.flushAll || editor?.flushSave;
    if (typeof fn !== "function") {
        return true;
    }
    const ok = await fn();
    if (ok === false) {
        const detail =
            editor?.errorMessage?.value ||
            editor?.errorMessage ||
            "Enregistrement des degrés impossible.";
        throw new Error(typeof detail === "string" ? detail : "Enregistrement des degrés impossible.");
    }
    return true;
}

function onHeaderDeleted() {
    if (isEmbeddedInModal.value) {
        emit("cancel");
        entityEditEmbed?.onCancel?.();
    }
}

function onHeaderCancel() {
    emit("cancel");
    entityEditEmbed?.onCancel?.();
}

function onFormSaved() {
    emit("saved");
    entityEditEmbed?.onSaved?.();
}
</script>

<template>
    <div class="spell-edit-form-content space-y-4">
        <EntityEditHeader
            entity-type="spells"
            :entity="spellModel"
            :form-ref="entityEditFormRef"
            list-route-name="entities.spells.index"
            delete-route-name="entities.spells.delete"
            route-param-key="spell"
            :can-delete="canDeleteSpell"
            :embedded-in-modal="embeddedInModal"
            :show-formula-help="true"
            save-data-cy="spell-edit-save"
            header-data-cy="spell-edit-header"
            delete-confirm-message="Supprimer ce sort ? Il sera placé en corbeille (récupération possible côté admin)."
            @cancel="onHeaderCancel"
            @deleted="onHeaderDeleted"
        >
            <template #left>
                <SpellHoldersPanel :spell-holders="spellHolders" />
            </template>
            <template #title>
                <SpellViewText :spell="spellModel" />
            </template>
        </EntityEditHeader>

        <EntityEditForm
            ref="entityEditFormRef"
            :entity="spellModel"
            entity-type="spell"
            :fields-config="fieldsConfig"
            :is-updating="true"
            :hidden-field-keys="['dofus_version']"
            :field-sections="fieldSections"
            :show-state-toolbar="true"
            :show-access-levels-in-footer="false"
            :hide-top-toolbar="true"
            :hide-action-dock="true"
            characteristics-group="spell"
            layout-profile="spell"
            :fixed-footer-actions="false"
            :fixed-footer-inset-class="fixedFooterInsetClass"
            :embedded-in-modal="embeddedInModal"
            :redirect-after-update="redirectAfterUpdate || undefined"
            :before-submit-async="beforeSpellSubmitAsync"
            @cancel="onHeaderCancel"
            @submit="onFormSaved"
        >
            <template #after-sections>
                <EntityEditContainer
                    title="Effet"
                    subtitle="Identité de l’effet, puis progression par degrés (sous-effets). Un nouveau degré copie le précédent."
                    icon="fa-solid fa-wand-magic-sparkles"
                    :span="2"
                    root-class="mt-3 lg:col-span-2"
                >
                    <div class="mb-4 space-y-3 rounded-box border border-base-300 bg-base-100 p-3">
                        <p class="text-xs text-base-content/70">
                            Nom et description de l’effet (communs à tous les degrés). Enregistrez avec
                            le sort pour les persister lorsqu’une définition est liée.
                        </p>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <InputField
                                v-model="effectCommon.name"
                                label="Nom de l’effet"
                                name="effect_common_name"
                            />
                            <SelectField
                                v-model="effectCommon.target_type"
                                label="Type de cible"
                                name="effect_common_target_type"
                                :options="EFFECT_TARGET_TYPE_OPTIONS"
                                :searchable="false"
                                helper="Direct, piège ou glyphe."
                            />
                            <div class="sm:col-span-2">
                                <InputField
                                    v-model="effectCommon.description"
                                    label="Description"
                                    name="effect_common_description"
                                    type="textarea"
                                />
                            </div>
                        </div>
                    </div>

                    <SpellDegreesEditor
                        ref="spellDegreesEditorRef"
                        :spell-id="Number(spellModel.id)"
                        :spell="spellModel"
                        :spell-degrees="spellDegrees"
                        :effect-form-options="effectFormOptions"
                        :embedded-in-modal="embeddedInModal"
                    />
                </EntityEditContainer>

                <Collapse
                    arrow
                    :default-open="false"
                    bg-off="bg-base-100"
                    bg-on="bg-base-100"
                    class="mt-3 border border-base-300 lg:col-span-2"
                    data-cy="spell-effects-legacy-section"
                >
                    <template #title>Définitions liées (avancé)</template>
                    <template #content>
                        <p class="mb-3 text-xs text-base-content/70">
                            Outils legacy / admin : rechercher, lier ou créer une définition
                            <code class="text-[10px]">effect_spell</code>. Le chemin normal reste
                            l’effet unique ci-dessus (degrés + sous-effets).
                        </p>
                        <SpellEffectsUnifiedSection
                            ref="spellEffectsSectionRef"
                            hide-effect-group-submit-button
                            hide-common-card
                            :common-model="effectCommon"
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
