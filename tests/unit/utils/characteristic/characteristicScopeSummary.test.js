// @vitest-environment node
import { describe, expect, it } from 'vitest';
import { buildCharacteristicKrefScopes } from '../../../../resources/js/Utils/characteristic/characteristicScopeSummary.js';

describe('buildCharacteristicKrefScopes', () => {
    it('sépare personnage, créature, équipement et sort pour les PM', () => {
        const scopes = buildCharacteristicKrefScopes([
            {
                group: 'object',
                entity: '*',
                key: 'movement_points_object',
                min: '-2',
                max: '2',
                default_value: '0',
                formula: '{"1":"0","3":"1","16":"5","characteristic":"level_object"}',
                formula_display: null,
                forgemagie_max_bonus: 1,
            },
            {
                group: 'creature',
                entity: 'monster',
                key: 'movement_points_creature',
                min: '2',
                max: '10',
                default_value: '3',
                formula: null,
            },
            {
                group: 'spell',
                entity: '*',
                key: 'movement_points_spell',
                min: '0',
                max: '4',
                default_value: '0',
                formula: null,
            },
            {
                group: 'creature',
                entity: '*',
                key: 'movement_points_creature',
                min: '3',
                max: '6',
                default_value: '3',
                formula: null,
            },
        ]);

        expect(scopes.map((scope) => scope.label)).toEqual([
            'Personnage et PNJ',
            'Créature',
            'Équipement',
            'Sort',
        ]);
        expect(scopes[0].detail).toBe('3 à 6 · défaut 3');
        expect(scopes[0].formula).toBe('');
        expect(scopes[1].detail).toBe('2 à 10 · défaut 3');
        expect(scopes[2].detail).toBe('sur un objet : -2 à 2 · forgemagie +1');
        expect(scopes[2].formula).toContain('level_object');
        expect(scopes[3].detail).toBe('0 à 4');
    });

    it('ne reprend pas la formule d’équipement sur la ligne créature', () => {
        const scopes = buildCharacteristicKrefScopes([
            { group: 'creature', entity: '*', key: 'movement_points_creature', min: '3', max: '6', formula: '' },
            { group: 'object', entity: '*', key: 'movement_points_object', formula: '{"characteristic":"level_object","1":"0"}', min: '-2', max: '2' },
        ]);

        expect(scopes[0].formula).toBe('');
        expect(scopes[1].formula).toContain('level_object');
    });
});
