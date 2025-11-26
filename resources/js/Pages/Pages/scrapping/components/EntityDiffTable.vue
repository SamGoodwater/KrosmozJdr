<script setup>
/**
 * Composant de comparaison entre deux entités (base actuelle vs importée)
 */
import { computed } from 'vue';

const props = defineProps({
    existing: {
        type: Object,
        default: null,
    },
    incoming: {
        type: Object,
        required: true,
    },
});

const flattenObject = (obj, prefix = '') => {
    const result = {};

    if (!obj || typeof obj !== 'object') {
        return result;
    }

    Object.keys(obj).forEach((key) => {
        const value = obj[key];
        const newKey = prefix ? `${prefix}.${key}` : key;

        if (value !== null && typeof value === 'object' && !Array.isArray(value)) {
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
};

const diff = computed(() => {
    if (!props.existing && !props.incoming) {
        return [];
    }

    const existingFlat = props.existing ? flattenObject(props.existing) : {};
    const incomingFlat = props.incoming ? flattenObject(props.incoming) : {};
    const keys = new Set([...Object.keys(existingFlat), ...Object.keys(incomingFlat)]);

    const diffs = [];

    keys.forEach((key) => {
        const current = existingFlat[key];
        const next = incomingFlat[key];

        if (current !== next) {
            diffs.push({
                field: key,
                current: current !== undefined ? String(current) : '(vide)',
                incoming: next !== undefined ? String(next) : '(vide)',
            });
        }
    });

    return diffs;
});
</script>

<template>
    <div v-if="diff.length" class="overflow-x-auto rounded border border-base-300">
        <table class="table table-zebra text-xs w-full">
            <thead>
                <tr class="text-primary-200 bg-base-200">
                    <th class="font-semibold">Champ</th>
                    <th class="font-semibold">Base actuelle</th>
                    <th class="font-semibold">Version importée</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="entry in diff" :key="entry.field">
                    <td class="font-semibold text-primary-100">{{ entry.field }}</td>
                    <td class="break-all text-primary-300">{{ entry.current }}</td>
                    <td class="break-all text-primary-100">{{ entry.incoming }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <p v-else class="text-sm text-primary-300 italic">
        Aucun écart détecté entre la base actuelle et la nouvelle version.
    </p>
</template>

