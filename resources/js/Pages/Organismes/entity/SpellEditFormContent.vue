<script setup>
/**
 * Corps de la fiche d’édition d’un sort (toolbar, formulaire, degrés).
 *
 * @description
 * Partagé entre la page {@link Pages/entity/spell/Edit} et {@link SpellEditModal}.
 * Enregistrement unique : degrés (bulk) puis formulaire sort.
 */
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import { Spell } from "@/Models/Entity/Spell";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import EntityEditForm from "@/Pages/Organismes/entity/EntityEditForm.vue";
import SpellDegreesEditor from "@/Pages/Organismes/entity/SpellDegreesEditor.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import EntityListBackButton from "@/Pages/Atoms/action/EntityListBackButton.vue";
import EntityEditContainer from "@/Pages/Molecules/entity/shared/EntityEditContainer.vue";
import SpellHoldersPanel from "@/Pages/Molecules/entity/spell/SpellHoldersPanel.vue";
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

const forceShowBaseCast = ref(false);
const localDegreesCount = ref(
    Array.isArray(props.spellDegrees?.degrees) ? props.spellDegrees.degrees.length : 0,
);

const hasDegrees = computed(() => localDegreesCount.value > 0);

const fieldSections = computed(() => {
    const sections = SPELL_FORM_FIELD_SECTIONS_EDIT.map((s) => ({ ...s }));
    if (hasDegrees.value && !forceShowBaseCast.value) {
        return sections.filter((s) => s.id !== "base_cast_properties");
    }
    if (hasDegrees.value && forceShowBaseCast.value) {
        return sections.map((s) =>
            s.id === "base_cast_properties"
                ? {
                      ...s,
                      collapsedByDefault: false,
                      subtitle:
                          "Les degrés définissent déjà ces valeurs. Ces champs ne s’appliquent qu’en l’absence de degrés.",
                  }
                : s,
        );
    }
    return sections;
});

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

/** PATCH degrés (bulk) puis le formulaire entité. */
async function beforeSpellSubmitAsync() {
    const fn = spellDegreesEditorRef.value?.flushAll || spellDegreesEditorRef.value?.flushSave;
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

function onDegreesChanged(payload) {
    localDegreesCount.value = Array.isArray(payload?.degrees) ? payload.degrees.length : 0;
}
</script>

<template>
    <div class="spell-edit-form-content space-y-4">
        <div
            class="sticky top-0 z-20 px-3 py-1 bg-glass-3xl backdrop-blur-md border-glass-b-md sm:px-4"
            style="--bg-color: var(--color-base-100)"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-md font-bold text-base-content sm:text-lg">
                        {{ spellModel.name || "Sort sans nom" }}
                    </h1>
                    <p class="text-xs text-base-content/70">
                        Édition · ID {{ spellModel.id }}
                    </p>
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
                    <EntityActions
                        entity-type="spells"
                        :entity="spellModel"
                        format="buttons"
                        display="icon-text"
                        size="sm"
                        color="neutral"
                        :whitelist="['refresh', 'view-dofusdb']"
                        :context="embeddedInModal ? { inModal: true, modalMode: 'edit' } : { inPage: true, pageMode: 'edit' }"
                    />
                    <SpellHoldersPanel :spell-holders="spellHolders" />
                    <div class="flex items-center gap-1">
                        <span class="text-xs text-base-content/70">Options</span>
                        <EntityActions
                            entity-type="spells"
                            :entity="spellModel"
                            format="dropdown"
                            display="icon-text"
                            size="sm"
                            color="neutral"
                            :whitelist="['view', 'view-dofusdb', 'refresh', 'copy-link']"
                            :context="embeddedInModal ? { inModal: true, modalMode: 'edit' } : { inPage: true, pageMode: 'edit' }"
                            @action="handleOptionsAction"
                        />
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
                </div>
            </div>
        </div>

        <p
            v-if="hasDegrees && !forceShowBaseCast"
            class="text-xs text-base-content/70 px-1"
        >
            Les propriétés de lancement du sort de base sont masquées car des degrés existent.
            <button
                type="button"
                class="link link-hover ml-1"
                @click="forceShowBaseCast = true"
            >
                Afficher quand même
            </button>
        </p>

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
            <template #after-sections>
                <EntityEditContainer
                    title="Degrés & effets"
                    subtitle="Onglets par niveau, propriétés de lancement et sous-effets."
                    icon="fa-solid fa-layer-group"
                    :span="3"
                    root-class="mt-3"
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
