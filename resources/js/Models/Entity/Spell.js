/**
 * Modèle Spell pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de spell côté frontend.
 * 
 * @example
 * const spell = new Spell(props.spell);
 * console.log(spell.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';
import { resolveEntityRouteHref } from '@/Composables/entity/entityRouteRegistry';
import { buildCharacteristicEffectCell } from '@/Composables/entity/useCharacteristicEffectFormatter';
import { getByCharacteristicKey, getByDbColumnMap } from '@/Composables/store/useCharacteristicsStore';
import {
    isPoCac,
    formatPoRangeDisplay,
    trimTrailingPoSeparators,
    PO_CAC_ICON,
    PO_CAC_LABEL,
    resolveDef,
    SPELL_EFFECT_CHIP_SOURCE_GROUPS,
} from '@/Composables/entity/useCharacteristicDisplay';
import { getElementLabel, getElementIcon, getElementColor, getElementIconForValue } from '@/Utils/Entity/Elements';
import { getAreaShape, getAreaShortLabel } from '@/Utils/Entity/Areas';

/**
 * Icône / couleur d’un chip `effect_usages_chips` : store caractéristiques (spell puis creature), sinon élément.
 *
 * @param {object} chip
 * @returns {{ icon: string, color: string|null }}
 */
function effectUsageChipIconAndColor(chip) {
    const charKey =
        chip.characteristic != null && String(chip.characteristic).trim() !== ''
            ? String(chip.characteristic).trim()
            : null;
    const elementIndex = Number(chip.element ?? 0);
    const fallbackIcon = getElementIcon(elementIndex);
    const fallbackColor = getElementColor(elementIndex);
    if (charKey) {
        const def = resolveDef(charKey, undefined, {
            sourceGroups: [...SPELL_EFFECT_CHIP_SOURCE_GROUPS],
        });
        if (def) {
            const icon = def._resolvedIcon ?? def.icon ?? fallbackIcon;
            const color = def._resolvedColor ?? def.color ?? fallbackColor;
            return {
                icon: icon || fallbackIcon,
                color: color ?? fallbackColor,
            };
        }
    }
    return { icon: fallbackIcon, color: fallbackColor };
}

