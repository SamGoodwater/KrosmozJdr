/**
 * field-schema — génération de configs UI à partir d'un schéma de champs.
 *
 * @description
 * Objectif : factoriser la définition des champs (type/contraintes/options/builders)
 * pour pouvoir générer automatiquement :
 * - `fieldsConfig` pour `EntityEditForm`
 * - `fieldMeta` pour `useBulkEditPanel`
 *
 * Gardes-fous :
 * - le schéma reste explicite (pas de magie “auto”)
 * - on autorise des overrides par champ (custom UI) si besoin
 *
 * @example
 * const fieldsConfig = createFieldsConfigFromSchema(schema, ctx)
 * const fieldMeta = createBulkFieldMetaFromSchema(schema)
 */

/**
 * @typedef {Object} FieldSchema
 * @property {'text'|'number'|'textarea'|'select'|'checkbox'|'file'} type
 * @property {string} label
 * @property {boolean} [required]
 * @property {boolean} [showInCompact]
 * @property {Array<{value:any,label:string}>|((ctx:any)=>Array<{value:any,label:string}>)} [options]
 * @property {Object} [bulk]
 * @property {boolean} [bulk.enabled]
 * @property {boolean} [bulk.nullable]
 * @property {(raw:any, ctx?:any)=>any} [bulk.build]
 * @property {any} [defaultValue]
 */

/**
 * Résout les options d'un champ (statique ou fonction).
 * @param {FieldSchema} field
 * @param {any} ctx
 * @returns {Array<{value:any,label:string}>|undefined}
 */
export function resolveFieldOptions(field, ctx = {}) {
  const opts = field?.options;
  if (!opts) return undefined;
  if (typeof opts === "function") return opts(ctx) || [];
  if (Array.isArray(opts)) return opts;
  return undefined;
}

/**
 * Génère le `fieldsConfig` attendu par `EntityEditForm` (create/edit).
 * @param {Record<string, FieldSchema>} schema
 * @param {any} ctx
 * @returns {Record<string, any>}
 */
export function createFieldsConfigFromSchema(schema, ctx = {}) {
  const out = {};
  for (const [key, field] of Object.entries(schema || {})) {
    if (!field?.type || !field?.label) continue;
    const options = resolveFieldOptions(field, ctx);
    out[key] = {
      type: field.type,
      label: field.label,
      required: Boolean(field.required),
      showInCompact: field.showInCompact !== false,
      ...(options ? { options } : {}),
    };
  }
  return out;
}

/**
 * Génère le `fieldMeta` attendu par `useBulkEditPanel`.
 * @param {Record<string, FieldSchema>} schema
 * @param {any} ctx
 * @returns {Record<string, {label?: string, nullable?: boolean, build: (raw:any)=>any}>}
 */
export function createBulkFieldMetaFromSchema(schema, ctx = {}) {
  const out = {};
  for (const [key, field] of Object.entries(schema || {})) {
    if (!field?.bulk?.enabled) continue;
    if (typeof field.bulk.build !== "function") continue;
    out[key] = {
      label: field.label,
      nullable: Boolean(field.bulk.nullable),
      build: (raw) => field.bulk.build(raw, ctx),
    };
  }
  return out;
}

/**
 * Génère une entité par défaut à partir du schéma (utilisable dans CreateEntityModal).
 * @param {Record<string, FieldSchema>} schema
 * @returns {Record<string, any>}
 */
export function createDefaultEntityFromSchema(schema) {
  const out = {};
  for (const [key, field] of Object.entries(schema || {})) {
    if (typeof field?.defaultValue !== "undefined") out[key] = field.defaultValue;
  }
  return out;
}


