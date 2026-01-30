/**
 * ResourceMapper — Mapper pour transformer les données backend en modèles Resource
 *
 * @description
 * Mapper statique et pur qui transforme les données brutes du backend en instances de Resource.
 * Centralise la normalisation, le renommage et les conversions de types.
 *
 * **Avantages :**
 * - Les modèles deviennent indépendants du backend
 * - Centralisation des transformations (renommage, normalisation, valeurs par défaut)
 * - Les migrations backend ne cassent plus le frontend
 * - Testable sans Vue, sans API
 *
 * @example
 * // Depuis une réponse API
 * const resources = ResourceMapper.fromApiArray(response.data);
 *
 * // Depuis une seule entité
 * const resource = ResourceMapper.fromApi(payload);
 *
 * // Depuis un formulaire
 * const resource = ResourceMapper.fromForm(formData);
 */

import { Resource } from '@/Models/Entity/Resource';

/**
 * Mapper statique pour Resource
 */
export class ResourceMapper {
  /**
   * Transforme une réponse API backend en instance Resource
   *
   * @param {Object} payload - Données brutes du backend
   * @returns {Resource} Instance de Resource normalisée
   */
  static fromApi(payload) {
    if (!payload) {
      return new Resource({});
    }

    return new Resource({
      // Identifiants
      id: payload.id ?? null,
      dofusdb_id: payload.dofusdb_id ?? payload.dofusdbId ?? null,
      official_id: payload.official_id ?? payload.officialId ?? null,

      // Propriétés de base
      name: payload.name ?? '',
      description: payload.description ?? '',
      level: payload.level !== undefined ? Number(payload.level) : null,
      price: payload.price !== undefined ? Number(payload.price) : null,
      weight: payload.weight !== undefined ? Number(payload.weight) : null,
      rarity: payload.rarity !== undefined ? Number(payload.rarity) : 0,
      dofus_version: payload.dofus_version ?? payload.dofusVersion ?? null,
      state: payload.state ?? 'draft',
      auto_update: Boolean(payload.auto_update ?? payload.autoUpdate),
      read_level: payload.read_level !== undefined ? Number(payload.read_level) : (payload.readLevel !== undefined ? Number(payload.readLevel) : 0),
      write_level: payload.write_level !== undefined ? Number(payload.write_level) : (payload.writeLevel !== undefined ? Number(payload.writeLevel) : 4),

      // Images (normalisation des noms de champs)
      image: payload.image ?? payload.image_url ?? payload.imageUrl ?? '',

      // Relations
      resource_type_id: payload.resource_type_id ?? payload.resourceTypeId ?? null,
      resourceType: payload.resource_type ?? payload.resourceType ?? null,
      created_by: payload.created_by ?? payload.createdBy ?? null,
      createdBy: payload.created_by ?? payload.createdBy ?? null,

      // Timestamps (conversion en Date si string)
      created_at: payload.created_at
        ? new Date(payload.created_at)
        : payload.createdAt
          ? new Date(payload.createdAt)
          : null,
      updated_at: payload.updated_at
        ? new Date(payload.updated_at)
        : payload.updatedAt
          ? new Date(payload.updatedAt)
          : null,

      // Relations complexes (conservées telles quelles pour l'instant)
      consumables: payload.consumables ?? [],
      creatures: payload.creatures ?? [],
      items: payload.items ?? [],
      scenarios: payload.scenarios ?? [],
      campaigns: payload.campaigns ?? [],
    });
  }

  /**
   * Transforme un tableau de réponses API en tableau d'instances Resource
   *
   * @param {Array<Object>} list - Tableau de données brutes du backend
   * @returns {Array<Resource>} Tableau d'instances Resource
   */
  static fromApiArray(list) {
    if (!Array.isArray(list)) {
      return [];
    }
    return list.map((item) => this.fromApi(item));
  }

