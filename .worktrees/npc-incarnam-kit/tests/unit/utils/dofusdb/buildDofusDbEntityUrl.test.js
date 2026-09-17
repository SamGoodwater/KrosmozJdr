/**
 * Tests — buildDofusDbEntityUrl
 */
import { describe, expect, it } from 'vitest';
import {
    buildDofusDbEntityUrl,
    getEntityDofusDbId,
    resolveDofusDbDatabaseSegment,
} from '@/Utils/dofusdb/buildDofusDbEntityUrl';

describe('buildDofusDbEntityUrl', () => {
    it('construit l’URL typée pour spells / monsters / items', () => {
        expect(buildDofusDbEntityUrl('spells', 201)).toBe(
            'https://dofusdb.fr/fr/database/spells/201',
        );
        expect(buildDofusDbEntityUrl('monster', '31')).toBe(
            'https://dofusdb.fr/fr/database/monsters/31',
        );
        expect(buildDofusDbEntityUrl('items', 15)).toBe(
            'https://dofusdb.fr/fr/database/items/15',
        );
    });

    it('retourne null si id ou type manquant', () => {
        expect(buildDofusDbEntityUrl('spells', null)).toBeNull();
        expect(buildDofusDbEntityUrl('unknown', 1)).toBeNull();
        expect(buildDofusDbEntityUrl('', 1)).toBeNull();
    });

    it('résout les segments panoplies / breeds', () => {
        expect(resolveDofusDbDatabaseSegment('panoplies')).toBe('item-sets');
        expect(resolveDofusDbDatabaseSegment('breeds')).toBe('breeds');
    });
});

describe('getEntityDofusDbId', () => {
    it('lit dofusdb_id sur objet brut ou modèle', () => {
        expect(getEntityDofusDbId({ dofusdb_id: 42 })).toBe('42');
        expect(getEntityDofusDbId({ _data: { dofusdb_id: '7' } })).toBe('7');
        expect(getEntityDofusDbId({ dofusdbId: 9 })).toBe('9');
        expect(getEntityDofusDbId({})).toBeNull();
    });
});
