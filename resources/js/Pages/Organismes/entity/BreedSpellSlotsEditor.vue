<script setup>
/**
 * Édition des sorts par emplacement + bloc « hors emplacement » (pivot 0/1).
 * Affichage des sorts via SpellViewText (aperçu minimal au survol).
 */
import { ref, computed, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import SpellViewText from "@/Pages/Molecules/entity/spell/SpellViewText.vue";
import { Spell } from "@/Models/Entity/Spell";
import {
    getStandardBreedSlotDefinitions,
    isStandardSlot,
} from "@/Utils/entity/breedSpellSlots";
import {
    BREED_SPELL_EXTRA_LEVEL,
    BREED_SPELL_EXTRA_SLOT,
    isBreedExtraSpellPivot,
} from "@/Utils/entity/breedSpellExtra";
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
});

const notificationStore = useNotificationStore();

const STANDARD_MAX_ODD = 21;
const standardSlots = getStandardBreedSlotDefinitions(STANDARD_MAX_ODD);

/** Sorts liés sans emplacement de progression (hors grille). */
const extraSlotDef = Object.freeze({
    character_level: BREED_SPELL_EXTRA_LEVEL,
    slot_index: BREED_SPELL_EXTRA_SLOT,
    label: "Hors emplacement (liste libre)",
});

/**
 * @param {object} raw
 * @returns {Spell}
 */
const asSpellModel = (raw) => (raw instanceof Spell ? raw : new Spell(raw));

const localRelations = ref([...props.relations]);

const pivotDefaults = () => ({
    character_level: "1",
    slot_index: "1",
    choice_order: "0",
});

/** @type {Record<number, Record<string, string>>} */
const pivotValues = ref({});

const initializePivotValues = () => {
    const pivots = {};
    for (const item of props.relations) {
        pivots[item.id] = { ...pivotDefaults() };
        const p = item.pivot || {};
        if (p.character_level != null && p.character_level !== "") {
            pivots[item.id].character_level = String(p.character_level);
        }
        if (p.slot_index != null && p.slot_index !== "") {
            pivots[item.id].slot_index = String(p.slot_index);
        }
        if (p.choice_order != null && p.choice_order !== "") {
            pivots[item.id].choice_order = String(p.choice_order);
        }
    }
    pivotValues.value = pivots;
};

initializePivotValues();

watch(
    () => props.relations,
    (next) => {
        localRelations.value = [...next];
        initializePivotValues();
    },
    { deep: true }
);

const availableSorted = computed(() => {
    const list = [...props.availableItems];
    list.sort((a, b) => {
        const la = Number(a.level);
        const lb = Number(b.level);
        if (Number.isFinite(la) && Number.isFinite(lb) && la !== lb) {
            return la - lb;
        }
        return String(a.name || "").localeCompare(String(b.name || ""));
    });
    return list;
});

const slotKey = (def) => `${def.character_level}|${def.slot_index}`;

const spellsInSlot = (def) => {
    return localRelations.value.filter((item) => {
        const pv = pivotValues.value[item.id];
        if (!pv) return false;
        return (
            Number(pv.character_level) === def.character_level &&
            Number(pv.slot_index) === def.slot_index
        );
    });
};

/** Sorts d’un emplacement triés par ordre de choix puis nom (affichage type « un coup d’œil »). */
const sortedSpellsInSlot = (def) => {
    const list = spellsInSlot(def);
    return [...list].sort((a, b) => {
        const oa = Number(pivotValues.value[a.id]?.choice_order ?? 0);
        const ob = Number(pivotValues.value[b.id]?.choice_order ?? 0);
        if (Number.isFinite(oa) && Number.isFinite(ob) && oa !== ob) {
            return oa - ob;
        }
        return String(a.name || "").localeCompare(String(b.name || ""));
    });
};

/** Emplacements présents en données mais hors grille standard (ni extra 0/1) */
const orphanRelationItems = computed(() =>
    localRelations.value.filter((item) => {
        const pv = pivotValues.value[item.id];
        if (!pv) return false;
        if (isBreedExtraSpellPivot(pv)) return false;
        return !isStandardSlot(Number(pv.character_level), Number(pv.slot_index), standardSlots);
    })
);

const slotQueries = ref({});

const nextChoiceOrder = (def) => {
    const inSlot = spellsInSlot(def);
    let max = -1;
    for (const s of inSlot) {
        const o = Number(pivotValues.value[s.id]?.choice_order ?? 0);
        if (Number.isFinite(o)) max = Math.max(max, o);
    }
    return max + 1;
};

const isSpellInSlot = (spellId, def) => {
    return spellsInSlot(def).some((s) => s.id === spellId);
};

