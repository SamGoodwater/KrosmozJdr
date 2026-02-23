<script setup>
/**
 * CompareModal — Comparaison Brut / Converti / Krosmoz avec choix par propriété.
 *
 * @description
 * Affiche les 3 formats : données brutes (DofusDB), converties, et KrosmozJDR si existant.
 * Pour chaque propriété : choisir Existant (Krosmoz) ou Converti (nouveau), puis importer.
 * Ouverture : double-clic sur une ligne du tableau des résultats.
 *
 * @props {String} entityType — Type d'entité (monster, spell, class, resource, consumable, equipment)
 * @props {Number} dofusdbId — ID DofusDB de l'entité
 * @props {Boolean} open — Contrôle l'ouverture du modal
 * @emits close — Fermeture du modal
 * @emits imported — Import réussi (payload)
 */
import { ref, computed, watch } from "vue";
import Modal from "@/Pages/Molecules/action/Modal.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Loading from "@/Pages/Atoms/feedback/Loading.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import { getFieldLabel, getSectionFromFlatKey } from "@/Pages/Pages/scrapping/components/previewDiffLabels";

const props = defineProps({
    entityType: { type: String, default: "" },
    dofusdbId: { type: Number, default: null },
    open: { type: Boolean, default: false },
});

const emit = defineEmits(["close", "imported"]);

const loading = ref(false);
const preview = ref(null);
const error = ref(null);
const choices = ref({}); // { "field.path": "krosmoz" | "dofusdb" }
const importing = ref(false);

/** Aplatit au plus 2 niveaux (section.clé) pour ne garder que les propriétés du modèle. */
function flattenShallow(obj, prefix = "") {
    if (!obj || typeof obj !== "object") return {};
    const result = {};
    Object.keys(obj).forEach((key) => {
        const value = obj[key];
        const newKey = prefix ? `${prefix}.${key}` : key;
        if (value !== null && typeof value === "object" && !Array.isArray(value)) {
            Object.keys(value).forEach((k2) => {
                const v2 = value[k2];
                const key2 = `${newKey}.${k2}`;
                if (v2 !== null && typeof v2 === "object" && (Array.isArray(v2) || typeof v2 === "object")) {
                    result[key2] = Array.isArray(v2) ? `[${v2.length} élément(s)]` : `{${Object.keys(v2).length} clé(s)}`;
                } else {
                    result[key2] = v2;
                }
            });
        } else if (Array.isArray(value)) {
            result[newKey] = `[${value.length} élément(s)]`;
        } else {
            result[newKey] = value;
        }
    });
    return result;
}

function findInFlat(flat, modelKey) {
    if (flat[modelKey] !== undefined) return flat[modelKey];
    const suffix = `.${modelKey}`;
    const found = Object.keys(flat).find((k) => k === modelKey || k.endsWith(suffix));
    return found !== undefined ? flat[found] : undefined;
}

const existingRecord = computed(() => {
    const ex = preview.value?.existing;
    if (!ex || !ex.record) return null;
    return ex.record;
});

const rawData = computed(() => preview.value?.raw ?? {});
const convertedData = computed(() => preview.value?.converted ?? {});

const rawFlat = computed(() => flattenShallow(rawData.value));
const existingFlat = computed(() =>
    existingRecord.value ? flattenShallow(existingRecord.value) : {}
);
const convertedFlat = computed(() => flattenShallow(convertedData.value));

/** Clés à afficher : union Brut, Converti, Krosmoz. */
const allKeys = computed(() => {
    const keys = new Set([
        ...Object.keys(rawFlat.value),
        ...Object.keys(convertedFlat.value),
        ...Object.keys(existingFlat.value),
    ]);
    return [...keys].sort();
});

