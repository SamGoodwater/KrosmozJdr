/**
 * Tests unitaires pour FormConfig et FormFieldConfig
 *
 * @description
 * Vérifie que :
 * - La configuration de formulaires fonctionne
 * - Les champs sont correctement ajoutés
 * - Les groupes sont correctement configurés
 * - La validation fonctionne
 */

import { describe, it, expect } from "vitest";
import { FormConfig } from "@/Utils/Entity/Configs/FormConfig.js";
import { FormFieldConfig } from "@/Utils/Entity/Configs/FormFieldConfig.js";

describe("FormFieldConfig", () => {
  it("crée un champ avec les propriétés de base", () => {
    const field = new FormFieldConfig({
      key: "name",
      type: "text",
      label: "Nom",
    });

    expect(field.key).toBe("name");
    expect(field.type).toBe("text");
    expect(field.label).toBe("Nom");
  });

  it("lance une erreur si key est manquant", () => {
    expect(() => {
      new FormFieldConfig({ type: "text", label: "Nom" });
    }).toThrow("key est obligatoire");
  });

  it("lance une erreur si type est manquant", () => {
    expect(() => {
      new FormFieldConfig({ key: "name", label: "Nom" });
    }).toThrow("type est obligatoire");
  });

  it("lance une erreur si type est invalide", () => {
    expect(() => {
      new FormFieldConfig({ key: "name", type: "invalid" });
    }).toThrow("type invalide");
  });

  it("configure le groupe", () => {
    const field = new FormFieldConfig({
      key: "name",
      type: "text",
    }).withGroup("Informations générales");

    expect(field.group).toBe("Informations générales");
  });

  it("configure required", () => {
    const field = new FormFieldConfig({
      key: "name",
      type: "text",
    }).withRequired(true);

    expect(field.required).toBe(true);
  });

  it("configure les options", () => {
    const options = [
      { value: "1", label: "Option 1" },
      { value: "2", label: "Option 2" },
    ];

    const field = new FormFieldConfig({
      key: "select_field",
      type: "select",
    }).withOptions(options);

    expect(field.options).toEqual(options);
  });

  it("build retourne la configuration complète", () => {
    const field = new FormFieldConfig({
      key: "name",
      type: "text",
      label: "Nom",
    })
      .withGroup("Informations générales")
      .withRequired(true)
      .withPlaceholder("Entrez un nom")
      .build();

    expect(field.key).toBe("name");
    expect(field.type).toBe("text");
    expect(field.label).toBe("Nom");
    expect(field.group).toBe("Informations générales");
    expect(field.required).toBe(true);
    expect(field.placeholder).toBe("Entrez un nom");
  });
});

describe("FormConfig", () => {
  it("crée une configuration de formulaire", () => {
    const formConfig = new FormConfig({
      entityType: "resource",
    });

    expect(formConfig.entityType).toBe("resource");
    expect(formConfig.fields).toEqual({});
    expect(formConfig.groups).toEqual({});
  });

  it("lance une erreur si entityType est manquant", () => {
    expect(() => {
      new FormConfig({});
    }).toThrow("entityType est obligatoire");
  });

  it("ajoute un champ", () => {
    const formConfig = new FormConfig({
      entityType: "resource",
    }).addField(
      new FormFieldConfig({
        key: "name",
        type: "text",
        label: "Nom",
      })
    );

    const field = formConfig.getField("name");
    expect(field).toBeDefined();
    expect(field.key).toBe("name");
    expect(field.type).toBe("text");
  });

  it("ajoute un groupe", () => {
    const formConfig = new FormConfig({
      entityType: "resource",
    }).addGroup({
      name: "Informations générales",
      label: "Informations générales",
      order: 1,
    });

    expect(formConfig.groups["Informations générales"]).toBeDefined();
    expect(formConfig.groups["Informations générales"].order).toBe(1);
  });

  it("obtient les champs d'un groupe", () => {
    const formConfig = new FormConfig({
      entityType: "resource",
    })
      .addGroup({ name: "Groupe 1", order: 1 })
      .addField(
        new FormFieldConfig({
          key: "name",
          type: "text",
        }).withGroup("Groupe 1")
      )
      .addField(
        new FormFieldConfig({
          key: "level",
          type: "text",
        }).withGroup("Groupe 1")
      )
      .addField(
        new FormFieldConfig({
          key: "other",
          type: "text",
        }).withGroup("Groupe 2")
      );

    const fields = formConfig.getFieldsByGroup("Groupe 1");
    expect(fields.length).toBe(2);
    expect(fields.map((f) => f.key)).toContain("name");
    expect(fields.map((f) => f.key)).toContain("level");
  });

  it("build retourne la configuration complète", () => {
    const formConfig = new FormConfig({
      entityType: "resource",
    })
      .addGroup({ name: "Groupe 1", order: 1 })
      .addField(
        new FormFieldConfig({
          key: "name",
          type: "text",
          label: "Nom",
        }).withGroup("Groupe 1")
      );

    const config = formConfig.build();

    expect(config.fields).toBeDefined();
    expect(config.fields.name).toBeDefined();
    expect(config.groups).toBeDefined();
    expect(config.groups["Groupe 1"]).toBeDefined();
  });

  it("résout les options dynamiques lors du build", () => {
    const formConfig = new FormConfig({
      entityType: "resource",
    }).addField(
      new FormFieldConfig({
        key: "select_field",
        type: "select",
      }).withOptions((ctx) => {
        return ctx.items || [];
      })
    );

    const ctx = {
      items: [
        { value: "1", label: "Item 1" },
        { value: "2", label: "Item 2" },
      ],
    };

    const config = formConfig.build(ctx);

    expect(config.fields.select_field.options).toEqual(ctx.items);
  });

  it("gère les champs sans options dynamiques", () => {
    const formConfig = new FormConfig({
      entityType: "resource",
    }).addField(
      new FormFieldConfig({
        key: "name",
        type: "text",
      })
    );

    const config = formConfig.build();

    expect(config.fields.name).toBeDefined();
    expect(config.fields.name.options).toBeNull();
  });
});
