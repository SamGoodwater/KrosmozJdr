<script setup>
/**
 * Cases à cocher des propriétés à importer (tout / rien, image incluse).
 *
 * @example
 * <ScrappingPropertyCheckboxes :keys="['name','image']" :selected="selected" @update:selected="selected = $event" />
 */
const PROPERTY_LABELS = {
    image: "Image",
    name: "Nom",
    description: "Description",
    level: "Niveau",
    effect: "Effet",
    bonus: "Bonus",
    pa: "PA",
    pm: "PM",
    po: "PO",
    po_min: "PO min",
    po_max: "PO max",
    weight: "Poids",
    price: "Prix",
    rarity: "Rareté",
    size: "Taille",
    is_boss: "Boss",
    boss_pa: "PA légendaires",
    monster_race_id: "Race",
    item_type_id: "Type d’équipement",
    resource_type_id: "Type de ressource",
    consumable_type_id: "Type de consommable",
    spell_type_id: "Type de sort",
    official_id: "ID officiel",
    dofusdb_id: "ID DofusDB",
    raw: "Brut",
};

const props = defineProps({
    keys: { type: Array, default: () => [] },
    selected: { type: Array, default: () => [] },
    disabled: { type: Boolean, default: false },
});

const emit = defineEmits(["update:selected"]);

function labelFor(key) {
    return PROPERTY_LABELS[key] ?? key;
}

function isChecked(key) {
    return Array.isArray(props.selected) && props.selected.includes(key);
}

function toggle(key) {
    if (props.disabled) return;
    const current = Array.isArray(props.selected) ? [...props.selected] : [];
    const i = current.indexOf(key);
    if (i === -1) {
        current.push(key);
    } else {
        current.splice(i, 1);
    }
    emit("update:selected", current);
}

function selectAll() {
    if (props.disabled) return;
    emit("update:selected", [...props.keys]);
}

function selectNone() {
    if (props.disabled) return;
    emit("update:selected", []);
}
</script>

<template>
    <div class="space-y-2">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <p class="text-sm font-medium text-primary-200">Propriétés à récupérer</p>
            <div class="flex gap-2">
                <button type="button" class="btn btn-ghost btn-xs" :disabled="disabled" @click="selectAll">
                    Tout
                </button>
                <button type="button" class="btn btn-ghost btn-xs" :disabled="disabled" @click="selectNone">
                    Rien
                </button>
            </div>
        </div>
        <p class="text-xs text-primary-400">
            Tout coché = toutes les propriétés (image incluse). Décoche pour ne pas les écrire.
        </p>
        <div class="flex flex-wrap gap-2">
            <label
                v-for="key in keys"
                :key="`prop-${key}`"
                class="flex cursor-pointer items-center gap-2 rounded-lg border border-base-300 bg-base-100 px-3 py-2 text-sm"
            >
                <input
                    type="checkbox"
                    class="checkbox checkbox-sm"
                    :checked="isChecked(key)"
                    :disabled="disabled"
                    @change="toggle(key)"
                />
                <span>{{ labelFor(key) }}</span>
            </label>
        </div>
    </div>
</template>
