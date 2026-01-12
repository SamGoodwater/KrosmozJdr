<script setup>
/**
 * EntityQuickEditPanel
 *
 * @description
 * Panneau générique d’édition rapide (sélection multiple) basé sur :
 * - FieldDescriptors (`edit.form` + `bulk`)
 * - `useBulkEditPanel` (agrégation, dirty, payload)
 *
 * Le panneau ne fait pas l’appel API : il émet `applied(payload)` et laisse la page gérer l’endpoint.
 *
 * @example
 * <EntityQuickEditPanel
 *   entity-type="resources"
 *   :selected-entities="selectedEntities"
 *   :is-admin="canModify"
 *   :extra-ctx="{ resourceTypes }"
 *   mode="client"
 *   :filtered-ids="selectedIds"
 *   @applied="handleBulkApplied"
 *   @clear="clearSelection"
 * />
 */
import { computed, toRef } from "vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import SelectCore from "@/Pages/Atoms/data-input/SelectCore.vue";
import InputCore from "@/Pages/Atoms/data-input/InputCore.vue";
import TextareaCore from "@/Pages/Atoms/data-input/TextareaCore.vue";
import RadioCore from "@/Pages/Atoms/data-input/RadioCore.vue";
import ToggleCore from "@/Pages/Atoms/data-input/ToggleCore.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import { useBulkEditPanel } from "@/Composables/entity/useBulkEditPanel";
import { getEntityConfig } from "@/Entities/entity-registry";
import { createFieldsConfigFromDescriptors, createBulkFieldMetaFromDescriptors } from "@/Utils/entity/descriptor-form";
import { getCachedDescriptors } from "@/Utils/entity/descriptor-cache";

const props = defineProps({
  entityType: { type: String, required: true },
  selectedEntities: { type: Array, default: () => [] },
  isAdmin: { type: Boolean, default: false },
  filteredIds: { type: Array, default: () => [] },
  mode: { type: String, default: "client" }, // server | client
  /**
   * Contexte additionnel pour les descriptors (ex: { resourceTypes }).
   */
  extraCtx: { type: Object, default: () => ({}) },
  /**
   * Liste optionnelle de champs à afficher (sinon: tous les champs bulk-enabled).
   */
  fields: { type: Array, default: null },
  title: { type: String, default: "" },
});

const emit = defineEmits(["applied", "clear"]);

const uiColor = computed(() => "primary");

const cfg = computed(() => getEntityConfig(props.entityType));

const ctx = computed(() => ({
  ...(props.extraCtx || {}),
  capabilities: { updateAny: props.isAdmin },
}));

const descriptors = computed(() => {
  if (!cfg.value?.getDescriptors) return {};
  return getCachedDescriptors(props.entityType, cfg.value.getDescriptors, ctx.value);
});

const isFieldVisible = (key) => {
  const d = descriptors.value?.[key] || null;
  const fn = d?.visibleIf;
  if (typeof fn !== "function") return true;
  try {
    return Boolean(fn(ctx.value));
  } catch {
    return false;
  }
};

const isFieldEditable = (key) => {
  if (!props.isAdmin) return false;
  const d = descriptors.value?.[key] || null;
  const fn = d?.editableIf;
  if (typeof fn !== "function") return true;
  try {
    return Boolean(fn(ctx.value));
  } catch {
    return false;
  }
};

const fieldsConfigAll = computed(() => createFieldsConfigFromDescriptors(descriptors.value, ctx.value));
const fieldMetaAll = computed(() => createBulkFieldMetaFromDescriptors(descriptors.value, ctx.value));

const fieldKeys = computed(() => {
  if (Array.isArray(props.fields) && props.fields.length) return props.fields;
  const preferred = cfg.value?.viewFields?.quickEdit;
  if (Array.isArray(preferred) && preferred.length) return preferred;
  return Object.keys(fieldMetaAll.value || {});
});

const fieldsConfig = computed(() => {
  const out = {};
  for (const k of fieldKeys.value || []) {
    if (!isFieldVisible(k)) continue;
    if (!fieldsConfigAll.value?.[k]) continue;
    // On autorise l’affichage en read-only (disabled) mais on garde le champ visible si présent.
    out[k] = fieldsConfigAll.value[k];
  }
  return out;
});

const getFieldGroup = (key) => {
  const d = descriptors.value?.[key] || null;
  const g = d?.edit?.form?.group;
  return g ? String(g) : "";
};

