import { describe, expect, it } from 'vitest';
import { Capability } from '@/Models/Entity/Capability';

describe('Capability.toFormData()', () => {
    it('conserve ritual_available à false (ne pas le réduire à null)', () => {
        const capability = new Capability({
            name: 'Bond',
            ritual_available: false,
        });

        expect(capability.ritualAvailable).toBe(false);
        expect(capability.toFormData().ritual_available).toBe(false);
    });

    it('conserve ritual_available à true', () => {
        const capability = new Capability({
            name: 'Rite',
            ritual_available: true,
        });

        expect(capability.ritualAvailable).toBe(true);
        expect(capability.toFormData().ritual_available).toBe(true);
    });

    it('laisse ritual_available à null si le champ est absent', () => {
        const capability = new Capability({ name: 'Inconnu' });

        expect(capability.ritualAvailable).toBeNull();
        expect(capability.toFormData().ritual_available).toBeNull();
    });
});
