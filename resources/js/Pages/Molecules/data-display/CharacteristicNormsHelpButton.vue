<script setup>
/**
 * Bouton d’aide : ouvre les normes (grille + graphique) d’une caractéristique via l’API publique.
 *
 * @props {string} characteristicKey - Clé BDD (ex. `vitality_creature`)
 * @props {string} [entity] - Contexte entité (`creature`, `object`, `spell`, `*`)
 * @props {string} [label] - Libellé du bouton (accessibilité)
 */
import { ref, watch } from "vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import NormsViewer from "@/Pages/Organismes/data-display/NormsViewer.vue";
import axios from "axios";

const props = defineProps({
    characteristicKey: {
        type: String,
        required: true,
    },
    entity: {
        type: String,
        default: "*",
    },
    label: {
        type: String,
        default: "Normes",
    },
});

const open = ref(false);
const loading = ref(false);
const error = ref("");
const normsData = ref(null);

async function loadNorms() {
    const key = String(props.characteristicKey || "").trim();
    if (!key) {
        error.value = "Caractéristique inconnue.";
        return;
    }
    loading.value = true;
    error.value = "";
    try {
        const entity = props.entity || "*";
        const url = route("api.characteristics.norms", { key, entity });
        const params = {};
        if (["creature", "object", "spell"].includes(entity)) {
            params.group = entity;
        }
        const { data } = await axios.get(url, { params });
        normsData.value = data;
    } catch (e) {
        error.value = e?.response?.data?.error || "Impossible de charger les normes.";
        normsData.value = null;
    } finally {
        loading.value = false;
    }
}

watch(open, (isOpen) => {
    if (isOpen && !normsData.value && !loading.value) {
        void loadNorms();
    }
});

function close() {
    open.value = false;
}
</script>

<template>
    <Btn
        type="button"
        size="xs"
        variant="ghost"
        class="btn-square shrink-0 text-info"
        :title="`${label} — charte et graphique`"
        :aria-label="`${label} pour ${characteristicKey}`"
        @click.stop="open = true"
    >
        <i class="fa-solid fa-circle-question" aria-hidden="true" />
    </Btn>

    <dialog class="modal" :class="{ 'modal-open': open }" @close="close">
        <div class="modal-box max-w-4xl w-[min(100vw-2rem,56rem)]" @click.stop>
            <div class="mb-3 flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="text-lg font-bold leading-tight">Charte — {{ characteristicKey }}</h3>
                    <p class="text-sm text-base-content/70">Grille des puissances, conditions et graphique de référence.</p>
                </div>
                <form method="dialog">
                    <button type="submit" class="btn btn-sm btn-circle btn-ghost" aria-label="Fermer" @click="close">
                        <i class="fa-solid fa-xmark" />
                    </button>
                </form>
            </div>

            <div v-if="loading" class="flex justify-center py-12">
                <span class="loading loading-spinner loading-md" />
            </div>

            <div v-else-if="error" class="alert alert-warning text-sm">
                <i class="fa-solid fa-triangle-exclamation" />
                <span>{{ error }}</span>
            </div>

            <div v-else-if="!normsData?.norms" class="alert alert-info text-sm">
                <i class="fa-solid fa-info-circle" />
                <span>Aucune norme définie pour cette caractéristique dans ce contexte.</span>
            </div>

            <NormsViewer
                v-else
                :grid="normsData.norms.grid"
                :conditions="normsData.norms.conditions || []"
                :description="normsData.norms.description || ''"
                :min-limit="normsData.norms.limits?.min ?? null"
                :max-limit="normsData.norms.limits?.max ?? null"
                :characteristic-name="normsData.characteristic?.name || characteristicKey"
                :characteristic-color="normsData.characteristic?.color || 'indigo'"
                :available-characteristics="normsData.available_characteristics || {}"
                :help-section-html="normsData.norms.help_section?.html || ''"
                :help-section-title="normsData.norms.help_section?.title || ''"
                :show-header="false"
            />
        </div>
        <form method="dialog" class="modal-backdrop">
            <button type="submit" @click="close">Fermer</button>
        </form>
    </dialog>
</template>
