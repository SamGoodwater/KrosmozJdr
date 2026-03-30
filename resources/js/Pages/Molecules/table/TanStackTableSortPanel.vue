<script setup>
/**
 * Panneau de tri multi-critères (TanStack Table).
 *
 * @description
 * Liste ordonnée des tris actifs, directions A-Z / Z-A, ajout et suppression.
 */

import { computed } from "vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Dropdown from "@/Pages/Atoms/action/Dropdown.vue";
import ResponsiveActionButton from "@/Pages/Atoms/action/ResponsiveActionButton.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import { shiftUiSize } from "@/Utils/atomic-design";

const props = defineProps({
    uiSize: { type: String, default: "md" },
    uiColor: { type: String, default: "primary" },
    /** Colonnes avec sort.enabled */
    sortableColumns: { type: Array, default: () => [] },
    /** État TanStack : [{ id, desc }, ...] */
    sorting: { type: Array, default: () => [] },
});

const emit = defineEmits(["update:sorting"]);

const inputSizeClass = computed(() => {
    if (props.uiSize === "xs") return "select-xs";
    if (props.uiSize === "sm") return "select-sm";
    if (props.uiSize === "lg") return "select-lg";
    return "select-md";
});

const actionBtnSize = computed(() => shiftUiSize(props.uiSize, -1));

const colLabel = (id) => {
    const c = props.sortableColumns.find((x) => x.id === id);
    return c?.label || id;
};

const idsInUse = computed(() => new Set((props.sorting || []).map((s) => s.id)));

const availableToAdd = computed(() =>
    (props.sortableColumns || []).filter((c) => c?.id && !idsInUse.value.has(c.id)),
);

const emitSorting = (next) => {
    emit("update:sorting", Array.isArray(next) ? next : []);
};

const moveCriterion = (index, delta) => {
    const list = [...(props.sorting || [])];
    const j = index + delta;
    if (j < 0 || j >= list.length) return;
    const t = list[index];
    list[index] = list[j];
    list[j] = t;
    emitSorting(list);
};

const removeAt = (index) => {
    const list = [...(props.sorting || [])];
    list.splice(index, 1);
    emitSorting(list);
};

const setDescAt = (index, desc) => {
    const list = [...(props.sorting || [])];
    if (!list[index]) return;
    list[index] = { ...list[index], desc: Boolean(desc) };
    emitSorting(list);
};

const addCriterion = (columnId) => {
    if (!columnId) return;
    const list = [...(props.sorting || [])];
    if (list.some((s) => s.id === columnId)) return;
    list.unshift({ id: columnId, desc: false });
    emitSorting(list);
};

const togglePrimaryDirection = () => {
    const list = [...(props.sorting || [])];
    if (!list.length) return;
    list[0] = { ...list[0], desc: !list[0].desc };
    emitSorting(list);
};

const primaryLabel = computed(() => {
    const s = props.sorting?.[0];
    if (!s?.id) return "Trier…";
    return colLabel(s.id);
});
</script>

<template>
    <Dropdown v-if="sortableColumns.length > 0" placement="bottom-end" :close-on-content-click="false">
        <template #trigger>
            <ResponsiveActionButton
                :size="actionBtnSize"
                :color="uiColor"
                icon="fa-solid fa-arrow-down-wide-short"
                :label="primaryLabel"
                aria-label="Configurer le tri des colonnes"
                :title="sorting.length ? `Tri : ${primaryLabel}` : 'Configurer le tri'"
            />
        </template>
        <template #content>
            <div class="p-3 w-80 max-h-[min(70vh,24rem)] overflow-y-auto">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <div class="text-sm font-semibold">Ordre de tri</div>
                    <Btn
                        v-if="sorting.length"
                        :size="actionBtnSize"
                        variant="ghost"
                        :color="uiColor"
                        title="Inverser le sens du premier critère"
                        @click="togglePrimaryDirection"
                    >
                        <Icon source="fa-solid fa-arrow-down-wide-short" alt="" size="sm" />
                    </Btn>
                </div>
                <p class="text-xs text-base-content/60 mb-2">
                    Le premier critère est prioritaire. Utilise les flèches pour réordonner.
                </p>
                <ul class="space-y-2 mb-3">
                    <li
                        v-for="(s, idx) in sorting"
                        :key="s.id"
                        class="flex items-center gap-1 flex-wrap border border-base-200 rounded-btn p-2 bg-base-200/30"
                    >
                        <span class="text-xs font-medium flex-1 min-w-0 truncate">{{ colLabel(s.id) }}</span>
                        <div class="join">
                            <Btn
                                :size="actionBtnSize"
                                variant="ghost"
                                class="join-item px-2 min-h-7"
                                :disabled="idx === 0"
                                title="Monter"
                                @click="moveCriterion(idx, -1)"
                            >
                                <Icon source="fa-solid fa-chevron-up" alt="" size="sm" />
                            </Btn>
                            <Btn
                                :size="actionBtnSize"
                                variant="ghost"
                                class="join-item px-2 min-h-7"
                                :disabled="idx >= sorting.length - 1"
                                title="Descendre"
                                @click="moveCriterion(idx, 1)"
                            >
                                <Icon source="fa-solid fa-chevron-down" alt="" size="sm" />
                            </Btn>
                        </div>
                        <div class="join">
                            <Btn
                                :size="actionBtnSize"
                                variant="ghost"
                                class="join-item px-2 min-h-7"
                                :color="!s.desc ? uiColor : undefined"
                                title="A→Z"
                                @click="setDescAt(idx, false)"
                            >
                                <Icon source="fa-solid fa-arrow-down-a-z" alt="A-Z" size="sm" />
                            </Btn>
                            <Btn
                                :size="actionBtnSize"
                                variant="ghost"
                                class="join-item px-2 min-h-7"
                                :color="s.desc ? uiColor : undefined"
                                title="Z→A"
                                @click="setDescAt(idx, true)"
                            >
                                <Icon source="fa-solid fa-arrow-down-z-a" alt="Z-A" size="sm" />
                            </Btn>
                        </div>
                        <Btn
                            :size="actionBtnSize"
                            variant="ghost"
                            color="error"
                            class="min-h-7 px-2"
                            title="Retirer ce critère"
                            @click="removeAt(idx)"
                        >
                            <Icon source="fa-solid fa-xmark" alt="" size="sm" />
                        </Btn>
                    </li>
                </ul>
                <div v-if="availableToAdd.length" class="flex flex-col gap-2">
                    <label class="text-xs text-base-content/70">Ajouter un critère</label>
                    <select
                        :class="['select select-bordered w-full', inputSizeClass]"
                        aria-label="Ajouter un critère de tri"
                        @change="(e) => { addCriterion(e.target.value); e.target.value = ''; }"
                    >
                        <option value="">Choisir une colonne…</option>
                        <option v-for="c in availableToAdd" :key="c.id" :value="c.id">
                            {{ c.label || c.id }}
                        </option>
                    </select>
                </div>
                <div v-else-if="!sorting.length" class="text-xs text-base-content/50">
                    Aucune colonne triable.
                </div>
            </div>
        </template>
    </Dropdown>
</template>
