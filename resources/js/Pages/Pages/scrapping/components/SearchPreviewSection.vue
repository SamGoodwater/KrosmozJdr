<script setup>
/**
 * Section de recherche et prévisualisation d'entités
 */
import { ref, computed, watch, onMounted, nextTick } from 'vue';
import Card from '@/Pages/Atoms/data-display/Card.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Loading from '@/Pages/Atoms/feedback/Loading.vue';
import Alert from '@/Pages/Atoms/feedback/Alert.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import EntityDiffTable from './EntityDiffTable.vue';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
import { getSectionLabel, getFieldLabel } from './previewDiffLabels';

const props = defineProps({
    entityType: {
        type: String,
        required: true,
    },
    maxId: {
        type: Number,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    /**
     * Pré-remplissage/contrôle (utile pour usage en modal ou depuis une entité donnée).
     */
    initialMode: { type: String, default: 'single' }, // 'single'|'range'|'all'
    initialSingleId: { type: [String, Number], default: '' },
    initialRangeStart: { type: [String, Number], default: '' },
    initialRangeEnd: { type: [String, Number], default: '' },
    /**
     * Modes autorisés (ex: ['single'] en modal)
     */
    availableModes: { type: Array, default: () => ['single', 'range', 'all'] },
    /**
     * Verrouille les champs (utile en modal "mettre à jour cet élément").
     */
    lockInputs: { type: Boolean, default: false },
    /**
     * Lance automatiquement une prévisualisation au montage.
     */
    autoPreview: { type: Boolean, default: false },
    /**
     * Cache le header (utile en modal compacte).
     */
    hideHeader: { type: Boolean, default: false },
    /**
     * Permet de déclencher une prévisualisation "à la demande" depuis un parent
     * (ex: depuis une table de recherche). Incrémentez la valeur pour relancer.
     */
    previewNonce: { type: Number, default: 0 },
});

const emit = defineEmits(['preview', 'import', 'simulate']);

const notificationStore = useNotificationStore();
const { error: showError } = notificationStore;

// Mode de recherche
const normalizeMode = (m) => {
    const raw = String(m || '').trim();
    return ['single', 'range', 'all'].includes(raw) ? raw : 'single';
};

const allowedModes = computed(() => {
    const modes = Array.isArray(props.availableModes)
        ? props.availableModes.map((m) => normalizeMode(m))
        : ['single', 'range', 'all'];
    return [...new Set(modes)].filter((m) => ['single', 'range', 'all'].includes(m));
});

const searchMode = ref(
    allowedModes.value.includes(normalizeMode(props.initialMode))
        ? normalizeMode(props.initialMode)
        : (allowedModes.value[0] || 'single')
);
const singleId = ref(props.initialSingleId !== null && typeof props.initialSingleId !== 'undefined' ? String(props.initialSingleId) : '');
const rangeStart = ref(props.initialRangeStart !== null && typeof props.initialRangeStart !== 'undefined' ? String(props.initialRangeStart) : '');
const rangeEnd = ref(props.initialRangeEnd !== null && typeof props.initialRangeEnd !== 'undefined' ? String(props.initialRangeEnd) : '');

// État de prévisualisation
const previewData = ref(null);
const previewLoading = ref(false);

const isValidSingleId = computed(() => {
    const id = parseInt(singleId.value);
    return !isNaN(id) && id >= 1 && id <= props.maxId;
});

const isValidRange = computed(() => {
    const start = parseInt(rangeStart.value);
    const end = parseInt(rangeEnd.value);
    if (isNaN(start) || isNaN(end)) return false;
    if (start < 1 || end > props.maxId) return false;
    return start <= end;
});

const rangeCount = computed(() => {
    if (!isValidRange.value) return 0;
    const start = parseInt(rangeStart.value);
    const end = parseInt(rangeEnd.value);
    return end - start + 1;
});

/** Retourne l'effet à l'index donné (pour la simulation des sorts). */
function getEffectByIndex(index) {
    const effects = previewData.value?.converted?.spell_effects?.effects;
    if (!Array.isArray(effects) || index < 0 || index >= effects.length) return null;
    return effects[index];
}

/** Formate une valeur pour affichage dans le résumé (primitif, tableau, objet). */
function formatSummaryValue(val) {
    if (val === null) return '—';
    if (typeof val === 'boolean') return val ? 'Oui' : 'Non';
    if (typeof val === 'number' || typeof val === 'string') return String(val);
    if (Array.isArray(val)) return `${val.length} élément(s)`;
    if (typeof val === 'object') return Object.keys(val).length ? `{ ${Object.keys(val).join(', ')} }` : '{}';
    return String(val);
}

/**
 * Construit une liste de paires (clé, valeur) pour un bloc converted/existing.
 * Chaque clé top-level (creatures, monsters, items…) devient une section avec libellé ; les clés sont affichées avec libellé métier.
 * @param {Record<string, unknown>|null} data
 * @returns {{ section: string, sectionLabel: string, rows: { key: string, label: string, value: string }[] }[]}
 */
function buildStructuredSummary(data) {
    if (!data || typeof data !== 'object') return [];
    return Object.entries(data).map(([section, content]) => {
        const sectionLabel = getSectionLabel(section);
        if (content === null || typeof content !== 'object') {
            return { section, sectionLabel, rows: [{ key: '(valeur)', label: '(valeur)', value: formatSummaryValue(content) }] };
        }
        const rows = Array.isArray(content)
            ? [{ key: 'length', label: getFieldLabel('length'), value: `${content.length} élément(s)` }]
            : Object.entries(content).map(([k, v]) => ({ key: k, label: getFieldLabel(k), value: formatSummaryValue(v) }));
        return { section, sectionLabel, rows };
    });
}

watch(() => props.initialMode, (m) => {
    const next = normalizeMode(m);
    if (allowedModes.value.includes(next)) searchMode.value = next;
});
watch(() => props.availableModes, () => {
    const current = normalizeMode(searchMode.value);
    if (!allowedModes.value.includes(current)) {
        searchMode.value = allowedModes.value[0] || 'single';
    }
}, { deep: true });
watch(() => props.initialSingleId, (v) => {
    if (v === null || typeof v === 'undefined') return;
    singleId.value = String(v);
});
watch(() => props.initialRangeStart, (v) => {
    if (v === null || typeof v === 'undefined') return;
    rangeStart.value = String(v);
});
watch(() => props.initialRangeEnd, (v) => {
    if (v === null || typeof v === 'undefined') return;
    rangeEnd.value = String(v);
});

onMounted(async () => {
    if (!props.autoPreview) return;
    if (searchMode.value === 'single' && String(singleId.value || '').trim() !== '') {
        await handlePreview();
    }
    if (searchMode.value === 'range' && String(rangeStart.value || '').trim() !== '') {
        await handlePreview();
    }
});

watch(
    () => props.previewNonce,
    async (v, old) => {
        if (v === old) return;
        // Laisser le temps aux watchers initialMode/initialSingleId d'appliquer la nouvelle valeur
        await nextTick();
        await handlePreview();
    }
);

watch(
    () => props.entityType,
    () => {
        previewData.value = null;
    }
);

watch(
    () => searchMode.value,
    () => {
        previewData.value = null;
    }
);

const handlePreview = async () => {
    if (searchMode.value === 'single' && !isValidSingleId.value) {
        showError('ID invalide pour ce type d\'entité');
        return;
    }

    previewLoading.value = true;
    previewData.value = null;

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            showError('Token CSRF introuvable');
            return;
        }

        let url = '';
        if (searchMode.value === 'single') {
            url = `/api/scrapping/preview/${props.entityType}/${singleId.value}`;
        } else {
            // Pour les plages, on prévisualise le premier ID
            url = `/api/scrapping/preview/${props.entityType}/${rangeStart.value}`;
        }

        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
            },
        });

        const data = await response.json();

        if (response.ok && data.success) {
            const payload = data.data;
            previewData.value = payload;
            emit('preview', payload);
            if (payload && payload.success === false) {
                showError(payload.validation_errors?.length
                    ? `Validation échouée (${payload.validation_errors.length} erreur(s))`
                    : (data.message || 'La prévisualisation a échoué'));
            }
        } else {
            previewData.value = null;
            showError(data.message || 'Impossible de prévisualiser cette entité');
        }
    } catch (err) {
        showError('Erreur lors de la prévisualisation : ' + err.message);
    } finally {
        previewLoading.value = false;
    }
};

