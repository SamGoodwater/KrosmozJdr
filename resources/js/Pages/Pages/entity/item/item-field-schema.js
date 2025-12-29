/**
 * Item — EntityFieldSchema
 *
 * @description
 * Schéma minimal pour générer `fieldsConfig` (EntityEditForm) et `defaultEntity`.
 * NB: pas de bulk panel Item pour l'instant, donc bulk.enabled reste à false.
 */

export function createItemFieldSchema() {
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
      bulk: { enabled: false },
    },
    level: {
      type: "number",
      label: "Niveau",
      required: false,
      showInCompact: true,
      bulk: { enabled: false },
    },
    rarity: {
      type: "select",
      label: "Rareté",
      required: false,
      showInCompact: true,
      options: [
        { value: "common", label: "Commun" },
        { value: "uncommon", label: "Peu commun" },
        { value: "rare", label: "Rare" },
        { value: "epic", label: "Épique" },
        { value: "legendary", label: "Légendaire" },
      ],
      defaultValue: "common",
      bulk: { enabled: false },
    },
    image: {
      type: "file",
      label: "Image",
      required: false,
      showInCompact: false,
      bulk: { enabled: false },
    },
  };
}

export default createItemFieldSchema;


