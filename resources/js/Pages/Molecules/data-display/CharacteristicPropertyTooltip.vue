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
    <div class="characteristic-property-tooltip max-w-xs space-y-2 text-left text-sm text-white/95">
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
                <div v-if="!model.hideDisplayValueInTooltip" class="text-lg font-medium text-white/90">
                    {{ model.displayValue ?? "—" }}
                </div>
            </div>
        </div>

        <p v-if="model.subtitle" class="text-xs italic text-white/80">{{ model.subtitle }}</p>
        <p v-if="model.helper" class="text-xs text-white/75">{{ model.helper }}</p>
        <p v-if="model.descriptions && model.descriptions !== model.helper" class="text-xs text-white/65">
            {{ model.descriptions }}
        </p>

        <div v-if="model.formulaDisplay" class="text-xs text-white/65">
            <span class="font-medium text-white/85">Affichage :</span>
            {{ model.formulaDisplay }}
        </div>

        <div v-if="model.formulaBdd" class="text-xs text-white/65">
            <span class="font-medium text-white/85">Formule (BDD) :</span>
            <code class="ml-1 block break-all rounded bg-white/10 px-1 py-0.5 font-mono text-[11px] text-white/90">{{
                model.formulaBdd
            }}</code>
        </div>

        <div v-if="model.runtimeFormula" class="text-xs text-white/65">
            <span class="font-medium text-white/85">Formule (runtime) :</span>
            <code class="ml-1 block break-all rounded bg-white/10 px-1 py-0.5 font-mono text-[11px] text-white/90">{{
                model.runtimeFormula
            }}</code>
        </div>

        <div v-if="model.substituted" class="text-xs text-white/65">
            <span class="font-medium text-white/85">Résolution :</span>
            <code class="ml-1 block break-all rounded bg-white/10 px-1 py-0.5 font-mono text-[11px] text-white/90">{{
                model.substituted
            }}</code>
        </div>

        <ul
            v-if="Array.isArray(model.placeholders) && model.placeholders.length > 0"
            class="text-xs text-white/75"
        >
            <li v-for="(ph, idx) in model.placeholders" :key="idx" class="flex justify-between gap-2 border-t border-dashed border-white/20 pt-1 first:border-t-0 first:pt-0">
                <span class="font-mono text-[11px] text-white/60">[{{ ph.id }}]</span>
                <span class="font-medium text-white/90">{{ ph.value }}</span>
            </li>
        </ul>

        <div v-if="model.formulaMetaResolved && !model.substituted" class="text-xs text-white/65">
            <span class="font-medium text-white/85">Métadonnée :</span>
            <code class="ml-1 block break-all font-mono text-[11px] text-white/80">{{ model.formulaMetaResolved }}</code>
        </div>

        <div v-if="Array.isArray(model.levelTable) && model.levelTable.length > 0" class="overflow-x-auto">
            <table class="table table-xs table-pin-rows text-white/90 [&_th]:text-white/70 [&_td]:border-white/10">
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
