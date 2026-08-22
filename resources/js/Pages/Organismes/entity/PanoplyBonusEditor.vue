<script setup>
/**
 * PanoplyBonusEditor
 *
 * @description
 * Édition des bonus de set par palier de pièces, sur le même modèle que
 * les effets d’équipement (caractéristique + valeur, ajout / suppression).
 *
 * @example
 * <PanoplyBonusEditor :panoply-id="1" :bonus="panoply.bonus" :characteristics="list" />
 */
import { computed, ref, watch } from "vue";
import axios from "axios";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import SelectSearchField from "@/Pages/Molecules/data-input/SelectSearchField.vue";
import EditActionDock from "@/Pages/Molecules/action/EditActionDock.vue";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import {
    parsePanoplyBonus,
    serializePanoplyBonus,
    shortBonusKey,
} from "@/Utils/entity/panoplyBonus";

const props = defineProps({
    panoplyId: { type: Number, required: true },
    bonus: { type: [String, Object, Array], default: null },
    characteristics: { type: Array, default: () => [] },
});

const notificationStore = useNotificationStore();
const tiers = ref(parsePanoplyBonus(props.bonus));
const errorMessage = ref("");

watch(
    () => props.bonus,
    (value) => {
        tiers.value = parsePanoplyBonus(value);
    },
);

const characteristicOptions = computed(() => {
    const fromCatalog = (props.characteristics || []).map((c) => {
        const key = shortBonusKey(c.key || c.db_column || "");
        const label = c.name || c.short_name || key;
        const short = c.short_name && c.short_name !== label ? ` (${c.short_name})` : "";
        return { value: key, label: `${label}${short}` };
    }).filter((opt) => opt.value);

    const seen = new Set(fromCatalog.map((o) => o.value));
    const extras = [];
    for (const tier of tiers.value) {
        for (const row of tier.rows || []) {
            if (row.key && !seen.has(row.key)) {
                seen.add(row.key);
                extras.push({ value: row.key, label: row.key });
            }
        }
    }

    return [...fromCatalog, ...extras];
});

const saveLoading = ref(false);

const snapshot = () => serializePanoplyBonus(tiers.value);

const initialSnapshot = ref(snapshot());

const isDirty = computed(() => snapshot() !== initialSnapshot.value);

function addTier() {
    const used = new Set(tiers.value.map((t) => Number(t.pieceCount)));
    let next = Math.max(2, ...[...used].filter((n) => Number.isFinite(n)), 1) + 1;
    if (used.size === 0) {
        next = 2;
    }
    while (used.has(next)) {
        next += 1;
    }
    tiers.value.push({
        pieceCount: next,
        rows: [{ key: "", value: "" }],
    });
}

function removeTier(index) {
    tiers.value.splice(index, 1);
}

function addRow(tier) {
    tier.rows.push({ key: "", value: "" });
}

function removeRow(tier, index) {
    tier.rows.splice(index, 1);
    if (tier.rows.length === 0) {
        tier.rows.push({ key: "", value: "" });
    }
}

async function saveBonus() {
    errorMessage.value = "";
    const encoded = serializePanoplyBonus(tiers.value);
    saveLoading.value = true;
    try {
        await axios.patch(
            route("entities.panoplies.update", { panoply: props.panoplyId }),
            { bonus: encoded },
            {
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            },
        );
        initialSnapshot.value = encoded;
        notificationStore.success("Bonus de la panoplie mis à jour.", {
            duration: 3000,
            placement: "top-right",
        });
    } catch (err) {
        const errors = err?.response?.data?.errors;
        errorMessage.value =
            errors?.bonus?.[0]
            || err?.response?.data?.message
            || "Impossible d’enregistrer les bonus.";
        notificationStore.error(errorMessage.value, {
            duration: 5000,
            placement: "top-center",
        });
    } finally {
        saveLoading.value = false;
    }
}
</script>

<template>
    <div class="space-y-4">
        <p v-if="errorMessage" class="text-error text-sm">{{ errorMessage }}</p>

        <div v-if="tiers.length === 0" class="text-center py-6 text-base-content/60 text-sm">
            Aucun palier. Ajoutez un bonus pour commencer.
        </div>

        <div
            v-for="(tier, tierIndex) in tiers"
            :key="'tier-' + tierIndex"
            class="rounded-xl border border-base-300/70 bg-base-100/25 p-3 space-y-3 md:p-3.5"
        >
            <div class="flex flex-wrap items-end justify-between gap-3">
                <InputField
                    v-model="tier.pieceCount"
                    type="number"
                    size="sm"
                    class="w-36"
                    label="Pièces équipées"
                    :min="1"
                />
                <Btn
                    variant="ghost"
                    size="sm"
                    color="error"
                    aria-label="Supprimer ce palier"
                    @click="removeTier(tierIndex)"
                >
                    <i class="fa-solid fa-trash-can mr-1" aria-hidden="true"></i>
                    Palier
                </Btn>
            </div>

            <div
                v-for="(row, rowIndex) in tier.rows"
                :key="'row-' + tierIndex + '-' + rowIndex"
                class="flex flex-wrap items-end gap-3"
            >
                <div class="min-w-[14rem] flex-1">
                    <SelectSearchField
                        size="sm"
                        label="Caractéristique"
                        placeholder="Choisir…"
                        :options="characteristicOptions"
                        :model-value="row.key"
                        :searchable="characteristicOptions.length > 8"
                        @update:model-value="row.key = $event"
                    />
                </div>
                <InputField
                    v-model="row.value"
                    type="number"
                    size="sm"
                    class="w-28"
                    label="Valeur"
                    placeholder="—"
                />
                <Btn
                    variant="ghost"
                    size="sm"
                    color="error"
                    class="mb-0.5"
                    aria-label="Supprimer cet effet"
                    @click="removeRow(tier, rowIndex)"
                >
                    <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                </Btn>
            </div>

            <Btn variant="outline" size="sm" type="button" @click="addRow(tier)">
                + Ajouter un effet
            </Btn>
        </div>

        <Btn variant="outline" size="sm" type="button" @click="addTier">
            + Ajouter un palier
        </Btn>

        <div class="flex justify-end border-t border-base-300/50 pt-4">
            <EditActionDock
                primary-label="Sauvegarder les bonus"
                processing-label="Sauvegarde..."
                :processing="saveLoading"
                :disabled="!isDirty"
                :show-secondary="false"
                :secondary-actions="[]"
                :fixed-on-desktop="false"
                @primary="saveBonus"
            />
        </div>
    </div>
</template>
