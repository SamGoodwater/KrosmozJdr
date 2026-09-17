// @vitest-environment node
import { describe, expect, it } from 'vitest';
import { buildSquareGridModel, parseAreaToCells } from '../../../resources/js/Utils/Entity/areaNotationGrid.js';
import { getAreaShortLabel } from '../../../resources/js/Utils/Entity/Areas.js';

describe('parseAreaToCells', () => {
    it('point', () => {
        expect(parseAreaToCells('point')).toEqual([{ x: 0, y: 0 }]);
    });

    it('line-1x3', () => {
        const c = parseAreaToCells('line-1x3');
        expect(c).toHaveLength(3);
    });

    it('rect-2x2', () => {
        expect(parseAreaToCells('rect-2x2')).toHaveLength(4);
    });
});

describe('buildSquareGridModel', () => {
    it('ajoute du padding et carré', () => {
        const cells = parseAreaToCells('point');
        const m = buildSquareGridModel(cells, 1);
        expect(m).not.toBeNull();
        expect(m.side).toBeGreaterThanOrEqual(3);
        // Point (0,0) + marge 1 → grille 3×3, origine décalée : case active au centre
        expect(m.active.has('1,1')).toBe(true);
    });
});

describe('getAreaShortLabel', () => {
    it('raccourcit les formes connues', () => {
        expect(getAreaShortLabel('point')).toBe('1');
        expect(getAreaShortLabel('line-1x9')).toBe('L9');
        expect(getAreaShortLabel('circle-0-2')).toBe('○0-2');
        expect(getAreaShortLabel('rect-3x4')).toBe('3×4');
    });
});
