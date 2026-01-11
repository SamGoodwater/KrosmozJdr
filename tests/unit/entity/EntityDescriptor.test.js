/**
 * Tests unitaires pour EntityDescriptor (classe de base)
 *
 * @description
 * ⚠️ DÉPRÉCIÉ : Cette classe est dépréciée dans le nouveau système.
 * Les tests sont conservés pour vérifier la rétrocompatibilité.
 * 
 * Vérifie que :
 * - La classe de base fonctionne correctement
 * - Les constantes sont accessibles
 * - Les fonctions de formatage fonctionnent (wrappers vers formatters)
 * - La validation fonctionne
 */

import { describe, it, expect } from "vitest";
import { EntityDescriptor } from "@/Entities/entity/EntityDescriptor.js";
import {
  RARITY_OPTIONS,
  VISIBILITY_OPTIONS,
  BREAKPOINTS,
  SCREEN_SIZES,
} from "@/Entities/entity/EntityDescriptorConstants.js";

describe("EntityDescriptor (classe de base)", () => {
  describe("Constantes statiques", () => {
    it("expose RARITY_OPTIONS", () => {
      expect(EntityDescriptor.RARITY_OPTIONS).toBeDefined();
      expect(Array.isArray(EntityDescriptor.RARITY_OPTIONS)).toBe(true);
      expect(EntityDescriptor.RARITY_OPTIONS.length).toBeGreaterThan(0);
    });

    it("expose VISIBILITY_OPTIONS", () => {
      expect(EntityDescriptor.VISIBILITY_OPTIONS).toBeDefined();
      expect(Array.isArray(EntityDescriptor.VISIBILITY_OPTIONS)).toBe(true);
    });

    it("expose BREAKPOINTS", () => {
      expect(EntityDescriptor.BREAKPOINTS).toBeDefined();
      expect(EntityDescriptor.BREAKPOINTS.xs).toBe(0);
      expect(EntityDescriptor.BREAKPOINTS.sm).toBe(640);
    });

    it("expose SCREEN_SIZES", () => {
      expect(EntityDescriptor.SCREEN_SIZES).toBeDefined();
      expect(Array.isArray(EntityDescriptor.SCREEN_SIZES)).toBe(true);
      expect(EntityDescriptor.SCREEN_SIZES).toContain("xs");
      expect(EntityDescriptor.SCREEN_SIZES).toContain("md");
    });
  });

  describe("Fonctions de formatage", () => {
    let descriptor;

    beforeEach(() => {
      descriptor = new EntityDescriptor("test");
    });

    it("truncate fonctionne correctement", () => {
      expect(descriptor.truncate("Hello World", 5)).toBe("Hell…");
      expect(descriptor.truncate("Short", 10)).toBe("Short");
      expect(descriptor.truncate("", 10)).toBe("");
    });

    it("capitalize fonctionne correctement", () => {
      expect(descriptor.capitalize("hello")).toBe("Hello");
      expect(descriptor.capitalize("WORLD")).toBe("World");
    });

    it("formatRarity fonctionne correctement", () => {
      const result = descriptor.formatRarity(0);
      expect(result).toBeDefined();
    });

    it("formatVisibility fonctionne correctement", () => {
      expect(descriptor.formatVisibility("guest")).toBe("Invité");
      expect(descriptor.formatVisibility("admin")).toBe("Administrateur");
    });

    it("formatDate fonctionne correctement", () => {
      const date = new Date("2024-01-01");
      const formatted = descriptor.formatDate(date, "md");
      expect(typeof formatted).toBe("string");
      expect(formatted.length).toBeGreaterThan(0);
    });

    it("formatNumber fonctionne correctement", () => {
      expect(descriptor.formatNumber(1234)).toBe("1 234");
      expect(descriptor.formatNumber(1234.56, { decimals: 2 })).toBe("1 234,56");
    });

    it("getCurrentScreenSize retourne une taille valide", () => {
      const size = descriptor.getCurrentScreenSize();
      expect(SCREEN_SIZES).toContain(size);
    });

    it("subtractSize fonctionne correctement", () => {
      expect(descriptor.subtractSize("lg", 1)).toBe("md");
      expect(descriptor.subtractSize("md", 2)).toBe("xs");
      expect(descriptor.subtractSize("xs", 1)).toBe("xs"); // Ne peut pas aller en dessous
    });

    it("addSize fonctionne correctement", () => {
      expect(descriptor.addSize("md", 1)).toBe("lg");
      expect(descriptor.addSize("sm", 2)).toBe("lg");
      expect(descriptor.addSize("xl", 1)).toBe("xl"); // Ne peut pas aller au-dessus
    });
  });

  describe("Validation", () => {
    let descriptor;

    beforeEach(() => {
      descriptor = new EntityDescriptor("test");
    });

    it("valide un descriptor correct", () => {
      const validDescriptor = {
        key: "name",
        label: "Nom",
        format: "text",
      };
      const result = descriptor.validateFieldDescriptor(validDescriptor, "name");
      expect(result).toBe(true);
      expect(descriptor.errors.length).toBe(0);
    });

    it("rejette un descriptor sans key", () => {
      const invalidDescriptor = {
        label: "Nom",
      };
      const result = descriptor.validateFieldDescriptor(invalidDescriptor, "name");
      expect(result).toBe(false);
      expect(descriptor.errors.length).toBeGreaterThan(0);
    });

    it("rejette un descriptor sans label", () => {
      const invalidDescriptor = {
        key: "name",
      };
      const result = descriptor.validateFieldDescriptor(invalidDescriptor, "name");
      expect(result).toBe(false);
    });

    it("valide un ensemble de descriptors", () => {
      const descriptors = {
        name: { key: "name", label: "Nom", format: "text" },
        level: { key: "level", label: "Niveau", format: "number" },
      };
      const validation = descriptor.validate(descriptors);
      expect(validation.valid).toBe(true);
      expect(validation.errors).toHaveLength(0);
    });

    it("détecte les erreurs dans un ensemble de descriptors", () => {
      const descriptors = {
        name: { key: "name", label: "Nom", format: "text" },
        invalid: { key: "invalid" }, // Manque label
      };
      const validation = descriptor.validate(descriptors);
      expect(validation.valid).toBe(false);
      expect(validation.errors.length).toBeGreaterThan(0);
    });
  });

  describe("Méthodes abstraites", () => {
    it("getFieldDescriptors lance une erreur si non implémentée", () => {
      const descriptor = new EntityDescriptor("test");
      expect(() => {
        descriptor.getFieldDescriptors();
      }).toThrow("doit être implémentée");
    });

    it("getTableConfig lance une erreur si non implémentée", () => {
      const descriptor = new EntityDescriptor("test");
      expect(() => {
        descriptor.getTableConfig();
      }).toThrow("doit être implémentée");
    });

    it("getViewConfig lance une erreur si non implémentée", () => {
      const descriptor = new EntityDescriptor("test");
      expect(() => {
        descriptor.getViewConfig("compact");
      }).toThrow("doit être implémentée");
    });

    it("getFormConfig lance une erreur si non implémentée", () => {
      const descriptor = new EntityDescriptor("test");
      expect(() => {
        descriptor.getFormConfig();
      }).toThrow("doit être implémentée");
    });

    it("getBulkConfig lance une erreur si non implémentée", () => {
      const descriptor = new EntityDescriptor("test");
      expect(() => {
        descriptor.getBulkConfig();
      }).toThrow("doit être implémentée");
    });
  });
});
