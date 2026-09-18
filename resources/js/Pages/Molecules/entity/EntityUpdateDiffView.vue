<script setup>
/**
 * Tableau avant / après d’une maj DofusDB ou d’une conversion IA.
 * Chaque cellule changée se choisit (ancienne ou nouvelle) ; les en-têtes
 * « Avant » / « Après » sélectionnent toute la colonne.
 *
 * @example
 * <EntityUpdateDiffView :diff="payload" @save="onSave" @restore="onRestore" />
 */
import { computed, ref, watch } from "vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";

const props = defineProps({
    diff: { type: Object, default: null },
    busy: { type: Boolean, default: false },
});

const emit = defineEmits(["save", "restore"]);

const onlyChanged = ref(true);
/** @type {import('vue').Ref<Record<string, 'before'|'after'>>} */
const choice = ref({});

const sourceLabel = computed(() => {
    const source = props.diff?.source;
    if (source === "ia") return "Conversion IA";
    if (source === "dofusdb") return "DofusDB";
    return "Mise à jour";
});

const fields = computed(() => (Array.isArray(props.diff?.fields) ? props.diff.fields : []));

const visibleFields = computed(() => {
    if (!onlyChanged.value) return fields.value;
    return fields.value.filter((row) => row?.changed);
});

const changedCount = computed(() => Number(props.diff?.changed_count ?? 0));

const beforeName = computed(() => props.diff?.before?.preview?.name || "Version initiale");
const afterName = computed(() => props.diff?.after?.preview?.name || "Nouvelle version");
const beforeState = computed(() => props.diff?.before?.preview?.state || "");
const afterState = computed(() => props.diff?.after?.preview?.state || "");

watch(
    () => props.diff?.snapshot_id,
    () => {
        const next = {};
        for (const row of fields.value) {
            if (row?.key) {
                next[row.key] = "after";
            }
        }
        choice.value = next;
    },
    { immediate: true },
);

/**
 * @param {string} key
 * @param {'before'|'after'} side
 */
function pick(key, side) {
    const row = fields.value.find((item) => item?.key === key);
    if (!row?.changed) return;
    choice.value = { ...choice.value, [key]: side };
}

/**
 * @param {'before'|'after'} side
 */
function pickAll(side) {
    const next = { ...choice.value };
    for (const row of fields.value) {
        if (row?.changed && row.key) {
            next[row.key] = side;
        }
    }
    choice.value = next;
}

function restoreKeys() {
    return fields.value
        .filter((row) => row?.changed && row.key && choice.value[row.key] === "before")
        .map((row) => row.key);
}

function onSave() {
    emit("save", { restore_keys: restoreKeys() });
}

/**
 * @param {{ key: string, changed?: boolean }} row
 * @param {'before'|'after'} side
 */
function isSelected(row, side) {
    return Boolean(row?.changed) && choice.value[row.key] === side;
}
</script>

<template>
    <div class="space-y-4" data-testid="entity-update-diff">
        <p class="text-sm text-base-content/80">
            {{ sourceLabel }} — clique une cellule pour garder l’ancienne ou la nouvelle valeur.
            Les en-têtes <span class="font-medium">Avant</span> et
            <span class="font-medium">Après</span> sélectionnent toute la colonne. Enregistrer
            applique le mix ; Rétablir annule toute la conversion.
        </p>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div class="rounded-box border border-base-300 bg-base-200/40 p-3">
                <div class="text-xs uppercase tracking-wide text-base-content/60">Version initiale</div>
                <div class="mt-1 font-medium text-primary-100">{{ beforeName }}</div>
                <div v-if="beforeState" class="text-xs text-base-content/70">État : {{ beforeState }}</div>
            </div>
            <div class="rounded-box border border-secondary/40 bg-secondary/10 p-3">
                <div class="text-xs uppercase tracking-wide text-base-content/60">Nouvelle version</div>
                <div class="mt-1 font-medium text-primary-100">{{ afterName }}</div>
                <div v-if="afterState" class="text-xs text-base-content/70">État : {{ afterState }}</div>
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input v-model="onlyChanged" type="checkbox" class="checkbox checkbox-sm" />
            Changements seulement
        </label>

        <p v-if="changedCount === 0" class="text-sm text-warning" data-testid="entity-update-diff-empty">
            Aucun champ modifié. Les champs gelés n’ont pas bougé.
        </p>

        <div v-if="visibleFields.length" class="overflow-x-auto rounded-box border border-base-300 bg-base-100/30 p-2 pr-3">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th class="w-36">Champ</th>
                        <th>
                            <button
                                type="button"
                                class="btn btn-ghost btn-xs font-semibold"
                                data-testid="entity-update-diff-pick-before"
                                title="Garder toutes les anciennes valeurs"
                                @click="pickAll('before')"
                            >
                                Avant
                            </button>
                        </th>
                        <th>
                            <button
                                type="button"
                                class="btn btn-ghost btn-xs font-semibold"
                                data-testid="entity-update-diff-pick-after"
                                title="Garder toutes les nouvelles valeurs"
                                @click="pickAll('after')"
                            >
                                Après
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="row in visibleFields"
                        :key="row.key"
                        :class="row.changed ? 'bg-warning/5' : ''"
                        :data-changed="row.changed ? '1' : '0'"
                    >
                        <td class="align-top font-medium whitespace-nowrap py-2">{{ row.label }}</td>
                        <td class="align-top p-1">
                            <button
                                type="button"
                                class="w-full min-h-12 text-left align-top whitespace-pre-wrap px-3 py-2 rounded-box border-2"
                                :class="
                                    isSelected(row, 'before')
                                        ? 'border-primary bg-base-200'
                                        : row.changed
                                          ? 'border-base-300 bg-transparent cursor-pointer opacity-60 hover:opacity-100'
                                          : 'border-transparent'
                                "
                                :disabled="!row.changed || busy"
                                :data-testid="`entity-update-diff-cell-${row.key}-before`"
                                :aria-pressed="isSelected(row, 'before') ? 'true' : 'false'"
                                @click="pick(row.key, 'before')"
                            >
                                {{ row.before }}
                            </button>
                        </td>
                        <td class="align-top p-1">
                            <button
                                type="button"
                                class="w-full min-h-12 text-left align-top whitespace-pre-wrap px-3 py-2 rounded-box border-2 font-medium"
                                :class="
                                    isSelected(row, 'after')
                                        ? 'border-secondary bg-secondary/20'
                                        : row.changed
                                          ? 'border-base-300 bg-transparent cursor-pointer opacity-60 hover:opacity-100'
                                          : 'border-transparent'
                                "
                                :disabled="!row.changed || busy"
                                :data-testid="`entity-update-diff-cell-${row.key}-after`"
                                :aria-pressed="isSelected(row, 'after') ? 'true' : 'false'"
                                @click="pick(row.key, 'after')"
                            >
                                {{ row.after }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p v-else-if="changedCount > 0" class="text-sm text-base-content/70">
            Aucune ligne à afficher avec ce filtre.
        </p>

        <div class="flex flex-wrap justify-end gap-2">
            <Btn variant="ghost" :disabled="busy" data-testid="entity-update-diff-restore" @click="emit('restore')">
                <Icon source="fa-rotate-left" pack="solid" alt="" class="mr-2" />
                Rétablir
            </Btn>
            <Btn color="primary" :disabled="busy" data-testid="entity-update-diff-save" @click="onSave">
                <Icon source="fa-floppy-disk" pack="solid" alt="" class="mr-2" />
                {{ busy ? "Enregistrement…" : "Enregistrer" }}
            </Btn>
        </div>
    </div>
</template>