const rows = computed(() => {
    return allKeys.value.map((key) => {
        const rawVal = findInFlat(rawFlat.value, key) ?? findInFlat(rawFlat.value, key.split(".").pop());
        const convertedVal = findInFlat(convertedFlat.value, key) ?? findInFlat(convertedFlat.value, key.split(".").pop());
        const krosmozVal = existingFlat.value[key];
        const rawStr = rawVal === undefined || rawVal === null ? "—" : String(rawVal);
        const convertedStr = convertedVal === undefined || convertedVal === null ? "—" : String(convertedVal);
        const krosmozStr = krosmozVal === undefined || krosmozVal === null ? "—" : String(krosmozVal);
        return {
            key,
            label: getFieldLabel(key),
            section: getSectionFromFlatKey(key),
            rawStr,
            convertedStr,
            krosmozStr,
            choice: choices.value[key] ?? (existingRecord.value ? "krosmoz" : "dofusdb"),
        };
    });
});

const rowsBySection = computed(() => {
    const map = {};
    for (const row of rows.value) {
        const sec = row.section || "Autres";
        if (!map[sec]) map[sec] = [];
        map[sec].push(row);
    }
    return Object.entries(map).sort((a, b) => a[0].localeCompare(b[0]));
});

const hasExisting = computed(() => !!existingRecord.value);

function setChoice(key, value) {
    choices.value = { ...choices.value, [key]: value };
}

function setAllChoices(value) {
    const next = {};
    allKeys.value.forEach((k) => { next[k] = value; });
    choices.value = next;
}

async function fetchPreview() {
    if (!props.entityType || !props.dofusdbId) return;
    loading.value = true;
    error.value = null;
    preview.value = null;
    choices.value = {};
    try {
        const res = await fetch(
            `/api/scrapping/preview/${props.entityType}/${props.dofusdbId}`,
            { headers: { Accept: "application/json" } }
        );
        const json = await res.json();
        if (!res.ok || !json?.success) {
            throw new Error(json?.message || "Prévisualisation impossible");
        }
        preview.value = json.data ?? null;
        if (preview.value?.existing?.record) {
            const flat = flattenShallow(preview.value.existing.record);
            const defaults = {};
            Object.keys(flat).forEach((k) => {
                defaults[k] = "krosmoz";
            });
            choices.value = { ...defaults };
        }
    } catch (e) {
        error.value = e.message || "Erreur chargement";
    } finally {
        loading.value = false;
    }
}

async function importWithChoices() {
    if (!props.entityType || !props.dofusdbId) return;
    importing.value = true;
    error.value = null;
    try {
        const body = {
            type: props.entityType,
            dofusdb_id: props.dofusdbId,
            choices: hasExisting.value ? choices.value : {},
        };
        const res = await fetch("/api/scrapping/import-with-merge", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
            },
            body: JSON.stringify(body),
        });
        const json = await res.json();
        if (!res.ok || !json?.success) {
            throw new Error(json?.message || "Import impossible");
        }
        emit("imported", json);
        emit("close");
    } catch (e) {
        error.value = e.message || "Erreur import";
    } finally {
        importing.value = false;
    }
}

watch(
    () => [props.open, props.entityType, props.dofusdbId],
    () => {
        if (props.open && props.entityType && props.dofusdbId) {
            fetchPreview();
        } else if (!props.open) {
            preview.value = null;
            error.value = null;
            choices.value = {};
        }
    },
    { immediate: true }
);
</script>

