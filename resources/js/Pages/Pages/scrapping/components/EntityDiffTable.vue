<script setup>
/**
 * Comparaison des propriétés de l'entité : liste des propriétés du modèle avec 3 colonnes
 * (Brut, Converti, Krosmoz existant). Les lignes où Converti ≠ Krosmoz existant sont surlignées.
 * Libellés métier et regroupement par section (Identité, Monstre, etc.).
 */
import { computed } from 'vue';
import { getFieldLabel, getSectionFromFlatKey } from './previewDiffLabels';
import { formatSpellStateDispellable, formatSpellStateDuration, formatSpellStateMode } from '@/Composables/spell/spellStateDisplay';

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

function summarizeSpellEffects(effects, maxEffects = 4, maxSubs = 4) {
    if (!Array.isArray(effects) || effects.length === 0) return '—';
    const chunks = [];
    for (const ef of effects.slice(0, maxEffects)) {
        if (!ef || typeof ef !== 'object') continue;
        const degree = ef.degree != null ? `D${ef.degree}` : 'D?';
        const subs = Array.isArray(ef.sub_effects) ? ef.sub_effects : [];
        const subLabel = subs
            .slice(0, maxSubs)
            .map((s) => {
                const slug = s?.sub_effect_slug ?? '?';
                const params = s?.params ?? {};
                const stateId = params?.state_dofusdb_id ?? null;
                const stateName = params?.state_name ?? null;
                const duration = params?.duration ?? null;
                const mode = slug === 's-appliquer-etat' ? 'self' : (slug === 'appliquer-etat' ? 'target' : null);

                if (stateId != null || stateName != null) {
                    const identity = [stateName, stateId != null ? `#${stateId}` : null].filter(Boolean).join(' ');
                    const details = [
                        mode != null ? formatSpellStateMode(mode) : null,
                        formatSpellStateDuration(duration),
                        formatSpellStateDispellable(params?.dispellable),
                    ].filter(Boolean).join(' ');
                    return `${slug} ${identity}${details ? ` (${details})` : ''}`;
                }

                const formula = params?.value_formula ?? null;
                const converted = params?.value_converted ?? null;
                const characteristic = params?.characteristic ?? null;
                const details = [
                    formula != null ? String(formula) : null,
                    converted != null ? `=>${converted}` : null,
                    characteristic != null ? `(${characteristic})` : null,
                ].filter(Boolean).join(' ');
                return details ? `${slug} ${details}` : String(slug);
            })
            .join(' ; ');
        const suffix = subs.length > maxSubs ? ` ; +${subs.length - maxSubs} autre(s)` : '';
        chunks.push(`${degree}: ${subLabel || '—'}${suffix}`);
    }
    if (effects.length > maxEffects) {
        chunks.push(`+${effects.length - maxEffects} degré(s)`);
    }
    return chunks.join(' | ');
}

function summarizeRawSpellEffects(raw) {
    const levels = Array.isArray(raw?.levels) ? raw.levels : [];
    if (!levels.length) return '—';
    const chunks = [];
    for (const lvl of levels.slice(0, 4)) {
        if (!lvl || typeof lvl !== 'object') continue;
        const grade = lvl.grade != null ? `D${lvl.grade}` : 'D?';
        const effects = Array.isArray(lvl.effects) ? lvl.effects : [];
        const effLabel = effects
            .slice(0, 3)
            .map((e) => {
                const effectId = e?.effectId ?? '?';
                const diceNum = e?.diceNum ?? null;
                const diceSide = e?.diceSide ?? null;
                const value = e?.value ?? null;
                if (Number(effectId) === 950 && Number(value) > 0) {
                    return `#${effectId} état:${value}`;
                }
                const formula = Number.isFinite(Number(diceNum)) && Number.isFinite(Number(diceSide)) && Number(diceNum) > 0
                    ? (Number(diceSide) > 0 ? `${diceNum}d${diceSide}` : String(diceNum))
                    : null;
                return formula ? `#${effectId} ${formula}` : `#${effectId}`;
            })
            .join(' ; ');
        const suffix = effects.length > 3 ? ` ; +${effects.length - 3} autre(s)` : '';
        chunks.push(`${grade}: ${effLabel || '—'}${suffix}`);
    }
    if (levels.length > 4) {
        chunks.push(`+${levels.length - 4} degré(s)`);
    }
    return chunks.join(' | ');
}

/** Toutes les lignes : propriété | brut | converti | krosmoz. Surlignée si converti !== krosmoz. Avec libellé et section pour regroupement. */
const rows = computed(() => {
    if (!props.incoming) return [];

    const existingFlat = props.existing && typeof props.existing === 'object'
        ? flattenShallow(props.existing)
        : {};
    const incomingFlat = flattenShallow(props.incoming);
    const rawFlat = props.raw && typeof props.raw === 'object' ? flattenShallow(props.raw) : {};

    const convertedEffects = props.incoming?.spell_effects?.effects;
    if (Array.isArray(convertedEffects) && convertedEffects.length) {
        incomingFlat['spell_effects.summary'] = summarizeSpellEffects(convertedEffects);
    }
    const rawEffectsSummary = summarizeRawSpellEffects(props.raw);
    if (rawEffectsSummary !== '—') {
        rawFlat['spell_effects.summary'] = rawEffectsSummary;
    }
    const isSpellPreview = incomingFlat['spell_effects.summary'] !== undefined || rawFlat['spell_effects.summary'] !== undefined;

    const modelKeys = Object.keys(existingFlat).length > 0
        ? Object.keys(existingFlat)
        : Object.keys(incomingFlat);

    const keysSet = new Set(modelKeys);
    if (isSpellPreview) {
        keysSet.add('spell_effects.summary');
    }

    return [...keysSet].sort().map((key) => {
        const brut = findInFlat(rawFlat, key) ?? findInFlat(rawFlat, key.split('.').pop());
        const converti = findInFlat(incomingFlat, key) ?? findInFlat(incomingFlat, key.split('.').pop());
        const krosmoz = existingFlat[key];
        const differs = converti !== krosmoz;
        return {
            field: key,
            label: getFieldLabel(key),
            section: getSectionFromFlatKey(key),
            brut,
            converti,
            krosmoz,
            differs,
        };
    });
});

/** Lignes regroupées par section pour affichage avec titres de section. */
const rowsBySection = computed(() => {
    const map = {};
    for (const row of rows.value) {
        const sec = row.section || 'Autres';
        if (!map[sec]) map[sec] = [];
        map[sec].push(row);
    }
    return Object.entries(map).sort((a, b) => a[0].localeCompare(b[0]));
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
                <template v-for="[sectionName, sectionRows] in rowsBySection" :key="sectionName">
                    <tr class="bg-base-300/60">
                        <td colspan="4" class="font-semibold text-primary-200 text-[11px] uppercase tracking-wide py-1.5 px-2">
                            {{ sectionName }}
                        </td>
                    </tr>
                    <tr
                        v-for="entry in sectionRows"
                        :key="entry.field"
                        :class="entry.differs ? 'bg-warning/15' : ''"
                    >
                        <td class="font-semibold text-primary-100 pl-3">{{ entry.label }}</td>
                        <td class="break-all text-primary-300">{{ formatVal(entry.brut) }}</td>
                        <td class="break-all text-primary-100">{{ formatVal(entry.converti) }}</td>
                        <td class="break-all" :class="entry.differs ? 'text-warning font-medium' : 'text-primary-200'">
                            {{ formatVal(entry.krosmoz) }}
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    <p v-else class="text-sm text-primary-300 italic">
        Aucune propriété à afficher.
    </p>
</template>