const groupedFieldKeys = computed(() => {
  /** @type {Array<{title: string, keys: string[]}>} */
  const groups = [];
  const indexByTitle = new Map();

  const orderedKeys = Object.keys(fieldsConfig.value || {});
  for (const key of orderedKeys) {
    const title = getFieldGroup(key);
    const groupTitle = title || "Champs";
    if (!indexByTitle.has(groupTitle)) {
      indexByTitle.set(groupTitle, groups.length);
      groups.push({ title: groupTitle, keys: [] });
    }
    groups[indexByTitle.get(groupTitle)].keys.push(key);
  }

  return groups;
});

const fieldMeta = computed(() => {
  const out = {};
  for (const k of fieldKeys.value || []) {
    if (!isFieldVisible(k)) continue;
    // Le bulk panel n’expose que des champs bulk-enabled.
    if (!fieldMetaAll.value?.[k]) continue;
    // Si pas éditable, on retire le champ du meta pour éviter un payload invalide.
    if (!isFieldEditable(k)) continue;
    out[k] = fieldMetaAll.value[k];
  }
  return out;
});

const isNullableBulkField = (key) => Boolean(fieldMeta.value?.[key]?.nullable);

const hasEmptyOption = (options) => {
  const arr = Array.isArray(options) ? options : [];
  return arr.some((o) => String(o?.value ?? "") === "");
};

const panelTitle = computed(() => {
  if (props.title) return props.title;
  return props.selectedEntities?.length <= 1 ? "Édition rapide" : "Édition rapide (multi)";
});

/**
 * Nombre de champs modifiés (dirty).
 */
const modifiedFieldsCount = computed(() => {
  return Object.values(dirty || {}).filter(Boolean).length;
});

const {
  showList,
  scope,
  ids,
  selectedList,
  aggregate,
  form,
  dirty,
  filteredIds: filteredIdsEffective,
  resetFromSelection,
  placeholder,
  onChange,
  canApply,
  buildPayload,
} = useBulkEditPanel({
  selectedEntities: toRef(props, "selectedEntities"),
  isAdmin: props.isAdmin,
  fieldMeta,
  mode: props.mode,
  filteredIds: toRef(props, "filteredIds"),
  entityType: props.entityType,
});

const apply = () => {
  if (!canApply.value) return;
  emit("applied", buildPayload());
};

/**
 * Reset un champ (revient à l'état "non modifié" => dirty=false).
 *
 * @param {string} key
 * @returns {void}
 */
const clearField = (key) => {
  if (!key) return;
  dirty[key] = false;
  // IMPORTANT: `useBulkEditPanel` construit le payload uniquement sur dirty=true.
  // On peut donc garder form[key] vide sans impact, mais on reset pour éviter les confusions UX.
  form[key] = "";
};

/**
 * Détermine l'état checked du booléen pour l'affichage:
 * - si l'utilisateur a modifié => basé sur `form`
 * - sinon => basé sur l'agrégation (valeur commune) ou false par défaut
 *
 * @param {string} key
 * @returns {boolean}
 */
const getBoolChecked = (key) => {
  if (dirty?.[key]) {
    const v = form?.[key];
    return v === true || v === 1 || String(v) === "1";
  }
  const v = aggregate.value?.[key]?.value;
  // Gérer les valeurs 1/0 (conversion depuis booléen) et true/false
  return v === true || v === 1 || String(v) === "1";
};

/**
 * Indeterminate si la sélection a des valeurs différentes et que l'utilisateur n'a pas modifié le champ.
 *
 * @param {string} key
 * @returns {boolean}
 */
const getBoolIndeterminate = (key) => {
  return !dirty?.[key] && aggregate.value?.[key]?.same === false;
};
</script>

