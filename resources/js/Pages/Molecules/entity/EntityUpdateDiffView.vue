<script setup>
/**
 * Tableau avant / après d’une maj DofusDB ou d’une conversion IA.
 *
 * @example
 * <EntityUpdateDiffView :diff="payload" @save="onSave" @restore="onRestore" />
 */
import { computed, ref } from "vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";

const props = defineProps({
    diff: { type: Object, default: null },
    busy: { type: Boolean, default: false },
});

const emit = defineEmits(["save", "restore"]);

const onlyChanged = ref(true);

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
</script>

<template>
    <div class="space-y-4" data-testid="entity-update-diff">
        <p class="text-sm text-base-content/80">
            {{ sourceLabel }} — gauche : version initiale, droite : nouvelle version.
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

        <div v-if="visibleFields.length" class="overflow-x-auto rounded-box border border-base-300">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Champ</th>
                        <th>Avant</th>
                        <th>Après</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="row in visibleFields"
                        :key="row.key"
                        :class="row.changed ? 'bg-warning/20' : ''"
                        :data-changed="row.changed ? '1' : '0'"
                    >
                        <td class="align-top font-medium whitespace-nowrap">{{ row.label }}</td>
                        <td class="align-top whitespace-pre-wrap">{{ row.before }}</td>
                        <td class="align-top whitespace-pre-wrap font-medium">{{ row.after }}</td>
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
            <Btn color="primary" :disabled="busy" data-testid="entity-update-diff-save" @click="emit('save')">
                <Icon source="fa-floppy-disk" pack="solid" alt="" class="mr-2" />
                {{ busy ? "Enregistrement…" : "Enregistrer" }}
            </Btn>
        </div>
    </div>
</template>
