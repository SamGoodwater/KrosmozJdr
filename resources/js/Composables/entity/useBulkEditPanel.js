/**
 * useBulkEditPanel
 *
 * @description
 * Composable générique pour les panneaux d'édition en masse.
 * Il centralise:
 * - extraction d'IDs + libellés
 * - agrégation "valeur commune" vs "valeurs différentes"
 * - état form/dirty
 * - construction du payload (incluant nullable / conversions)
 * - optionnel: scope "selected" vs "filtered" (mode client)
 *
 * @example
 * const { ids, selectedList, aggregate, form, dirty, canApply, placeholder, onChange, buildPayload } =
 *   useBulkEditPanel({ selectedEntities, isAdmin, fieldMeta, mode, filteredIds, entityType, mapper });
 */

import { computed, reactive, ref, watch, unref } from "vue";
import { ResourceMapper } from '@/Mappers/Entity/ResourceMapper';

const getId = (e) => (e && typeof e.id !== "undefined" ? Number(e.id) : null);
const getName = (e) => {
  if (!e) return "";
  if (typeof e?._data !== "undefined") return String(e.name ?? e._data?.name ?? "");
  return String(e?.name ?? "");
};

const valuesOf = (entities, key) => {
  return (entities || [])
    // IMPORTANT:
    // Pour l'agrégation "valeurs différentes", on privilégie les données brutes `_data`
    // quand l'entité est un BaseModel. Les getters peuvent normaliser (ex: `|| ''`)
    // et masquer des différences attendues en sélection multiple.
    .map((e) => (typeof e?._data !== "undefined" ? e._data?.[key] : e?.[key]))
    .map((v) => (typeof v === "boolean" ? (v ? 1 : 0) : v));
};

const allSame = (arr) => {
  if (!arr.length) return { same: true, value: null };
  const first = arr[0];
  for (const v of arr) {
    if (String(v ?? "") !== String(first ?? "")) return { same: false, value: null };
  }
  return { same: true, value: first ?? null };
};

/**
 * Registre des mappers par entityType
 * @type {Object<string, {fromBulkForm: function}>}
 */
const MAPPER_REGISTRY = {
  'resources': ResourceMapper,
  'resource': ResourceMapper,
  // Ajouter d'autres mappers ici au fur et à mesure de leur migration
};

/**
 * @param {Object} opts
 * @param {Array} opts.selectedEntities
 * @param {boolean} opts.isAdmin
 * @param {Object<string, {label?: string, nullable?: boolean}>} opts.fieldMeta
 * ⚠️ DEPRECATED: Le paramètre `build` dans `fieldMeta` est déprécié. Les transformations sont gérées par les mappers (ex: ResourceMapper.fromBulkForm()).
 * @param {"server"|"client"} [opts.mode]
 * @param {Array<number|string>} [opts.filteredIds]
 * @param {string} [opts.entityType] - Type d'entité (ex: 'resources', 'items'). Utilisé pour déterminer le mapper approprié.
 * @param {Object} [opts.mapper] - Mapper optionnel à utiliser directement (prioritaire sur entityType).
 */
export function useBulkEditPanel(opts) {
  // Support: selectedEntities peut être un array, un Ref/Computed, ou une fonction qui retourne l'array.
  const selectedEntities = computed(() => {
    const src = opts?.selectedEntities;
    if (typeof src === "function") return src() || [];
    return unref(src) || [];
  });
  const isAdmin = computed(() => Boolean(unref(opts?.isAdmin)));
  const fieldMeta = computed(() => unref(opts?.fieldMeta) || {});
  const mode = computed(() => (opts?.mode ? String(opts.mode) : "server"));
  const filteredIds = computed(() => {
    const src = opts?.filteredIds;
    const arr = typeof src === "function" ? (src() || []) : (unref(src) || []);
    return (arr || []).map((v) => Number(v)).filter(Boolean);
  });

  const showList = ref(false);
  const scope = ref("selected"); // selected | filtered

  const ids = computed(() => (selectedEntities.value || []).map(getId).filter(Boolean));
  const selectedList = computed(() =>
    (selectedEntities.value || [])
      .map((e) => ({ id: getId(e), name: getName(e) }))
      .filter((x) => x.id)
  );

  const aggregate = computed(() => {
    const out = {};
    for (const key of Object.keys(fieldMeta.value || {})) {
      out[key] = allSame(valuesOf(selectedEntities.value, key));
    }
    return out;
  });

  const form = reactive({});
  const dirty = reactive({});

  const resetFromSelection = () => {
    for (const key of Object.keys(fieldMeta.value || {})) {
      form[key] = aggregate.value?.[key]?.same ? String(aggregate.value?.[key]?.value ?? "") : "";
      dirty[key] = false;
    }
  };

  watch(
    () => (selectedEntities.value || []).map(getId).join(","),
    () => resetFromSelection(),
    { immediate: true }
  );

  const placeholder = (same) => (same ? "Choisir…" : "Valeurs différentes");

  const onChange = (key, eventOrValue) => {
    dirty[key] = true;
    // Supporte @change (event), @input (event), ou passage direct d'une valeur
    if (eventOrValue && typeof eventOrValue === "object" && "target" in eventOrValue) {
      form[key] = eventOrValue?.target?.value ?? "";
    } else {
      form[key] = eventOrValue ?? "";
    }
  };

  const canApply = computed(() => {
    if (!isAdmin.value) return false;
    const anyDirty = Object.values(dirty).some(Boolean);
    if (!anyDirty) return false;

    for (const key of Object.keys(dirty)) {
      if (!dirty[key]) continue;
      const meta = fieldMeta.value?.[key];
      if (!meta) continue;
      if (!meta.nullable && !form[key]) return false;
    }

    if (scope.value === "filtered" && mode.value !== "client") return false;
    if (scope.value === "filtered" && filteredIds.value.length === 0) return false;

    return true;
  });

  const buildPayload = () => {
    const targetIds = scope.value === "filtered" ? filteredIds.value : ids.value;
    const payload = { ids: targetIds };

    // Déterminer le mapper à utiliser
    const mapper = opts?.mapper || (opts?.entityType ? MAPPER_REGISTRY[opts.entityType] : null);

    // Si un mapper est disponible et a la méthode fromBulkForm, l'utiliser
    if (mapper && typeof mapper.fromBulkForm === 'function') {
      // Collecter tous les champs dirty dans un objet
      const bulkFormData = {};
      for (const key of Object.keys(dirty)) {
        if (dirty[key]) {
          bulkFormData[key] = form[key];
        }
      }
      // Utiliser le mapper pour transformer les données
      const mappedData = mapper.fromBulkForm(bulkFormData);
      Object.assign(payload, mappedData);
    } else {
      // Fallback sur l'ancienne logique (meta.build) pour rétrocompatibilité
      if (process.env.NODE_ENV !== 'production' && opts?.entityType) {
        console.warn(`[useBulkEditPanel] Entity type '${opts.entityType}' is using deprecated 'meta.build' logic. Please migrate to a dedicated mapper.`);
      }
      for (const key of Object.keys(dirty)) {
        if (!dirty[key]) continue;
        const meta = fieldMeta.value?.[key];
        if (!meta || typeof meta.build !== "function") continue;
        payload[key] = meta.build(form[key]);
      }
    }

    return payload;
  };

  return {
    // UI
    showList,
    scope,

    // data
    ids,
    selectedList,
    aggregate,
    form,
    dirty,
    filteredIds,

    // actions
    resetFromSelection,
    placeholder,
    onChange,
    canApply,
    buildPayload,
  };
}


