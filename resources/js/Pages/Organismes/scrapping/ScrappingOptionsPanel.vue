<script setup>
/**
 * ScrappingOptionsPanel — Options d'import, historique, erreurs batch.
 * Bloc repliable ; n'exécute pas de logique batch, uniquement affichage et emit.
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_REFONTE_UI_SCRAPPING.md
 */
import Card from "@/Pages/Atoms/data-display/Card.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Alert from "@/Pages/Atoms/feedback/Alert.vue";
import ToggleField from "@/Pages/Molecules/data-input/ToggleField.vue";

defineProps({
    open: { type: Boolean, default: false },
    optIncludeRelations: { type: Boolean, default: false },
    optUpdateMode: { type: String, default: "draft_raw_auto_update" },
    optPropertyWhitelist: { type: String, default: "" },
    optPropertyBlacklist: { type: String, default: "" },
    historyLines: { type: Array, default: () => [] },
    runId: { type: String, default: "" },
    unknownCharacteristics: { type: Object, default: null },
    batchErrorResults: { type: Array, default: () => [] },
});

const emit = defineEmits(["update:open", "update:optIncludeRelations", "update:optUpdateMode", "update:optPropertyWhitelist", "update:optPropertyBlacklist", "clear-history", "clear-errors", "export-errors-csv", "copy-run-id"]);
</script>

