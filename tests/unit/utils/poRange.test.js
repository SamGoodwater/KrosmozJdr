import { describe, expect, it } from 'vitest';
import { formatPoRange, parsePoRange } from '@/Utils/Entity/poRange.js';

describe('poRange', () => {
    it('formate une borne unique', () => {
        expect(formatPoRange('4', '4')).toBe('4');
        expect(formatPoRange(1, 1)).toBe('1');
        expect(formatPoRange('0', null)).toBe('0');
        expect(formatPoRange('', '')).toBe('');
    });

    it('formate une plage min-max', () => {
        expect(formatPoRange('1', '6')).toBe('1-6');
        expect(formatPoRange(' 1 ', ' 6 ')).toBe('1-6');
    });

    it('parse une valeur unique vers min=max', () => {
        expect(parsePoRange('4')).toEqual({ po_min: '4', po_max: '4' });
        expect(parsePoRange(' 0 ')).toEqual({ po_min: '0', po_max: '0' });
    });

    it('parse une plage avec tiret simple ou long', () => {
        expect(parsePoRange('1-6')).toEqual({ po_min: '1', po_max: '6' });
        expect(parsePoRange('1–6')).toEqual({ po_min: '1', po_max: '6' });
        expect(parsePoRange('1—6')).toEqual({ po_min: '1', po_max: '6' });
        expect(parsePoRange(' 2 - 8 ')).toEqual({ po_min: '2', po_max: '8' });
    });

    it('ignore les tirets dans les formules entre crochets', () => {
        expect(parsePoRange('[level]-6')).toEqual({ po_min: '[level]', po_max: '6' });
        expect(parsePoRange('[a-b]')).toEqual({ po_min: '[a-b]', po_max: '[a-b]' });
        expect(parsePoRange('[a-b]-3')).toEqual({ po_min: '[a-b]', po_max: '3' });
    });

    it('gère le vide', () => {
        expect(parsePoRange('')).toEqual({ po_min: null, po_max: null });
        expect(parsePoRange(null)).toEqual({ po_min: null, po_max: null });
        expect(parsePoRange('-')).toEqual({ po_min: null, po_max: null });
    });
});
