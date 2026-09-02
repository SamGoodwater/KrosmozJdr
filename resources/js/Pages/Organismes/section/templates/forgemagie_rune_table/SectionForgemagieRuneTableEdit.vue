<script setup>
/**
 * Édition du tableau des runes de forgemagie : le contenu vient du référentiel,
 * seuls le tri et l'affichage du prix de base se règlent ici.
 *
 * @example
 * <SectionForgemagieRuneTableEdit :section="section" :settings="settings" />
 */
import { nextTick, ref, watch } from "vue";
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
    sort_by: "name",
    sort_dir: "asc",
    show_base_price: false,
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
        sort_by: String(localSettings.value.sort_by || "name"),
        sort_dir: String(localSettings.value.sort_dir || "asc"),
        show_base_price: Boolean(localSettings.value.show_base_price),
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
            sort_by: s.sort_by ?? "name",
            sort_dir: s.sort_dir ?? "asc",
            show_base_price: Boolean(s.show_base_price ?? false),
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

        <div class="alert alert-info">
            <i class="fa-solid fa-circle-info" />
            <span>
                Les lignes viennent des caractéristiques objet forgemageables. Pour ajouter ou
                retirer une rune, modifie la caractéristique correspondante (bonus maximum de
                forgemagie, prix de rune, types d’équipement autorisés).
            </span>
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div>
                <label class="label"><span class="label-text">Trier par</span></label>
                <select v-model="localSettings.sort_by" class="select select-bordered select-sm w-full">
                    <option value="name">Nom de la rune</option>
                    <option value="rune_price">Prix de la rune</option>
                    <option value="max_bonus">Bonus maximum</option>
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

        <label class="label cursor-pointer justify-start gap-2">
            <input v-model="localSettings.show_base_price" type="checkbox" class="checkbox checkbox-sm" />
            <span class="label-text">Afficher aussi le prix de la caractéristique sur équipement</span>
        </label>
    </div>
</template>
