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
import SpellDegreesEditor from "@/Pages/Organismes/entity/SpellDegreesEditor.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import EntityListBackButton from "@/Pages/Atoms/action/EntityListBackButton.vue";
import Collapse from "@/Pages/Atoms/data-display/Collapse.vue";
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

const fieldSections = SPELL_FORM_FIELD_SECTIONS_EDIT;

const spellModel = computed(() =>
    props.spell instanceof Spell ? props.spell : new Spell(props.spell),
);

const holderGroups = computed(() => [
    { key: "monsters", label: "Monstres", items: props.spellHolders?.monsters || [] },
    { key: "npcs", label: "PNJ", items: props.spellHolders?.npcs || [] },
    { key: "creatures", label: "Créatures", items: props.spellHolders?.creatures || [] },
    { key: "breeds", label: "Classes", items: props.spellHolders?.breeds || [] },
].filter((group) => group.items.length > 0));

const holderCount = computed(() =>
    holderGroups.value.reduce((total, group) => total + group.items.length, 0),
);

/**
 * Le layout décale déjà `<main>` sous la sidebar ; pas de second décalage sur le pied.
 * @see CapabilityEditFormContent — même raison (`sticky` dans la colonne scrollable).
 */
const fixedFooterInsetClass = "left-0 right-0";

const spellDegreesEditorRef = ref(null);

/** PATCH degrés puis le formulaire entité (via {@link EntityEditForm}). */
async function beforeSpellSubmitAsync() {
    const fn = spellDegreesEditorRef.value?.flushSave;
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
    }
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
                    <div class="flex items-center gap-1">
                        <span class="text-xs text-base-content/60">Options</span>
                        <EntityActions
                            entity-type="spells"
                            :entity="spellModel"
                            format="dropdown"
                            display="icon-text"
                            size="sm"
                            color="neutral"
                            :whitelist="['view', 'view-dofusdb', 'copy-link']"
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

        <details
            v-if="holderCount"
            class="rounded-box border border-base-300 bg-base-100/50 px-3 py-2"
        >
            <summary class="cursor-pointer list-none text-sm font-medium [&::-webkit-details-marker]:hidden">
                Utilisé par {{ holderCount }} entité{{ holderCount > 1 ? "s" : "" }}
                <span class="ml-1 text-xs font-normal text-base-content/55">Afficher</span>
            </summary>
            <div class="mt-2 flex flex-wrap gap-2">
                <template v-for="group in holderGroups" :key="group.key">
                    <a
                        v-for="holder in group.items"
                        :key="`${group.key}-${holder.id}`"
                        :href="holder.href"
                        class="badge badge-outline gap-1.5 border-base-300 hover:border-base-content/40"
                        :title="group.label"
                    >
                        <i v-if="group.key === 'monsters'" class="fa-solid fa-dragon" aria-hidden="true"></i>
                        <i v-else-if="group.key === 'npcs'" class="fa-solid fa-user" aria-hidden="true"></i>
                        <i v-else-if="group.key === 'creatures'" class="fa-solid fa-paw" aria-hidden="true"></i>
                        <i v-else class="fa-solid fa-hat-wizard" aria-hidden="true"></i>
                        {{ holder.name }}
                    </a>
                </template>
            </div>
        </details>

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

        <Collapse arrow :default-open="true" bg-off="bg-base-100" class="border border-base-300">
            <template #title>Degrés & effets</template>
            <template #content>
                <SpellDegreesEditor
                    ref="spellDegreesEditorRef"
                    :spell-id="Number(spellModel.id)"
                    :spell-degrees="spellDegrees"
                    :effect-form-options="effectFormOptions"
                    :embedded-in-modal="embeddedInModal"
                />
            </template>
        </Collapse>
    </div>
</template>