const filteredForSlot = (def) => {
    const q = (slotQueries.value[slotKey(def)] || "").trim().toLowerCase();
    return availableSorted.value.filter((item) => {
        if (isSpellInSlot(item.id, def)) return false;
        if (!q) return true;
        const name = String(item.name || "").toLowerCase();
        const desc = String(item.description || "").toLowerCase();
        return name.includes(q) || desc.includes(q);
    });
};

const addToSlot = (spell, def) => {
    if (isSpellInSlot(spell.id, def)) return;
    const existing = localRelations.value.find((r) => r.id === spell.id);
    if (existing) {
        pivotValues.value[spell.id] = {
            character_level: String(def.character_level),
            slot_index: String(def.slot_index),
            choice_order: String(nextChoiceOrder(def)),
        };
        slotQueries.value[slotKey(def)] = "";
        notificationStore.success(`« ${spell.name || spell.id} » déplacé vers cet emplacement.`, {
            duration: 2500,
            placement: "top-right",
        });
        return;
    }
    localRelations.value.push(spell);
    pivotValues.value[spell.id] = {
        character_level: String(def.character_level),
        slot_index: String(def.slot_index),
        choice_order: String(nextChoiceOrder(def)),
    };
    slotQueries.value[slotKey(def)] = "";
    notificationStore.success(`« ${spell.name || spell.id} » ajouté à l’emplacement.`, {
        duration: 2500,
        placement: "top-right",
    });
};

const removeItem = (spellId) => {
    localRelations.value = localRelations.value.filter((r) => r.id !== spellId);
    const next = { ...pivotValues.value };
    delete next[spellId];
    pivotValues.value = next;
};

const relationsForm = useForm({ spells: {} });

const hasUnsavedChanges = computed(() => {
    const origIds = [...props.relations].map((r) => r.id).sort((a, b) => a - b);
    const locIds = [...localRelations.value].map((r) => r.id).sort((a, b) => a - b);
    if (JSON.stringify(origIds) !== JSON.stringify(locIds)) return true;
    for (const id of locIds) {
        const cur = pivotValues.value[id];
        const orig = props.relations.find((r) => r.id === id);
        const op = orig?.pivot || {};
        const fields = ["character_level", "slot_index", "choice_order"];
        for (const f of fields) {
            const a = String(cur?.[f] ?? "");
            const b =
                op[f] !== undefined && op[f] !== null && op[f] !== ""
                    ? String(op[f])
                    : f === "choice_order"
                      ? "0"
                      : "1";
            if (a !== b) return true;
        }
    }
    return false;
});

const save = () => {
    const dataWithPivots = {};
    for (const item of localRelations.value) {
        const id = item.id;
        const pv = pivotValues.value[id] || pivotDefaults();
        const nCl = Number(pv.character_level);
        const nSi = Number(pv.slot_index);
        const nCo = Number(pv.choice_order);
        let cl = Number.isFinite(nCl) && nCl >= 0 ? Math.floor(nCl) : 1;
        let si = Number.isFinite(nSi) && nSi >= 1 ? Math.floor(nSi) : 1;
        if (cl === 0) {
            si = BREED_SPELL_EXTRA_SLOT;
        }
        dataWithPivots[id] = {
            character_level: cl,
            slot_index: si,
            choice_order: Number.isFinite(nCo) && nCo >= 0 ? Math.floor(nCo) : 0,
        };
    }
    relationsForm.spells = dataWithPivots;
    relationsForm.patch(route("entities.breeds.updateSpells", { breed: props.entityId }), {
        preserveScroll: true,
        onSuccess: () => {
            notificationStore.success("Sorts de la classe mis à jour.", {
                duration: 3000,
                placement: "top-right",
            });
        },
        onError: (errors) => {
            notificationStore.error("Erreur lors de la mise à jour des sorts.", {
                duration: 5000,
                placement: "top-center",
            });
            warnDev("[BreedSpellSlotsEditor] erreurs", errors);
        },
    });
};
</script>

