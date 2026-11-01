import { describe, it, expect } from 'vitest';
import {
    RICH_TEXT_COLOR_PRESETS,
    findRichTextColorPreset,
} from '@/Constants/richTextColorPresets';

describe('richTextColorPresets', () => {
    it('expose une pastille par teinte Tailwind courante en nuance 300', () => {
        expect(RICH_TEXT_COLOR_PRESETS.length).toBeGreaterThanOrEqual(20);
        const ids = RICH_TEXT_COLOR_PRESETS.map((preset) => preset.id);
        expect(ids).toContain('teal-300');
        expect(ids).toContain('violet-300');
        expect(ids).toContain('sky-300');
        expect(ids).toContain('red-300');
    });

    it('utilise des hex #rrggbb uniques', () => {
        const hexes = RICH_TEXT_COLOR_PRESETS.map((preset) => preset.hex.toLowerCase());
        expect(hexes.every((hex) => /^#[0-9a-f]{6}$/.test(hex))).toBe(true);
        expect(new Set(hexes).size).toBe(hexes.length);
    });

    it('retrouve une pastille par hex (insensible à la casse)', () => {
        expect(findRichTextColorPreset('#5EEAD4')?.id).toBe('teal-300');
        expect(findRichTextColorPreset('#c4b5fd')?.id).toBe('violet-300');
        expect(findRichTextColorPreset('nope')).toBeUndefined();
    });
});
