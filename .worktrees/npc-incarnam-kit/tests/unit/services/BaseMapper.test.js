/**
 * Tests unitaires pour BaseMapper
 * 
 * @description
 * Tests pour la classe de base des mappers : normalisation, permissions, relations, etc.
 */
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { BaseMapper } from '@/Utils/Services/BaseMapper';
import { TransformService } from '@/Utils/Services/TransformService';

// Classe de test qui hérite de BaseMapper
class TestMapper extends BaseMapper {
  static mapToModel(rawData) {
    const normalized = this.normalize(rawData);
    return normalized;
  }
}

describe('BaseMapper', () => {
  describe('normalize', () => {
    it('devrait normaliser des données brutes', () => {
      const rawData = { id: 1, title: 'Test' };
      const normalized = BaseMapper.normalize(rawData);
      expect(normalized).toEqual({ id: 1, title: 'Test' });
    });

    it('devrait extraire les données depuis .data', () => {
      const rawData = {
        data: { id: 1, title: 'Test' },
        can: { update: true }
      };
      const normalized = BaseMapper.normalize(rawData);
      expect(normalized.id).toBe(1);
      expect(normalized.title).toBe('Test');
      expect(normalized.can).toEqual({ update: true });
    });

    it('devrait fusionner .data et .can au niveau racine', () => {
      const rawData = {
        data: { id: 1, title: 'Test' },
        can: { update: true, delete: false }
      };
      const normalized = BaseMapper.normalize(rawData);
      expect(normalized.can).toEqual({ update: true, delete: false });
    });

    it('devrait retourner null si rawData est null', () => {
      const normalized = BaseMapper.normalize(null);
      expect(normalized).toBeNull();
    });
  });

  describe('normalizePermissions', () => {
    it('devrait normaliser les permissions depuis can', () => {
      const rawData = {
        can: {
          update: true,
          delete: false,
          forceDelete: true,
          restore: false,
          view: true
        }
      };
      const permissions = BaseMapper.normalizePermissions(rawData);
      expect(permissions).toEqual({
        update: true,
        delete: false,
        forceDelete: true,
        restore: false,
        view: true
      });
    });

    it('devrait normaliser les permissions depuis .data.can', () => {
      const rawData = {
        data: {
          can: {
            update: 1,
            delete: 0
          }
        }
      };
      const permissions = BaseMapper.normalizePermissions(rawData);
      expect(permissions.update).toBe(true);
      expect(permissions.delete).toBe(false);
    });

    it('devrait retourner des permissions par défaut si can est absent', () => {
      const rawData = { id: 1 };
      const permissions = BaseMapper.normalizePermissions(rawData);
      expect(permissions).toEqual({
        update: false,
        delete: false,
        forceDelete: false,
        restore: false,
        view: false
      });
    });

    it('devrait convertir 1 en true et 0 en false', () => {
      const rawData = {
        can: {
          update: 1,
          delete: 0,
          forceDelete: 1,
          restore: 0
        }
      };
      const permissions = BaseMapper.normalizePermissions(rawData);
      expect(permissions.update).toBe(true);
      expect(permissions.delete).toBe(false);
      expect(permissions.forceDelete).toBe(true);
      expect(permissions.restore).toBe(false);
    });
  });

  describe('toEnum / fromEnum', () => {
    it('devrait utiliser TransformService pour toEnum', () => {
      const spy = vi.spyOn(TransformService, 'toEnum');
      BaseMapper.toEnum('value', null, 'default');
      expect(spy).toHaveBeenCalledWith('value', null, 'default');
      spy.mockRestore();
    });

    it('devrait utiliser TransformService pour fromEnum', () => {
      const spy = vi.spyOn(TransformService, 'fromEnum');
      BaseMapper.fromEnum({ value: 'test' });
      expect(spy).toHaveBeenCalledWith({ value: 'test' });
      spy.mockRestore();
    });
  });

  describe('normalizePivot', () => {
    it('devrait utiliser TransformService pour normalizePivot', () => {
      const spy = vi.spyOn(TransformService, 'normalizePivot');
      const pivotData = [{ id: 1 }];
      BaseMapper.normalizePivot(pivotData, { extractIds: true });
      expect(spy).toHaveBeenCalledWith(pivotData, { extractIds: true });
      spy.mockRestore();
    });
  });

  describe('normalizeRelation', () => {
    it('devrait retourner null si relationData est null et isArray est false', () => {
      const result = BaseMapper.normalizeRelation(null, null, false);
      expect(result).toBeNull();
    });

    it('devrait retourner un tableau vide si relationData est null et isArray est true', () => {
      const result = BaseMapper.normalizeRelation(null, null, true);
      expect(result).toEqual([]);
    });

    it('devrait mapper une relation avec un mapper', () => {
      const mapperClass = {
        mapToModel: vi.fn((data) => ({ mapped: data }))
      };
      const relationData = { id: 1, name: 'Test' };
      const result = BaseMapper.normalizeRelation(relationData, mapperClass, false);
      expect(mapperClass.mapToModel).toHaveBeenCalledWith(relationData);
      expect(result).toEqual({ mapped: relationData });
    });

    it('devrait mapper un tableau de relations', () => {
      const mapperClass = {
        mapToModel: vi.fn((data) => ({ mapped: data }))
      };
      const relationData = [
        { id: 1, name: 'Test 1' },
        { id: 2, name: 'Test 2' }
      ];
      const result = BaseMapper.normalizeRelation(relationData, mapperClass, true);
      expect(mapperClass.mapToModel).toHaveBeenCalledTimes(2);
      expect(result).toHaveLength(2);
    });

    it('devrait retourner les données telles quelles si pas de mapper', () => {
      const relationData = { id: 1 };
      const result = BaseMapper.normalizeRelation(relationData, null, false);
      expect(result).toEqual(relationData);
    });
  });

  describe('extractValue', () => {
    it('devrait extraire une valeur simple', () => {
      const obj = { id: 1, name: 'Test' };
      const result = BaseMapper.extractValue(obj, 'id');
      expect(result).toBe(1);
    });

    it('devrait retourner la valeur par défaut si la clé n\'existe pas', () => {
      const obj = { id: 1 };
      const result = BaseMapper.extractValue(obj, 'name', 'default');
      expect(result).toBe('default');
    });

    it('devrait naviguer dans un chemin avec points', () => {
      const obj = { user: { profile: { name: 'John' } } };
      const result = BaseMapper.extractValue(obj, 'user.profile.name');
      expect(result).toBe('John');
    });

    it('devrait essayer plusieurs clés si un tableau est fourni', () => {
      const obj = { title: 'Test' };
      const result = BaseMapper.extractValue(obj, ['name', 'title', 'label'], 'default');
      expect(result).toBe('Test');
    });

    it('devrait retourner null si l\'objet est null', () => {
      const result = BaseMapper.extractValue(null, 'id', 'default');
      expect(result).toBe('default');
    });
  });

  describe('mapToModels', () => {
    it('devrait mapper un tableau de données', () => {
      const rawDataArray = [
        { id: 1 },
        { id: 2 }
      ];
      const result = TestMapper.mapToModels(rawDataArray);
      expect(result).toHaveLength(2);
      expect(result[0].id).toBe(1);
      expect(result[1].id).toBe(2);
    });

    it('devrait retourner un tableau vide si pas un tableau', () => {
      const result = TestMapper.mapToModels(null);
      expect(result).toEqual([]);
    });
  });
});

