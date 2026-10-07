<script setup>
/**
 * Corps de la fiche d’édition d’un sort (header compact, formulaire, degrés).
 *
 * @description
 * Partagé entre la page {@link Pages/entity/spell/Edit} et {@link SpellEditModal}.
 * Enregistrement unique : degrés (bulk) puis formulaire sort.
 */
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import { Spell } from "@/Models/Entity/Spell";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { ACTION } from "@/Utils/atomic-design/actionLabels";
import EntityEditForm from "@/Pages/Organismes/entity/EntityEditForm.vue";
import SpellDegreesEditor from "@/Pages/Organismes/entity/SpellDegreesEditor.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import EntityListBackButton from "@/Pages/Atoms/action/EntityListBackButton.vue";
import EntityEditContainer from "@/Pages/Molecules/entity/shared/EntityEditContainer.vue";
import SpellHoldersPanel from "@/Pages/Molecules/entity/spell/SpellHoldersPanel.vue";
import SpellViewText from "@/Pages/Molecules/entity/spell/SpellViewText.vue";
import FormulaHelpHint from "@/Pages/Molecules/entity/FormulaHelpHint.vue";
import SelectField from "@/Pages/Molecules/data-input/SelectField.vue";
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

const emit = defineEmits(["cancel", "saved"]);

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
const entityEditFormRef = ref(null);

const headerStateField = computed(() => entityEditFormRef.value?.stateField ?? null);
const headerForm = computed(() => entityEditFormRef.value?.form ?? null);
const headerProcessing = computed(
    () => Boolean(entityEditFormRef.value?.processing?.value ?? entityEditFormRef.value?.processing),
);
const headerSaveLabel = computed(
    () => entityEditFormRef.value?.primarySaveLabel ?? ACTION.save.label,
);

/** PATCH degrés (bulk) puis le formulaire entité. */
async function beforeSpellSubmitAsync() {
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
        // Remonter une erreur explicite (sinon le submit s’arrête sans toast).
        throw new Error(typeof detail === "string" ? detail : "Enregistrement des degrés impossible.");
    }
    return true;
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

function goToShow() {
    const id = spellModel.value?.id;
    if (!id) return;
    router.visit(route("entities.spells.show", { spell: id }));
}

async function handleOptionsAction(actionKey) {
    if (actionKey === "view") {
        goToShow();
        return;
    }
    if (actionKey === "copy-link") {
        const href = route("entities.spells.show", { spell: spellModel.value.id });
        await navigator.clipboard?.writeText(new URL(href, window.location.origin).toString());
        return;
    }
    if (actionKey === "refresh" || actionKey === "view-dofusdb") {
        const dispatch = entityEditFormRef.value?.dispatchEntityAction;
        if (typeof dispatch === "function") {
            await dispatch(actionKey, spellModel.value);
        }
    }
}

function onHeaderCancel() {
    const cancel = entityEditFormRef.value?.cancel;
    if (typeof cancel === "function") {
        cancel();
        return;
    }
    emit("cancel");
}

function onHeaderReset() {
    entityEditFormRef.value?.resetForm?.();
}

function onHeaderSave() {
    entityEditFormRef.value?.submit?.();
}

function onDegreesChanged() {
    // Compteur local retiré : le container des propriétés communes reste toujours visible.
}
</script>

