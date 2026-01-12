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
import { computed, toRef, ref } from "vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import RadioCore from "@/Pages/Atoms/data-input/RadioCore.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import { getEntityConfig } from "@/Entities/entity-registry";
import { createBulkFieldMetaFromDescriptors } from "@/Utils/entity/descriptor-form";
import { getCachedDescriptors } from "@/Utils/entity/descriptor-cache";
import { resolveEntityViewComponentSync } from "@/Utils/entity/resolveEntityViewComponent";
import { getMapperForEntityType } from '@/Utils/Entity/MapperRegistry';

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

const fieldMetaAll = computed(() => createBulkFieldMetaFromDescriptors(descriptors.value, ctx.value));

const fieldKeys = computed(() => {
  if (Array.isArray(props.fields) && props.fields.length) return props.fields;
  const preferred = cfg.value?.viewFields?.quickEdit;
  if (Array.isArray(preferred) && preferred.length) return preferred;
  return Object.keys(fieldMetaAll.value || {});
});

const fieldMeta = computed(() => {
  const out = {};
  for (const k of fieldKeys.value || []) {
    if (!fieldMetaAll.value?.[k]) continue;
    out[k] = fieldMetaAll.value[k];
  }
  return out;
});

const panelTitle = computed(() => {
  if (props.title) return props.title;
  return props.selectedEntities?.length <= 1 ? "Édition rapide" : "Édition rapide (multi)";
});

// Référence au composant QuickEdit pour accéder aux valeurs exposées
const quickEditViewRef = ref(null);

// Calculer les IDs et la liste sélectionnée depuis les entités
const getId = (e) => (e && typeof e.id !== "undefined" ? Number(e.id) : null);
const getName = (e) => {
  if (!e) return "";
  if (typeof e?._data !== "undefined") return String(e.name ?? e._data?.name ?? "");
  return String(e?.name ?? "");
};

const ids = computed(() => {
  return (props.selectedEntities || [])
    .map(getId)
    .filter(Boolean);
});

const selectedList = computed(() => {
  return (props.selectedEntities || [])
    .map((e) => ({ id: getId(e), name: getName(e) }))
    .filter((x) => x.id);
});

// Scope et filteredIds (gérés par le panneau, pas par useBulkEditPanel)
const showList = ref(false);
const scope = ref("selected"); // selected | filtered
const filteredIdsEffective = computed(() => {
  const arr = props.filteredIds || [];
  return arr.map((v) => Number(v)).filter(Boolean);
});

/**
 * Nombre de champs modifiés (dirty).
 * Récupéré depuis la vue QuickEdit qui expose cette information.
 */
const modifiedFieldsCount = computed(() => {
  return quickEditViewRef.value?.modifiedFieldsCount || 0;
});

/**
 * Calculer canApply en utilisant le dirty de ResourceQuickEdit
 * au lieu du dirty de useBulkEditPanel dans EntityQuickEditPanel
 */
const canApply = computed(() => {
  if (!props.isAdmin) return false;
  
  // Utiliser le dirty exposé par ResourceQuickEdit
  const dirty = quickEditViewRef.value?.dirty;
  if (!dirty) return false;
  
  const anyDirty = Object.values(dirty).some(Boolean);
  if (!anyDirty) return false;

  // Récupérer form depuis ResourceQuickEdit
  const form = quickEditViewRef.value?.form;
  if (!form) return false;

  // Vérifier que les champs non-nullable ont une valeur
  for (const key of Object.keys(dirty)) {
    if (!dirty[key]) continue;
    const meta = fieldMeta.value?.[key];
    if (!meta) continue;
    if (!meta.nullable && !form[key]) return false;
  }

  if (scope.value === "filtered" && props.mode !== "client") return false;
  if (scope.value === "filtered" && filteredIdsEffective.value.length === 0) return false;

  return true;
});

const apply = () => {
  if (!canApply.value) return;
  
  // Utiliser buildPayload exposé par ResourceQuickEdit si disponible
  // Sinon, construire le payload manuellement
  if (quickEditViewRef.value?.buildPayload) {
    const payload = quickEditViewRef.value.buildPayload();
    // Ajuster les IDs selon le scope
    const targetIds = scope.value === "filtered" ? filteredIdsEffective.value : ids.value;
    payload.ids = targetIds;
    emit("applied", payload);
    return;
  }
  
  // Fallback : construire le payload manuellement
  const dirty = quickEditViewRef.value?.dirty;
  const form = quickEditViewRef.value?.form;
  
  if (!dirty || !form) return;
  
  const targetIds = scope.value === "filtered" ? filteredIdsEffective.value : ids.value;
  const payload = { ids: targetIds };
  
  // Récupérer le mapper depuis le registre centralisé
  const mapper = getMapperForEntityType(props.entityType);
  
  if (mapper && typeof mapper.fromBulkForm === 'function') {
    const bulkFormData = {};
    for (const key of Object.keys(dirty)) {
      if (dirty[key]) {
        bulkFormData[key] = form[key];
      }
    }
    const mappedData = mapper.fromBulkForm(bulkFormData);
    Object.assign(payload, mappedData);
  } else {
    // Fallback : utiliser directement les valeurs du form
    for (const key of Object.keys(dirty)) {
      if (dirty[key]) {
        payload[key] = form[key];
      }
    }
  }
  
  emit("applied", payload);
};

// Réinitialiser depuis la sélection (exposé par la vue QuickEdit)
const resetFromSelection = () => {
  if (quickEditViewRef.value?.resetFromSelection) {
    quickEditViewRef.value.resetFromSelection();
  }
};

// Résoudre le composant QuickEdit pour cette entité (synchrone)
const QuickEditComponent = computed(() => {
  return resolveEntityViewComponentSync(props.entityType, 'quickedit');
});
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

    <!-- Vue QuickEdit générée depuis les descriptors -->
    <!-- Note: Les événements 'change' et 'clear-field' sont émis par EntityQuickEdit mais non utilisés ici -->
    <component
      v-if="QuickEditComponent"
      :is="QuickEditComponent"
      ref="quickEditViewRef"
      :selected-entities="selectedEntities"
      :is-admin="isAdmin"
      :extra-ctx="extraCtx"
      :fields="fields"
    />
    <div v-else class="text-sm text-warning">
      Vue QuickEdit non trouvée pour {{ entityType }}. Vérifiez que le fichier existe.
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