export class Spell extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get officialId() {
        return this._data.official_id || null;
    }

    get dofusdbId() {
        return this._data.dofusdb_id || null;
    }

    get name() {
        return this._data.name || '';
    }

    get description() {
        return this._data.description || '';
    }

    get effect() {
        return this._data.effect || null;
    }

    /** Résumé des effect_usages (nom effet, degré, sous-effets) fourni par l'API table. */
    get effectUsagesSummary() {
        return this._data.effect_usages_summary ?? '';
    }

    /** Chips structurés (text, element, target_type, area, duration_label, tooltip) fournis par l'API table. */
    get effectUsagesChips() {
        return this._data.effect_usages_chips ?? [];
    }

    get area() {
        return this._data.area || null;
    }

    get level() {
        const v = this._data.level;
        if (v === null || v === undefined || v === '') {
            return null;
        }
        const n = Number(v);
        return Number.isFinite(n) ? n : null;
    }

    /** Affichage portée (calculé côté API à partir de po_min/po_max). */
    get po() {
        const v = this._data.po;
        if (v === null || v === undefined || v === '') {
            return null;
        }
        if (typeof v === 'number') {
            return Number.isFinite(v) ? v : null;
        }
        if (typeof v === 'string') {
            const t = v.trim();
            if (t === '') {
                return null;
            }
            const n = Number(t);
            if (Number.isFinite(n) && t === String(n)) {
                return n;
            }
            return v;
        }
        return v;
    }

    get poMin() {
        return this._data.po_min ?? null;
    }

    get poMax() {
        return this._data.po_max ?? null;
    }

    get poEditable() {
        return this._data.po_editable ?? null;
    }

    get pa() {
        const v = this._data.pa;
        if (v === null || v === undefined || v === '') {
            return null;
        }
        const n = Number(v);
        return Number.isFinite(n) ? n : null;
    }

    /** Temps d'incantation (texte libre), chaîne vide si absent. */
    get castingTime() {
        const v = this._data.casting_time;
        if (v == null || v === "") {
            return "";
        }
        return String(v);
    }

    get castPerTurn() {
        return this._data.cast_per_turn || null;
    }

    get castPerTarget() {
        return this._data.cast_per_target || null;
    }

    get sightLine() {
        const v = this._data.sight_line;
        if (v === null || v === undefined) {
            return null;
        }
        return Boolean(v);
    }

    get numberBetweenTwoCast() {
        return this._data.number_between_two_cast || null;
    }

    get element() {
        const v = this._data.element;
        return v === undefined || v === null ? null : v;
    }

    get category() {
        return this._data.category || null;
    }

    get isMagic() {
        const v = this._data.is_magic;
        if (v === null || v === undefined) {
            return null;
        }
        return Boolean(v);
    }

    /** Définitions d’effets (pivot + degrés) pour la fiche sort, si chargées par l’API. */
    get effectsDefinitions() {
        const raw = this._data.effects_definitions;
        return Array.isArray(raw) ? raw : [];
    }

    /**
     * Monstres invoqués uniques à partir du payload `effects_definitions` (API fiche ou table).
     *
     * @param {unknown} definitions - Tableau renvoyé par l’API ou `[]`
     * @returns {Array<{ id: number, name: string, image: string|null }>}
     * @example
     * Spell.summonMonstersFromEffectsDefinitionsPayload(spell.effects_definitions);
     */
    static summonMonstersFromEffectsDefinitionsPayload(definitions) {
        const defs = Array.isArray(definitions) ? definitions : [];
        const out = [];
        const seen = new Set();
        for (const def of defs) {
            for (const deg of def.degrees || []) {
                for (const row of deg.rows || []) {
                    const sm = row.summon_monster;
                    if (sm?.id != null && !seen.has(sm.id)) {
                        seen.add(sm.id);
                        out.push({
                            id: sm.id,
                            name: sm.name ?? `Monstre #${sm.id}`,
                            image: sm.image ?? null,
                        });
                    }
                }
            }
        }
        return out;
    }

    /**
     * Monstres invoqués uniques (résumés `summon_monster` des lignes pivot), pour vues minimal / texte.
     *
     * @returns {Array<{ id: number, name: string, image: string|null }>}
     */
    get summonMonstersFromEffectDefinitions() {
        return Spell.summonMonstersFromEffectsDefinitionsPayload(this.effectsDefinitions);
    }

    /** Utilisable en rituel — présent si la colonne existe côté API. */
    get ritualAvailable() {
        return this._data.ritual_available ?? null;
    }

    /**
     * Rituel actif (affichage UI : icône / mention seulement si true).
     * Priorité `is_ritual` puis `ritual_available` pour compatibilité.
     */
    get isRitual() {
        if (this._data.is_ritual === true) {
            return true;
        }
        if (this._data.ritual_available === true) {
            return true;
        }
        return false;
    }

    /**
     * Sort lançable en réaction de combat (1 réaction / round / créature ; PA non récupérés au tour suivant).
     */
    get allowsReaction() {
        return Boolean(this._data.allows_reaction);
    }

    get powerful() {
        return this._data.powerful || null;
    }

    get resolutionMode() {
        return this._data.resolution_mode || "attack_roll";
    }

    get attackCharacteristicKey() {
        return this._data.attack_characteristic_key || null;
    }

    get saveCharacteristicKey() {
        return this._data.save_characteristic_key || null;
    }

    get saveDcFormula() {
        return this._data.save_dc_formula || null;
    }

    get saveSuccessNote() {
        return this._data.save_success_note || null;
    }

    /** Réussite automatique si la cible est consentante (règle optionnelle côté résolution). */
    get autoSuccessIfWillingTarget() {
        return Boolean(this._data.auto_success_if_willing_target);
    }

    get image() {
        return this._data.image || '';
    }

    get autoUpdate() {
        return this._data.auto_update ?? false;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get createdBy() {
        return this._data.createdBy || null;
    }

    get creatures() {
        return this._data.creatures || [];
    }

    get breeds() {
        return this._data.breeds || [];
    }

    /** @deprecated Utiliser breeds. Conservé pour compat. */
    get classes() {
        return this.breeds;
    }

    get scenarios() {
        return this._data.scenarios || [];
    }

    get campaigns() {
        return this._data.campaigns || [];
    }

    get spellTypes() {
        return this._data.spellTypes || [];
    }

    get spellEffects() {
        return this._data.spellEffects || [];
    }

    get monsters() {
        return this._data.monsters || [];
    }

    get spellTypesCount() {
        return Number(this._data.spell_types_count ?? this.spellTypes.length ?? 0);
    }

    get breedsCount() {
        return Number(this._data.breeds_count ?? this.breeds.length ?? 0);
    }

    get creaturesCount() {
        return Number(this._data.creatures_count ?? this.creatures.length ?? 0);
    }

    get monstersCount() {
        return Number(this._data.monsters_count ?? this.monsters.length ?? 0);
    }

    /**
     * Retourne la map des caractéristiques spell indexées par db_column.
     * Source: store (Inertia share) ; fallback ctx pour compat.
     * @private
     */
    _getSpellCharacteristicsByColumn(options = {}) {
        const fromStore = getByDbColumnMap('spell');
        if (fromStore && Object.keys(fromStore).length > 0) return fromStore;
        return options?.ctx?.characteristics?.spell?.byDbColumn || {};
    }

    /**
     * Résout une caractéristique par ses colonnes candidates (ex: pa, po, po_max).
     * @private
     */
    _getCharacteristicDef(options = {}, candidates = []) {
        const byColumn = this._getSpellCharacteristicsByColumn(options);
        for (const key of candidates) {
            const found = byColumn?.[key];
            if (found) return found;
        }
        return null;
    }

    // ============================================
    // FORMATAGE DES CELLULES (surcharge pour champs spécifiques)
    // ============================================

    /**
     * Génère une cellule pour un champ (surcharge pour gérer les champs spécifiques à Spell)
     * @param {string} fieldKey - Clé du champ
     * @param {Object} [options={}] - Options (size, context, config, ctx)
     * @returns {Object|null} Cell object ou null si valeur invalide
     */
    toCell(fieldKey, options = {}) {
        // D'abord, essayer la méthode de base (gère les formatters automatiquement)
        const baseCell = super.toCell(fieldKey, options);
        const overrideFields = new Set(['pa', 'po', 'po_range', 'spell_summary_profile', 'effect', 'effect_summary']);
        
        // Si la méthode de base a trouvé quelque chose (formatter ou valeur par défaut valide), l'utiliser
        if (!overrideFields.has(fieldKey) && baseCell && (baseCell.type !== 'text' || (baseCell.value && baseCell.value !== '-'))) {
            return baseCell;
        }

        // Sinon, gérer les champs spécifiques à Spell
        const { size = 'md', format = {} } = options;
        
        switch (fieldKey) {
            case 'name':
                return this._toNameCell(format, size, options);
            case 'description':
                return this._toDescriptionCell(format, size, options);
            case 'effect':
                return this._toEffectCell(format, size, options);
            case 'area':
                return this._toAreaCell(format, size, options);
            case 'po':
                return this._toPoCell(format, size, options);
            case 'po_range':
                return this._toPoRangeCell(format, size, options);
            case 'po_min':
                return this._toPoMinMaxCell(this.poMin, options);
            case 'po_max':
                return this._toPoMinMaxCell(this.poMax, options);
            case 'pa':
                return this._toPaCell(format, size, options);
            case 'cast_per_turn':
                return this._toCastPerTurnCell(format, size, options);
            case 'cast_per_target':
                return this._toCastPerTargetCell(format, size, options);
            case 'sight_line':
                return this._toSightLineCell(format, size, options);
            case 'number_between_two_cast':
                return this._toNumberBetweenTwoCastCell(format, size, options);
            case 'is_magic':
                return this._toIsMagicCell(format, size, options);
            case 'allows_reaction':
                return this._toAllowsReactionCell(format, size, options);
            case 'powerful':
                return this._toPowerfulCell(format, size, options);
            case 'image':
                return this._toImageCell(format, size, options);
            case 'spell_types':
            case 'spellTypes':
                return this._toSpellTypesCell(format, size, options);
            case 'spell_summary_profile':
                return this._toSpellSummaryProfileCell(format, size, options);
            case 'effect_summary':
                return this._toEffectSummaryCell(format, size, options);
            case 'created_by':
            case 'createdBy':
                return this._toCreatedByCell(format, size, options);
            case 'created_at':
                return this._toCreatedAtCell(format, size, options);
            case 'updated_at':
                return this._toUpdatedAtCell(format, size, options);
            default:
                // Fallback vers la méthode de base
                return baseCell;
        }
    }

    /**
     * Génère une cellule pour le nom (lien vers la page de détail)
     * @private
     */
    _toNameCell(format, size, options) {
        const name = this.name || '-';
        const href = options.href || resolveEntityRouteHref('spells', 'show', this.id) || `/entities/spells/${this.id}`;
        
        return {
            type: 'route',
            value: name,
            params: {
                href,
                tooltip: name === '-' ? '' : name,
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 20 : null),
                searchValue: name === '-' ? '' : name,
                sortValue: name,
            },
        };
    }

    /**
     * Génère une cellule pour la description (texte tronqué)
     * @private
     */
    _toDescriptionCell(format, size, options) {
        const description = this.description || '';
        const maxLength = format.truncate || (size === 'xs' || size === 'sm' ? 30 : 50);
        const truncated = description.length > maxLength 
            ? description.slice(0, maxLength - 1) + '…'
            : description;
        
        return {
            type: 'text',
            value: truncated || '-',
            params: {
                tooltip: description || '',
                sortValue: description,
                searchValue: description,
            },
        };
    }

    /**
     * Génère une cellule pour l'effet
     * @private
     */
    _toEffectCell(format, size, options) {
        const userLayout = options?.chipsLayout && typeof options.chipsLayout === 'object' ? options.chipsLayout : {};
        return buildCharacteristicEffectCell({
            rawValues: [this.effect],
            options,
            sourceGroups: ['spell'],
            format,
            size,
            chipsLayout: { maxRows: 3, ...userLayout },
        });
    }

    /**
     * Génère une cellule pour les effets (type spell_effects pour SpellEffectChips).
     * @private
     */
    _toEffectSummaryCell(format, size, options) {
        const chips = this.effectUsagesChips;
        if (chips.length === 0) {
            return {
                type: 'text',
                value: '—',
                params: {
                    sortValue: '',
                    searchValue: this.effectUsagesSummary || '',
                },
            };
        }
        // Niveau créature requis par chip (effect_usage), distinct du niveau du sort — voir creature_level_requirement (API).
        const items = chips.map((chip) => {
            const req =
                chip.creature_level_requirement && typeof chip.creature_level_requirement === 'object'
                    ? chip.creature_level_requirement
                    : null;
            const requiredCreatureLevel = {
                min: req?.min ?? req?.value ?? chip.required_creature_level ?? chip.level_min ?? null,
                max: req?.max ?? chip.level_max ?? null,
                label: req?.label ?? chip.creature_level_label ?? null,
            };
            const { icon, color } = effectUsageChipIconAndColor(chip);
            const elRaw = chip.element;
            const elementNum =
                elRaw !== null && elRaw !== undefined && elRaw !== '' && Number.isFinite(Number(elRaw))
                    ? Number(elRaw)
                    : null;
            const summonMonster =
                chip.summon_monster &&
                typeof chip.summon_monster === 'object' &&
                chip.summon_monster.id != null
                    ? chip.summon_monster
                    : null;
            return {
                icon,
                color,
                characteristic:
                    chip.characteristic != null && String(chip.characteristic).trim() !== ''
                        ? String(chip.characteristic).trim()
                        : null,
                element: elementNum,
                value: chip.text ?? '',
                tooltip: chip.tooltip ?? chip.text ?? '',
                degree: chip.degree ?? null,
                requiredCreatureLevel,
                area:
                    chip.area != null && String(chip.area).trim() !== ''
                        ? String(chip.area)
                        : null,
                summon_monster: summonMonster,
                action_slug:
                    chip.action_slug != null && String(chip.action_slug).trim() !== ''
                        ? String(chip.action_slug).trim()
                        : null,
                crit_only: Boolean(chip.crit_only),
                scope: chip.scope ?? null,
                value_formula:
                    chip.value_formula != null && String(chip.value_formula).trim() !== ''
                        ? String(chip.value_formula).trim()
                        : null,
                value_formula_crit:
                    chip.value_formula_crit != null && String(chip.value_formula_crit).trim() !== ''
                        ? String(chip.value_formula_crit).trim()
                        : null,
                life_steal_formula:
                    chip.life_steal_formula != null && String(chip.life_steal_formula).trim() !== ''
                        ? String(chip.life_steal_formula).trim()
                        : null,
                condition_name:
                    chip.condition_name != null && String(chip.condition_name).trim() !== ''
                        ? String(chip.condition_name).trim()
                        : null,
                cells_display:
                    chip.cells_display != null && String(chip.cells_display).trim() !== ''
                        ? String(chip.cells_display).trim()
                        : null,
                teleport: Boolean(chip.teleport),
                duration_formula:
                    chip.duration_formula != null && String(chip.duration_formula).trim() !== ''
                        ? String(chip.duration_formula).trim()
                        : null,
                duration_label:
                    chip.duration_label != null && String(chip.duration_label).trim() !== ''
                        ? String(chip.duration_label).trim()
                        : null,
            };
        });
        const subEffectSlugs = this._data.effect_sub_effect_slugs ?? [];
        const ctx = options?.context || 'table';
        const isMinimalLayout = ctx === 'minimal' || size === 'xs';
        const labelMode = isMinimalLayout
            ? 'icon-only'
            : ctx === 'compact' || size === 'sm'
              ? 'short'
              : 'full';
        return {
            type: 'spell_effects',
            value: '',
            params: {
                items,
                sortValue: this.effectUsagesSummary || '',
                searchValue: this.effectUsagesSummary || '',
                filterValue: subEffectSlugs,
                chipsLayout: {
                    labelMode,
                    layout: isMinimalLayout ? 'minimal' : 'default',
                },
            },
        };
    }

    /**
     * Génère une cellule pour la zone (area) avec icône de forme.
     * @private
     */
    _toAreaCell(format, size, options) {
        const area = this.area ?? null;
        if (area === null || String(area).trim() === '') {
            return {
                type: 'text',
                value: '—',
                params: { sortValue: '', searchValue: '' },
            };
        }
        const value = String(area);
        const shape = getAreaShape(area);
        return {
            type: 'area',
            value: '',
            params: {
                area: value,
                sortValue: value,
                searchValue: value,
                filterValue: shape || value,
            },
        };
    }

    /**
     * Génère une cellule pour les PO (portée)
     * @private
     */
    /**
     * Portée affichée à partir de po_min / po_max : tiret seulement si les deux bornes sont renseignées et différentes.
     * @private
     */
    _toPoRangeCell(format, size, options) {
        const display = formatPoRangeDisplay(this.poMin, this.poMax);

        if (display == null) {
            return {
                type: 'text',
                value: '—',
                params: { sortValue: '', searchValue: '' },
            };
        }
        const poDef =
            this._getCharacteristicDef(options, ['po_max', 'po_min', 'po']) ||
            getByCharacteristicKey('spell', 'range_spell');
        const poLabel = poDef?.short_name || poDef?.name || 'Portée';
        const cac = isPoCac(display);

        if (poDef && display !== '-') {
            const poFilterValue = this._parsePoForFilter(display);
            return {
                type: 'chips',
                value: '',
                params: {
                    items: [
                        {
                            icon: cac ? PO_CAC_ICON : (poDef.icon || 'fa-solid fa-crosshairs'),
                            color: poDef.color || null,
                            value: cac ? '' : display,
                            tooltip: cac ? PO_CAC_LABEL : `${poLabel}: ${display}`,
                        },
                    ],
                    sortValue: display,
                    searchValue: display,
                    filterValue: poFilterValue,
                },
            };
        }

        const poFilterValue = this._parsePoForFilter(display);
        return {
            type: 'text',
            value: display,
            params: {
                sortValue: display,
                searchValue: display,
                filterValue: poFilterValue,
            },
        };
    }

    _toPoCell(format, size, options) {
        const fromMinMax = formatPoRangeDisplay(this.poMin, this.poMax);
        const poLegacy = trimTrailingPoSeparators(this.po != null ? String(this.po) : null);
        const po = fromMinMax ?? poLegacy ?? '-';
        const poDef = this._getCharacteristicDef(options, ['po', 'po_max', 'po_min']);
        const poLabel = poDef?.short_name || poDef?.name || 'PO';

        if (poDef && po !== '-') {
            const poFilterValue = this._parsePoForFilter(po);
            const cac = isPoCac(po);
            return {
                type: 'chips',
                value: '',
                params: {
                    items: [
                        {
                            icon: cac ? PO_CAC_ICON : (poDef.icon || 'fa-solid fa-crosshairs'),
                            color: poDef.color || null,
                            /** Texte « CàC » pour l’inline (useEntityPropertyDisplay lit les chips). */
                            value: cac ? 'CàC' : String(po),
                            tooltip: cac ? PO_CAC_LABEL : `${poLabel}: ${po}`,
                        },
                    ],
                    sortValue: String(po),
                    searchValue: String(po),
                    filterValue: poFilterValue,
                },
            };
        }

        const poFilterValue = po !== '-' ? this._parsePoForFilter(po) : [];
        return {
            type: 'text',
            value: po,
            params: {
                sortValue: po === '-' ? '' : po,
                searchValue: po === '-' ? '' : po,
                filterValue: poFilterValue,
            },
        };
    }

    /**
     * Parse la portée pour le filtre (ex: "2-6" → ["2","3","4","5","6"])
     * @private
     */
    _parsePoForFilter(po) {
        if (!po || String(po).trim() === '') return [];
        const s = String(po).trim();
        const m = s.match(/^(\d+)\s*-\s*(\d+)$/);
        if (m) {
            const lo = parseInt(m[1], 10);
            const hi = parseInt(m[2], 10);
            if (Number.isFinite(lo) && Number.isFinite(hi) && lo <= hi) {
                return Array.from({ length: hi - lo + 1 }, (_, i) => String(lo + i));
            }
        }
        const single = parseInt(s, 10);
        return Number.isFinite(single) ? [String(single)] : [s];
    }

    /**
     * Génère une cellule pour portée min ou max (po_min / po_max).
     * @private
     */
    _toPoMinMaxCell(val, options) {
        const v = val != null ? String(val) : '-';
        return {
            type: 'text',
            value: v,
            params: {
                sortValue: v === '-' ? '' : v,
                searchValue: v === '-' ? '' : v,
            },
        };
    }

    /**
     * Génère une cellule pour les PA (coût)
     * @private
     */
    _toPaCell(format, size, options) {
        const pa = this.pa || '-';
        const paDef = this._getCharacteristicDef(options, ['pa']);
        const paLabel = paDef?.short_name || paDef?.name || 'PA';

        if (paDef && pa !== '-') {
            return {
                type: 'chips',
                value: '',
                params: {
                    items: [
                        {
                            icon: paDef.icon || 'fa-solid fa-bolt',
                            color: paDef.color || null,
                            value: String(pa),
                            tooltip: `${paLabel}: ${pa}`,
                        },
                    ],
                    sortValue: String(pa),
                    searchValue: String(pa),
                    filterValue: String(pa),
                },
            };
        }
        
        return {
            type: 'text',
            value: pa,
            params: {
                sortValue: pa === '-' ? '' : pa,
                searchValue: pa === '-' ? '' : pa,
                filterValue: pa === '-' ? null : String(pa),
            },
        };
    }

    /**
     * Génère une cellule pour les lancers par tour
     * @private
     */
    _toCastPerTurnCell(format, size, options) {
        const castPerTurn = this.castPerTurn || '-';
        
        return {
            type: 'text',
            value: castPerTurn,
            params: {
                sortValue: castPerTurn === '-' ? '' : castPerTurn,
                searchValue: castPerTurn === '-' ? '' : castPerTurn,
            },
        };
    }

    /**
     * Génère une cellule pour les lancers par cible
     * @private
     */
    _toCastPerTargetCell(format, size, options) {
        const castPerTarget = this.castPerTarget || '-';
        
        return {
            type: 'text',
            value: castPerTarget,
            params: {
                sortValue: castPerTarget === '-' ? '' : castPerTarget,
                searchValue: castPerTarget === '-' ? '' : castPerTarget,
            },
        };
    }

    /**
     * Génère une cellule pour la ligne de vue
     * @private
     */
    _toSightLineCell(format, size, options) {
        // Utiliser le BooleanFormatter via la méthode de base
        return super.toCell('sight_line', options);
    }

    /**
     * Génère une cellule pour le nombre entre deux lancers
     * @private
     */
    _toNumberBetweenTwoCastCell(format, size, options) {
        const number = this.numberBetweenTwoCast || '-';
        
        return {
            type: 'text',
            value: number,
            params: {
                sortValue: number === '-' ? '' : number,
                searchValue: number === '-' ? '' : number,
            },
        };
    }

    /**
     * Génère une cellule pour is_magic (Wakfu = magique Dofus, Physique = non magique).
     * @private
     */
    _toIsMagicCell(_format, _size, _options) {
        const on = Boolean(this.isMagic);
        const label = on ? 'Wakfu' : 'Physique';
        return {
            type: 'badge',
            value: label,
            params: {
                color: on ? 'success' : 'neutral',
                sortValue: on ? 1 : 0,
                searchValue: label,
                filterValue: on ? 1 : 0,
            },
        };
    }

    /**
     * Génère une cellule pour allows_reaction
     * @private
     */
    _toAllowsReactionCell(format, size, options) {
        const on = this.allowsReaction;
        const label = on ? 'Oui' : 'Non';

        return {
            type: 'badge',
            value: label,
            params: {
                color: on ? 'success' : 'neutral',
                sortValue: on ? 1 : 0,
                searchValue: label,
                filterValue: on ? 1 : 0,
            },
        };
    }

    /**
     * Génère une cellule pour powerful
     * @private
     */
    _toPowerfulCell(format, size, options) {
        const powerful = this.powerful ?? null;
        const value = powerful !== null ? String(powerful) : '-';
        
        return {
            type: 'text',
            value,
            params: {
                sortValue: powerful ?? 0,
                searchValue: value === '-' ? '' : value,
            },
        };
    }

    /**
     * Génère une cellule pour l'image (miniature)
     * @private
     */
    _toImageCell(format, size, options) {
        const imageUrl = this.image || '';
        
        if (!imageUrl) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }

        const imageSize = size === 'xs' || size === 'sm' ? 32 : 48;
        
        return {
            type: 'image',
            value: imageUrl,
            params: {
                alt: this.name || 'Spell image',
                width: imageSize,
                height: imageSize,
                sortValue: imageUrl,
                searchValue: imageUrl,
            },
        };
    }

    /**
     * Génère une cellule pour les types de sort
     * @private
     */
    _toSpellTypesCell(_format, _size, _options) {
        const spellTypes = this.spellTypes || [];
        
        if (!spellTypes.length) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                    filterValue: [],
                },
            };
        }

        const typeNames = spellTypes.map(t => t.name || t.label || '-').filter(n => n !== '-');
        const displayValue = typeNames.join(', ') || '-';

        const typeIds = spellTypes.map((t) => String(t.id ?? t.value ?? ''));
        if (displayValue === '-') {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                    filterValue: [],
                },
            };
        }

        const items = spellTypes.map((t) => ({
            id: t.id,
            name: String(t.name ?? t.label ?? '').trim() || '—',
            color: t.color ?? null,
            icon: t.icon != null && String(t.icon).trim() !== '' ? String(t.icon) : null,
        }));

        return {
            type: 'spell_types',
            value: '',
            params: {
                items,
                sortValue: displayValue,
                searchValue: typeNames.join(' '),
                filterValue: typeIds,
            },
        };
    }

    /**
     * Génère une cellule Profil (PO, PA, zone d'effet, élément, niveau).
     * Regroupe les infos clés du sort pour un affichage compact.
     * @private
     */
    _toSpellSummaryProfileCell(format, size, options) {
        const paValue = this.pa != null ? String(this.pa) : null;
        const poValue = this.po ? String(this.po) : null;
        const areaValue = this.area != null ? String(this.area) : null;
        const elementValue = this.element != null ? getElementLabel(Number(this.element)) : null;
        const levelValue = this.level != null ? String(this.level) : null;

        const paDef = this._getCharacteristicDef(options, ['pa']);
        const poDef = this._getCharacteristicDef(options, ['po', 'po_max', 'po_min']);
        const paLabel = paDef?.short_name || paDef?.name || 'PA';
        const poLabel = poDef?.short_name || poDef?.name || 'PO';

        const poCac = poValue && isPoCac(poValue);
        const items = [
            {
                icon: paDef?.icon || 'fa-solid fa-bolt',
                color: paDef?.color || null,
                value: paValue,
                tooltip: paValue ? `${paLabel}: ${paValue}` : '',
            },
            {
                icon: poCac ? PO_CAC_ICON : (poDef?.icon || 'fa-solid fa-crosshairs'),
                color: poDef?.color || null,
                value: poCac ? '' : poValue,
                tooltip: poCac ? PO_CAC_LABEL : (poValue ? `${poLabel}: ${poValue}` : ''),
            },
            {
                area: areaValue,
                value: areaValue ? getAreaShortLabel(areaValue) : '',
            },
            {
                icon: getElementIconForValue(this.element),
                value: elementValue,
                tooltip: elementValue ? `Élément: ${elementValue}` : '',
            },
            {
                icon: 'fa-solid fa-level-up-alt',
                value: levelValue,
                tooltip: levelValue ? `Niveau: ${levelValue}` : '',
            },
        ].filter((it) => it.value !== null && it.value !== undefined && (String(it.value) !== '' || (it.icon && it.tooltip)));

        const searchValue = items
            .map((it) => (it.area != null ? String(it.area) : String(it.value ?? '')))
            .join(' ');

        return {
            type: 'chips',
            value: '',
            params: {
                items,
                sortValue: Number(this.level) || 0,
                searchValue,
                filterValue: searchValue,
            },
        };
    }

    /**
     * Génère une cellule pour le créateur
     * @private
     */
    _toCreatedByCell(format, size, options) {
        const createdBy = this.createdBy;
        
        if (!createdBy) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }

        const userName = createdBy.name || createdBy.email || '-';

        return {
            type: 'text',
            value: userName,
            params: {
                tooltip: userName === '-' ? '' : userName,
                sortValue: userName,
                searchValue: userName,
            },
        };
    }

    /**
     * Génère une cellule pour la date de création
     * @private
     */
    _toCreatedAtCell(format, size, options) {
        // Utiliser le DateFormatter via la méthode de base
        return super.toCell('created_at', options);
    }

    /**
     * Génère une cellule pour la date de modification
     * @private
     */
    _toUpdatedAtCell(format, size, options) {
        // Utiliser le DateFormatter via la méthode de base
        return super.toCell('updated_at', options);
    }

    // ============================================
    // MÉTHODES UTILITAIRES
    // ============================================

    /**
     * Retourne les données pour un formulaire
     * @returns {Object}
     */
    toFormData() {
        return {
            id: this.id,
            official_id: this.officialId,
            dofusdb_id: this.dofusdbId,
            name: this.name,
            description: this.description,
            effect: this.effect,
            area: this.area,
            level: this.level,
            po_min: this.poMin,
            po_max: this.poMax,
            po_editable: this.poEditable,
            pa: this.pa,
            cast_per_turn: this.castPerTurn,
            cast_per_target: this.castPerTarget,
            sight_line: this.sightLine,
            number_between_two_cast: this.numberBetweenTwoCast,
            element: this._data.element === undefined ? null : this._data.element,
            spellTypes: (this.spellTypes || []).map((t) => Number(t.id ?? t)).filter((n) => Number.isFinite(n)),
            category: this.category,
            is_magic: this.isMagic,
            allows_reaction: this.allowsReaction,
            casting_time: this.castingTime,
            ritual_available:
                this._data.ritual_available !== null && this._data.ritual_available !== undefined
                    ? Boolean(this._data.ritual_available)
                    : Boolean(this._data.is_ritual),
            powerful: this.powerful,
            resolution_mode: this.resolutionMode,
            attack_characteristic_key: this.attackCharacteristicKey,
            save_characteristic_key: this.saveCharacteristicKey,
            save_dc_formula: this.saveDcFormula,
            save_success_note: this.saveSuccessNote,
            auto_success_if_willing_target: this.autoSuccessIfWillingTarget,
            state: this.state,
            read_level: this.readLevel,
            write_level: this.writeLevel,
            image: this.image,
            auto_update: this.autoUpdate,
            created_at: this._data.created_at ?? null,
            updated_at: this._data.updated_at ?? null,
        };
    }
}

export default Spell;
