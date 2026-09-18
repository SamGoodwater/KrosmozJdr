// @vitest-environment node
import { describe, expect, it } from 'vitest';
import {
    fromInclusiveRange,
    parseDiceFormula,
    rollDiceFormula,
} from '../../../resources/js/Utils/dice/diceParser.js';

describe('fromInclusiveRange', () => {
    it('convertit une fourchette inclusive en 1dN+k exact', () => {
        expect(fromInclusiveRange(2, 6)).toBe('1d5+1');
        expect(fromInclusiveRange(1, 6)).toBe('1d6');
        expect(fromInclusiveRange(0, 4)).toBe('1d5-1');
        expect(fromInclusiveRange(10, 10)).toBe('10');
        expect(fromInclusiveRange(6, 2)).toBe('1d5+1');
    });
});

describe('parseDiceFormula', () => {
    it('ignore une saisie vide', () => {
        const parsed = parseDiceFormula('');
        expect(parsed.isValid).toBe(false);
        expect(parsed.isRecognized).toBe(false);
        expect(parsed.error).toBeNull();
    });

    it('valide un nombre seul sans le reconnaître comme formule de dé', () => {
        const parsed = parseDiceFormula('12');
        expect(parsed.isValid).toBe(true);
        expect(parsed.isRecognized).toBe(false);
        expect(parsed.min).toBe(12);
    });

    it('parse d12 et 3d8', () => {
        expect(parseDiceFormula('d12')).toMatchObject({
            isRecognized: true,
            min: 1,
            max: 12,
            average: 6.5,
        });
        expect(parseDiceFormula('3d8')).toMatchObject({
            isRecognized: true,
            min: 3,
            max: 24,
            average: 13.5,
        });
    });

    it('parse une tranche et expose l’équivalent en dés', () => {
        expect(parseDiceFormula('[2-6]')).toMatchObject({
            isRecognized: true,
            min: 2,
            max: 6,
            average: 4,
            rangeEquivalents: ['[2-6] = 1d5+1'],
        });
    });

    it('accepte les opérateurs et alias x / ÷', () => {
        expect(parseDiceFormula('2d6+3')).toMatchObject({ min: 5, max: 15, average: 10 });
        expect(parseDiceFormula('2d6 x 2')).toMatchObject({ min: 4, max: 24 });
        expect(parseDiceFormula('4d6÷2')).toMatchObject({ min: 2, max: 12 });
    });

    it('accepte la virgule décimale', () => {
        const dot = parseDiceFormula('1.5+d4');
        const comma = parseDiceFormula('1,5+d4');
        expect(dot.min).toBe(2.5);
        expect(comma.min).toBe(dot.min);
        expect(comma.max).toBe(dot.max);
    });

    it('combine dés et tranche', () => {
        expect(parseDiceFormula('2d6+[2-6]')).toMatchObject({
            min: 4,
            max: 18,
            rangeEquivalents: ['[2-6] = 1d5+1'],
        });
    });

    it.each([
        '<?php echo 1;',
        '<script>alert(1)</script>',
        '2d6; system("id")',
        '1e999',
        '99d6',
        '1d99999',
        '(2d6)+3',
        'foo',
        "1'; DROP TABLE users;--",
        '[1-99999]',
        '2d6+',
    ])('refuse %s', (input) => {
        const parsed = parseDiceFormula(input);
        expect(parsed.isValid).toBe(false);
        expect(parsed.isRecognized).toBe(false);
        expect(parsed.min).toBeNull();
    });
});

describe('rollDiceFormula', () => {
    it('reste dans les bornes d’une tranche', () => {
        for (let i = 0; i < 20; i += 1) {
            const rolled = rollDiceFormula('[2-6]');
            expect(rolled.isValid).toBe(true);
            expect(rolled.result).toBeGreaterThanOrEqual(2);
            expect(rolled.result).toBeLessThanOrEqual(6);
        }
    });

    it('refuse une formule dangereuse', () => {
        expect(rollDiceFormula('<script>')).toMatchObject({ isValid: false });
    });
});
