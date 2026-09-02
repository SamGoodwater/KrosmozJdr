<script setup>
/**
 * Édition du catalogue de téléchargements : filtre optionnel de groupes.
 *
 * @example
 * <SectionDownloadCatalogEdit :section="section" :settings="settings" />
 */
import { nextTick, ref, watch } from "vue";
import InlineSaveStatus from "@/Pages/Atoms/feedback/InlineSaveStatus.vue";
import { useSectionSave } from "../../composables/useSectionSave";

const props = defineProps({
    section: { type: Object, required: true },
    data: { type: Object, default: () => ({}) },
    settings: { type: Object, default: () => ({}) },
});

const { saveSection } = useSectionSave();
const syncFromProps = ref(false);
const lastPersistSignature = ref("");
const saveState = ref("idle");
let saveStateTimer = null;

const groupsText = ref("");

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

function groupsFromSettings(settings) {
    const raw = settings?.groups;
    if (Array.isArray(raw)) {
        return raw.map((g) => String(g).trim()).filter(Boolean).join(", ");
    }
    return "";
}

function normalizeForPersist() {
    const groups = groupsText.value
        .split(",")
        .map((g) => g.trim())
        .filter(Boolean);
    return { groups };
}

function persist() {
    const sectionId = props.section?.id;
    if (!sectionId || syncFromProps.value) {
        return;
    }
    const normalized = normalizeForPersist();
    const signature = JSON.stringify(normalized);
    if (signature === lastPersistSignature.value) {
        return;
    }
    lastPersistSignature.value = signature;
    setSaveState("saving");
    saveSection(
        sectionId,
        {
            settings: {
                ...props.settings,
                ...normalized,
            },
        },
        {
            onSuccess: () => setSaveState("saved"),
            onError: () => setSaveState("error"),
        },
    );
}

watch(
    () => props.settings,
    async (settings) => {
        syncFromProps.value = true;
        groupsText.value = groupsFromSettings(settings);
        lastPersistSignature.value = JSON.stringify(normalizeForPersist());
        await nextTick();
        syncFromProps.value = false;
    },
    { immediate: true, deep: true },
);
</script>

<template>
    <div class="space-y-3">
        <label class="form-control w-full max-w-lg">
            <span class="label-text">Groupes à afficher (vide = tous)</span>
            <input
                v-model="groupsText"
                type="text"
                class="input input-bordered"
                placeholder="regles, fiches, identite"
                @change="persist"
                @blur="persist"
            />
            <span class="label-text-alt opacity-70">
                Clés séparées par des virgules, telles que définies dans le catalogue.
            </span>
        </label>
        <InlineSaveStatus :state="saveState" />
    </div>
</template>
