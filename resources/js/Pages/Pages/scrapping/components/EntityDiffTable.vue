<script setup>
/**
 * Comparaison des propriétés de l'entité : liste des propriétés du modèle avec 3 colonnes
 * (Brut, Converti, Krosmoz existant). Les lignes où Converti ≠ Krosmoz existant sont surlignées.
 */
import { computed } from 'vue';

const props = defineProps({
    /** Données brutes DofusDB (optionnel). */
    raw: {
        type: Object,
        default: null,
    },
    /** Données converties (structure par section). */
    incoming: {
        type: Object,
        required: true,
    },
    /** Enregistrement existant en base Krosmoz (record API). */
    existing: {
        type: Object,
        default: null,
    },
});

/** Aplatit au plus 2 niveaux (section.clé) pour ne garder que les propriétés du modèle. */
function flattenShallow(obj, prefix = '') {
    if (!obj || typeof obj !== 'object') return {};
    const result = {};
    for (const key of Object.keys(obj)) {
        const value = obj[key];
        const fullKey = prefix ? `${prefix}.${key}` : key;
        if (value !== null && typeof value === 'object' && !Array.isArray(value)) {
            for (const k2 of Object.keys(value)) {
                const v2 = value[k2];
                const key2 = `${fullKey}.${k2}`;
                if (v2 !== null && typeof v2 === 'object' && (Array.isArray(v2) || typeof v2 === 'object')) {
                    result[key2] = Array.isArray(v2) ? `[${v2.length} élément(s)]` : `{${Object.keys(v2).length} clé(s)}`;
                } else {
                    result[key2] = v2;
                }
            }
        } else if (Array.isArray(value)) {
            result[fullKey] = `[${value.length} élément(s)]`;
        } else {
            result[fullKey] = value;
        }
    }
    return result;
}

function findInFlat(flat, modelKey) {
    if (flat[modelKey] !== undefined) return flat[modelKey];
    const dotKey = `.${modelKey}`;
    const found = Object.keys(flat).find((k) => k === modelKey || k.endsWith(dotKey));
    return found !== undefined ? flat[found] : undefined;
}

function formatVal(val) {
    if (val === undefined || val === null) return '—';
    if (typeof val === 'object') return typeof val === 'string' ? val : `[${Object.keys(val).length} clé(s)]`;
    return String(val);
}

/** Toutes les lignes : propriété | brut | converti | krosmoz. Surlignée si converti !== krosmoz. */
const rows = computed(() => {
    if (!props.incoming) return [];

    const existingFlat = props.existing && typeof props.existing === 'object'
        ? flattenShallow(props.existing)
        : {};
    const incomingFlat = flattenShallow(props.incoming);
    const rawFlat = props.raw && typeof props.raw === 'object' ? flattenShallow(props.raw) : {};

    const modelKeys = Object.keys(existingFlat).length > 0
        ? Object.keys(existingFlat)
        : Object.keys(incomingFlat);

    return modelKeys.sort().map((key) => {
        const brut = findInFlat(rawFlat, key) ?? findInFlat(rawFlat, key.split('.').pop());
        const converti = findInFlat(incomingFlat, key) ?? findInFlat(incomingFlat, key.split('.').pop());
        const krosmoz = existingFlat[key];
        const differs = converti !== krosmoz;
        return {
            field: key,
            brut,
            converti,
            krosmoz,
            differs,
        };
    });
});
</script>

<template>
    <div v-if="rows.length" class="overflow-x-auto rounded border border-base-300">
        <table class="table table-zebra text-xs w-full">
            <thead>
                <tr class="text-primary-200 bg-base-200">
                    <th class="font-semibold w-40">Propriété</th>
                    <th class="font-semibold">Brut (DofusDB)</th>
                    <th class="font-semibold">Converti</th>
                    <th class="font-semibold">Krosmoz (existant)</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="entry in rows"
                    :key="entry.field"
                    :class="entry.differs ? 'bg-warning/15' : ''"
                >
                    <td class="font-semibold text-primary-100">{{ entry.field }}</td>
                    <td class="break-all text-primary-300">{{ formatVal(entry.brut) }}</td>
                    <td class="break-all text-primary-100">{{ formatVal(entry.converti) }}</td>
                    <td class="break-all" :class="entry.differs ? 'text-warning font-medium' : 'text-primary-200'">
                        {{ formatVal(entry.krosmoz) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <p v-else class="text-sm text-primary-300 italic">
        Aucune propriété à afficher.
    </p>
</template>

