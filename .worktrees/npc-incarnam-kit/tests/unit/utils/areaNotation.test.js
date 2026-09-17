// @vitest-environment node
import { describe, expect, it } from 'vitest';
import { isValidAreaNotation } from '../../../resources/js/Utils/Entity/areaNotation.js';

describe('isValidAreaNotation', () => {
    it('accepte vide ou null', () => {
        expect(isValidAreaNotation(null)).toBe(true);
        expect(isValidAreaNotation('')).toBe(true);
        expect(isValidAreaNotation('   ')).toBe(true);
    });

    it('accepte les formes documentées', () => {
        expect(isValidAreaNotation('point')).toBe(true);
        expect(isValidAreaNotation('line-1x1')).toBe(true);
        expect(isValidAreaNotation('line-1x99')).toBe(true);
        expect(isValidAreaNotation('cross-0-2')).toBe(true);
        expect(isValidAreaNotation('cross-1-2')).toBe(true);
        expect(isValidAreaNotation('circle-0-2')).toBe(true);
        expect(isValidAreaNotation('circle-2-2')).toBe(true);
        expect(isValidAreaNotation('rect-1x1')).toBe(true);
        expect(isValidAreaNotation('rect-3x4')).toBe(true);
        expect(isValidAreaNotation('shape-99')).toBe(true);
        expect(isValidAreaNotation('shape-12-0-5')).toBe(true);
    });

    it('refuse les notations invalides', () => {
        expect(isValidAreaNotation('foo')).toBe(false);
        expect(isValidAreaNotation('line-2x3')).toBe(false);
        expect(isValidAreaNotation('line-1x0')).toBe(false);
        expect(isValidAreaNotation('cross-2-1')).toBe(false);
        expect(isValidAreaNotation('circle-3-1')).toBe(false);
        expect(isValidAreaNotation('rect-0x1')).toBe(false);
        expect(isValidAreaNotation('shape-0')).toBe(false);
        expect(isValidAreaNotation('shape-1-2')).toBe(false);
        expect(isValidAreaNotation('point-1')).toBe(false);
    });
});
