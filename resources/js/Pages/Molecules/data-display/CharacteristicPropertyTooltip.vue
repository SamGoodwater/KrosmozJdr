<script setup>
/**
 * Contenu riche du tooltip pour une caractéristique (vue unifiée).
 *
 * @props {Object} model — sortie viewModel (useCharacteristicViewModel / viewModelFromFormulaGroupItem)
 */
import Icon from "@/Pages/Atoms/data-display/Icon.vue";

defineProps({
    model: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="characteristic-property-tooltip max-w-xs space-y-2 text-left text-sm">
        <div class="flex items-start gap-2">
            <Icon
                v-if="model.icon"
                :source="model.icon"
                :alt="model.name"
                size="sm"
                class="shrink-0 opacity-90"
            />
            <div class="min-w-0 space-y-1">
                <div class="font-semibold leading-tight">{{ model.name }}</div>
                <div class="text-base-content/90 text-lg font-medium">{{ model.displayValue }}</div>
            </div>
        </div>

        <p v-if="model.helper" class="text-xs text-base-content/80">{{ model.helper }}</p>
        <p v-if="model.descriptions && model.descriptions !== model.helper" class="text-xs text-base-content/70">
            {{ model.descriptions }}
        </p>

        <div v-if="model.formulaDisplay" class="text-xs text-base-content/70">
            <span class="font-medium opacity-90">Affichage :</span>
            {{ model.formulaDisplay }}
        </div>

        <div v-if="model.formulaBdd" class="text-xs text-base-content/70">
            <span class="font-medium opacity-90">Formule (BDD) :</span>
            <code class="ml-1 block break-all rounded bg-base-200 px-1 py-0.5 font-mono text-[11px]">{{
                model.formulaBdd
            }}</code>
        </div>

        <div v-if="model.substituted" class="text-xs text-base-content/70">
            <span class="font-medium opacity-90">Résolution :</span>
            <code class="ml-1 block break-all rounded bg-base-200 px-1 py-0.5 font-mono text-[11px]">{{
                model.substituted
            }}</code>
        </div>

        <ul
            v-if="Array.isArray(model.placeholders) && model.placeholders.length > 0"
            class="border-base-300/60 text-xs text-base-content/80"
        >
            <li v-for="(ph, idx) in model.placeholders" :key="idx" class="flex justify-between gap-2 border-t border-dashed border-base-300/50 pt-1 first:border-t-0 first:pt-0">
                <span class="font-mono text-[11px] opacity-80">[{{ ph.id }}]</span>
                <span class="font-medium">{{ ph.value }}</span>
            </li>
        </ul>

        <div v-if="model.formulaMetaResolved && !model.substituted" class="text-xs text-base-content/70">
            <span class="font-medium opacity-90">Métadonnée :</span>
            <code class="ml-1 block break-all font-mono text-[11px]">{{ model.formulaMetaResolved }}</code>
        </div>

        <div v-if="Array.isArray(model.levelTable) && model.levelTable.length > 0" class="overflow-x-auto">
            <table class="table table-xs table-pin-rows">
                <thead>
                    <tr>
                        <th>Niveau</th>
                        <th>Valeur</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in model.levelTable" :key="row.level">
                        <td>{{ row.level }}</td>
                        <td>{{ row.value }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
