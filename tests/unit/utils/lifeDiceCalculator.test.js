// @vitest-environment node
import { describe, expect, it } from 'vitest';
import { rollDiceFormula } from '../../../resources/js/Utils/dice/diceParser.js';
import {
    LIFE_DICE_METHOD_STABLE,
    parseClassDieSides,
    previewLifePoints,
    totalForClass,
    totalForFormula,
} from '../../../resources/js/Utils/dice/lifeDiceCalculator.js';

describe('lifeDiceCalculator', () => {
    it('fixe 5+2d6/2 à 8 quand d6 = 3', () => {
        const rolled = rollDiceFormula('5+2d6/2', { fixedBySides: { 6: 3 } });
        expect(rolled.isValid).toBe(true);
        expect(rolled.result).toBe(8);
    });

    it('relance les 1', () => {
        const sequence = [1, 1, 5];
        let index = 0;
        const rolled = rollDiceFormula('1d6', {
            rerollOnes: true,
            randomInt: () => sequence[index++] ?? 6,
        });
        expect(rolled.isValid).toBe(true);
        expect(rolled.result).toBe(5);
    });

    it('niveau 1 d10 = 10', () => {
        const total = totalForClass(10, 1, LIFE_DICE_METHOD_STABLE);
        expect(total.ok).toBe(true);
        expect(total.lifeDiceTotal).toBe(10);
    });

    it('mod. Vitalité négatif = 0 dans l’aperçu', () => {
        expect(previewLifePoints(20, 4, -2)).toBe(20);
        expect(previewLifePoints(20, 4, 1)).toBe(24);
    });

    it('parse le dé de classe', () => {
        expect(parseClassDieSides('1d8')).toBe(8);
        expect(parseClassDieSides('d12')).toBe(12);
        expect(parseClassDieSides('foo')).toBeNull();
    });

    it('formule monstre niveau 1 = max', () => {
        const total = totalForFormula('5+2d6/2', 1, LIFE_DICE_METHOD_STABLE);
        expect(total.ok).toBe(true);
        expect(total.lifeDiceTotal).toBe(11);
    });
});
