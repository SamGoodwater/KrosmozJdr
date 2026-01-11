/**
 * Tests unitaires pour entity-registry
 *
 * @description
 * Vérifie que :
 * - normalizeEntityType normalise correctement les types
 * - getEntityConfig retourne la bonne config
 * - getEntityResponseAdapter retourne le bon adapter
 */

import { describe, it, expect } from 'vitest';
import { normalizeEntityType, getEntityConfig, getEntityResponseAdapter } from '@/Entities/entity-registry';

describe('entity-registry', () => {
    describe('normalizeEntityType', () => {
        it('normalise resource vers resources', () => {
            expect(normalizeEntityType('resource')).toBe('resources');
            expect(normalizeEntityType('resources')).toBe('resources');
        });

        it('normalise resource-type vers resource-types', () => {
            expect(normalizeEntityType('resourceType')).toBe('resource-types');
            expect(normalizeEntityType('resource-types')).toBe('resource-types');
            expect(normalizeEntityType('resourceTypes')).toBe('resource-types');
        });

        it('normalise item vers items', () => {
            expect(normalizeEntityType('item')).toBe('items');
            expect(normalizeEntityType('items')).toBe('items');
        });

        it('normalise spell vers spells', () => {
            expect(normalizeEntityType('spell')).toBe('spells');
            expect(normalizeEntityType('spells')).toBe('spells');
        });

        it('normalise creature vers creatures', () => {
            expect(normalizeEntityType('creature')).toBe('creatures');
            expect(normalizeEntityType('creatures')).toBe('creatures');
        });

        it('normalise npc vers npcs', () => {
            expect(normalizeEntityType('npc')).toBe('npcs');
            expect(normalizeEntityType('npcs')).toBe('npcs');
        });

        it('normalise classe vers classes', () => {
            expect(normalizeEntityType('classe')).toBe('classes');
            expect(normalizeEntityType('classes')).toBe('classes');
        });

        it('normalise capability vers capabilities', () => {
            expect(normalizeEntityType('capability')).toBe('capabilities');
            expect(normalizeEntityType('capabilities')).toBe('capabilities');
        });

        it('normalise specialization vers specializations', () => {
            expect(normalizeEntityType('specialization')).toBe('specializations');
            expect(normalizeEntityType('specializations')).toBe('specializations');
        });

        it('normalise shop vers shops', () => {
            expect(normalizeEntityType('shop')).toBe('shops');
            expect(normalizeEntityType('shops')).toBe('shops');
        });

        it('retourne la valeur originale si non reconnue', () => {
            expect(normalizeEntityType('unknown')).toBe('unknown');
            expect(normalizeEntityType('')).toBe('');
        });
    });

    describe('getEntityConfig', () => {
        it('retourne la config pour resources', () => {
            const config = getEntityConfig('resources');

            expect(config).toBeDefined();
            expect(config.key).toBe('resources');
            expect(config.getDescriptors).toBeDefined();
            expect(config.buildCell).toBeDefined();
            // viewFields peut être un tableau (QUICK_EDIT_FIELDS) ou un objet avec quickEdit/compact/extended
            expect(config.viewFields).toBeDefined();
            // Pour resources, c'est encore un objet, pour les autres entités migrées c'est un tableau
            if (Array.isArray(config.viewFields)) {
                expect(config.viewFields.length).toBeGreaterThan(0);
            } else {
                expect(config.viewFields).toHaveProperty('quickEdit');
            }
            expect(config.responseAdapter).toBeDefined();
        });

        it('retourne la config pour spells', () => {
            const config = getEntityConfig('spells');

            expect(config).toBeDefined();
            expect(config.key).toBe('spells');
            expect(config.getDescriptors).toBeDefined();
            expect(config.buildCell).toBeDefined();
            expect(config.responseAdapter).toBeDefined();
        });

        it('retourne la config pour creatures', () => {
            const config = getEntityConfig('creatures');

            expect(config).toBeDefined();
            expect(config.key).toBe('creatures');
        });

        it('retourne null pour un type inconnu', () => {
            const config = getEntityConfig('unknown');

            expect(config).toBeNull();
        });

        it('normalise le type avant de chercher', () => {
            const config1 = getEntityConfig('spell');
            const config2 = getEntityConfig('spells');

            expect(config1).toBeDefined();
            expect(config2).toBeDefined();
            expect(config1.key).toBe(config2.key);
        });
    });

    describe('getEntityResponseAdapter', () => {
        it('retourne l\'adapter pour resources', () => {
            const adapter = getEntityResponseAdapter('resources');

            expect(adapter).toBeDefined();
            expect(typeof adapter).toBe('function');
        });

        it('retourne l\'adapter pour spells', () => {
            const adapter = getEntityResponseAdapter('spells');

            expect(adapter).toBeDefined();
            expect(typeof adapter).toBe('function');
        });

        it('retourne null pour un type inconnu', () => {
            const adapter = getEntityResponseAdapter('unknown');

            expect(adapter).toBeNull();
        });

        it('normalise le type avant de chercher', () => {
            const adapter1 = getEntityResponseAdapter('spell');
            const adapter2 = getEntityResponseAdapter('spells');

            expect(adapter1).toBeDefined();
            expect(adapter2).toBeDefined();
        });
    });
});

