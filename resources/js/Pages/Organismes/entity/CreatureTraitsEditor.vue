<script setup>
/**
 * Éditeur générique des traits de créature.
 *
 * `withLevel` est réservé aux classes et spécialisations : le niveau indique
 * à partir de quand le trait devient actif.
 */
import { computed, ref, watch } from "vue";
import axios from "axios";
import { useForm } from "@inertiajs/vue3";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import EditActionDock from "@/Pages/Molecules/action/EditActionDock.vue";
import CreatureTraitBadges from "@/Pages/Molecules/entity/creature-trait/CreatureTraitBadges.vue";
import { warnDev } from "@/Utils/dev-logger";

const props = defineProps({
    relations: { type: Array, default: () => [] },
    availableItems: { type: Array, default: () => [] },
    entityId: { type: Number, required: true },
    routeName: { type: String, required: true },
    routeParamName: { type: String, required: true },
    title: { type: String, default: "Traits" },
    help: {
        type: String,
        default: "Ajoute les traits permanents applicables à cette entité.",
    },
    withLevel: { type: Boolean, default: false },
});

const notificationStore = useNotificationStore();
const query = ref("");
const localRows = ref(normalizeRelations(props.relations));
const localAvailable = ref([...props.availableItems]);
const creating = ref(false);

watch(
    () => props.relations,
    (next) => {
        localRows.value = normalizeRelations(next);
    },
    { deep: true },
);

watch(
    () => props.availableItems,
    (next) => {
        localAvailable.value = [...(next || [])];
    },
    { deep: true },
);

function normalizeRelations(list) {
    return (Array.isArray(list) ? list : [])
        .map((trait) => ({
            id: Number(trait.id),
            level: Math.max(1, Number(trait?.pivot?.level ?? trait?.level ?? 1)),
        }))
        .filter((row) => Number.isFinite(row.id) && row.id > 0);
}

const byId = computed(() => {
    const map = new Map();
    for (const item of props.relations) {
        if (item?.id) map.set(Number(item.id), item);
    }
    for (const item of localAvailable.value) {
        if (item?.id && !map.has(Number(item.id))) {
            map.set(Number(item.id), item);
        }
    }
    return map;
});

const selectedTraits = computed(() =>
    localRows.value
        .map((row) => {
            const raw = byId.value.get(row.id);
            return raw ? { ...raw, pivot: { ...(raw.pivot || {}), level: row.level } } : null;
        })
        .filter(Boolean),
);

const selectedIds = computed(() => new Set(localRows.value.map((row) => row.id)));

const filteredToAdd = computed(() => {
    const q = query.value.trim().toLowerCase();
    return [...localAvailable.value]
        .filter((trait) => !selectedIds.value.has(Number(trait.id)))
        .filter((trait) => {
            if (!q) return true;
            return [trait.name, trait.description]
                .some((value) => String(value || "").toLowerCase().includes(q));
        })
        .sort((a, b) => String(a.name || "").localeCompare(String(b.name || ""), "fr"));
});

const originalSignature = computed(() => JSON.stringify(normalizeRelations(props.relations).sort(sortRows)));
const localSignature = computed(() => JSON.stringify([...localRows.value].sort(sortRows)));
const hasUnsavedChanges = computed(() => originalSignature.value !== localSignature.value);

function sortRows(a, b) {
    return a.id - b.id;
}

function addTrait(id) {
    const n = Number(id);
    if (!Number.isFinite(n) || selectedIds.value.has(n)) return;
    localRows.value = [...localRows.value, { id: n, level: 1 }];
    query.value = "";
}

function removeTrait(id) {
    const n = Number(id);
    localRows.value = localRows.value.filter((row) => row.id !== n);
}

function updateLevel(id, value) {
    const n = Math.max(1, Number(value) || 1);
    localRows.value = localRows.value.map((row) => (row.id === Number(id) ? { ...row, level: n } : row));
}

async function createFromQuery() {
    const name = query.value.trim();
    if (!name || creating.value) return;
    creating.value = true;
    try {
        const { data } = await axios.post(route("entities.creature-traits.store"), {
            name,
            description: null,
            state: "playable",
            read_level: 0,
            write_level: 4,
        });
        localAvailable.value = [...localAvailable.value, data];
        addTrait(data.id);
        notificationStore.success("Trait créé et ajouté.", { duration: 2500, placement: "top-right" });
    } catch (error) {
        notificationStore.error("Impossible de créer le trait.", { duration: 5000, placement: "top-center" });
        warnDev("[CreatureTraitsEditor] création échouée", error);
    } finally {
        creating.value = false;
    }
}

const form = useForm({ creature_traits: [] });

function save() {
    form.creature_traits = props.withLevel
        ? localRows.value.map((row) => ({ id: row.id, level: row.level }))
        : localRows.value.map((row) => row.id);

    form.patch(route(props.routeName, { [props.routeParamName]: props.entityId }), {
        preserveScroll: true,
        onSuccess: () => {
            notificationStore.success("Traits mis à jour.", { duration: 3000, placement: "top-right" });
        },
        onError: (errors) => {
            notificationStore.error("Erreur lors de la mise à jour des traits.", {
                duration: 5000,
                placement: "top-center",
            });
            warnDev("[CreatureTraitsEditor] erreurs", errors);
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

        <div v-if="selectedTraits.length" class="space-y-3 rounded-lg border border-base-300/60 bg-base-100/30 p-3">
            <CreatureTraitBadges :traits="selectedTraits" :show-level="withLevel" />
            <ul class="space-y-2">
                <li
                    v-for="trait in selectedTraits"
                    :key="trait.id"
                    class="flex flex-wrap items-center gap-2 border-b border-base-300/40 pb-2 last:border-0 last:pb-0"
                >
                    <div class="min-w-0 flex-1">
                        <div class="font-medium text-sm">{{ trait.name || `#${trait.id}` }}</div>
                        <p v-if="trait.description" class="text-xs text-base-content/60 line-clamp-1">
                            {{ trait.description }}
                        </p>
                    </div>
                    <InputField
                        v-if="withLevel"
                        :model-value="trait.pivot?.level || 1"
                        type="number"
                        label="Niveau"
                        size="sm"
                        class="w-28 shrink-0"
                        min="1"
                        max="200"
                        @update:model-value="updateLevel(trait.id, $event)"
                    />
                    <button type="button" class="btn btn-ghost btn-xs text-error shrink-0" @click="removeTrait(trait.id)">
                        Retirer
                    </button>
                </li>
            </ul>
        </div>
        <p v-else class="text-sm text-base-content/50 italic">Aucun trait lié.</p>

        <div class="space-y-2">
            <InputField v-model="query" label="Ajouter un trait" placeholder="Rechercher ou créer un trait…" size="sm" />
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
                    @mousedown.prevent="addTrait(opt.id)"
                >
                    <span class="font-medium">{{ opt.name || `#${opt.id}` }}</span>
                    <span v-if="opt.description" class="text-base-content/55 text-xs ml-1.5">{{ opt.description }}</span>
                </button>
                <button
                    type="button"
                    class="w-full text-left px-2.5 py-2 hover:bg-base-200 text-primary font-medium"
                    :disabled="creating"
                    @mousedown.prevent="createFromQuery"
                >
                    <span v-if="creating">Création…</span>
                    <span v-else>Créer « {{ query.trim() }} »</span>
                </button>
            </div>
        </div>

        <div class="flex justify-end border-t border-base-300 pt-2">
            <EditActionDock
                primary-label="Enregistrer les traits"
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