<template>
    <Modal
        :open="open"
        size="xl"
        placement="middle-center"
        close-on-esc
        @close="emit('close')"
    >
        <template #header>
            <div class="flex items-center justify-between gap-3 w-full">
                <div class="font-semibold text-primary-100">
                    Comparaison — Brut / Converti / Krosmoz
                    <span v-if="entityType && dofusdbId" class="font-normal text-primary-300 ml-2">
                        {{ entityType }} #{{ dofusdbId }}
                    </span>
                </div>
                <Btn size="sm" variant="ghost" @click="emit('close')"></Btn>
            </div>
        </template>

        <div class="space-y-4 max-h-[70vh] overflow-y-auto">
            <div v-if="loading" class="flex items-center gap-3 text-primary-300 py-8">
                <Loading />
                <span>Chargement de la prévisualisation…</span>
            </div>

            <p v-else-if="error" class="text-error text-sm">{{ error }}</p>

            <template v-else-if="preview">
                <p v-if="!hasExisting" class="text-sm text-primary-300 italic">
                    Aucune entrée existante en base. L’import créera une nouvelle entrée à partir des données converties.
                </p>
                <p v-else class="text-sm text-primary-200">
                    Pour chaque propriété, choisis <strong>Existant</strong> (Krosmoz) ou <strong>Converti</strong> (nouveau). Puis importer.
                </p>

                <div v-if="hasExisting" class="flex flex-wrap gap-2">
                    <Btn size="sm" variant="outline" @click="setAllChoices('krosmoz')">
                        Tout l'existant (Krosmoz)
                    </Btn>
                    <Btn size="sm" variant="outline" @click="setAllChoices('dofusdb')">
                        Tout le converti (nouveau)
                    </Btn>
                </div>

                <div class="overflow-x-auto rounded-lg border border-base-300">
                    <table class="table table-zebra text-xs w-full">
                        <thead>
                            <tr class="text-primary-200 bg-base-200">
                                <th class="font-semibold w-40">Champ</th>
                                <th class="font-semibold max-w-[200px]">Brut (DofusDB)</th>
                                <th class="font-semibold max-w-[200px]">Converti</th>
                                <th class="font-semibold max-w-[200px]">Krosmoz (existant)</th>
                                <th v-if="hasExisting" class="font-semibold w-48">Choix</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="[sectionName, sectionRows] in rowsBySection" :key="sectionName">
                                <tr class="bg-base-300/60">
                                    <td :colspan="hasExisting ? 5 : 4" class="font-semibold text-primary-200 text-[11px] uppercase tracking-wide py-1.5 px-2">
                                        {{ sectionName }}
                                    </td>
                                </tr>
                                <tr v-for="row in sectionRows" :key="row.key">
                                    <td class="font-semibold text-primary-100 pl-3">{{ row.label }}</td>
                                    <td class="break-all text-primary-400 max-w-[200px]">{{ row.rawStr }}</td>
                                    <td class="break-all text-primary-100 max-w-[200px]">{{ row.convertedStr }}</td>
                                    <td class="break-all text-primary-300 max-w-[200px]">{{ row.krosmozStr }}</td>
                                    <td v-if="hasExisting" class="align-middle">
                                        <div class="flex flex-wrap gap-2">
                                            <label class="label cursor-pointer gap-1 p-0">
                                                <input
                                                    type="radio"
                                                    :name="`choice-${row.key}`"
                                                    :checked="row.choice === 'krosmoz'"
                                                    class="radio radio-sm"
                                                    @change="setChoice(row.key, 'krosmoz')"
                                                />
                                                <span class="text-xs">Existant</span>
                                            </label>
                                            <label class="label cursor-pointer gap-1 p-0">
                                                <input
                                                    type="radio"
                                                    :name="`choice-${row.key}`"
                                                    :checked="row.choice === 'dofusdb'"
                                                    class="radio radio-sm"
                                                    @change="setChoice(row.key, 'dofusdb')"
                                                />
                                                <span class="text-xs">Converti</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </template>
        </div>

        <template #actions>
            <div class="flex items-center gap-2">
                <Btn variant="ghost" @click="emit('close')">Annuler</Btn>
                <Btn
                    color="success"
                    :disabled="!preview || importing"
                    @click="importWithChoices"
                >
                    <Loading v-if="importing" class="mr-2" />
                    <Icon v-else source="fa-solid fa-cloud-arrow-down" alt="" pack="solid" class="mr-2" />
                    Importer
                </Btn>
            </div>
        </template>
    </Modal>
</template>
