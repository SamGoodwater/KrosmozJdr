<script setup>
/**
 * Corps de décomposition d'une caractéristique (base / objets / contexte / total + table niveaux).
 *
 * @props {Object} model — viewModel enrichi (base, object, context, total, source, levelTable…)
 */
defineProps({
    model: {
        type: Object,
        required: true,
    },
});

function fmt(value) {
    if (value === null || value === undefined || value === "") return "—";
    return String(value);
}
</script>

<template>
    <div class="characteristic-decomposition max-w-sm space-y-3 text-left text-sm">
        <div>
            <div class="font-semibold">{{ model.name }}</div>
            <div class="text-lg font-medium">{{ model.displayValue ?? "—" }}</div>
            <p v-if="model.source === 'total_column'" class="text-xs opacity-70">
                Total explicite (colonne)
            </p>
            <p v-else-if="model.source === 'composed'" class="text-xs opacity-70">
                Composition base + objets + contexte
            </p>
        </div>

        <dl class="grid grid-cols-[auto_1fr] gap-x-3 gap-y-1 text-xs">
            <dt class="opacity-70">Base</dt>
            <dd class="font-mono text-right">{{ fmt(model.base) }}</dd>
            <dt class="opacity-70">Objets</dt>
            <dd class="font-mono text-right">{{ fmt(model.object) }}</dd>
            <dt class="opacity-70">Contexte</dt>
            <dd class="font-mono text-right">
                {{ fmt(model.context) }}
                <span v-if="model.contextRaw" class="ml-1 opacity-60">({{ model.contextRaw }})</span>
            </dd>
            <dt class="font-medium">Total</dt>
            <dd class="font-mono text-right font-medium">{{ fmt(model.total ?? model.displayValue) }}</dd>
        </dl>

        <div v-if="model.formulaDisplay || model.runtimeFormula || model.formulaBdd" class="space-y-1 text-xs opacity-80">
            <div v-if="model.formulaDisplay">
                <span class="font-medium">Affichage :</span> {{ model.formulaDisplay }}
            </div>
            <div v-if="model.runtimeFormula || model.formulaBdd">
                <span class="font-medium">Formule :</span>
                <code class="ml-1 break-all font-mono">{{ model.runtimeFormula || model.formulaBdd }}</code>
            </div>
            <div v-if="model.substituted">
                <span class="font-medium">Résolution :</span>
                <code class="ml-1 break-all font-mono">{{ model.substituted }}</code>
            </div>
        </div>

        <ul
            v-if="Array.isArray(model.itemContributions) && model.itemContributions.length"
            class="space-y-1 text-xs"
        >
            <li
                v-for="line in model.itemContributions"
                :key="line.item_id"
                class="flex justify-between gap-2 border-t border-dashed border-base-300 pt-1"
            >
                <span class="truncate">{{ line.name }} ×{{ line.quantity }}</span>
                <span class="font-mono">{{ line.amount }}</span>
            </li>
        </ul>

        <div v-if="Array.isArray(model.levelTable) && model.levelTable.length > 0" class="overflow-x-auto">
            <table class="table table-xs">
                <thead>
                    <tr>
                        <th>Niveau</th>
                        <th>Total</th>
                        <th>Base</th>
                        <th>Ctx</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in model.levelTable" :key="row.level">
                        <td>{{ row.level }}</td>
                        <td>{{ row.value ?? row.total }}</td>
                        <td>{{ row.base ?? "—" }}</td>
                        <td>{{ row.context ?? "—" }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
