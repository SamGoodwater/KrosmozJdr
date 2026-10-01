// @vitest-environment node
import { describe, expect, it } from 'vitest';
import {
    buildFormulaTableView,
    formulaTableRangeLabel,
} from '../../../../resources/js/Utils/characteristic/formulaConfig.js';

describe('formulaTableRangeLabel', () => {
    it('découpe une tranche jusqu’au palier suivant exclus', () => {
        expect(formulaTableRangeLabel(1, 3)).toBe('1–2');
        expect(formulaTableRangeLabel(5, 8)).toBe('5–7');
        expect(formulaTableRangeLabel(3, 4)).toBe('3');
        expect(formulaTableRangeLabel(16, null)).toBe('16+');
    });
});

describe('buildFormulaTableView', () => {
    it('rend les paliers PM selon le niveau', () => {
        const view = buildFormulaTableView(
            '{"1":"0","3":"1","5":"2","8":"3","11":"4","16":"5","characteristic":"level"}',
        );
        expect(view).toEqual({
            characteristic: 'level',
            rows: [
                { range: '1–2', value: '0' },
                { range: '3–4', value: '1' },
                { range: '5–7', value: '2' },
                { range: '8–10', value: '3' },
                { range: '11–15', value: '4' },
                { range: '16+', value: '5' },
            ],
        });
    });

    it('laisse une expression simple hors tableau', () => {
        expect(buildFormulaTableView('[level]/4+2')).toBeNull();
        expect(buildFormulaTableView('')).toBeNull();
    });
});
