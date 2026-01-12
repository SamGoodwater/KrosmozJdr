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

import { computed, reactive, ref, watch, unref, nextTick } from "vue";
import { getMapperForEntityType } from '@/Utils/Entity/MapperRegistry';

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
    .map((e) => {
      if (!e) return undefined;
      
      // Si c'est une instance BaseModel, utiliser _data en priorité
      if (typeof e._data !== "undefined") {
        // Essayer d'abord _data[key]
        if (key in e._data) {
          return e._data[key];
        }
        // Si la clé n'existe pas dans _data, essayer un getter (pour les propriétés calculées)
        // mais seulement si c'est vraiment nécessaire (ex: pour les propriétés qui n'existent pas dans _data)
        if (typeof e[key] !== "undefined") {
          return e[key];
        }
        return undefined;
      }
      
      // Sinon, essayer directement sur l'objet
      return e?.[key];
    })
    .map((v) => {
      // Convertir les booléens en 1/0 pour la comparaison
      if (typeof v === "boolean") {
        return v ? 1 : 0;
      }
      return v;
    });
};

const allSame = (arr) => {
  if (!arr.length) return { same: true, value: null };
  
  // Filtrer les valeurs undefined (entités qui n'ont pas cette propriété)
  const definedValues = arr.filter((v) => v !== undefined);
  if (definedValues.length === 0) return { same: true, value: null };
  
  const first = definedValues[0];
  // Comparer en tenant compte des types
  for (const v of definedValues) {
    // Comparaison stricte : même type et même valeur
    if (v !== first) {
      // Pour les nombres et strings, comparer aussi en string pour gérer 1 vs "1"
      if (String(v) !== String(first)) {
        return { same: false, value: null };
      }
    }
  }
  return { same: true, value: first ?? null };
};

// Le MAPPER_REGISTRY est maintenant centralisé dans Utils/Entity/MapperRegistry.js

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
    // S'assurer que fieldMeta et aggregate sont disponibles
    if (!fieldMeta.value || Object.keys(fieldMeta.value).length === 0) {
      return;
    }
    
    for (const key of Object.keys(fieldMeta.value || {})) {
      const agg = aggregate.value?.[key];
      if (agg?.same) {
        // Si toutes les valeurs sont identiques, utiliser cette valeur
        const value = agg.value;
        // Convertir en string pour les selects, mais préserver null/undefined pour les champs nullable
        // Gérer les cas où value est 0 (falsy mais valide)
        if (value === null || value === undefined) {
          form[key] = "";
        } else if (value === 0 || value === false) {
          // Préserver 0 et false comme valeurs valides
          form[key] = String(value);
        } else {
          form[key] = String(value);
        }
      } else {
        // Si les valeurs sont différentes, laisser vide
        form[key] = "";
      }
      dirty[key] = false;
    }
  };

  // Watch sur les IDs sélectionnés pour réinitialiser le formulaire
  // On utilise nextTick pour s'assurer que aggregate (qui dépend de selectedEntities) est à jour
  watch(
    () => (selectedEntities.value || []).map(getId).join(","),
    () => {
      nextTick(() => {
        // Ne réinitialiser que si on n'a pas de modifications en cours
        const hasDirty = Object.values(dirty).some(Boolean);
        if (!hasDirty) {
          resetFromSelection();
        }
      });
    },
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
    const mapper = opts?.mapper || (opts?.entityType ? getMapperForEntityType(opts.entityType) : null);

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


