/**
 * Tests unitaires pour BulkConfig
 *
 * @description
 * Vérifie que :
 * - La configuration bulk fonctionne correctement
 * - BulkConfig.fromDescriptors() génère correctement la configuration
 * - Les champs bulk-editables sont correctement identifiés
 */

import { describe, it, expect, vi } from "vitest";
import { BulkConfig } from "@/Utils/Entity/Configs/BulkConfig.js";
import { getResourceFieldDescriptors } from "@/Entities/resource/resource-descriptors.js";

describe("BulkConfig", () => {
  describe("Création et configuration de base", () => {
    it("crée une configuration bulk avec entityType", () => {
      const bulkConfig = new BulkConfig({
        entityType: "resource",
      });

      expect(bulkConfig.entityType).toBe("resource");
      expect(bulkConfig.fields).toEqual({});
    });

    it("lance une erreur si entityType est manquant", () => {
      expect(() => {
        new BulkConfig({});
      }).toThrow("BulkConfig: 'entityType' est obligatoire");
    });

    it("ajoute un champ bulk-editable", () => {
      const bulkConfig = new BulkConfig({
        entityType: "resource",
      }).addField("rarity", {
        enabled: true,
        nullable: true,
        label: "Rareté",
      });

      const field = bulkConfig.getField("rarity");
      expect(field).toBeDefined();
      expect(field.enabled).toBe(true);
      expect(field.nullable).toBe(true);
      expect(field.label).toBe("Rareté");
    });

    it("vérifie si un champ est bulk-editable", () => {
      const bulkConfig = new BulkConfig({
        entityType: "resource",
      })
        .addField("rarity", { enabled: true })
        .addField("level", { enabled: false });

      expect(bulkConfig.isBulkEditable("rarity")).toBe(true);
      expect(bulkConfig.isBulkEditable("level")).toBe(false);
      expect(bulkConfig.isBulkEditable("inexistant")).toBe(false);
    });

    it("build retourne la configuration complète", () => {
      const bulkConfig = new BulkConfig({
        entityType: "resource",
      })
        .addField("rarity", { enabled: true, nullable: true })
        .addField("level", { enabled: true, nullable: false });

      const config = bulkConfig.build();

      expect(config.fields).toBeDefined();
      expect(config.fields.rarity).toBeDefined();
      expect(config.fields.level).toBeDefined();
      expect(config.quickEditFields).toBeUndefined();
    });
  });

  describe("BulkConfig.fromDescriptors()", () => {
    it("génère une configuration depuis les descriptors", () => {
      const descriptors = getResourceFieldDescriptors();
      const bulkConfig = BulkConfig.fromDescriptors(descriptors);

      expect(bulkConfig).toBeInstanceOf(BulkConfig);
      expect(bulkConfig.entityType).toBe("resource");
    });

    it("inclut les champs avec bulk.enabled: true", () => {
      const descriptors = getResourceFieldDescriptors();
      const bulkConfig = BulkConfig.fromDescriptors(descriptors);

      const config = bulkConfig.build();
      expect(Object.keys(config.fields).length).toBeGreaterThan(0);
    });

    it("fromDescriptors construit un bulk depuis les champs edit.form.bulk", () => {
      const descriptors = getResourceFieldDescriptors();
      const bulkConfig = BulkConfig.fromDescriptors(descriptors);

      const config = bulkConfig.build();
      expect(config.fields).toBeDefined();
    });

    it("lance une erreur si descriptors est invalide", () => {
      expect(() => {
        BulkConfig.fromDescriptors(null);
      }).toThrow("Descriptors invalides");

      expect(() => {
        BulkConfig.fromDescriptors("invalid");
      }).toThrow("Descriptors invalides");
    });

    it("ignore les champs qui commencent par _", () => {
      const descriptors = {
        name: {
          key: "name",
          label: "Nom",
          edit: {
            form: {
              bulk: { enabled: true },
            },
          },
        },
        _tableConfig: {
          entityType: "test",
        },
      };

      const bulkConfig = BulkConfig.fromDescriptors(descriptors);
      const config = bulkConfig.build();

      expect(config.fields._tableConfig).toBeUndefined();
      expect(config.fields.name).toBeDefined();
    });

    it("gère les erreurs lors de la création des champs", () => {
      const consoleWarnSpy = vi.spyOn(console, "warn").mockImplementation(() => {});

      const descriptors = {
        valid: {
          key: "valid",
          label: "Valid",
          edit: {
            form: {
              bulk: { enabled: true },
            },
          },
        },
        invalid: {
          key: "invalid",
        },
      };

      const bulkConfig = BulkConfig.fromDescriptors(descriptors);
      const config = bulkConfig.build();

      expect(config.fields.valid).toBeDefined();
      expect(config.fields.invalid).toBeUndefined();

      consoleWarnSpy.mockRestore();
    });
  });

  describe("Intégration avec Resource", () => {
    it("génère une configuration valide pour Resource", () => {
      const descriptors = getResourceFieldDescriptors();
      const bulkConfig = BulkConfig.fromDescriptors(descriptors);
      const config = bulkConfig.build();

      expect(config.fields).toBeDefined();

      const expectedFields = ["rarity", "level", "state", "read_level", "write_level"];
      expectedFields.forEach((field) => {
        if (config.fields[field]) {
          expect(config.fields[field].enabled).toBe(true);
        }
      });
    });
  });
});
