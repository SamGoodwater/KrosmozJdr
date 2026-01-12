/**
 * Tests unitaires pour TableConfig et TableColumnConfig
 *
 * @description
 * Vérifie que :
 * - La configuration de colonnes fonctionne
 * - La configuration globale du tableau fonctionne
 * - Les permissions sont respectées
 * - Le formatage responsive fonctionne
 */

import { describe, it, expect, beforeEach, vi } from "vitest";
import { TableConfig } from "@/Utils/Entity/Configs/TableConfig.js";
import { TableColumnConfig } from "@/Utils/Entity/Configs/TableColumnConfig.js";

describe("TableColumnConfig", () => {
  it("crée une colonne avec les propriétés de base", () => {
    const column = new TableColumnConfig({
      key: "name",
      label: "Nom",
      type: "text",
    });

    expect(column.key).toBe("name");
    expect(column.label).toBe("Nom");
    expect(column.type).toBe("text");
  });

  it("lance une erreur si key est manquant", () => {
    expect(() => {
      new TableColumnConfig({ label: "Nom", type: "text" });
    }).toThrow("key est obligatoire");
  });

  it("lance une erreur si type est invalide", () => {
    expect(() => {
      new TableColumnConfig({ key: "name", label: "Nom", type: "invalid" });
    }).toThrow("type invalide");
  });

  it("configure la permission", () => {
    const column = new TableColumnConfig({
      key: "name",
      label: "Nom",
      type: "text",
    }).withPermission("view");

    expect(column.permission).toBe("view");
  });

  it("configure la visibilité par défaut", () => {
    const column = new TableColumnConfig({
      key: "name",
      label: "Nom",
      type: "text",
    }).withDefaultVisible({ xs: false, md: true });

    expect(column.defaultVisible.xs).toBe(false);
    expect(column.defaultVisible.md).toBe(true);
  });

  it("configure le formatage responsive", () => {
    const column = new TableColumnConfig({
      key: "name",
      label: "Nom",
      type: "text",
    }).withFormat({
      xs: { mode: "truncate", maxLength: 20 },
      md: { mode: "full" },
    });

    expect(column.format.xs).toBeDefined();
    expect(column.format.md).toBeDefined();
  });

  it("build retourne la configuration complète", () => {
    const column = new TableColumnConfig({
      key: "name",
      label: "Nom",
      type: "route",
      icon: "fa-solid fa-font",
    })
      .withPermission("view")
      .withOrder(1)
      .asMain(true)
      .build();

    expect(column.id).toBe("name");
    expect(column.label).toBe("Nom");
    expect(column.cell.type).toBe("route");
    expect(column.icon).toBe("fa-solid fa-font");
    expect(column.permissions.ability).toBe("view");
    expect(column.isMain).toBe(true);
    expect(column.hideable).toBe(false);
  });
});

describe("TableConfig", () => {
  it("crée une configuration de tableau", () => {
    const tableConfig = new TableConfig({
      id: "test.index",
      entityType: "test",
    });

    expect(tableConfig.id).toBe("test.index");
    expect(tableConfig.entityType).toBe("test");
  });

  it("lance une erreur si id est manquant", () => {
    expect(() => {
      new TableConfig({ entityType: "test" });
    }).toThrow("id est obligatoire");
  });

  it("configure quickEdit", () => {
    const tableConfig = new TableConfig({
      id: "test.index",
      entityType: "test",
    }).withQuickEdit({ enabled: true, permission: "updateAny" });

    expect(tableConfig.quickEdit.enabled).toBe(true);
    expect(tableConfig.quickEdit.permission).toBe("updateAny");
  });

  it("configure les actions", () => {
    const tableConfig = new TableConfig({
      id: "test.index",
      entityType: "test",
    }).withActions({
      enabled: true,
      permission: "view",
      available: ["view", "edit"],
    });

    expect(tableConfig.actions.enabled).toBe(true);
    expect(tableConfig.actions.permission).toBe("view");
    expect(tableConfig.actions.available).toContain("view");
  });

  it("ajoute des colonnes", () => {
    const tableConfig = new TableConfig({
      id: "test.index",
      entityType: "test",
    });

    tableConfig.addColumn(
      new TableColumnConfig({
        key: "name",
        label: "Nom",
        type: "text",
      })
    );

    expect(tableConfig.columns.length).toBe(1);
  });

  it("build retourne la configuration complète", () => {
    const ctx = {
      capabilities: { view: true, updateAny: true },
    };

    const tableConfig = new TableConfig({
      id: "test.index",
      entityType: "test",
    })
      .withQuickEdit({ enabled: true, permission: "updateAny" })
      .withActions({ enabled: true, permission: "view", available: ["view"] })
      .addColumn(
        new TableColumnConfig({
          key: "name",
          label: "Nom",
          type: "text",
        })
      );

    const config = tableConfig.build(ctx);

    expect(config.id).toBe("test.index");
    expect(config.columns).toBeDefined();
    expect(config._metadata).toBeDefined();
    expect(config._metadata.quickEdit.enabled).toBe(true);
    expect(config._metadata.actions.enabled).toBe(true);
  });

  it("getVisibleColumns filtre selon les permissions", () => {
    const ctxWithPermission = {
      capabilities: { view: true, updateAny: true },
    };
    const ctxWithoutPermission = {
      capabilities: { view: true },
    };

    const tableConfig = new TableConfig({
      id: "test.index",
      entityType: "test",
    })
      .addColumn(
        new TableColumnConfig({
          key: "name",
          label: "Nom",
          type: "text",
        })
      )
      .addColumn(
        new TableColumnConfig({
          key: "admin_field",
          label: "Admin",
          type: "text",
        }).withPermission("updateAny")
      );

    const columnsWith = tableConfig.getVisibleColumns("md", ctxWithPermission);
    const columnsWithout = tableConfig.getVisibleColumns("md", ctxWithoutPermission);

    expect(columnsWith.length).toBe(2);
    expect(columnsWithout.length).toBe(1);
  });
});
