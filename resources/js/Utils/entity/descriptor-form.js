/**
 * descriptor-form — génération de configs UI à partir de FieldDescriptors.
 *
 * @description
 * Objectif : remplacer progressivement `field-schema` en s’appuyant sur les descriptors
 * (source de vérité unifiée display + edit + permissions UX).
 *
 * Le descriptor doit exposer `edit.form` pour être inclus dans `fieldsConfig`.
 *
 * @example
 * const descriptors = getItemFieldDescriptors(ctx)
 * const fieldsConfig = createFieldsConfigFromDescriptors(descriptors, ctx)
 * const defaultEntity = createDefaultEntityFromDescriptors(descriptors)
 */

/**
 * @typedef {Object} DescriptorEditForm
 * @property {'text'|'number'|'textarea'|'select'|'checkbox'|'file'} type
 * @property {boolean} [required]
 * @property {boolean} [showInCompact]
 * @property {Array<{value:any,label:string}>|((ctx:any)=>Array<{value:any,label:string}>)} [options]
 * @property {any} [defaultValue]
 * @property {Object} [bulk]
 * @property {boolean} [bulk.enabled]
 * @property {boolean} [bulk.nullable]
 * @property {(raw:any, ctx?:any)=>any} [bulk.build]
 */

/**
 * @param {any} field
 * @param {any} ctx
 * @returns {Array<{value:any,label:string}>|undefined}
 */
export function resolveDescriptorOptions(field, ctx = {}) {
  const opts = field?.options;
  if (!opts) return undefined;
  if (typeof opts === "function") return opts(ctx) || [];
  if (Array.isArray(opts)) return opts;
  return undefined;
}

/**
 * Génère le `fieldsConfig` attendu par `EntityEditForm` (create/edit).
 *
 * @param {Record<string, any>} descriptors
 * @param {any} ctx
 * @returns {Record<string, any>}
 */
export function createFieldsConfigFromDescriptors(descriptors, ctx = {}) {
  const out = {};
  for (const [key, d] of Object.entries(descriptors || {})) {
    const form = d?.edit?.form;
    if (!form?.type) continue;
    const options = resolveDescriptorOptions(form, ctx);
    out[key] = {
      type: form.type,
      label: form?.label || d?.label || key,
      help: form?.help ? String(form.help) : "",
      tooltip: form?.tooltip ? String(form.tooltip) : "",
      placeholder: form?.placeholder ? String(form.placeholder) : "",
      required: Boolean(form.required),
      showInCompact: form.showInCompact !== false,
      ...(options ? { options } : {}),
    };
  }
  return out;
}

/**
 * Génère une entité par défaut à partir des descriptors (CreateEntityModal).
 *
 * @param {Record<string, any>} descriptors
 * @returns {Record<string, any>}
 */
export function createDefaultEntityFromDescriptors(descriptors) {
  const out = {};
  for (const [key, d] of Object.entries(descriptors || {})) {
    const form = d?.edit?.form;
    if (!form) continue;
    if (typeof form.defaultValue !== "undefined") out[key] = form.defaultValue;
  }
  return out;
}

/**
 * Génère le `fieldMeta` attendu par `useBulkEditPanel`.
 *
 * @param {Record<string, any>} descriptors
 * @param {any} ctx
 * @returns {Record<string, {label?: string, nullable?: boolean, build: (raw:any)=>any}>}
 */
export function createBulkFieldMetaFromDescriptors(descriptors, ctx = {}) {
  const out = {};
  for (const [key, d] of Object.entries(descriptors || {})) {
    const bulk = d?.edit?.form?.bulk;
    if (!bulk?.enabled) continue;
    // ⚠️ IMPORTANT : bulk.build est déprécié. Les transformations sont maintenant gérées par les mappers.
    // On crée quand même le fieldMeta pour permettre l'agrégation des valeurs, même sans build.
    out[key] = {
      label: d?.label || key,
      nullable: Boolean(bulk.nullable),
      // Si bulk.build existe encore (rétrocompatibilité), on le garde, sinon on laisse undefined
      // Le mapper sera utilisé à la place dans useBulkEditPanel
      ...(typeof bulk.build === "function" ? { build: (raw) => bulk.build(raw, ctx) } : {}),
    };
  }
  return out;
}


