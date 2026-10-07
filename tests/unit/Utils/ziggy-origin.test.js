import { describe, expect, it } from 'vitest';
import { resolveZiggyOrigin } from '@/Utils/ziggy-origin.js';

describe('resolveZiggyOrigin', () => {
    it('aligne url/port sur 127.0.0.1 quand le navigateur n’utilise pas localhost', () => {
        const origin = resolveZiggyOrigin('http://127.0.0.1:8000/entities/spells/12624/edit');
        expect(origin.url).toBe('http://127.0.0.1:8000');
        expect(origin.port).toBe(8000);
    });

    it('conserve localhost quand c’est l’hôte courant', () => {
        const origin = resolveZiggyOrigin('http://localhost:8000/pages/accueil');
        expect(origin.url).toBe('http://localhost:8000');
        expect(origin.port).toBe(8000);
    });
});
