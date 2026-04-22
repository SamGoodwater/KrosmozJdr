<script setup>
import { ref, watch, nextTick } from "vue";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import InlineSaveStatus from "@/Pages/Atoms/feedback/InlineSaveStatus.vue";
import { useSectionSave } from "../../composables/useSectionSave";

const props = defineProps({
    section: { type: Object, required: true },
    data: { type: Object, default: () => ({}) },
    settings: { type: Object, default: () => ({}) },
});

const emit = defineEmits(["data-updated"]);
const { saveSection } = useSectionSave();

const syncFromProps = ref(false);
const lastPersistSignature = ref("");
const saveState = ref("idle");
let saveStateTimer = null;

const localSettings = ref({
    group: "all",
    entity: "*",
    search: "",
    sort_by: "group",
    sort_dir: "asc",
    status_filter: "all",
    show_prices: true,
    show_only_with_equipment: false,
});

function setSaveState(state) {
    saveState.value = state;
    if (saveStateTimer) {
        clearTimeout(saveStateTimer);
        saveStateTimer = null;
    }
    if (state === "saved") {
        saveStateTimer = setTimeout(() => {
            saveState.value = "idle";
        }, 1600);
    }
}

function normalizeForPersist() {
    return {
        group: String(localSettings.value.group || "all"),
        entity: String(localSettings.value.entity || "*").trim() || "*",
        search: String(localSettings.value.search || "").trim(),
        sort_by: String(localSettings.value.sort_by || "group"),
        sort_dir: String(localSettings.value.sort_dir || "asc"),
        status_filter: String(localSettings.value.status_filter || "all"),
        show_prices: Boolean(localSettings.value.show_prices),
        show_only_with_equipment: Boolean(localSettings.value.show_only_with_equipment),
    };
}

function safeStringify(obj) {
    try {
        return JSON.stringify(obj);
    } catch {
        return "";
    }
}

function persist() {
    const sectionId = props.section?.id;
    if (!sectionId || syncFromProps.value) {
        return;
    }

    const normalized = normalizeForPersist();
    const signature = safeStringify(normalized);
    if (signature === lastPersistSignature.value) {
        return;
    }
    lastPersistSignature.value = signature;

    saveSection(
        sectionId,
        {
            settings: {
                ...props.settings,
                ...normalized,
            },
        },
        {
            onQueued: () => setSaveState("saving"),
            onSuccess: () => {
                setSaveState("saved");
                emit("data-updated");
            },
            onError: () => setSaveState("error"),
        },
    );
}

watch(
    () => props.settings,
    async (s) => {
        if (!s) return;
        syncFromProps.value = true;
        localSettings.value = {
            group: s.group ?? "all",
            entity: s.entity ?? "*",
            search: s.search ?? "",
            sort_by: s.sort_by ?? "group",
            sort_dir: s.sort_dir ?? "asc",
            status_filter: s.status_filter ?? "all",
            show_prices: Boolean(s.show_prices ?? true),
            show_only_with_equipment: Boolean(s.show_only_with_equipment ?? false),
        };
        await nextTick();
        lastPersistSignature.value = safeStringify(normalizeForPersist());
        syncFromProps.value = false;
    },
    { deep: true, immediate: true },
);

watch(localSettings, () => persist(), { deep: true });
</script>

<template>
    <div class="space-y-4">
        <div class="flex justify-end">
            <InlineSaveStatus :state="saveState" />
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div>
                <label class="label"><span class="label-text">Groupe</span></label>
                <select v-model="localSettings.group" class="select select-bordered select-sm w-full">
                    <option value="all">Tous</option>
                    <option value="creature">Créature</option>
                    <option value="object">Objet</option>
                    <option value="spell">Sort</option>
                </select>
            </div>
            <InputField
                label="Entité"
                v-model="localSettings.entity"
                placeholder="*"
                helper="* = toutes les entités du groupe."
            />
        </div>

        <InputField
            label="Recherche initiale"
            v-model="localSettings.search"
            placeholder="Ex: pa, dégâts, armor..."
            helper="Filtre appliqué au chargement (clé, nom, groupe, entité)."
        />

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div>
                <label class="label"><span class="label-text">Trier par</span></label>
                <select v-model="localSettings.sort_by" class="select select-bordered select-sm w-full">
                    <option value="group">Groupe</option>
                    <option value="entity">Entité</option>
                    <option value="name">Nom</option>
                    <option value="key">Clé</option>
                    <option value="equipment_max_bonus">Bonus max équipement</option>
                    <option value="forgemagie_max">Bonus max FM</option>
                </select>
            </div>
            <div>
                <label class="label"><span class="label-text">Ordre</span></label>
                <select v-model="localSettings.sort_dir" class="select select-bordered select-sm w-full">
                    <option value="asc">Ascendant</option>
                    <option value="desc">Descendant</option>
                </select>
            </div>
        </div>

        <div>
            <label class="label"><span class="label-text">Filtre statut</span></label>
            <select v-model="localSettings.status_filter" class="select select-bordered select-sm w-full">
                <option value="all">Tous</option>
                <option value="a_valider">À valider</option>
                <option value="en_cours_de_validation">En cours de validation</option>
                <option value="validee">Validée</option>
            </select>
        </div>

        <label class="label cursor-pointer justify-start gap-2">
            <input v-model="localSettings.show_prices" type="checkbox" class="checkbox checkbox-sm" />
            <span class="label-text">Afficher les colonnes de prix indicatifs</span>
        </label>

        <label class="label cursor-pointer justify-start gap-2">
            <input v-model="localSettings.show_only_with_equipment" type="checkbox" class="checkbox checkbox-sm" />
            <span class="label-text">Limiter aux lignes avec bonus équipement ou FM</span>
        </label>
    </div>
</template>