const handleSimulate = () => {
    if (searchMode.value === 'single' && !isValidSingleId.value) {
        showError('ID invalide pour la simulation');
        return;
    }
    if (searchMode.value === 'range' && !isValidRange.value) {
        showError('Plage invalide pour la simulation');
        return;
    }

    emit('simulate', {
        mode: searchMode.value,
        entityType: props.entityType,
        singleId: searchMode.value === 'single' ? parseInt(singleId.value) : null,
        rangeStart: searchMode.value === 'range' ? parseInt(rangeStart.value) : null,
        rangeEnd: searchMode.value === 'range' ? parseInt(rangeEnd.value) : null,
    });
};

const handleImport = () => {
    if (searchMode.value === 'single' && !isValidSingleId.value) {
        showError('ID invalide pour l\'import');
        return;
    }
    if (searchMode.value === 'range' && !isValidRange.value) {
        showError('Plage invalide pour l\'import');
        return;
    }

    emit('import', {
        mode: searchMode.value,
        entityType: props.entityType,
        singleId: searchMode.value === 'single' ? parseInt(singleId.value) : null,
        rangeStart: searchMode.value === 'range' ? parseInt(rangeStart.value) : null,
        rangeEnd: searchMode.value === 'range' ? parseInt(rangeEnd.value) : null,
    });
};
</script>