<template>
    <div class="spell-edit-form-content space-y-4">
        <div
            class="sticky top-0 z-20 px-3 py-2 bg-glass-3xl backdrop-blur-md border-glass-b-md sm:px-4"
            style="--bg-color: var(--color-base-100)"
            data-cy="spell-edit-header"
        >
            <div
                class="grid grid-cols-1 gap-3 lg:grid-cols-[1fr_auto_1fr] lg:items-center"
            >
                <div class="flex min-w-0 flex-wrap items-center gap-2 justify-self-start">
                    <EntityListBackButton
                        v-if="!embeddedInModal"
                        route-name="entities.spells.index"
                    />
                    <SpellHoldersPanel :spell-holders="spellHolders" />
                </div>

                <div class="min-w-0 justify-self-center text-center max-w-xl">
                    <SpellViewText :spell="spellModel" />
                    <p class="text-xs text-base-content/70 mt-0.5">
                        Édition · #{{ spellModel.id }}
                    </p>
                </div>

                <div
                    class="flex min-w-0 flex-wrap items-center gap-2 justify-self-start lg:justify-self-end"
                >
                    <div
                        class="rounded-(--radius-field) border border-base-300 bg-base-100/60 px-2 py-1.5"
                    >
                        <FormulaHelpHint placement="bottom-end" />
                    </div>

                    <div
                        v-if="headerStateField?.config && headerForm"
                        class="state-field w-[9.5rem] shrink-0"
                    >
                        <SelectField
                            v-model="headerForm[headerStateField.key]"
                            :label="
                                entityEditFormRef?.getFieldLabel?.(
                                    headerStateField.key,
                                    headerStateField.config,
                                ) || headerStateField.config.label || 'État'
                            "
                            :options="headerStateField.config.options || []"
                            :option-badge="headerStateField.config.optionBadge || null"
                            :required="headerStateField.config.required"
                            :validation="
                                entityEditFormRef?.getFieldValidation?.(headerStateField.key) || null
                            "
                            :searchable="false"
                            @update:model-value="
                                () => entityEditFormRef?.markDirty?.(headerStateField.key)
                            "
                        />
                    </div>

                    <div class="flex items-center gap-1">
                        <span class="text-xs text-base-content/70 sr-only sm:not-sr-only">Options</span>
                        <EntityActions
                            entity-type="spells"
                            :entity="spellModel"
                            format="dropdown"
                            display="icon-text"
                            size="sm"
                            color="neutral"
                            :whitelist="['view', 'view-dofusdb', 'refresh', 'copy-link']"
                            :context="
                                embeddedInModal
                                    ? { inModal: true, modalMode: 'edit' }
                                    : { inPage: true, pageMode: 'edit' }
                            "
                            @action="handleOptionsAction"
                        />
                    </div>

                    <div class="flex flex-col gap-1">
                        <Btn
                            color="neutral"
                            variant="outline"
                            size="xs"
                            type="button"
                            class="gap-1"
                            @click="onHeaderCancel"
                        >
                            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                            Annuler
                        </Btn>
                        <Btn
                            color="neutral"
                            variant="outline"
                            size="xs"
                            type="button"
                            class="gap-1"
                            @click="onHeaderReset"
                        >
                            <i :class="ACTION.discard.icon" aria-hidden="true"></i>
                            Annuler les modifications
                        </Btn>
                    </div>

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

                    <Btn
                        color="primary"
                        size="sm"
                        type="button"
                        class="gap-1.5"
                        :disabled="headerProcessing"
                        data-cy="spell-edit-save"
                        @click="onHeaderSave"
                    >
                        <i :class="ACTION.save.icon" aria-hidden="true"></i>
                        {{ headerProcessing ? ACTION.save.processing : headerSaveLabel }}
                    </Btn>
                </div>
            </div>
        </div>

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
            @cancel="emit('cancel')"
            @submit="emit('saved')"
        >
            <template #after-sections>
                <EntityEditContainer
                    title="Degrés & effets"
                    subtitle="Onglets par niveau, propriétés de lancement et sous-effets."
                    icon="fa-solid fa-layer-group"
                    :span="2"
                    root-class="mt-3 lg:col-span-2"
                >
                    <SpellDegreesEditor
                        ref="spellDegreesEditorRef"
                        :spell-id="Number(spellModel.id)"
                        :spell="spellModel"
                        :spell-degrees="spellDegrees"
                        :effect-form-options="effectFormOptions"
                        :embedded-in-modal="embeddedInModal"
                        @changed="onDegreesChanged"
                    />
                </EntityEditContainer>
            </template>
        </EntityEditForm>
    </div>
</template>
