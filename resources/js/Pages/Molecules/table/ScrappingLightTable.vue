<script setup>
/**
 * Tableau léger des résultats de recherche DofusDB (id, nom, présence locale).
 */
import Badge from "@/Pages/Atoms/data-display/Badge.vue";

const props = defineProps({
    rows: { type: Array, default: () => [] },
    selectedIds: { type: [Set, Array], default: () => new Set() },
    allSelected: { type: Boolean, default: false },
    formatName: { type: Function, default: (n) => (n?.fr ?? n?.en ?? (typeof n === "string" ? n : "—")) },
});

const emit = defineEmits(["update:selectedIds"]);

function selectedHas(id) {
    const s = props.selectedIds;
    if (s instanceof Set) return s.has(Number(id));
    return Array.isArray(s) && s.includes(Number(id));
}
</script>

<template>
    <div class="overflow-x-auto rounded-box border border-base-300">
        <table class="table table-sm w-full">
            <thead>
                <tr>
                    <th class="w-10">
                        <input
                            type="checkbox"
                            class="checkbox checkbox-sm"
                            :checked="allSelected"
                            :aria-label="allSelected ? 'Tout décocher' : 'Tout cocher'"
                            @change="emit('update:selectedIds', 'toggle-all')"
                        />
                    </th>
                    <th>ID DofusDB</th>
                    <th>Nom</th>
                    <th>Local</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="row in rows" :key="row?.id ?? row?.item?.id">
                    <td>
                        <input
                            type="checkbox"
                            class="checkbox checkbox-sm"
                            :checked="selectedHas(row?.id ?? row?.item?.id)"
                            :aria-label="`Sélectionner ${formatName(row?.name ?? row?.item?.name)}`"
                            @change="emit('update:selectedIds', Number(row?.id ?? row?.item?.id))"
                        />
                    </td>
                    <td class="font-mono">{{ row?.id ?? row?.item?.id }}</td>
                    <td>{{ formatName(row?.name ?? row?.item?.name) }}</td>
                    <td>
                        <Badge
                            :content="(row?.exists ?? row?.item?.exists) ? 'Existe' : 'Nouveau'"
                            :color="(row?.exists ?? row?.item?.exists) ? 'neutral' : 'success'"
                            size="xs"
                        />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
