<script setup>
/**
 * Capacités liées à une classe (liste plate, pas d’emplacement).
 * Ajout via EntityPicker / api.tables.capabilities.
 */
import { ref, computed, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import { Capability } from "@/Models/Entity/Capability";
import EditActionDock from "@/Pages/Molecules/action/EditActionDock.vue";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import CapabilityViewText from "@/Pages/Molecules/entity/capability/CapabilityViewText.vue";
import EntityPickerCore from "@/Pages/Organismes/entity/EntityPickerCore.vue";
import { warnDev } from "@/Utils/dev-logger";

const props = defineProps({
    relations: {
        type: Array,
        default: () => [],
    },
    /** @deprecated Seed local optionnel — recherche via EntityPicker. */
    availableItems: {
        type: Array,
        default: () => [],
    },
    entityId: {
        type: Number,
        required: true,
    },
});

const notificationStore = useNotificationStore();

const localIds = ref(props.relations.map((r) => Number(r.id)).filter((n) => Number.isFinite(n)));
const pickedExtras = ref([]);
const pickerValue = ref(null);

watch(
    () => props.relations,
    (next) => {
        localIds.value = next.map((r) => Number(r.id)).filter((n) => Number.isFinite(n));
    },
    { deep: true }
);

const byId = computed(() => {
    const m = new Map();
    for (const r of props.relations) {
        m.set(Number(r.id), r);
    }
    for (const a of props.availableItems || []) {
        const id = Number(a.id);
        if (!m.has(id)) m.set(id, a);
    }
    for (const a of pickedExtras.value) {
        const id = Number(a.id);
        if (!m.has(id)) m.set(id, a);
    }
    return m;
});

const linkedCapabilities = computed(() =>
    localIds.value
        .map((id) => byId.value.get(id))
        .filter(Boolean)
);

const asCapabilityModel = (raw) => (raw instanceof Capability ? raw : new Capability(raw));

const isPassiveCapability = (raw) => asCapabilityModel(raw).isPassive;

const normalizePicked = (raw) => {
    if (!raw || typeof raw !== "object") return null;
    const data = raw._data && typeof raw._data === "object" ? { ...raw._data, ...raw } : raw;
    const id = Number(data.id);
    if (!Number.isFinite(id)) return null;
    return { ...data, id, name: data.name ?? `#${id}` };
};

const addEntity = (raw) => {
    const entity = normalizePicked(raw);
    if (!entity) return;
    if (localIds.value.includes(entity.id)) return;
    pickedExtras.value = [...pickedExtras.value, entity];
    localIds.value = [...localIds.value, entity.id];
    pickerValue.value = null;
    notificationStore.success("Capacité ajoutée à la liste.", { duration: 2000, placement: "top-right" });
};

const onPickerSelected = (entities) => {
    const entity = Array.isArray(entities) ? entities[0] : null;
    if (!entity) return;
    addEntity(entity);
};

const removeId = (id) => {
    const n = Number(id);
    localIds.value = localIds.value.filter((x) => x !== n);
};

const origIds = computed(() =>
    [...props.relations].map((r) => Number(r.id)).sort((a, b) => a - b)
);
const hasUnsavedChanges = computed(() => {
    const loc = [...localIds.value].sort((a, b) => a - b);
    return JSON.stringify(origIds.value) !== JSON.stringify(loc);
});

const form = useForm({ capabilities: [] });

const save = () => {
    form.capabilities = [...localIds.value];
    form.patch(route("entities.breeds.updateCapabilities", { breed: props.entityId }), {
        preserveScroll: true,
        onSuccess: () => {
            notificationStore.success("Capacités de la classe mises à jour.", {
                duration: 3000,
                placement: "top-right",
            });
        },
        onError: (errors) => {
            notificationStore.error("Erreur lors de la mise à jour des capacités.", {
                duration: 5000,
                placement: "top-center",
            });
            warnDev("[BreedCapabilitiesEditor] erreurs", errors);
        },
    });
};
</script>

<template>
    <Container class="space-y-4">
        <div>
            <h3 class="text-lg font-semibold">Capacités de classe</h3>
            <p class="text-sm text-base-content/70 max-w-3xl mt-1">
                Capacités supplémentaires sans emplacement (en plus des sorts). Recherche via le catalogue capacités.
            </p>
        </div>

        <ul v-if="linkedCapabilities.length" class="space-y-2 rounded-lg border border-base-300/60 bg-base-100/30 p-3">
            <li
                v-for="c in linkedCapabilities"
                :key="c.id"
                class="flex flex-wrap items-center justify-between gap-2 border-b border-base-300/40 pb-2 last:border-0 last:pb-0"
            >
                <div class="min-w-0 flex-1 text-sm flex flex-wrap items-center gap-2">
                    <CapabilityViewText :capability="asCapabilityModel(c)" />
                    <span
                        v-if="isPassiveCapability(c)"
                        class="badge badge-sm badge-info shrink-0"
                    >
                        Passif
                    </span>
                </div>
                <button
                    type="button"
                    class="btn btn-ghost btn-xs text-error shrink-0"
                    @click="removeId(c.id)"
                >
                    Retirer
                </button>
            </li>
        </ul>
        <p v-else class="text-sm text-base-content/50 italic">Aucune capacité liée.</p>

        <div class="space-y-2">
            <p class="text-sm font-medium">Ajouter une capacité</p>
            <EntityPickerCore
                :model-value="pickerValue"
                entity-type="capabilities"
                :multiple="false"
                variant="extended"
                :blacklist="localIds"
                placeholder="Rechercher une capacité…"
                size="sm"
                @update:model-value="pickerValue = $event"
                @update:selected-entities="onPickerSelected"
            />
        </div>

        <div class="flex justify-end border-t border-base-300 pt-2">
            <EditActionDock
                primary-label="Enregistrer les capacités"
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
