<script setup>
import { computed, ref, watch } from "vue";
import axios from "axios";
import { useForm } from "@inertiajs/vue3";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import EditActionDock from "@/Pages/Molecules/action/EditActionDock.vue";
import ConditionBadges from "@/Pages/Molecules/entity/condition/ConditionBadges.vue";
import { mergeEntityOptionsById } from "@/Utils/entity/mergeEntityOptionsById";
import { warnDev } from "@/Utils/dev-logger";

const props = defineProps({
    relations: { type: Array, default: () => [] },
    availableItems: { type: Array, default: () => [] },
    entityId: { type: Number, required: true },
    routeName: { type: String, required: true },
    routeParamName: { type: String, required: true },
    title: { type: String, default: "Conditions" },
    help: {
        type: String,
        default: "Associe les conditions que cette entité peut appliquer. Le détail d’interaction reste décrit dans le texte.",
    },
});

const notificationStore = useNotificationStore();
const query = ref("");
const localIds = ref(normalizeIds(props.relations));
const localAvailable = ref([...props.availableItems]);

watch(() => props.relations, (next) => { localIds.value = normalizeIds(next); }, { deep: true });
watch(() => props.availableItems, (next) => { localAvailable.value = [...(next || [])]; }, { deep: true });

function normalizeIds(list) {
    return (Array.isArray(list) ? list : [])
        .map((condition) => Number(condition.id ?? condition))
        .filter((id) => Number.isFinite(id) && id > 0);
}

const byId = computed(() => mergeEntityOptionsById(props.relations, localAvailable.value));

const selectedConditions = computed(() => localIds.value.map((id) => byId.value.get(id)).filter(Boolean));
const selectedSet = computed(() => new Set(localIds.value));

const filteredToAdd = computed(() => {
    const q = query.value.trim().toLowerCase();
    return [...localAvailable.value]
        .filter((condition) => !selectedSet.value.has(Number(condition.id)))
        .filter((condition) => {
            if (!q) return true;
            return [condition.name, condition.description, condition.dofusdb_id]
                .some((value) => String(value || "").toLowerCase().includes(q));
        })
        .sort((a, b) => String(a.name || "").localeCompare(String(b.name || ""), "fr"));
});

const originalSignature = computed(() => JSON.stringify([...normalizeIds(props.relations)].sort((a, b) => a - b)));
const localSignature = computed(() => JSON.stringify([...localIds.value].sort((a, b) => a - b)));
const hasUnsavedChanges = computed(() => originalSignature.value !== localSignature.value);

function addCondition(id) {
    const n = Number(id);
    if (!Number.isFinite(n) || selectedSet.value.has(n)) return;
    localIds.value = [...localIds.value, n];
    query.value = "";
}

function removeCondition(id) {
    localIds.value = localIds.value.filter((current) => current !== Number(id));
}

async function createFromQuery() {
    const name = query.value.trim();
    if (!name) return;
    try {
        const { data } = await axios.post(route("entities.conditions.store"), {
            name,
            description: null,
            state: "playable",
            read_level: 0,
            write_level: 4,
        });
        localAvailable.value = [...localAvailable.value, data];
        addCondition(data.id);
        notificationStore.success("Condition créée et ajoutée.", { duration: 2500, placement: "top-right" });
    } catch (error) {
        notificationStore.error("Impossible de créer la condition.", { duration: 5000, placement: "top-center" });
        warnDev("[ConditionsEditor] création échouée", error);
    }
}

const form = useForm({ conditions: [] });

function save() {
    form.conditions = [...localIds.value];
    form.patch(route(props.routeName, { [props.routeParamName]: props.entityId }), {
        preserveScroll: true,
        onSuccess: () => notificationStore.success("Conditions mises à jour.", { duration: 3000, placement: "top-right" }),
        onError: (errors) => {
            notificationStore.error("Erreur lors de la mise à jour des conditions.", { duration: 5000, placement: "top-center" });
            warnDev("[ConditionsEditor] erreurs", errors);
        },
    });
}
</script>

<template>
    <Container class="space-y-4">
        <div>
            <h3 class="text-lg font-semibold">{{ title }}</h3>
            <p class="text-sm text-base-content/70 max-w-3xl mt-1">{{ help }}</p>
        </div>

        <div v-if="selectedConditions.length" class="space-y-3 rounded-lg border border-base-300/60 bg-base-100/30 p-3">
            <ConditionBadges :conditions="selectedConditions" />
            <ul class="space-y-2">
                <li
                    v-for="condition in selectedConditions"
                    :key="condition.id"
                    class="flex flex-wrap items-center justify-between gap-2 border-b border-base-300/40 pb-2 last:border-0 last:pb-0"
                >
                    <div class="min-w-0 flex-1">
                        <div class="font-medium text-sm">{{ condition.name || `#${condition.id}` }}</div>
                        <p v-if="condition.description" class="text-xs text-base-content/60 line-clamp-1">
                            {{ condition.description }}
                        </p>
                    </div>
                    <button type="button" class="btn btn-ghost btn-xs text-error shrink-0" @click="removeCondition(condition.id)">
                        Retirer
                    </button>
                </li>
            </ul>
        </div>
        <p v-else class="text-sm text-base-content/50 italic">Aucune condition liée.</p>

        <div class="space-y-2">
            <InputField v-model="query" label="Ajouter une condition" placeholder="Rechercher ou créer une condition…" size="sm" />
            <div
                v-if="query.trim()"
                class="max-h-56 overflow-y-auto rounded border border-base-300/80 bg-glass-3xl text-[13px]"
                style="--bg-color: var(--color-base-100)"
            >
                <button
                    v-for="opt in filteredToAdd.slice(0, 20)"
                    :key="opt.id"
                    type="button"
                    class="w-full text-left px-2.5 py-1.5 hover:bg-base-200 border-b border-base-200/80"
                    @mousedown.prevent="addCondition(opt.id)"
                >
                    <span class="font-medium">{{ opt.name || `#${opt.id}` }}</span>
                    <span v-if="opt.dofusdb_id" class="text-base-content/55 text-xs ml-1.5">DofusDB {{ opt.dofusdb_id }}</span>
                </button>
                <button
                    type="button"
                    class="w-full text-left px-2.5 py-2 hover:bg-base-200 text-primary font-medium"
                    @mousedown.prevent="createFromQuery"
                >
                    Créer « {{ query.trim() }} »
                </button>
            </div>
        </div>

        <div class="flex justify-end border-t border-base-300 pt-2">
            <EditActionDock
                primary-label="Enregistrer les conditions"
                processing-label="Sauvegarde…"
                :processing="form.processing"
                :disabled="!hasUnsavedChanges"
                :show-secondary="false"
                :secondary-actions="[]"
                :fixed-on-desktop="false"
                @primary="save"
            />
        </div>
    </Container>
</template>