<template>
  <div
    class="rounded-lg border border-base-300 bg-base-200 p-4 space-y-4 max-h-[calc(100vh-7rem)] overflow-y-auto transition-all duration-300 ease-out"
  >
    <div class="flex items-start justify-between gap-3">
      <div class="flex-1">
        <div class="flex items-center gap-2">
          <div class="text-lg font-bold text-primary-100">{{ panelTitle }}</div>
          <transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 scale-90"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-90"
          >
            <div
              v-if="modifiedFieldsCount > 0"
              class="badge badge-primary badge-sm gap-1 animate-pulse"
              :class="{ 'animate-pulse': modifiedFieldsCount > 0 }"
            >
              <Icon source="fa-solid fa-edit" alt="Modifié" size="xs" />
              <span>{{ modifiedFieldsCount }} champ{{ modifiedFieldsCount > 1 ? 's' : '' }} modifié{{ modifiedFieldsCount > 1 ? 's' : '' }}</span>
            </div>
          </transition>
        </div>
        <div class="text-sm text-primary-300 mt-1">{{ ids.length }} sélectionnée(s)</div>
      </div>
      <Tooltip content="Fermer l'édition rapide" placement="left" color="neutral">
        <Btn size="sm" variant="ghost" @click="$emit('clear')" :aria-label="'Fermer l\'édition rapide'">
          <Icon source="fa-solid fa-xmark" alt="Fermer" size="sm" />
        </Btn>
      </Tooltip>
    </div>

    <div class="rounded-md border border-base-300 bg-base-100 p-3">
      <div class="flex items-center justify-between gap-3">
        <div class="text-sm font-semibold">Sélection</div>
        <Btn size="xs" variant="ghost" @click="showList = !showList">{{ showList ? "Masquer" : "Afficher" }}</Btn>
      </div>
      <div v-if="showList" class="mt-2 max-h-40 overflow-y-auto text-sm space-y-1">
        <div v-for="it in selectedList" :key="it.id" class="flex items-center justify-between gap-2">
          <span class="truncate">{{ it.name || "—" }}</span>
          <span class="font-mono opacity-60">#{{ it.id }}</span>
        </div>
      </div>
      <div v-else class="mt-1 text-xs opacity-70">
        Clique sur “Afficher” pour voir les {{ ids.length }} lignes sélectionnées.
      </div>
    </div>

    <div v-if="!isAdmin" class="text-sm text-warning">Tu dois avoir les droits pour modifier.</div>

    <div class="space-y-5">
      <div v-for="group in groupedFieldKeys" :key="group.title" class="space-y-3">
        <div
          v-if="groupedFieldKeys.length > 1"
          class="divider my-0 text-base-content/60 font-semibold text-sm uppercase tracking-wide"
        >
          {{ group.title }}
        </div>

        <div
          v-for="key in group.keys"
          :key="key"
          class="form-control transition-all duration-200"
          :class="{ 'ring-2 ring-primary/30 rounded-md p-2 -m-2': dirty?.[key] }"
        >
          <label class="label">
            <span class="label-text flex items-center gap-2">
              <span :class="{ 'font-semibold text-primary': dirty?.[key] }">{{ fieldsConfig[key]?.label }}</span>
              <transition
                enter-active-class="transition-all duration-200 ease-out"
                enter-from-class="opacity-0 scale-75"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition-all duration-150 ease-in"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-75"
              >
                <Icon
                  v-if="dirty?.[key]"
                  source="fa-solid fa-circle-check"
                  alt="Modifié"
                  size="xs"
                  class="text-primary"
                />
              </transition>
              <Tooltip v-if="fieldsConfig[key]?.tooltip" :content="fieldsConfig[key]?.tooltip" placement="top" color="neutral">
                <Btn size="xs" variant="ghost" class="px-1" :aria-label="`Info: ${fieldsConfig[key]?.label || ''}`">
                  <Icon source="fa-solid fa-circle-info" alt="Info" size="xs" />
                </Btn>
              </Tooltip>
            </span>
          </label>
          <div v-if="fieldsConfig[key]?.help" class="text-xs opacity-70 mb-1">
            {{ fieldsConfig[key]?.help }}
          </div>

          <!-- bool (checkbox) -->
          <div v-if="fieldsConfig[key]?.type === 'checkbox'" class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
              <ToggleCore
                variant="glass"
                size="sm"
                :color="uiColor"
                :disabled="!isFieldEditable(key)"
                :model-value="getBoolChecked(key)"
                :indeterminate="getBoolIndeterminate(key)"
                @update:model-value="(v) => onChange(key, v ? '1' : '0')"
              />
              <span class="text-xs opacity-70">
                <template v-if="getBoolIndeterminate(key)">Valeurs différentes</template>
                <template v-else>{{ getBoolChecked(key) ? "Oui" : "Non" }}</template>
              </span>
            </div>

            <Btn
              v-if="dirty?.[key]"
              size="xs"
              variant="ghost"
              :disabled="!isFieldEditable(key)"
              title="Annuler la modification de ce champ"
              @click="clearField(key)"
            >
              <i class="fa-solid fa-rotate-left"></i>
            </Btn>
          </div>

          <!-- select -->
          <SelectCore
            v-else-if="fieldsConfig[key]?.type === 'select'"
            class="w-full"
            variant="glass"
            size="sm"
            :color="uiColor"
            :disabled="!isFieldEditable(key)"
            :model-value="dirty[key] ? form[key] : (aggregate[key]?.same ? String(aggregate[key]?.value ?? '') : '')"
            @update:model-value="(v) => onChange(key, v)"
          >
            <option value="" disabled hidden>{{ placeholder(aggregate[key]?.same) }}</option>
            <option v-if="isNullableBulkField(key) && !hasEmptyOption(fieldsConfig[key]?.options)" value="">—</option>
            <option v-for="opt in fieldsConfig[key]?.options || []" :key="String(opt.value)" :value="String(opt.value)">
              {{ opt.label }}
            </option>
          </SelectCore>

          <!-- textarea -->
          <TextareaCore
            v-else-if="fieldsConfig[key]?.type === 'textarea'"
            class="w-full"
            variant="glass"
            size="sm"
            :color="uiColor"
            :disabled="!isFieldEditable(key)"
            :model-value="dirty[key] ? form[key] : (aggregate[key]?.same ? String(aggregate[key]?.value ?? '') : '')"
            @update:model-value="(v) => onChange(key, v)"
            :placeholder="fieldsConfig[key]?.placeholder || placeholder(aggregate[key]?.same)"
          />

          <!-- number/text/file fallback -->
          <InputCore
            v-else-if="fieldsConfig[key]?.type !== 'file'"
            class="w-full"
            :type="fieldsConfig[key]?.type === 'number' ? 'number' : 'text'"
            variant="glass"
            size="sm"
            :color="uiColor"
            :disabled="!isFieldEditable(key)"
            :model-value="dirty[key] ? form[key] : (aggregate[key]?.same ? String(aggregate[key]?.value ?? '') : '')"
            @update:model-value="(v) => onChange(key, v)"
            :placeholder="fieldsConfig[key]?.placeholder || placeholder(aggregate[key]?.same)"
          />

          <!-- file (non supporté en bulk JSON pour l’instant) -->
          <InputCore
            v-else
            class="w-full"
            type="file"
            variant="glass"
            size="sm"
            :color="uiColor"
            :disabled="true"
            title="Upload de fichier non supporté en bulk (payload JSON). Utilise un champ URL ou l’édition unitaire."
          />
        </div>
      </div>
    </div>

    <div class="flex flex-col gap-3 pt-2 border-t border-base-300">
      <div class="flex items-center gap-3 text-xs opacity-70">
        <Tooltip content="Appliquer les modifications uniquement aux entités actuellement sélectionnées dans le tableau" placement="top" color="neutral">
          <label class="flex items-center gap-1.5 cursor-pointer">
            <RadioCore v-model="scope" name="bulk-scope" value="selected" size="xs" :color="uiColor" />
            <span>Sélection ({{ ids.length }})</span>
          </label>
        </Tooltip>
        <Tooltip 
          :content="mode !== 'client' ? 'Disponible en mode client uniquement (dataset chargé)' : 'Appliquer les modifications à tous les résultats filtrés, même ceux non sélectionnés'"
          placement="top" 
          color="neutral"
        >
          <label
            class="flex items-center gap-1.5"
            :class="{ 'opacity-60 cursor-not-allowed': mode !== 'client' }"
          >
            <RadioCore v-model="scope" name="bulk-scope" value="filtered" size="xs" :color="uiColor" :disabled="mode !== 'client'" />
            <span>Filtrés ({{ filteredIdsEffective.length }})</span>
          </label>
        </Tooltip>
      </div>
      <div class="flex items-center justify-end gap-2">
        <Btn
          v-if="modifiedFieldsCount > 0"
          size="sm"
          variant="outline"
          color="warning"
          @click="resetFromSelection"
          :disabled="!isAdmin"
          class="gap-2"
        >
          <Icon source="fa-solid fa-rotate-left" alt="Réinitialiser" size="sm" />
          <span>Tout réinitialiser</span>
        </Btn>
        <Btn size="sm" variant="glass" color="primary" @click="apply" :disabled="!canApply" class="gap-2">
          <Icon source="fa-solid fa-check" alt="Appliquer" size="sm" />
          <span>Appliquer</span>
        </Btn>
      </div>
    </div>
  </div>
</template>