<template>
    <Container class="space-y-4">
        <div class="flex flex-wrap items-start justify-between gap-2">
            <div>
                <h3 class="text-lg font-semibold">Sorts par emplacement</h3>
                <p class="text-sm text-base-content/70 max-w-3xl mt-1">
                    Au niveau 1 : trois choix de sort. Aux niveaux impairs suivants (3, 5, 7…), un sort de classe
                    supplémentaire. Ajoutez plusieurs sorts au même emplacement pour proposer des options au joueur.
                    Les sorts « hors emplacement » sont en plus de la grille (pas liés à un niveau PJ).
                </p>
            </div>
            <Badge :content="String(localRelations.length)" color="primary" />
        </div>

        <!-- Présentation « texte » : même logique visuelle que BreedSpellSlotsDisplay (densité minimal) -->
        <div class="breed-spell-slots-edit space-y-3 text-[13px] leading-snug text-base-content/90">
            <div
                v-for="def in standardSlots"
                :key="slotKey(def)"
                class="border-b border-base-300/40 pb-3 last:border-0"
            >
                <div class="flex flex-wrap items-baseline gap-x-1 gap-y-1.5">
                    <span
                        class="text-[11px] tabular-nums text-base-content/55 font-medium shrink-0"
                        :title="def.label"
                    >
                        {{ def.character_level }}/{{ def.slot_index }}
                    </span>
                    <span class="text-base-content/35 shrink-0">·</span>
                    <template v-if="sortedSpellsInSlot(def).length">
                        <template v-for="(s, si) in sortedSpellsInSlot(def)" :key="s.id">
                            <span class="inline-flex flex-wrap items-start gap-x-2 gap-y-1 max-w-full">
                                <span class="min-w-0 flex-1 spell-text-embed">
                                    <SpellViewText :spell="asSpellModel(s)" />
                                </span>
                                <span class="inline-flex items-center gap-0.5 text-[11px] text-base-content/55 shrink-0">
                                    <span class="sr-only">Ordre</span>
                                    <input
                                        v-model="pivotValues[s.id].choice_order"
                                        type="number"
                                        min="0"
                                        max="255"
                                        class="input input-bordered input-xs w-11 px-1 text-center"
                                        :aria-label="`Ordre pour ${s.name || s.id}`"
                                    />
                                </span>
                                <Tooltip content="Retirer de la classe" placement="top">
                                    <button
                                        type="button"
                                        class="btn btn-ghost btn-xs h-6 min-h-0 px-1 text-error shrink-0"
                                        @click="removeItem(s.id)"
                                    >
                                        <Icon source="fa-solid fa-times" size="xs" />
                                    </button>
                                </Tooltip>
                            </span>
                            <span
                                v-if="si < sortedSpellsInSlot(def).length - 1"
                                class="text-base-content/30 select-none"
                            >
                                ,
                            </span>
                        </template>
                    </template>
                    <span v-else class="text-[11px] text-base-content/45 italic">—</span>
                </div>

                <div class="mt-2 ml-0 sm:ml-6 sm:border-l sm:border-base-300/50 sm:pl-3 space-y-1">
                    <InputField
                        v-model="slotQueries[slotKey(def)]"
                        :label="`Ajouter (${def.character_level}/${def.slot_index})`"
                        placeholder="Filtrer par nom…"
                        size="sm"
                    />
                    <div
                        v-if="(slotQueries[slotKey(def)] || '').trim() && filteredForSlot(def).length > 0"
                        class="max-h-32 overflow-y-auto rounded border border-base-300/80 bg-base-100/80 text-[13px]"
                    >
                        <button
                            v-for="item in filteredForSlot(def).slice(0, 12)"
                            :key="item.id"
                            type="button"
                            class="w-full text-left px-2.5 py-1.5 hover:bg-base-200 border-b border-base-200/80 last:border-0"
                            @mousedown.prevent="addToSlot(item, def)"
                        >
                            <span class="font-medium">{{ item.name || `#${item.id}` }}</span>
                            <span v-if="item.level != null" class="text-base-content/55 text-xs ml-1.5">
                                nv. {{ item.level }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sorts hors emplacement (pivot 0 / 1) -->
        <div class="border border-primary/25 bg-primary/5 rounded-lg p-3 space-y-2 text-[13px]">
            <div class="flex flex-wrap items-baseline gap-x-1 gap-y-1.5">
                <span
                    class="text-[11px] tabular-nums text-primary font-medium shrink-0"
                    :title="extraSlotDef.label"
                >
                    0/1
                </span>
                <span class="text-base-content/35 shrink-0">·</span>
                <span class="text-xs text-base-content/70">{{ extraSlotDef.label }}</span>
            </div>
            <div class="flex flex-wrap items-baseline gap-x-1 gap-y-1.5">
                <template v-if="sortedSpellsInSlot(extraSlotDef).length">
                    <template v-for="(s, si) in sortedSpellsInSlot(extraSlotDef)" :key="`ex-${s.id}`">
                        <span class="inline-flex flex-wrap items-start gap-x-2 gap-y-1 max-w-full">
                            <span class="min-w-0 flex-1 spell-text-embed">
                                <SpellViewText :spell="asSpellModel(s)" />
                            </span>
                            <span class="inline-flex items-center gap-0.5 text-[11px] text-base-content/55 shrink-0">
                                <span class="sr-only">Ordre</span>
                                <input
                                    v-model="pivotValues[s.id].choice_order"
                                    type="number"
                                    min="0"
                                    max="255"
                                    class="input input-bordered input-xs w-11 px-1 text-center"
                                    :aria-label="`Ordre pour ${s.name || s.id}`"
                                />
                            </span>
                            <Tooltip content="Retirer de la classe" placement="top">
                                <button
                                    type="button"
                                    class="btn btn-ghost btn-xs h-6 min-h-0 px-1 text-error shrink-0"
                                    @click="removeItem(s.id)"
                                >
                                    <Icon source="fa-solid fa-times" size="xs" />
                                </button>
                            </Tooltip>
                        </span>
                        <span
                            v-if="si < sortedSpellsInSlot(extraSlotDef).length - 1"
                            class="text-base-content/30 select-none"
                        >
                            ,
                        </span>
                    </template>
                </template>
                <span v-else class="text-[11px] text-base-content/45 italic">—</span>
            </div>
            <div class="mt-2 space-y-1">
                <InputField
                    v-model="slotQueries[slotKey(extraSlotDef)]"
                    :label="`Ajouter (hors emplacement)`"
                    placeholder="Filtrer par nom…"
                    size="sm"
                />
                <div
                    v-if="
                        (slotQueries[slotKey(extraSlotDef)] || '').trim() &&
                        filteredForSlot(extraSlotDef).length > 0
                    "
                    class="max-h-32 overflow-y-auto rounded border border-base-300/80 bg-base-100/80 text-[13px]"
                >
                    <button
                        v-for="item in filteredForSlot(extraSlotDef).slice(0, 12)"
                        :key="`ex-add-${item.id}`"
                        type="button"
                        class="w-full text-left px-2.5 py-1.5 hover:bg-base-200 border-b border-base-200/80 last:border-0"
                        @mousedown.prevent="addToSlot(item, extraSlotDef)"
                    >
                        <span class="font-medium">{{ item.name || `#${item.id}` }}</span>
                        <span v-if="item.level != null" class="text-base-content/55 text-xs ml-1.5">
                            nv. {{ item.level }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Emplacements hors grille (données legacy ou personnalisées) -->
        <div
            v-if="orphanRelationItems.length"
            class="border border-warning/35 bg-warning/5 rounded-md p-3 space-y-2 text-[13px]"
        >
            <h4 class="text-sm font-semibold text-warning-content">Autres emplacements</h4>
            <p class="text-xs text-base-content/70">
                Ces groupes ne correspondent pas à la grille 3×niv.1 + niv. impairs. Ajustez les valeurs ou déplacez les
                sorts vers une ligne standard ci-dessus.
            </p>
            <ul class="space-y-2 border-t border-warning/20 pt-2">
                <li
                    v-for="item in orphanRelationItems"
                    :key="`orphan-${item.id}`"
                    class="flex flex-wrap items-start gap-x-2 gap-y-2"
                >
                    <div class="min-w-0 flex-1 max-w-md spell-text-embed">
                        <SpellViewText :spell="asSpellModel(item)" />
                    </div>
                    <span class="text-base-content/45 hidden sm:inline">·</span>
                    <label class="text-xs inline-flex items-center gap-1">
                        Nv. PJ
                        <input
                            v-model="pivotValues[item.id].character_level"
                            type="number"
                            min="1"
                            class="input input-bordered input-xs w-12"
                        />
                    </label>
                    <label class="text-xs inline-flex items-center gap-1">
                        Empl.
                        <input
                            v-model="pivotValues[item.id].slot_index"
                            type="number"
                            min="1"
                            class="input input-bordered input-xs w-12"
                        />
                    </label>
                    <label class="text-xs inline-flex items-center gap-1">
                        Ordre
                        <input
                            v-model="pivotValues[item.id].choice_order"
                            type="number"
                            min="0"
                            class="input input-bordered input-xs w-12"
                        />
                    </label>
                    <button type="button" class="btn btn-ghost btn-xs h-6 min-h-0 px-1 text-error" @click="removeItem(item.id)">
                        <Icon source="fa-solid fa-times" size="xs" />
                    </button>
                </li>
            </ul>
        </div>

        <div class="flex justify-end pt-4 border-t border-base-300">
            <Btn color="primary" :disabled="relationsForm.processing || !hasUnsavedChanges" @click="save">
                <i class="fa-solid fa-save mr-2" />
                {{ relationsForm.processing ? "Sauvegarde…" : "Enregistrer les sorts" }}
            </Btn>
        </div>
    </Container>
</template>
