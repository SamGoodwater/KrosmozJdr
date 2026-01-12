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
 * @property {'text'|'number'|'textarea'|'select'|'checkbox'|'file'|'date'} type
 * @property {string} [label] - Libellé spécifique (optionnel, utilise general.label par défaut)
 * @property {string} [group] - Groupe de champs
 * @property {string} [help] - Texte d'aide
 * @property {string} [placeholder] - Placeholder
 * @property {boolean} [required]
 * @property {any} [defaultValue]
 * @property {Array<{value:any,label:string}>|((ctx:any)=>Array<{value:any,label:string}>)} [options]
 * @property {Object} [validation] - Règles de validation
 * @property {string|RegExp|Function} [validation.pattern]
 * @property {number} [validation.min]
 * @property {number} [validation.max]
 * @property {number} [validation.minLength]
 * @property {number} [validation.maxLength]
 * @property {Function} [validation.validator]
 * @property {string} [validation.message]
 * @property {number} [rows] - Pour textarea
 * @property {number} [cols] - Pour textarea
 * @property {string} [accept] - Pour file
 * @property {boolean} [multiple] - Pour select/file
 * @property {number} [step] - Pour number
 * @property {Object} [bulk]
 * @property {boolean} [bulk.enabled]
 * @property {boolean} [bulk.nullable]
 * ⚠️ Pas de `showInCompact` : c'est la vue qui décide quels champs afficher
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
    const form = d?.edition?.form;
    if (!form?.type) continue;
    const options = resolveDescriptorOptions(form, ctx);
    out[key] = {
      type: form.type,
      label: form?.label || d?.general?.label || key,
      group: form?.group || "",
      help: form?.help ? String(form.help) : "",
      placeholder: form?.placeholder ? String(form.placeholder) : "",
      required: Boolean(form.required),
      defaultValue: form?.defaultValue,
      // Support du select avec recherche
      ...(form.type === 'select' && form.searchable ? { searchable: true } : {}),
      // Validation
      ...(form?.validation ? {
        validation: {
          ...(form.validation.pattern !== undefined ? { pattern: form.validation.pattern } : {}),
          ...(form.validation.min !== undefined ? { min: form.validation.min } : {}),
          ...(form.validation.max !== undefined ? { max: form.validation.max } : {}),
          ...(form.validation.minLength !== undefined ? { minLength: form.validation.minLength } : {}),
          ...(form.validation.maxLength !== undefined ? { maxLength: form.validation.maxLength } : {}),
          ...(form.validation.validator ? { validator: form.validation.validator } : {}),
          ...(form.validation.message ? { message: form.validation.message } : {}),
        }
      } : {}),
      // Options pour select
      ...(options ? { options } : {}),
      // Propriétés spécifiques par type
      ...(form.type === 'textarea' ? {
        rows: form.rows,
        cols: form.cols,
      } : {}),
      ...(form.type === 'file' ? {
        accept: form.accept,
        multiple: Boolean(form.multiple),
      } : {}),
      ...(form.type === 'select' ? {
        multiple: Boolean(form.multiple),
      } : {}),
      ...(form.type === 'number' ? {
        step: form.step,
        min: form.validation?.min,
        max: form.validation?.max,
      } : {}),
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
    const form = d?.edition?.form;
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
    const bulk = d?.edition?.bulk;
    if (!bulk?.enabled) continue;
    // ⚠️ IMPORTANT : bulk.build est déprécié. Les transformations sont maintenant gérées par les mappers.
    // On crée quand même le fieldMeta pour permettre l'agrégation des valeurs, même sans build.
    out[key] = {
      label: d?.general?.label || key,
      nullable: Boolean(bulk.nullable),
      // Si bulk.build existe encore (rétrocompatibilité), on le garde, sinon on laisse undefined
      // Le mapper sera utilisé à la place dans useBulkEditPanel
      ...(typeof bulk.build === "function" ? { build: (raw) => bulk.build(raw, ctx) } : {}),
    };
  }
  return out;
}


