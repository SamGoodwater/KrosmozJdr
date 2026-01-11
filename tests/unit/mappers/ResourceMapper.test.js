/**
 * Tests unitaires pour ResourceMapper
 *
 * @description
 * Vérifie que :
 * - fromApi transforme correctement les données backend en instances Resource
 * - fromApiArray transforme un tableau de données
 * - fromForm transforme les données de formulaire avec les nouvelles transformations
 * - fromBulkForm transforme les données bulk (sans build dans descriptors)
 * - toApi transforme une instance Resource en données pour l'API
 */

import { describe, it, expect } from 'vitest';
import { ResourceMapper } from '@/Mappers/Entity/ResourceMapper.js';
import { Resource } from '@/Models/Entity/Resource.js';

describe('ResourceMapper', () => {
  describe('fromApi', () => {
    it('transforme une réponse API en instance Resource', () => {
      const payload = {
        id: 1,
        name: 'Test Resource',
        description: 'Description test',
        level: '50',
        rarity: 3,
        price: '100',
        weight: '2.5',
        dofus_version: '2.70',
        usable: true,
        auto_update: false,
        is_visible: 'user',
        image_url: 'https://example.com/image.jpg',
        resource_type_id: 5,
        created_at: '2024-01-01T00:00:00Z',
        updated_at: '2024-01-02T00:00:00Z',
      };

      const resource = ResourceMapper.fromApi(payload);

      expect(resource).toBeInstanceOf(Resource);
      expect(resource.id).toBe(1);
      expect(resource.name).toBe('Test Resource');
      expect(resource.description).toBe('Description test');
      expect(resource.level).toBe(50);
      expect(resource.rarity).toBe(3);
      expect(resource.price).toBe(100);
      expect(resource.weight).toBe(2.5);
      expect(resource.dofus_version).toBe('2.70');
      expect(resource.usable).toBe(true);
      expect(resource.auto_update).toBe(false);
      expect(resource.is_visible).toBe('user');
      expect(resource.image).toBe('https://example.com/image.jpg');
      expect(resource.resource_type_id).toBe(5);
      expect(resource.created_at).toBeInstanceOf(Date);
      expect(resource.updated_at).toBeInstanceOf(Date);
    });

    it('gère les valeurs nulles et undefined', () => {
      const payload = {
        id: 1,
        name: 'Test',
        level: null,
        rarity: undefined,
        price: null,
      };

      const resource = ResourceMapper.fromApi(payload);

      expect(resource.level).toBeNull();
      expect(resource.rarity).toBe(0); // Valeur par défaut
      expect(resource.price).toBeNull();
    });

    it('gère les alias de champs (image_url, imageUrl)', () => {
      const payload1 = { id: 1, name: 'Test', image_url: 'url1.jpg' };
      const payload2 = { id: 2, name: 'Test', imageUrl: 'url2.jpg' };
      const payload3 = { id: 3, name: 'Test', image: 'url3.jpg' };

      expect(ResourceMapper.fromApi(payload1).image).toBe('url1.jpg');
      expect(ResourceMapper.fromApi(payload2).image).toBe('url2.jpg');
      expect(ResourceMapper.fromApi(payload3).image).toBe('url3.jpg');
    });

    it('retourne une Resource vide si payload est null', () => {
      const resource = ResourceMapper.fromApi(null);
      expect(resource).toBeInstanceOf(Resource);
      expect(resource.id).toBeNull();
    });
  });

  describe('fromApiArray', () => {
    it('transforme un tableau de réponses API', () => {
      const list = [
        { id: 1, name: 'Resource 1', rarity: 1 },
        { id: 2, name: 'Resource 2', rarity: 2 },
      ];

      const resources = ResourceMapper.fromApiArray(list);

      expect(resources).toHaveLength(2);
      expect(resources[0]).toBeInstanceOf(Resource);
      expect(resources[0].id).toBe(1);
      expect(resources[1].id).toBe(2);
    });

    it('gère un tableau vide', () => {
      const resources = ResourceMapper.fromApiArray([]);
      expect(resources).toHaveLength(0);
    });

    it('gère null ou undefined', () => {
      expect(ResourceMapper.fromApiArray(null)).toHaveLength(0);
      expect(ResourceMapper.fromApiArray(undefined)).toHaveLength(0);
    });
  });

  describe('fromForm', () => {
    it('transforme les données de formulaire avec les nouvelles transformations', () => {
      const formData = {
        id: 1,
        name: 'Test Resource',
        description: 'Description',
        level: '50',
        price: '100',
        weight: '2.5',
        rarity: '3',
        dofus_version: '2.70',
        usable: true,
        auto_update: '1',
        is_visible: 'user',
        image: 'https://example.com/image.jpg',
        resource_type_id: '5',
      };

      const resource = ResourceMapper.fromForm(formData);

      expect(resource).toBeInstanceOf(Resource);
      expect(resource.id).toBe(1);
      expect(resource.name).toBe('Test Resource');
      expect(resource.description).toBe('Description');
      expect(resource.level).toBe(50); // Converti en Number
      expect(resource.price).toBe('100'); // Reste String
      expect(resource.weight).toBe('2.5'); // Reste String
      expect(resource.rarity).toBe(3); // Converti en Number
      expect(resource.dofus_version).toBe('2.70');
      expect(resource.usable).toBe(true);
      expect(resource.auto_update).toBe(true); // '1' converti en true
      expect(resource.is_visible).toBe('user');
      expect(resource.image).toBe('https://example.com/image.jpg');
      expect(resource.resource_type_id).toBe(5); // Converti en Number
    });

    it('gère les valeurs vides (null/undefined)', () => {
      const formData = {
        name: 'Test',
        description: '',
        level: '',
        price: '',
        rarity: '',
        resource_type_id: '',
      };

      const resource = ResourceMapper.fromForm(formData);

      expect(resource.description).toBeNull(); // '' devient null
      expect(resource.level).toBeNull(); // '' devient null
      expect(resource.price).toBeNull(); // '' devient null
      expect(resource.rarity).toBe(0); // '' devient 0 (valeur par défaut)
      expect(resource.resource_type_id).toBeNull(); // '' devient null
    });

    it('retourne une Resource vide si formData est null', () => {
      const resource = ResourceMapper.fromForm(null);
      expect(resource).toBeInstanceOf(Resource);
      expect(resource.id).toBeNull();
    });
  });

  describe('fromBulkForm', () => {
    it('transforme les données bulk sans utiliser build des descriptors', () => {
      const formData = {
        resource_type_id: '5',
        rarity: '3',
        level: '50',
        usable: '1',
        auto_update: true,
        is_visible: 'user',
        price: '100',
        weight: '2.5',
        dofus_version: '2.70',
        description: 'Description',
        image: 'https://example.com/image.jpg',
        dofusdb_id: '12345',
        official_id: '67890',
      };

      const result = ResourceMapper.fromBulkForm(formData);

      expect(result.resource_type_id).toBe(5); // Converti en Number
      expect(result.rarity).toBe(3); // Converti en Number
      expect(result.level).toBe('50'); // Reste String
      expect(result.usable).toBe(true); // '1' converti en true
      expect(result.auto_update).toBe(true); // true reste true
      expect(result.is_visible).toBe('user');
      expect(result.price).toBe('100'); // Reste String
      expect(result.weight).toBe('2.5'); // Reste String
      expect(result.dofus_version).toBe('2.70');
      expect(result.description).toBe('Description');
      expect(result.image).toBe('https://example.com/image.jpg');
      expect(result.dofusdb_id).toBe('12345'); // Reste String
      expect(result.official_id).toBe(67890); // Converti en Number
    });

    it('gère les valeurs vides (null/empty string)', () => {
      const formData = {
        resource_type_id: '',
        rarity: '',
        level: '',
        price: '',
        description: '',
        image: '',
        dofusdb_id: '',
        official_id: '',
      };

      const result = ResourceMapper.fromBulkForm(formData);

      expect(result.resource_type_id).toBeNull();
      expect(result.rarity).toBeNull();
      expect(result.level).toBeNull();
      expect(result.price).toBeNull();
      expect(result.description).toBeNull();
      expect(result.image).toBeNull();
      expect(result.dofusdb_id).toBeNull();
      expect(result.official_id).toBeNull();
    });

    it('gère les valeurs null explicitement', () => {
      const formData = {
        resource_type_id: null,
        rarity: null,
        level: null,
      };

      const result = ResourceMapper.fromBulkForm(formData);

      expect(result.resource_type_id).toBeNull();
      expect(result.rarity).toBeNull();
      expect(result.level).toBeNull();
    });

    it('ignore les champs undefined', () => {
      const formData = {
        rarity: '3',
        // resource_type_id est undefined
      };

      const result = ResourceMapper.fromBulkForm(formData);

      expect(result.rarity).toBe(3);
      expect(result.resource_type_id).toBeUndefined();
    });

    it('retourne un objet vide si formData est null', () => {
      const result = ResourceMapper.fromBulkForm(null);
      expect(result).toEqual({});
    });

    it('gère les booléens correctement', () => {
      const formData1 = { usable: '1', auto_update: true };
      const formData2 = { usable: true, auto_update: 1 };
      const formData3 = { usable: false, auto_update: '0' };

      expect(ResourceMapper.fromBulkForm(formData1).usable).toBe(true);
      expect(ResourceMapper.fromBulkForm(formData1).auto_update).toBe(true);
      expect(ResourceMapper.fromBulkForm(formData2).usable).toBe(true);
      expect(ResourceMapper.fromBulkForm(formData2).auto_update).toBe(true);
      expect(ResourceMapper.fromBulkForm(formData3).usable).toBe(false);
      expect(ResourceMapper.fromBulkForm(formData3).auto_update).toBe(false);
    });
  });

  describe('toApi', () => {
    it('transforme une instance Resource en données pour l\'API', () => {
      const resource = new Resource({
        id: 1,
        name: 'Test Resource',
        description: 'Description',
        level: 50,
        price: 100,
        weight: 2.5,
        rarity: 3,
        dofus_version: '2.70',
        usable: true,
        auto_update: false,
        is_visible: 'user',
        image: 'https://example.com/image.jpg',
        resource_type_id: 5,
      });

      const result = ResourceMapper.toApi(resource);

      expect(result).toEqual({
        id: 1,
        dofusdb_id: undefined,
        official_id: undefined,
        name: 'Test Resource',
        description: 'Description',
        level: 50,
        price: 100,
        weight: 2.5,
        rarity: 3,
        dofus_version: '2.70',
        usable: true,
        auto_update: false,
        is_visible: 'user',
        image: 'https://example.com/image.jpg',
        resource_type_id: 5,
      });
    });

    it('retourne un objet vide si resource est null', () => {
      expect(ResourceMapper.toApi(null)).toEqual({});
      expect(ResourceMapper.toApi(undefined)).toEqual({});
    });
  });
});
