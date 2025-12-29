/**
 * Resource — EntityFieldSchema
 *
 * @description
 * Schéma source de vérité pour générer :
 * - `fieldsConfig` (EntityEditForm)
 * - `FIELD_META` (bulk panel via useBulkEditPanel)
 *
 * Les options relations (resourceTypes) peuvent être injectées via `ctx`.
 */

export function createResourceFieldSchema(ctx = {}) {
  const resourceTypes = Array.isArray(ctx.resourceTypes) ? ctx.resourceTypes : [];

  return {
    name: {
      type: "text",
      label: "Nom",
      required: true,
      showInCompact: true,
      bulk: { enabled: false },
    },
    description: {
      type: "textarea",
      label: "Description",
      required: false,
      showInCompact: false,
      bulk: {
        enabled: true,
        nullable: true,
        build: (v) => (v === "" ? null : String(v)),
      },
    },
    level: {
      type: "text",
      label: "Niveau",
      required: false,
      showInCompact: true,
      bulk: {
        enabled: true,
        nullable: true,
        build: (v) => (v === "" ? null : String(v)),
      },
    },
    rarity: {
      type: "select",
      label: "Rareté",
      required: false,
      showInCompact: true,
      options: [
        { value: 0, label: "Commun" },
        { value: 1, label: "Peu commun" },
        { value: 2, label: "Rare" },
        { value: 3, label: "Très rare" },
        { value: 4, label: "Légendaire" },
        { value: 5, label: "Unique" },
      ],
      bulk: {
        enabled: true,
        nullable: false,
        build: (v) => Number(v),
      },
    },
    resource_type_id: {
      type: "select",
      label: "Type de ressource",
      required: false,
      showInCompact: true,
      options: () => [
        { value: "", label: "—" },
        ...resourceTypes.map((t) => ({ value: t.id, label: t.name })),
      ],
      bulk: {
        enabled: true,
        nullable: true,
        build: (v) => (v === "" ? null : Number(v)),
      },
    },
    usable: {
      type: "checkbox",
      label: "Utilisable",
      required: false,
      showInCompact: true,
      defaultValue: false,
      bulk: {
        enabled: true,
        nullable: false,
        build: (v) => v === "1",
      },
    },
    auto_update: {
      type: "checkbox",
      label: "Auto-update",
      required: false,
      showInCompact: true,
      defaultValue: false,
      bulk: {
        enabled: true,
        nullable: false,
        build: (v) => v === "1",
      },
    },
    is_visible: {
      type: "select",
      label: "Visibilité",
      required: false,
      showInCompact: true,
      options: [
        { value: "guest", label: "Invité" },
        { value: "user", label: "Utilisateur" },
        { value: "game_master", label: "Maître de jeu" },
        { value: "admin", label: "Administrateur" },
      ],
      bulk: {
        enabled: true,
        nullable: false,
        build: (v) => v,
      },
    },
    price: {
      type: "text",
      label: "Prix",
      required: false,
      showInCompact: true,
      bulk: {
        enabled: true,
        nullable: true,
        build: (v) => (v === "" ? null : String(v)),
      },
    },
    weight: {
      type: "text",
      label: "Poids",
      required: false,
      showInCompact: true,
      bulk: {
        enabled: true,
        nullable: true,
        build: (v) => (v === "" ? null : String(v)),
      },
    },
    dofus_version: {
      type: "text",
      label: "Version Dofus",
      required: false,
      showInCompact: true,
      bulk: {
        enabled: true,
        nullable: true,
        build: (v) => (v === "" ? null : String(v)),
      },
    },
    image: {
      type: "text",
      label: "Image (URL)",
      required: false,
      showInCompact: false,
      bulk: {
        enabled: true,
        nullable: true,
        build: (v) => (v === "" ? null : String(v)),
      },
    },
  };
}

export default createResourceFieldSchema;


