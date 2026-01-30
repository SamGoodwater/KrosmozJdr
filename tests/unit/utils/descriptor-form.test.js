/**
 * Tests unitaires pour descriptor-form utils
 *
 * @description
 * Vérifie que :
 * - createFieldsConfigFromDescriptors génère correctement fieldsConfig
 * - createBulkFieldMetaFromDescriptors génère correctement fieldMeta
 * - createDefaultEntityFromDescriptors génère correctement defaultEntity
 */

import { describe, it, expect } from 'vitest';
import {
    createFieldsConfigFromDescriptors,
    createBulkFieldMetaFromDescriptors,
    createDefaultEntityFromDescriptors,
} from '@/Utils/entity/descriptor-form';

describe('descriptor-form', () => {
    const mockDescriptors = {
        name: {
            key: 'name',
            label: 'Nom',
            edit: {
                form: {
                    type: 'text',
                    required: true,
                    showInCompact: true,
                    bulk: { enabled: false },
                },
            },
        },
        level: {
            key: 'level',
            label: 'Niveau',
            edit: {
                form: {
                    type: 'text',
                    required: false,
                    showInCompact: true,
                    bulk: { enabled: true, nullable: true, build: (v) => (v === '' ? null : String(v)) },
                },
            },
        },
        state: {
            key: 'state',
            label: 'État',
            edit: {
                form: {
                    type: 'select',
                    required: false,
                    showInCompact: true,
                    bulk: { enabled: true, nullable: true, build: (v) => (v === '' || v === null ? null : String(v)) },
                },
            },
        },
        description: {
            key: 'description',
            label: 'Description',
            edit: {
                form: {
                    type: 'textarea',
                    required: false,
                    showInCompact: false,
                    bulk: { enabled: true, nullable: true, build: (v) => (v === '' ? null : String(v)) },
                },
            },
        },
    };

    describe('createFieldsConfigFromDescriptors', () => {
        it('génère fieldsConfig avec tous les champs ayant edit.form', () => {
            const config = createFieldsConfigFromDescriptors(mockDescriptors);

            expect(config.name).toBeDefined();
            expect(config.level).toBeDefined();
            expect(config.state).toBeDefined();
            expect(config.description).toBeDefined();
        });

        it('génère fieldsConfig avec les bonnes propriétés', () => {
            const config = createFieldsConfigFromDescriptors(mockDescriptors);

            expect(config.name.type).toBe('text');
            expect(config.name.required).toBe(true);
            expect(config.name.showInCompact).toBe(true);

            expect(config.level.type).toBe('text');
            expect(config.level.required).toBe(false);

            expect(config.state.type).toBe('select');
        });

        it('exclut les champs sans edit.form', () => {
            const descriptors = {
                ...mockDescriptors,
                id: {
                    key: 'id',
                    label: 'ID',
                    // Pas de edit.form
                },
            };

            const config = createFieldsConfigFromDescriptors(descriptors);

            expect(config.id).toBeUndefined();
            expect(config.name).toBeDefined();
        });
    });

    describe('createBulkFieldMetaFromDescriptors', () => {
        it('génère fieldMeta uniquement pour les champs bulk-enabled', () => {
            const meta = createBulkFieldMetaFromDescriptors(mockDescriptors);

            expect(meta.name).toBeUndefined(); // bulk.enabled = false
            expect(meta.level).toBeDefined();
            expect(meta.state).toBeDefined();
            expect(meta.description).toBeDefined();
        });

        it('génère fieldMeta avec les bonnes propriétés', () => {
            const meta = createBulkFieldMetaFromDescriptors(mockDescriptors);

            expect(meta.level.nullable).toBe(true);
            expect(typeof meta.level.build).toBe('function');

            expect(meta.state.nullable).toBe(true);
            expect(typeof meta.state.build).toBe('function');
        });

        it('génère fieldMeta avec label', () => {
            const meta = createBulkFieldMetaFromDescriptors(mockDescriptors);

            expect(meta.level.label).toBe('Niveau');
            expect(meta.state.label).toBe('État');
        });

        it('exclut les champs sans bulk.enabled', () => {
            const meta = createBulkFieldMetaFromDescriptors(mockDescriptors);

            expect(meta.name).toBeUndefined();
        });
    });

    describe('createDefaultEntityFromDescriptors', () => {
        it('génère defaultEntity avec les valeurs par défaut', () => {
            const descriptors = {
                ...mockDescriptors,
                level: {
                    ...mockDescriptors.level,
                    edit: {
                        form: {
                            ...mockDescriptors.level.edit.form,
                            defaultValue: '1',
                        },
                    },
                },
                state: {
                    ...mockDescriptors.state,
                    edit: {
                        form: {
                            ...mockDescriptors.state.edit.form,
                            defaultValue: 'draft',
                        },
                    },
                },
            };

            const defaultEntity = createDefaultEntityFromDescriptors(descriptors);

            expect(defaultEntity.level).toBe('1');
            expect(defaultEntity.state).toBe('draft');
        });

        it('génère defaultEntity avec valeurs vides si pas de defaultValue', () => {
            const defaultEntity = createDefaultEntityFromDescriptors(mockDescriptors);

            expect(defaultEntity.name).toBe('');
            expect(defaultEntity.level).toBe('');
            expect(defaultEntity.description).toBe('');
        });

        it('génère defaultEntity uniquement pour les champs avec edit.form', () => {
            const descriptors = {
                ...mockDescriptors,
                id: {
                    key: 'id',
                    label: 'ID',
                    // Pas de edit.form
                },
            };

            const defaultEntity = createDefaultEntityFromDescriptors(descriptors);

            expect(defaultEntity.id).toBeUndefined();
            expect(defaultEntity.name).toBeDefined();
        });
    });
});

