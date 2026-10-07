import { describe, expect, it } from 'vitest';
import {
    buildAreaNotation,
    parseAreaShapeParams,
} from '@/Utils/Entity/areaShapePicker.js';

describe('areaShapePicker', () => {
    it('parse et rebuild circle', () => {
        const parsed = parseAreaShapeParams('circle-0-2');
        expect(parsed).toEqual({ shape: 'circle', a: 0, b: 2 });
        expect(buildAreaNotation(parsed)).toBe('circle-0-2');
    });

    it('parse et rebuild line', () => {
        expect(buildAreaNotation({ shape: 'line', length: 5 })).toBe('line-1x5');
        expect(parseAreaShapeParams('line-1x5')).toEqual({ shape: 'line', length: 5 });
    });

    it('point sans paramètres', () => {
        expect(parseAreaShapeParams('point')).toEqual({ shape: 'point' });
        expect(buildAreaNotation({ shape: 'point' })).toBe('point');
    });
});