<template>
    <Card class="p-6 space-y-4">
        <div class="flex items-center justify-between gap-2">
            <div>
                <h3 class="font-semibold text-primary-100">Options & historique</h3>
                <p class="text-xs text-primary-300 mt-1">
                    Masqué par défaut pour garder l'interface légère.
                </p>
            </div>
            <Btn size="sm" variant="outline" @click="emit('update:open', !open)">
                {{ open ? "Masquer" : "Afficher" }}
            </Btn>
        </div>

        <div v-if="open" class="grid gap-6 lg:grid-cols-2">
            <Card class="p-6 space-y-3">
                <h4 class="font-semibold text-primary-100">Options d'import</h4>
                <div class="flex flex-col gap-4">
                    <div class="space-y-2">
                        <ToggleField
                            :model-value="optIncludeRelations"
                            label="Inclure les relations"
                            helper="Importer aussi les relations (sorts, drops, recettes, invocations…). Ex. : monstres avec leurs ressources et sorts ; les sorts d'invocation importent à leur tour les monstres invoqués."
                            @update:model-value="emit('update:optIncludeRelations', $event)"
                        />
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-primary-200">Mise à jour des entités déjà en BDD</label>
                        <p class="text-xs text-primary-400">Un seul mode actif. Les entités existantes qu'on ne met pas à jour sont ignorées (pas d'appel API).</p>
                        <div class="space-y-2 pt-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    :checked="optUpdateMode === 'ignore'"
                                    type="radio"
                                    value="ignore"
                                    class="radio radio-sm radio-primary"
                                    @change="emit('update:optUpdateMode', 'ignore')"
                                />
                                <span class="text-sm">Ignorer si existant — ne jamais remplacer</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    :checked="optUpdateMode === 'draft_raw_auto_update'"
                                    type="radio"
                                    value="draft_raw_auto_update"
                                    class="radio radio-sm radio-primary"
                                    @change="emit('update:optUpdateMode', 'draft_raw_auto_update')"
                                />
                                <span class="text-sm">Mettre à jour si (brouillon/raw) ET auto_update</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    :checked="optUpdateMode === 'auto_update'"
                                    type="radio"
                                    value="auto_update"
                                    class="radio radio-sm radio-primary"
                                    @change="emit('update:optUpdateMode', 'auto_update')"
                                />
                                <span class="text-sm">Mettre à jour tous les auto_update</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    :checked="optUpdateMode === 'force'"
                                    type="radio"
                                    value="force"
                                    class="radio radio-sm radio-primary"
                                    @change="emit('update:optUpdateMode', 'force')"
                                />
                                <span class="text-sm">Forcer les mises à jour</span>
                            </label>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-primary-200">Filtrer les propriétés</label>
                        <p class="text-xs text-primary-400">Whitelist vide = toutes les propriétés. Sinon uniquement celles listées. Utilisez la blacklist pour exclure (ex. image).</p>
                        <div class="grid grid-cols-1 gap-2 pt-1">
                            <label class="text-xs text-primary-300">Whitelist (propriétés à inclure, séparées par des virgules)</label>
                            <input
                                :value="optPropertyWhitelist"
                                type="text"
                                placeholder="ex: name, description, level (vide = toutes)"
                                class="input input-bordered input-sm w-full"
                                @input="emit('update:optPropertyWhitelist', ($event.target).value)"
                            />
                            <label class="text-xs text-primary-300">Blacklist (propriétés à exclure, séparées par des virgules)</label>
                            <input
                                :value="optPropertyBlacklist"
                                type="text"
                                placeholder="ex: image, bonus"
                                class="input input-bordered input-sm w-full"
                                @input="emit('update:optPropertyBlacklist', ($event.target).value)"
                            />
                        </div>
                    </div>
                </div>
            </Card>

            <Card class="p-6 space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <h4 class="font-semibold text-primary-100">Historique</h4>
                        <p v-if="runId" class="text-[11px] text-primary-300">
                            Dernier run_id: <span class="font-mono">{{ runId }}</span>
                        </p>
                        <p
                            v-if="unknownCharacteristics?.total_occurrences > 0"
                            class="text-[11px] text-warning mt-1"
                        >
                            Debug unknown IDs:
                            <span class="font-mono">
                                {{ Object.entries(unknownCharacteristics.ids || {}).map(([id, c]) => `${id}(${c})`).join(", ") }}
                            </span>
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <Btn v-if="runId" size="sm" variant="outline" @click="emit('copy-run-id')">
                            Copier run_id
                        </Btn>
                        <Btn size="sm" variant="ghost" :disabled="!historyLines.length" @click="emit('clear-history')">
                            Vider
                        </Btn>
                    </div>
                </div>
                <pre class="text-xs bg-base-300/30 border border-base-300 rounded p-3 max-h-80 overflow-auto whitespace-pre-wrap wrap-break-word">{{ historyLines.join("\n") }}</pre>
            </Card>

            <Card v-if="batchErrorResults.length > 0" class="overflow-hidden border-error/30 bg-error/5">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-error/20 bg-error/10 px-4 py-3">
                    <div class="flex items-center gap-2">
                        <Icon source="fa-solid fa-triangle-exclamation" alt="" pack="solid" class="text-error text-lg" />
                        <h4 class="font-semibold text-primary-100">Erreurs import batch</h4>
                        <Badge :content="String(batchErrorResults.length)" color="error" size="sm" />
                    </div>
                    <div class="flex items-center gap-2">
                        <Btn size="sm" variant="outline" color="error" title="Télécharger les erreurs en CSV" @click="emit('export-errors-csv')">
                            Exporter (CSV)
                        </Btn>
                        <Btn size="sm" variant="ghost" color="error" @click="emit('clear-errors')">
                            Fermer
                        </Btn>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    <Alert color="error" variant="soft" class="text-sm">
                        <span class="font-medium">{{ batchErrorResults.length }} entité(s) en échec</span>
                        <span class="text-primary-200"> sur le dernier import. Détail ci-dessous.</span>
                    </Alert>
                    <div class="overflow-x-auto rounded-lg border border-base-300 bg-base-100 max-h-56 overflow-y-auto">
                        <table class="table table-zebra table-pin-rows table-xs">
                            <thead>
                                <tr class="bg-base-300/70 text-primary-200">
                                    <th class="w-24 font-semibold">Type</th>
                                    <th class="w-20 font-semibold">ID</th>
                                    <th class="w-16 font-semibold">Statut</th>
                                    <th class="font-semibold">Message / Détails</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(row, idx) in batchErrorResults"
                                    :key="idx"
                                    class="hover:bg-error/5"
                                >
                                    <td>
                                        <Badge :content="row.type" color="neutral" size="xs" class="font-mono" />
                                    </td>
                                    <td class="font-mono text-primary-100 font-medium">{{ row.id }}</td>
                                    <td><Badge content="Erreur" color="error" size="xs" /></td>
                                    <td class="text-xs">
                                        <span class="text-error-200 font-medium">{{ row.error || '—' }}</span>
                                        <ul v-if="row.validation_errors?.length" class="list-disc list-inside mt-1 text-primary-400 space-y-0.5">
                                            <li v-for="(ve, i) in row.validation_errors" :key="i">
                                                <span class="font-mono text-[11px]">{{ ve.path || '—' }}</span>
                                                <span> : {{ ve.message || '—' }}</span>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </Card>
        </div>
    </Card>
</template>
