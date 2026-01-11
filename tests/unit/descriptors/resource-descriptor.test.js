/**
 * Tests unitaires pour ResourceDescriptor (nouveau système)
 *
 * @description
 * Vérifie que :
 * - La structure du descriptor est correcte
 * - Les configurations (table, vues, formulaires, bulk) fonctionnent
 * - Les permissions sont respectées
 * - La compatibilité avec l'ancien système est maintenue
 */

import { describe, it, expect, beforeEach } from "vitest";
import ResourceDescriptor from "@/Entities/resource/ResourceDescriptor.js";
import { getResourceFieldDescriptors } from "@/Entities/resource/resource-descriptors.js";

describe("ResourceDescriptor (nouveau système)", () => {
  describe("Structure du descriptor", () => {
    it("est une instance de EntityDescriptor", () => {
      expect(ResourceDescriptor).toBeDefined();
      expect(ResourceDescriptor.entityType).toBe("resource");
    });

    it("retourne les field descriptors via getFieldDescriptors", () => {
      const descriptors = ResourceDescriptor.getFieldDescriptors();
      expect(descriptors).toBeDefined();
      expect(typeof descriptors).toBe("object");
      expect(descriptors.name).toBeDefined();
      expect(descriptors.name.key).toBe("name");
    });

    it("getFieldDescriptors est compatible avec l'ancien système", () => {
      const newDescriptors = ResourceDescriptor.getFieldDescriptors();
      const oldDescriptors = getResourceFieldDescriptors();
      expect(Object.keys(newDescriptors)).toEqual(Object.keys(oldDescriptors));
    });
  });

  describe("Configuration tableau", () => {
    it("retourne une configuration de tableau valide", () => {
      const ctx = {
        capabilities: { view: true, updateAny: true, createAny: true },
      };
      const tableConfig = ResourceDescriptor.getTableConfig(ctx);

      expect(tableConfig).toBeDefined();
      expect(tableConfig.id).toBe("resources.index");
      expect(tableConfig.columns).toBeDefined();
      expect(Array.isArray(tableConfig.columns)).toBe(true);
    });

    it("inclut la colonne name comme colonne principale", () => {
      const ctx = { capabilities: { view: true } };
      const tableConfig = ResourceDescriptor.getTableConfig(ctx);
      const nameColumn = tableConfig.columns.find((col) => col.id === "name");

      expect(nameColumn).toBeDefined();
      expect(nameColumn.isMain).toBe(true);
      expect(nameColumn.hideable).toBe(false);
    });

    it("respecte les permissions pour les colonnes conditionnelles", () => {
      const ctxWithPermission = {
        capabilities: { view: true, updateAny: true, createAny: true },
      };
      const ctxWithoutPermission = {
        capabilities: { view: true },
      };

      const configWith = ResourceDescriptor.getTableConfig(ctxWithPermission);
      const configWithout = ResourceDescriptor.getTableConfig(ctxWithoutPermission);

      const dofusdbColumnWith = configWith.columns.find((col) => col.id === "dofusdb_id");
      const dofusdbColumnWithout = configWithout.columns.find((col) => col.id === "dofusdb_id");

      expect(dofusdbColumnWith).toBeDefined();
      expect(dofusdbColumnWithout).toBeUndefined();
    });

    it("configure quickEdit correctement", () => {
      const ctx = {
        capabilities: { view: true, updateAny: true },
      };
      const tableConfig = ResourceDescriptor.getTableConfig(ctx);

      expect(tableConfig._metadata).toBeDefined();
      expect(tableConfig._metadata.quickEdit).toBeDefined();
      expect(tableConfig._metadata.quickEdit.enabled).toBe(true);
      expect(tableConfig._metadata.quickEdit.permission).toBe("updateAny");
    });

    it("configure les actions correctement", () => {
      const ctx = {
        capabilities: { view: true },
      };
      const tableConfig = ResourceDescriptor.getTableConfig(ctx);

      expect(tableConfig._metadata.actions).toBeDefined();
      expect(tableConfig._metadata.actions.enabled).toBe(true);
      expect(tableConfig._metadata.actions.available).toContain("view");
      expect(tableConfig._metadata.actions.available).toContain("edit");
    });
  });

  describe("Configuration vues", () => {
    it("retourne une configuration pour la vue compact", () => {
      const ctx = { capabilities: { view: true } };
      const viewConfig = ResourceDescriptor.getViewConfig("compact", ctx);

      expect(viewConfig).toBeDefined();
      expect(viewConfig.name).toBe("compact");
      expect(viewConfig.label).toBe("Vue compacte");
      expect(viewConfig.fields).toBeDefined();
      expect(Array.isArray(viewConfig.fields)).toBe(true);
    });

    it("retourne une configuration pour la vue minimal", () => {
      const ctx = { capabilities: { view: true } };
      const viewConfig = ResourceDescriptor.getViewConfig("minimal", ctx);

      expect(viewConfig).toBeDefined();
      expect(viewConfig.name).toBe("minimal");
      expect(viewConfig.fields).toContain("rarity");
      expect(viewConfig.fields).toContain("level");
    });

    it("retourne une configuration pour la vue large", () => {
      const ctx = {
        capabilities: { view: true, createAny: true },
      };
      const viewConfig = ResourceDescriptor.getViewConfig("large", ctx);

      expect(viewConfig).toBeDefined();
      expect(viewConfig.name).toBe("large");
      expect(viewConfig.fields.length).toBeGreaterThan(5);
    });

    it("retourne une configuration pour la vue quickEdit", () => {
      const ctx = { capabilities: { updateAny: true } };
      const viewConfig = ResourceDescriptor.getViewConfig("quickEdit", ctx);

      expect(viewConfig).toBeDefined();
      expect(viewConfig.name).toBe("quickEdit");
      expect(viewConfig.label).toBe("Édition rapide");
      expect(viewConfig.fields).toContain("rarity");
      expect(viewConfig.fields).toContain("level");
      expect(viewConfig.layout).toBeDefined();
    });

    it("lance une erreur pour une vue inconnue", () => {
      const ctx = { capabilities: { view: true } };
      expect(() => {
        ResourceDescriptor.getViewConfig("unknown", ctx);
      }).toThrow("Vue inconnue pour Resource");
    });

    it("les vues incluent les actions appropriées", () => {
      const ctx = { capabilities: { view: true } };
      const compactView = ResourceDescriptor.getViewConfig("compact", ctx);

      expect(compactView.actions).toBeDefined();
      expect(compactView.actions.available).toContain("view");
      expect(compactView.actions.available).toContain("edit");
    });
  });

  describe("Configuration formulaires", () => {
    it("retourne une configuration de formulaire valide", () => {
      const ctx = {
        capabilities: { view: true, updateAny: true },
        resourceTypes: [{ id: 1, name: "Minerai" }],
      };
      const formConfig = ResourceDescriptor.getFormConfig(ctx);

      expect(formConfig).toBeDefined();
      expect(formConfig.fields).toBeDefined();
      expect(formConfig.groups).toBeDefined();
    });

    it("inclut tous les champs éditables", () => {
      const ctx = {
        capabilities: { view: true, updateAny: true },
        resourceTypes: [],
      };
      const formConfig = ResourceDescriptor.getFormConfig(ctx);

      expect(formConfig.fields.name).toBeDefined();
      expect(formConfig.fields.rarity).toBeDefined();
      expect(formConfig.fields.level).toBeDefined();
    });

    it("organise les champs en groupes", () => {
      const ctx = {
        capabilities: { view: true },
        resourceTypes: [],
      };
      const formConfig = ResourceDescriptor.getFormConfig(ctx);

      expect(formConfig.groups["Informations générales"]).toBeDefined();
      expect(formConfig.groups["Métier"]).toBeDefined();
      expect(formConfig.groups["Statut"]).toBeDefined();
    });

    it("le champ name est obligatoire", () => {
      const ctx = {
        capabilities: { view: true },
        resourceTypes: [],
      };
      const formConfig = ResourceDescriptor.getFormConfig(ctx);

      expect(formConfig.fields.name.required).toBe(true);
    });

    it("les champs conditionnels sont inclus selon les permissions", () => {
      const ctxWithPermission = {
        capabilities: { view: true, updateAny: true },
        resourceTypes: [],
      };
      const ctxWithoutPermission = {
        capabilities: { view: true },
        resourceTypes: [],
      };

      const configWith = ResourceDescriptor.getFormConfig(ctxWithPermission);
      const configWithout = ResourceDescriptor.getFormConfig(ctxWithoutPermission);

      expect(configWith.fields.auto_update).toBeDefined();
      expect(configWithout.fields.auto_update).toBeUndefined();
    });
  });

  describe("Configuration bulk", () => {
    it("retourne une configuration bulk valide", () => {
      const ctx = {
        capabilities: { updateAny: true },
      };
      const bulkConfig = ResourceDescriptor.getBulkConfig(ctx);

      expect(bulkConfig).toBeDefined();
      expect(bulkConfig.fields).toBeDefined();
      expect(bulkConfig.quickEditFields).toBeDefined();
    });

    it("inclut les champs bulk-editables", () => {
      const ctx = {
        capabilities: { updateAny: true },
      };
      const bulkConfig = ResourceDescriptor.getBulkConfig(ctx);

      expect(bulkConfig.fields.rarity).toBeDefined();
      expect(bulkConfig.fields.rarity.enabled).toBe(true);
      expect(bulkConfig.fields.level).toBeDefined();
      expect(bulkConfig.fields.level.enabled).toBe(true);
    });

    it("le champ name n'est pas bulk-editable", () => {
      const ctx = {
        capabilities: { updateAny: true },
      };
      const bulkConfig = ResourceDescriptor.getBulkConfig(ctx);

      expect(bulkConfig.fields.name).toBeUndefined();
    });

    it("définit les champs quickEdit", () => {
      const ctx = {
        capabilities: { updateAny: true },
      };
      const bulkConfig = ResourceDescriptor.getBulkConfig(ctx);

      expect(bulkConfig.quickEditFields).toContain("rarity");
      expect(bulkConfig.quickEditFields).toContain("level");
      expect(bulkConfig.quickEditFields).toContain("usable");
    });

    it("les champs bulk conditionnels sont inclus selon les permissions", () => {
      const ctxWithPermission = {
        capabilities: { updateAny: true },
      };
      const ctxWithoutPermission = {
        capabilities: {},
      };

      const configWith = ResourceDescriptor.getBulkConfig(ctxWithPermission);
      const configWithout = ResourceDescriptor.getBulkConfig(ctxWithoutPermission);

      expect(configWith.fields.auto_update).toBeDefined();
      expect(configWithout.fields.auto_update).toBeUndefined();
    });

    it("les fonctions build sont définies pour les champs bulk", () => {
      const ctx = {
        capabilities: { updateAny: true },
      };
      const bulkConfig = ResourceDescriptor.getBulkConfig(ctx);

      expect(typeof bulkConfig.fields.rarity.build).toBe("function");
      expect(typeof bulkConfig.fields.level.build).toBe("function");
    });
  });

  describe("Validation", () => {
    it("valide les descriptors correctement", () => {
      const descriptors = ResourceDescriptor.getFieldDescriptors();
      const validation = ResourceDescriptor.validate(descriptors);

      expect(validation.valid).toBe(true);
      expect(validation.errors).toHaveLength(0);
    });
  });

  describe("Compatibilité avec l'ancien système", () => {
    it("getFieldDescriptors retourne la même structure que getResourceFieldDescriptors", () => {
      const ctx = {
        capabilities: { updateAny: true, createAny: true },
        resourceTypes: [{ id: 1, name: "Test" }],
      };

      const newDescriptors = ResourceDescriptor.getFieldDescriptors(ctx);
      const oldDescriptors = getResourceFieldDescriptors(ctx);

      // Vérifier que les clés sont identiques
      expect(Object.keys(newDescriptors).sort()).toEqual(Object.keys(oldDescriptors).sort());

      // Vérifier quelques champs clés
      expect(newDescriptors.name.key).toBe(oldDescriptors.name.key);
      expect(newDescriptors.rarity.key).toBe(oldDescriptors.rarity.key);
    });
  });
});
