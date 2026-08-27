<script setup>
/**
 * Panneau de confirmation d’une maj DofusDB unitaire (aperçu puis apply).
 *
 * @example
 * <EntityDofusdbRefreshPanel
 *   :open="refreshConfirm.open"
 *   :loading="refreshConfirm.loading"
 *   :preview="refreshConfirm.preview"
 *   :error="refreshConfirm.error"
 *   :playable="refreshConfirm.playable"
 *   :applying="refreshConfirm.applying"
 *   @close="cancelPendingRefresh"
 *   @confirm="confirmPendingRefresh"
 * />
 */
import { computed, ref, watch } from "vue";
import Modal from "@/Pages/Molecules/action/Modal.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import { usePermissions } from "@/Composables/permissions/usePermissions";

const props = defineProps({
    open: { type: Boolean, default: false },
    loading: { type: Boolean, default: false },
    applying: { type: Boolean, default: false },
    preview: { type: Object, default: null },
    error: { type: String, default: "" },
    playable: { type: Boolean, default: false },
    entityLabel: { type: String, default: "cette fiche" },
});

const emit = defineEmits(["close", "confirm"]);

const { isAdmin } = usePermissions();
const mode = ref("full");
const force = ref(false);

watch(
    () => props.open,
    (open) => {
        if (open) {
            mode.value = "full";
            force.value = false;
        }
    }
);

const convertedName = computed(() => {
    const converted = props.preview?.data?.converted;
    if (!converted || typeof converted !== "object") return "";
    const nested = Object.values(converted).find((v) => v && typeof v === "object" && !Array.isArray(v));
    const name = converted.name ?? nested?.name ?? "";
    if (typeof name === "string") return name;
    if (name && typeof name === "object") return name.fr || name.en || "";
    return "";
});

const validationErrors = computed(() => {
    const errors = props.preview?.data?.validation_errors;
    return Array.isArray(errors) ? errors : [];
});

function submit() {
    emit("confirm", { mode: mode.value, force: force.value });
}
</script>

<template>
    <Modal :open="open" size="md" placement="middle-center" close-on-esc @close="emit('close')">
        <template #header>
            <div class="flex items-center justify-between gap-3 w-full">
                <div class="font-semibold text-primary-100">Mettre à jour depuis DofusDB</div>
                <Btn size="sm" variant="ghost" @click="emit('close')">Fermer</Btn>
            </div>
        </template>

        <div class="space-y-4">
            <p class="text-sm text-base-content/80">
                Aperçu pour « {{ entityLabel }} » avant écriture.
            </p>

            <div v-if="loading" class="text-sm text-base-content/60">Chargement de l’aperçu…</div>
            <p v-else-if="error" class="text-sm text-error">{{ error }}</p>
            <div v-else class="space-y-3">
                <p v-if="convertedName" class="text-sm">
                    Nom converti : <span class="font-medium">{{ convertedName }}</span>
                </p>
                <p v-if="preview?.data?.dofusdb_id" class="text-xs text-base-content/60">
                    DofusDB #{{ preview.data.dofusdb_id }}
                </p>
                <ul
                    v-if="validationErrors.length"
                    class="list-disc space-y-1 pl-5 text-sm text-warning"
                >
                    <li v-for="(err, index) in validationErrors" :key="`ve-${index}`">
                        {{ err.message || err.path || JSON.stringify(err) }}
                    </li>
                </ul>

                <fieldset class="space-y-2">
                    <legend class="text-sm font-medium">Mode</legend>
                    <label class="flex items-center gap-2 text-sm">
                        <input v-model="mode" type="radio" class="radio radio-sm" value="full" />
                        Contenu + images
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input v-model="mode" type="radio" class="radio radio-sm" value="images_only" />
                        Images seules
                    </label>
                </fieldset>

                <label v-if="playable && isAdmin" class="flex items-center gap-2 text-sm">
                    <input v-model="force" type="checkbox" class="checkbox checkbox-sm" />
                    Forcer l’écrasement d’une fiche jouable / archivée
                </label>
                <p v-else-if="playable" class="text-sm text-warning">
                    Fiche jouable : un administrateur doit forcer la mise à jour.
                </p>
            </div>

            <div class="flex justify-end gap-2">
                <Btn variant="ghost" :disabled="applying" @click="emit('close')">Annuler</Btn>
                <Btn
                    color="primary"
                    :disabled="loading || applying || Boolean(error) || (playable && !isAdmin)"
                    @click="submit"
                >
                    <Icon source="fa-arrow-rotate-right" pack="solid" alt="" class="mr-2" />
                    {{ applying ? "Mise à jour…" : "Confirmer" }}
                </Btn>
            </div>
        </div>
    </Modal>
</template>
