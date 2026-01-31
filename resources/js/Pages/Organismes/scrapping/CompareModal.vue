<script setup>
/**
 * CompareModal — Comparaison Krosmoz vs DofusDB avec choix par propriété.
 *
 * @description
 * Affiche l'existant (Krosmoz) et les données récupérables (DofusDB), permet de choisir
 * pour chaque propriété : garder Krosmoz ou remplacer par DofusDB, puis importer avec ces choix.
 *
 * @props {String} entityType — Type d'entité (monster, spell, class, resource, consumable, equipment)
 * @props {Number} dofusdbId — ID DofusDB de l'entité
 * @props {Boolean} open — Contrôle l'ouverture du modal
 *
 * @emits close — Fermeture du modal
 * @emits imported — Import réussi (payload)
 */
import { ref, computed, watch } from "vue";
import Modal from "@/Pages/Molecules/action/Modal.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Loading from "@/Pages/Atoms/feedback/Loading.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";

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

function flattenObject(obj, prefix = "") {
    const result = {};
    if (!obj || typeof obj !== "object") return result;
    Object.keys(obj).forEach((key) => {
        const value = obj[key];
        const newKey = prefix ? `${prefix}.${key}` : key;
        if (value !== null && typeof value === "object" && !Array.isArray(value)) {
            Object.assign(result, flattenObject(value, newKey));
        } else if (Array.isArray(value)) {
            value.forEach((item, index) => {
                Object.assign(result, flattenObject(item, `${newKey}[${index}]`));
            });
        } else {
            result[newKey] = value;
        }
    });
    return result;
}

const existingRecord = computed(() => {
    const ex = preview.value?.existing;
    if (!ex || !ex.record) return null;
    return ex.record;
});

const convertedData = computed(() => preview.value?.converted ?? {});

const existingFlat = computed(() =>
    existingRecord.value ? flattenObject(existingRecord.value) : {}
);
const convertedFlat = computed(() => flattenObject(convertedData.value));

const allKeys = computed(() => {
    const a = new Set([...Object.keys(existingFlat.value), ...Object.keys(convertedFlat.value)]);
    return Array.from(a).sort();
});

const rows = computed(() => {
    return allKeys.value.map((key) => {
        const krosmozVal = existingFlat.value[key];
        const dofusdbVal = convertedFlat.value[key];
        const krosmozStr =
            krosmozVal === undefined || krosmozVal === null
                ? "(vide)"
                : String(krosmozVal);
        const dofusdbStr =
            dofusdbVal === undefined || dofusdbVal === null
                ? "(vide)"
                : String(dofusdbVal);
        const differs = krosmozStr !== dofusdbStr;
        return {
            key,
            krosmozStr,
            dofusdbStr,
            differs,
            choice: choices.value[key] ?? (existingRecord.value ? "krosmoz" : "dofusdb"),
        };
    });
});

const hasExisting = computed(() => !!existingRecord.value);

function setChoice(key, value) {
    choices.value = { ...choices.value, [key]: value };
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
            const flat = flattenObject(preview.value.existing.record);
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
                    Comparer Krosmoz / DofusDB
                    <span v-if="entityType && dofusdbId" class="font-normal text-primary-300 ml-2">
                        {{ entityType }} #{{ dofusdbId }}
                    </span>
                </div>
                <Btn size="sm" variant="ghost" @click="emit('close')">Fermer</Btn>
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
                    Aucune entrée existante en base. L’import créera une nouvelle entrée à partir de DofusDB.
                </p>
                <p v-else class="text-sm text-primary-200">
                    Choisis pour chaque propriété : <strong>Garder Krosmoz</strong> ou <strong>Remplacer par DofusDB</strong>.
                </p>

                <div class="overflow-x-auto rounded-lg border border-base-300">
                    <table class="table table-zebra text-xs w-full">
                        <thead>
                            <tr class="text-primary-200 bg-base-200">
                                <th class="font-semibold w-48">Champ</th>
                                <th class="font-semibold">Krosmoz (actuel)</th>
                                <th class="font-semibold">DofusDB (API)</th>
                                <th v-if="hasExisting" class="font-semibold w-52">Choix</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in rows" :key="row.key">
                                <td class="font-mono text-primary-100">{{ row.key }}</td>
                                <td class="break-all text-primary-300 max-w-xs">{{ row.krosmozStr }}</td>
                                <td class="break-all text-primary-100 max-w-xs">{{ row.dofusdbStr }}</td>
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
                                            <span class="text-xs">Krosmoz</span>
                                        </label>
                                        <label class="label cursor-pointer gap-1 p-0">
                                            <input
                                                type="radio"
                                                :name="`choice-${row.key}`"
                                                :checked="row.choice === 'dofusdb'"
                                                class="radio radio-sm"
                                                @change="setChoice(row.key, 'dofusdb')"
                                            />
                                            <span class="text-xs">DofusDB</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
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
                    {{ hasExisting ? "Importer avec ces choix" : "Importer" }}
                </Btn>
            </div>
        </template>
    </Modal>
</template>