  /**
   * Transforme les données d'un formulaire en instance Resource
   * (pour l'édition/création)
   *
   * @param {Object} formData - Données du formulaire
   * @returns {Resource} Instance de Resource normalisée
   */
  static fromForm(formData) {
    if (!formData) {
      return new Resource({});
    }

    return new Resource({
      // Identifiants
      id: formData.id ?? null,
      dofusdb_id: formData.dofusdb_id ?? formData.dofusdbId ?? null,
      official_id: formData.official_id ?? formData.officialId ?? null,

      // Propriétés de base
      name: formData.name ?? '',
      description: formData.description ?? '',
      level: formData.level !== undefined && formData.level !== '' ? Number(formData.level) : null,
      price: formData.price !== undefined && formData.price !== '' ? Number(formData.price) : null,
      weight: formData.weight !== undefined && formData.weight !== '' ? Number(formData.weight) : null,
      rarity: formData.rarity !== undefined ? Number(formData.rarity) : 0,
      dofus_version: formData.dofus_version ?? formData.dofusVersion ?? null,
      state: formData.state ?? 'draft',
      auto_update: Boolean(formData.auto_update ?? formData.autoUpdate),
      read_level: formData.read_level !== undefined ? Number(formData.read_level) : (formData.readLevel !== undefined ? Number(formData.readLevel) : 0),
      write_level: formData.write_level !== undefined ? Number(formData.write_level) : (formData.writeLevel !== undefined ? Number(formData.writeLevel) : 4),

      // Images
      image: formData.image ?? '',

      // Relations
      resource_type_id: formData.resource_type_id ?? formData.resourceTypeId ?? null,
    });
  }

  /**
   * Transforme les données d'un formulaire bulk en données pour l'API
   * (pour l'édition en masse)
   *
   * ⚠️ IMPORTANT : Cette méthode centralise toutes les transformations de données bulk.
   * Les descriptors ne contiennent plus de fonctions `build`, toute la logique est ici.
   *
   * @param {Object} formData - Données du formulaire bulk
   * @returns {Object} Données formatées pour l'API backend
   */
  static fromBulkForm(formData) {
    if (!formData) {
      return {};
    }

    const result = {};

    // Transformer chaque champ selon son type (logique centralisée ici)
    if (formData.resource_type_id !== undefined) {
      result.resource_type_id = formData.resource_type_id === '' || formData.resource_type_id === null ? null : Number(formData.resource_type_id);
    }

    if (formData.rarity !== undefined) {
      result.rarity = formData.rarity === '' || formData.rarity === null ? null : Number(formData.rarity);
    }

    if (formData.level !== undefined) {
      result.level = formData.level === '' || formData.level === null ? null : String(formData.level);
    }

    if (formData.state !== undefined) {
      result.state = formData.state === '' || formData.state === null ? null : String(formData.state);
    }

    if (formData.auto_update !== undefined) {
      result.auto_update = formData.auto_update === "1" || formData.auto_update === true || formData.auto_update === 1;
    }

    if (formData.read_level !== undefined) {
      result.read_level = formData.read_level === '' || formData.read_level === null ? null : Number(formData.read_level);
    }

    if (formData.write_level !== undefined) {
      result.write_level = formData.write_level === '' || formData.write_level === null ? null : Number(formData.write_level);
    }

    if (formData.price !== undefined) {
      result.price = formData.price === '' || formData.price === null ? null : String(formData.price);
    }

    if (formData.weight !== undefined) {
      result.weight = formData.weight === '' || formData.weight === null ? null : String(formData.weight);
    }

    if (formData.dofus_version !== undefined) {
      result.dofus_version = formData.dofus_version === '' || formData.dofus_version === null ? null : String(formData.dofus_version);
    }

    if (formData.description !== undefined) {
      result.description = formData.description === '' || formData.description === null ? null : String(formData.description);
    }

    if (formData.image !== undefined) {
      result.image = formData.image === '' || formData.image === null ? null : String(formData.image);
    }

    if (formData.dofusdb_id !== undefined) {
      result.dofusdb_id = formData.dofusdb_id === '' || formData.dofusdb_id === null ? null : String(formData.dofusdb_id);
    }

    if (formData.official_id !== undefined) {
      result.official_id = formData.official_id === '' || formData.official_id === null ? null : Number(formData.official_id);
    }

    return result;
  }

  /**
   * Transforme une instance Resource en données pour l'API backend
   * (pour l'envoi de données)
   *
   * @param {Resource} resource - Instance de Resource
   * @returns {Object} Données formatées pour l'API backend
   */
  static toApi(resource) {
    if (!resource || !(resource instanceof Resource)) {
      return {};
    }

    return {
      id: resource.id,
      dofusdb_id: resource.dofusdbId,
      official_id: resource.officialId,
      name: resource.name,
      description: resource.description,
      level: resource.level,
      price: resource.price,
      weight: resource.weight,
      rarity: resource.rarity,
      dofus_version: resource.dofusVersion,
      state: resource.state,
      auto_update: resource.autoUpdate,
      read_level: resource.readLevel,
      write_level: resource.writeLevel,
      image: resource.image,
      resource_type_id: resource.resourceTypeId,
    };
  }
}

export default ResourceMapper;
