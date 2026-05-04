<script setup>
/**
 * Synchronise les langues d’une classe ou d’un monstre (ordre = ordre de la liste).
 */
import { ref, computed, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import EditActionDock from "@/Pages/Molecules/action/EditActionDock.vue";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import LanguageChip from "@/Pages/Molecules/entity/language/LanguageChip.vue";
import { warnDev } from "@/Utils/dev-logger";

const props = defineProps({
    relations: {
        type: Array,
        default: () => [],
    },
    availableItems: {
        type: Array,
        default: () => [],
    },
    entityId: {
        type: Number,
        required: true,
    },
    /** breed | monster */
    entityType: {
        type: String,
        required: true,
        validator: (v) => ["breed", "monster"].includes(v),
    },
});

const notificationStore = useNotificationStore();

const localIds = ref(
    props.relations.map((r) => Number(r.id)).filter((n) => Number.isFinite(n))
);

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
    for (const a of props.availableItems) {
        const id = Number(a.id);
        if (!m.has(id)) m.set(id, a);
    }
    return m;
});

const linkedLanguages = computed(() =>
    localIds.value.map((id) => byId.value.get(id)).filter(Boolean)
);

const query = ref("");

const availableSorted = computed(() => {
    const list = [...props.availableItems];
    list.sort((a, b) => String(a.name || "").localeCompare(String(b.name || "")));
    return list;
});

const filteredToAdd = computed(() => {
    const q = query.value.trim().toLowerCase();
    const idSet = new Set(localIds.value);
    return availableSorted.value.filter((c) => {
        if (idSet.has(Number(c.id))) return false;
        if (!q) return true;
        const name = String(c.name || "").toLowerCase();
        const desc = String(c.description || "").toLowerCase();
        return name.includes(q) || desc.includes(q);
    });
});

const addId = (id) => {
    const n = Number(id);
    if (!Number.isFinite(n) || localIds.value.includes(n)) return;
    localIds.value = [...localIds.value, n];
    query.value = "";
    notificationStore.success("Langue ajoutée à la liste.", { duration: 2000, placement: "top-right" });
};

const removeId = (id) => {
    const n = Number(id);
    localIds.value = localIds.value.filter((x) => x !== n);
};

const origIds = computed(() => [...props.relations].map((r) => Number(r.id)).sort((a, b) => a - b));
const hasUnsavedChanges = computed(() => {
    const loc = [...localIds.value].sort((a, b) => a - b);
    return JSON.stringify(origIds.value) !== JSON.stringify(loc);
});

const form = useForm({ languages: [] });

const save = () => {
    form.languages = [...localIds.value];
    const routeName =
        props.entityType === "breed"
            ? "entities.breeds.updateLanguages"
            : "entities.monsters.updateLanguages";
    const params =
        props.entityType === "breed"
            ? { breed: props.entityId }
            : { monster: props.entityId };
    form.patch(route(routeName, params), {
        preserveScroll: true,
        onSuccess: () => {
            notificationStore.success("Langues mises à jour.", {
                duration: 3000,
                placement: "top-right",
            });
        },
        onError: (errors) => {
            notificationStore.error("Erreur lors de la mise à jour des langues.", {
                duration: 5000,
                placement: "top-center",
            });
            warnDev("[EntityLanguagesEditor] erreurs", errors);
        },
    });
};
</script>

<template>
    <Container class="space-y-4">
        <div>
            <h3 class="text-lg font-semibold">Langues</h3>
            <p class="text-sm text-base-content/70 mt-1 max-w-3xl">
                Langues parlées ou comprises (référentiel admin). L’ordre dans la liste définit l’ordre d’affichage.
            </p>
        </div>

        <ul v-if="linkedLanguages.length" class="space-y-2 rounded-lg border border-base-300/60 bg-base-100/30 p-3">
            <li
                v-for="lang in linkedLanguages"
                :key="lang.id"
                class="flex flex-wrap items-center justify-between gap-2 border-b border-base-300/40 pb-2 last:border-0 last:pb-0"
            >
                <LanguageChip :language="lang" />
                <button type="button" class="btn btn-ghost btn-xs text-error shrink-0" @click="removeId(lang.id)">
                    Retirer
                </button>
            </li>
        </ul>
        <p v-else class="text-sm text-base-content/50 italic">Aucune langue liée.</p>

        <div class="space-y-2">
            <InputField v-model="query" label="Ajouter une langue" placeholder="Filtrer par nom…" size="sm" />
            <div
                v-if="query.trim() && filteredToAdd.length > 0"
                class="max-h-40 overflow-y-auto rounded border border-base-300/80 bg-base-100/80 text-[13px]"
            >
                <button
                    v-for="opt in filteredToAdd.slice(0, 20)"
                    :key="opt.id"
                    type="button"
                    class="w-full text-left px-2.5 py-1.5 hover:bg-base-200 border-b border-base-200/80 last:border-0"
                    @mousedown.prevent="addId(opt.id)"
                >
                    <span class="font-medium">{{ opt.name || `#${opt.id}` }}</span>
                </button>
            </div>
        </div>

        <div class="flex justify-end border-t border-base-300 pt-2">
            <EditActionDock
                primary-label="Enregistrer les langues"
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