<template>
    <Card class="p-6 space-y-6">
        <div v-if="!hideHeader">
            <h2 class="text-xl font-bold text-primary-100 mb-2">Recherche & Prévisualisation</h2>
            <p class="text-sm text-primary-300">
                Recherchez une entité, prévisualisez-la et comparez-la avec la version en base avant d'importer.
            </p>
        </div>

        <!-- Mode de recherche -->
        <div class="space-y-4">
            <div v-if="allowedModes.length > 1" class="flex gap-2">
                <Btn
                    v-if="allowedModes.includes('single')"
                    :color="searchMode === 'single' ? 'primary' : undefined"
                    :variant="searchMode === 'single' ? undefined : 'ghost'"
                    size="sm"
                    @click="searchMode = 'single'"
                >
                    ID unique
                </Btn>
                <Btn
                    v-if="allowedModes.includes('range')"
                    :color="searchMode === 'range' ? 'primary' : undefined"
                    :variant="searchMode === 'range' ? undefined : 'ghost'"
                    size="sm"
                    @click="searchMode = 'range'"
                >
                    Plage d'ID
                </Btn>
                <Btn
                    v-if="allowedModes.includes('all')"
                    :color="searchMode === 'all' ? 'primary' : undefined"
                    :variant="searchMode === 'all' ? undefined : 'ghost'"
                    size="sm"
                    @click="searchMode = 'all'"
                >
                    Import complet
                </Btn>
            </div>

            <!-- ID unique -->
            <div v-if="searchMode === 'single' && allowedModes.includes('single')" class="space-y-4">
                <InputField
                    v-model="singleId"
                    type="number"
                    label="ID de l'entité"
                    :min="1"
                    :max="maxId"
                    placeholder="Ex: 1"
                    :disabled="lockInputs"
                />
                <div class="flex gap-2">
                    <Btn
                        color="primary"
                        :disabled="!isValidSingleId || previewLoading"
                        @click="handlePreview"
                    >
                        <Loading v-if="previewLoading" class="mr-2" />
                        <Icon v-else source="fa-solid fa-eye" alt="Prévisualiser" pack="solid" class="mr-2" />
                        Prévisualiser
                    </Btn>
                    <Btn
                        color="secondary"
                        :disabled="!isValidSingleId || loading"
                        @click="handleSimulate"
                    >
                        <Icon source="fa-solid fa-flask" alt="Simuler" pack="solid" class="mr-2" />
                        Simuler
                    </Btn>
                    <Btn
                        color="success"
                        :disabled="!isValidSingleId || loading"
                        @click="handleImport"
                    >
                        <Icon source="fa-solid fa-download" alt="Importer" pack="solid" class="mr-2" />
                        Importer
                    </Btn>
                </div>
            </div>

            <!-- Plage d'ID -->
            <div v-if="searchMode === 'range' && allowedModes.includes('range')" class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <InputField
                        v-model="rangeStart"
                        type="number"
                        label="ID de début"
                        :min="1"
                        :max="maxId"
                        placeholder="Ex: 10"
                        :disabled="lockInputs"
                    />
                    <InputField
                        v-model="rangeEnd"
                        type="number"
                        label="ID de fin"
                        :min="1"
                        :max="maxId"
                        placeholder="Ex: 20"
                        :disabled="lockInputs"
                    />
                </div>
                <div v-if="isValidRange" class="rounded-lg border border-base-300 bg-base-200/40 p-3 text-sm text-primary-200">
                    {{ rangeCount }} entité(s) seront importées
                </div>
                <div class="flex gap-2">
                    <Btn
                        color="secondary"
                        :disabled="!isValidRange || loading"
                        @click="handleSimulate"
                    >
                        <Icon source="fa-solid fa-flask" alt="Simuler" pack="solid" class="mr-2" />
                        Simuler
                    </Btn>
                    <Btn
                        color="success"
                        :disabled="!isValidRange || loading"
                        @click="handleImport"
                    >
                        <Icon source="fa-solid fa-download" alt="Importer" pack="solid" class="mr-2" />
                        Importer la plage
                    </Btn>
                </div>
            </div>

            <!-- Import complet -->
            <div v-if="searchMode === 'all' && allowedModes.includes('all')" class="space-y-4">
                <Alert color="warning" class="text-sm">
                    Cette opération importera toutes les entités de type <strong>{{ entityType }}</strong> ({{ maxId }} entités max).
                    Cela peut prendre plusieurs minutes.
                </Alert>
                <div class="flex gap-2">
                    <Btn
                        color="secondary"
                        :disabled="loading"
                        @click="handleSimulate"
                    >
                        <Icon source="fa-solid fa-flask" alt="Simuler" pack="solid" class="mr-2" />
                        Simuler l'import complet
                    </Btn>
                    <Btn
                        color="success"
                        :disabled="loading"
                        @click="handleImport"
                    >
                        <Icon source="fa-solid fa-database" alt="Importer toutes les entités" pack="solid" class="mr-2" />
                        Importer toutes les entités
                    </Btn>
                </div>
            </div>
        </div>

            <!-- Résultat de prévisualisation -->
        <div v-if="previewData" class="space-y-4 border-t border-base-300 pt-4">
            <div class="flex flex-wrap items-center gap-2">
                <h3 class="font-semibold text-primary-100">Résultat de la prévisualisation</h3>
                <Badge
                    v-if="previewData.success === true"
                    color="success"
                    size="sm"
                    content="Prêt pour l'import"
                />
                <Badge
                    v-else
                    color="warning"
                    size="sm"
                    content="Validation échouée"
                />
            </div>

            <!-- Échec de validation : ne pas afficher comme succès -->
            <Alert v-if="previewData.success === false" color="warning" class="text-sm">
                <span class="font-medium">La validation a échoué.</span>
                <span class="text-primary-200"> L'import pourrait échouer ou produire des données incohérentes.</span>
            </Alert>

            <!-- Erreurs de validation (path + message) -->
            <Alert v-if="previewData.validation_errors?.length > 0" color="error" class="text-sm">
                <p class="font-semibold mb-2 flex items-center gap-2">
                    <Icon source="fa-solid fa-circle-exclamation" alt="" pack="solid" class="text-error shrink-0" />
                    Erreurs de validation
                    <Badge :content="String(previewData.validation_errors.length)" color="error" size="xs" />
                </p>
                <ul class="list-disc list-inside space-y-1 text-xs text-primary-200">
                    <li v-for="(err, idx) in previewData.validation_errors" :key="idx">
                        <span class="font-mono text-error-200">{{ err.path || '—' }}</span>
                        <span> : {{ err.message || 'Erreur' }}</span>
                    </li>
                </ul>
            </Alert>

            <!-- Relations détectées -->
            <Alert v-if="previewData.raw" color="info" variant="soft" class="text-sm">
                <p class="font-semibold text-info mb-2 flex items-center gap-2">
                    <Icon source="fa-solid fa-link" alt="" pack="solid" class="shrink-0" />
                    Relations détectées
                </p>
                <div class="flex flex-wrap gap-2">
                    <Badge
                        v-if="previewData.raw.spells?.length"
                        color="info"
                        size="sm"
                        variant="outline"
                        :content="`${previewData.raw.spells.length} sort(s)`"
                    />
                    <Badge
                        v-if="previewData.raw.drops?.length"
                        color="info"
                        size="sm"
                        variant="outline"
                        :content="`${previewData.raw.drops.length} drop(s)`"
                    />
                    <Badge
                        v-if="previewData.raw.recipe?.length"
                        color="info"
                        size="sm"
                        variant="outline"
                        :content="`${previewData.raw.recipe.length} recette`"
                    />
                    <Badge
                        v-if="previewData.raw.summon"
                        color="info"
                        size="sm"
                        variant="outline"
                        content="Invoc."
                    />
                    <span
                        v-if="!previewData.raw.spells?.length && !previewData.raw.drops?.length && !previewData.raw.recipe?.length && !previewData.raw.summon"
                        class="text-primary-400 text-sm italic"
                    >
                        Aucune relation
                    </span>
                </div>
            </Alert>

            <!-- Version DofusDB / Version Krosmoz : ne pas afficher comme succès si validation a échoué -->
            <p v-if="previewData.success === false" class="text-xs text-warning">
                Les données ci-dessous sont affichées à titre indicatif ; la validation a échoué (voir erreurs ci-dessus).
            </p>
            <div class="grid gap-4 xl:grid-cols-2">
                <div
                    class="rounded-lg overflow-hidden transition-colors"
                    :class="previewData.success === false ? 'border border-warning/50 bg-warning/5' : 'border border-primary/20 bg-base-300/30'"
                >
                    <div class="flex items-center gap-2 border-b border-base-300 bg-base-300/50 px-3 py-2">
                        <Icon source="fa-solid fa-database" alt="" pack="solid" class="text-primary-300 text-sm" />
                        <h4 class="font-semibold text-primary-100 text-sm">Version DofusDB convertie</h4>
                        <Badge v-if="previewData.success === true" color="primary" size="xs" variant="outline" content="Import" />
                        <Badge v-else color="warning" size="xs" variant="outline" content="Invalide" />
                    </div>
                    <p class="text-[11px] text-primary-400 px-3 py-1.5 bg-base-300/30 border-b border-base-300/50">
                        Données DofusDB → converties via formules et limites (BDD Krosmoz).
                    </p>
                    <div class="space-y-3 p-3 max-h-80 overflow-auto">
                        <div
                            v-for="block in buildStructuredSummary(previewData.converted)"
                            :key="block.section"
                            class="rounded bg-base-300/50 p-2.5 text-xs"
                        >
                            <p class="font-semibold text-primary-200 mb-1.5 text-[11px] uppercase tracking-wide">{{ block.sectionLabel }}</p>
                            <dl class="space-y-1">
                                <div v-for="row in block.rows" :key="row.key" class="flex gap-2">
                                    <dt class="text-primary-400 shrink-0">{{ row.label }} :</dt>
                                    <dd class="text-primary-100 break-words">{{ row.value }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="rounded-lg border border-base-300 overflow-hidden">
                    <div class="flex items-center gap-2 border-b border-base-300 bg-base-300/50 px-3 py-2">
                        <Icon source="fa-solid fa-server" alt="" pack="solid" class="text-primary-300 text-sm" />
                        <h4 class="font-semibold text-primary-100 text-sm">Version actuelle (Krosmoz)</h4>
                        <Badge color="neutral" size="xs" variant="outline" content="Base" />
                    </div>
                    <template v-if="previewData.existing">
                        <div class="space-y-3 p-3 max-h-80 overflow-auto">
                            <div
                                v-for="block in buildStructuredSummary(previewData.existing)"
                                :key="block.section"
                                class="rounded bg-base-300/50 p-2.5 text-xs"
                            >
                                <p class="font-semibold text-primary-200 mb-1.5 text-[11px] uppercase tracking-wide">{{ block.sectionLabel }}</p>
                                <dl class="space-y-1">
                                    <div v-for="row in block.rows" :key="row.key" class="flex gap-2">
                                        <dt class="text-primary-400 shrink-0">{{ row.label }} :</dt>
                                        <dd class="text-primary-100 break-words">{{ row.value }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </template>
                    <Alert v-else color="info" variant="soft" class="m-3 text-sm">
                        Aucune donnée existante. L'import créera une nouvelle entrée.
                    </Alert>
                </div>
            </div>

            <!-- Comparaison : propriétés (Brut / Converti / Krosmoz existant) -->
            <div class="rounded-lg border border-base-300 overflow-hidden">
                <div class="flex items-center gap-2 border-b border-base-300 bg-base-300/50 px-3 py-2">
                    <Icon source="fa-solid fa-code-compare" alt="" pack="solid" class="text-primary-300 text-sm" />
                    <h4 class="font-semibold text-primary-100 text-sm">Propriétés : Brut / Converti / Krosmoz</h4>
                </div>
                <p class="text-[11px] text-primary-400 px-3 py-1.5 bg-base-300/30 border-b border-base-300/50">
                    Converti = DofusDB après formules et limites BDD.
                </p>
                <div class="p-3">
                    <EntityDiffTable
                        :raw="previewData.raw"
                        :incoming="previewData.converted"
                        :existing="previewData.existing?.record ?? null"
                    />
                </div>
            </div>

            <!-- Simulation des effets (sorts uniquement) : ce qui sera créé / réutilisé sans écriture en base -->
            <div
                v-if="entityType === 'spell' && previewData.converted?.spell_effects"
                class="rounded-lg border border-primary/30 overflow-hidden bg-base-300/20"
            >
                <div class="flex items-center gap-2 border-b border-base-300 bg-base-300/50 px-3 py-2">
                    <Icon source="fa-solid fa-wand-magic-sparkles" alt="" pack="solid" class="text-primary-300 text-sm" />
                    <h4 class="font-semibold text-primary-100 text-sm">Simulation des effets (aucune création en base)</h4>
                    <Badge color="info" size="xs" variant="outline" content="Preview" />
                </div>
                <p class="text-[11px] text-primary-400 px-3 py-1.5 bg-base-300/30 border-b border-base-300/50">
                    Conversion DofusDB → effets et sous-effets Krosmoz. « Création » = nouvel Effect ; « Réutilisation » = effet existant (même signature).
                </p>
                <div class="p-3 space-y-4 max-h-96 overflow-auto">
                    <div v-if="previewData.converted.spell_effects.effect_group" class="text-xs text-primary-300">
                        <span class="font-semibold">Groupe :</span>
                        {{ previewData.converted.spell_effects.effect_group.name || '—' }}
                        <span class="font-mono text-primary-400">({{ previewData.converted.spell_effects.effect_group.slug || '—' }})</span>
                    </div>
                    <Alert v-if="!previewData.spell_effects_simulation?.length" color="warning" variant="soft" class="text-xs">
                        Aucun effet à simuler pour ce sort. Vérifier que les niveaux du sort ont bien été récupérés et que le mapping DofusDB → sous-effets couvre les effectId de ce sort.
                    </Alert>
                    <div
                        v-for="(sim, idx) in previewData.spell_effects_simulation"
                        :key="idx"
                        class="rounded-lg border border-base-300 bg-base-200/50 p-3 text-xs space-y-2"
                    >
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-semibold text-primary-100">Degré {{ sim.degree }}</span>
                            <Badge
                                :color="sim.action === 'reuse' ? 'success' : 'primary'"
                                size="xs"
                                :content="sim.action === 'reuse' ? `Réutilisation (effet #${sim.existing_effect_id})` : 'Création'"
                            />
                            <span v-if="sim.target_type && sim.target_type !== 'direct'" class="text-primary-400">
                                Cible : {{ sim.target_type }}
                            </span>
                            <span v-if="sim.area" class="font-mono text-primary-400">Zone : {{ sim.area }}</span>
                        </div>
                        <p v-if="sim.name" class="text-primary-200 truncate">{{ sim.name }}</p>
                        <div v-if="getEffectByIndex(idx)?.sub_effects?.length" class="pl-2 border-l-2 border-base-300 space-y-1">
                            <p class="text-[11px] text-primary-400 font-semibold uppercase">Sous-effets ({{ getEffectByIndex(idx).sub_effects.length }})</p>
                            <ul class="list-disc list-inside space-y-0.5 text-primary-200">
                                <li
                                    v-for="(sub, si) in getEffectByIndex(idx).sub_effects"
                                    :key="si"
                                    class="flex flex-wrap items-center gap-1"
                                >
                                    <span class="font-mono">{{ sub.sub_effect_slug }}</span>
                                    <span v-if="sub.params?.value_formula" class="text-primary-400">→ {{ sub.params.value_formula }}</span>
                                    <span v-if="sub.params?.value_converted != null" class="text-secondary-400" title="Valeur convertie (Phase 3)">→ {{ sub.params.value_converted }}</span>
                                    <span v-if="sub.params?.dice_formula" class="text-accent-400 font-mono" title="Notation dés (convertToDice)">→ {{ sub.params.dice_formula }}</span>
                                    <span v-if="sub.params?.characteristic" class="text-primary-400">({{ sub.params.characteristic }})</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </Card>
</template>

