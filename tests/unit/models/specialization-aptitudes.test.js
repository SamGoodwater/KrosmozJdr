import { describe, expect, it } from 'vitest';
import { Specialization } from '@/Models/Entity/Specialization';

describe('Specialization.aptitudes', () => {
    it('expose les aptitudes déjà liées (éditeur de relations)', () => {
        const specialization = new Specialization({
            id: 4,
            name: 'Artiste',
            aptitudes: [
                { id: 11, name: 'Spectacle ambulant', pivot: { level: 3 } },
                { id: 12, name: 'Répertoire', pivot: { level: 9 } },
            ],
            capabilities: [{ id: 2, name: 'Applaudissement' }],
        });

        expect(specialization.aptitudes).toHaveLength(2);
        expect(specialization.aptitudes[0].id).toBe(11);
        expect(specialization.aptitudes[0].pivot.level).toBe(3);
        expect(specialization.capabilities).toHaveLength(1);
    });

    it('renvoie un tableau vide sans relation chargée', () => {
        const specialization = new Specialization({ id: 1, name: 'Artisan·e' });
        expect(specialization.aptitudes).toEqual([]);
    });
});
