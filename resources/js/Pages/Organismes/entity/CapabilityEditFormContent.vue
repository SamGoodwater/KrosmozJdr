<script setup>
/**
 * Corps de la fiche d’édition d’une capacité (toolbar + formulaire unique).
 *
 * @description
 * Partagé entre {@link Pages/entity/capability/Edit} et {@link CapabilityEditModal}.
 */
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import { Capability } from "@/Models/Entity/Capability";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import EntityEditForm from "@/Pages/Organismes/entity/EntityEditForm.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import {
    buildCapabilityFormFieldsConfig,
    CAPABILITY_FORM_FIELD_SECTIONS_EDIT,
} from "@/Entities/capability/capability-form-config";

const props = defineProps({
    capability: { type: Object, required: true },
    embeddedInModal: { type: Boolean, default: false },
    /**
     * Redirection après PATCH : `stay` = modal / même page ; `edit` = page édition.
     * @type {"stay"|"index"|"show"|"edit"|null}
     */
    redirectAfterUpdate: { type: String, default: "edit" },
});

const emit = defineEmits(["cancel", "saved"]);

const { canDeleteAny, isAdmin } = usePermissions();
const canDeleteCapability = computed(() => canDeleteAny("capabilities") || isAdmin.value);

const fieldsConfig = computed(() => buildCapabilityFormFieldsConfig({ includeReadonlyMeta: true }));

const fieldSections = CAPABILITY_FORM_FIELD_SECTIONS_EDIT;

const capabilityModel = computed(() =>
    props.capability instanceof Capability ? props.capability : new Capability(props.capability),
);

const fixedFooterInsetClass = computed(() =>
    props.embeddedInModal ? "left-0 right-0" : "left-0 right-0 lg:left-64",
);

function goToShow() {
    const id = capabilityModel.value?.id;
    if (!id) return;
    router.visit(route("entities.capabilities.show", { capability: id }));
}

function confirmDelete() {
    const id = capabilityModel.value?.id;
    if (!id) return;
    const ok = window.confirm(
        "Supprimer cette capacité ? Elle sera placée en corbeille (récupération possible côté admin).",
    );
    if (!ok) return;
    router.delete(route("entities.capabilities.delete", { capability: id }), {
        onSuccess: () => {
            if (props.embeddedInModal) {
                emit("cancel");
            }
        },
    });
}
</script>

<template>
    <div class="capability-edit-form-content space-y-6">
        <div
            class="sticky top-0 z-20 px-3 py-1 bg-base-100/80 backdrop-blur-md border-glass-b-md sm:px-4"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-md font-bold text-base-content sm:text-lg">
                        {{ capabilityModel.name || "Capacité sans nom" }}
                    </h1>
                    <p class="text-xs text-base-content/60">
                        Édition · ID {{ capabilityModel.id }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <Route v-if="!embeddedInModal" route="entities.capabilities.index">
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
                        v-if="canDeleteCapability"
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
            :entity="capabilityModel"
            entity-type="capability"
            :fields-config="fieldsConfig"
            :is-updating="true"
            :field-sections="fieldSections"
            :show-state-toolbar="true"
            :show-access-levels-in-footer="false"
            characteristics-group="capability"
            layout-profile="capability"
            :fixed-footer-actions="true"
            :fixed-footer-inset-class="fixedFooterInsetClass"
            :embedded-in-modal="embeddedInModal"
            :redirect-after-update="redirectAfterUpdate || undefined"
            :shortcuts-active="!embeddedInModal"
            @cancel="emit('cancel')"
            @submit="emit('saved')"
        />
    </div>
</template>
