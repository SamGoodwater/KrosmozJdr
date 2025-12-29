/**
 * ResourceType — EntityFieldSchema
 */

export function createResourceTypeFieldSchema() {
  return {
    name: {
      type: "text",
      label: "Nom",
      required: true,
      showInCompact: true,
      bulk: { enabled: false },
    },
    dofusdb_type_id: {
      type: "number",
      label: "DofusDB typeId",
      required: false,
      showInCompact: true,
      bulk: { enabled: false },
    },
    decision: {
      type: "select",
      label: "Statut",
      required: false,
      showInCompact: true,
      options: [
        { value: "pending", label: "En attente" },
        { value: "allowed", label: "Utilisé" },
        { value: "blocked", label: "Non utilisé" },
      ],
      defaultValue: "pending",
      bulk: {
        enabled: true,
        nullable: false,
        build: (v) => v,
      },
    },
    usable: {
      type: "checkbox",
      label: "Utilisable",
      required: false,
      showInCompact: true,
      defaultValue: true,
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
        { value: "super_admin", label: "Super admin" },
      ],
      defaultValue: "guest",
      bulk: {
        enabled: true,
        nullable: false,
        build: (v) => v,
      },
    },
  };
}

export default createResourceTypeFieldSchema;


