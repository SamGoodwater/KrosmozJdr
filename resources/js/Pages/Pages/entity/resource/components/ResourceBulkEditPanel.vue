<script setup>
/**
 * ResourceBulkEditPanel
 *
 * @description
 * Panneau d'édition en masse pour les ressources :
 * - (par défaut) tous les champs pertinents, sauf ceux qui n'ont pas de sens en bulk (name, ids, dofusdb_id, etc.)
 * - on applique uniquement les champs modifiés.
 *
 * Règle UX:
 * - valeur commune -> préremplie
 * - valeurs différentes -> champ vide + placeholder "Valeurs différentes"
 * - à la validation, on applique uniquement les champs modifiés.
 */
import { computed, toRef } from "vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import SelectCore from "@/Pages/Atoms/data-input/SelectCore.vue";
import InputCore from "@/Pages/Atoms/data-input/InputCore.vue";
import TextareaCore from "@/Pages/Atoms/data-input/TextareaCore.vue";
import RadioCore from "@/Pages/Atoms/data-input/RadioCore.vue";
import { useBulkEditPanel } from "@/Composables/entity/useBulkEditPanel";
import { createBulkFieldMetaFromSchema } from "@/Utils/entity/field-schema";
import createResourceFieldSchema from "../resource-field-schema";

const props = defineProps({
  selectedEntities: { type: Array, default: () => [] },
  isAdmin: { type: Boolean, default: false },
  resourceTypes: { type: Array, default: () => [] }, // [{id,name}]
  filteredIds: { type: Array, default: () => [] },
  mode: { type: String, default: "server" }, // server | client
});

const emit = defineEmits(["applied", "clear"]);
const uiColor = computed(() => "primary");

const FIELD_META = createBulkFieldMetaFromSchema(createResourceFieldSchema());

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
  fieldMeta: FIELD_META,
  mode: props.mode,
  filteredIds: toRef(props, "filteredIds"),
});

const panelTitle = computed(() => (ids.value.length <= 1 ? "Édition" : "Édition en masse"));

const applyBulk = () => {
  if (!canApply.value) return;
  emit("applied", buildPayload());
};
</script>

<template>
  <div class="rounded-lg border border-base-300 bg-base-200 p-4 space-y-4 max-h-[calc(100vh-7rem)] overflow-y-auto">
    <div class="flex items-start justify-between gap-3">
      <div>
        <div class="text-lg font-bold text-primary-100">{{ panelTitle }}</div>
        <div class="text-sm text-primary-300">{{ ids.length }} ressources sélectionnées</div>
      </div>
      <Btn size="sm" variant="ghost" @click="$emit('clear')">Effacer</Btn>
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

    <div class="rounded-md border border-base-300 bg-base-100 p-3 space-y-2">
      <div class="text-sm font-semibold">Cible</div>
      <div class="flex flex-col gap-2">
        <label class="flex items-center gap-2 cursor-pointer">
          <RadioCore v-model="scope" name="bulk-scope" value="selected" size="sm" :color="uiColor" />
          <span class="text-sm">Appliquer à la sélection ({{ ids.length }})</span>
        </label>
        <label
          class="flex items-center gap-2"
          :class="{ 'opacity-60 cursor-not-allowed': mode !== 'client' }"
          :title="mode !== 'client' ? 'Disponible en mode client uniquement (dataset chargé)' : ''"
        >
          <RadioCore v-model="scope" name="bulk-scope" value="filtered" size="sm" :color="uiColor" :disabled="mode !== 'client'" />
          <span class="text-sm">Appliquer à tous les résultats filtrés ({{ filteredIdsEffective.length }})</span>
        </label>
      </div>
    </div>

    <div v-if="!isAdmin" class="text-sm text-warning">
      Tu dois être administrateur pour modifier en masse.
    </div>

    <div class="space-y-3">
      <div class="form-control">
        <label class="label"><span class="label-text">Type de ressource</span></label>
        <SelectCore
          class="w-full"
          variant="glass"
          size="sm"
          :color="uiColor"
          :disabled="!isAdmin"
          :model-value="form.resource_type_id"
          @update:model-value="(v) => onChange('resource_type_id', v)"
        >
          <option value="" disabled hidden>{{ placeholder(aggregate.resource_type_id?.same) }}</option>
          <option value="">—</option>
          <option v-for="t in resourceTypes" :key="t.id" :value="String(t.id)">{{ t.name }}</option>
        </SelectCore>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="form-control">
          <label class="label"><span class="label-text">Utilisable</span></label>
          <SelectCore
            class="w-full"
            variant="glass"
            size="sm"
            :color="uiColor"
            :disabled="!isAdmin"
            :model-value="form.usable"
            @update:model-value="(v) => onChange('usable', v)"
          >
            <option value="" disabled hidden>{{ placeholder(aggregate.usable?.same) }}</option>
            <option value="1">Oui</option>
            <option value="0">Non</option>
          </SelectCore>
        </div>
        <div class="form-control">
          <label class="label"><span class="label-text">Auto-update</span></label>
          <SelectCore
            class="w-full"
            variant="glass"
            size="sm"
            :color="uiColor"
            :disabled="!isAdmin"
            :model-value="form.auto_update"
            @update:model-value="(v) => onChange('auto_update', v)"
          >
            <option value="" disabled hidden>{{ placeholder(aggregate.auto_update?.same) }}</option>
            <option value="1">Oui</option>
            <option value="0">Non</option>
          </SelectCore>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="form-control">
          <label class="label"><span class="label-text">Visibilité</span></label>
          <SelectCore
            class="w-full"
            variant="glass"
            size="sm"
            :color="uiColor"
            :disabled="!isAdmin"
            :model-value="form.is_visible"
            @update:model-value="(v) => onChange('is_visible', v)"
          >
            <option value="" disabled hidden>{{ placeholder(aggregate.is_visible?.same) }}</option>
            <option value="guest">Invité</option>
            <option value="user">Utilisateur</option>
            <option value="game_master">Maître de jeu</option>
            <option value="admin">Administrateur</option>
          </SelectCore>
        </div>
        <div class="form-control">
          <label class="label"><span class="label-text">Rareté</span></label>
          <SelectCore
            class="w-full"
            variant="glass"
            size="sm"
            :color="uiColor"
            :disabled="!isAdmin"
            :model-value="form.rarity"
            @update:model-value="(v) => onChange('rarity', v)"
          >
            <option value="" disabled hidden>{{ placeholder(aggregate.rarity?.same) }}</option>
            <option value="0">Commun</option>
            <option value="1">Peu commun</option>
            <option value="2">Rare</option>
            <option value="3">Très rare</option>
            <option value="4">Légendaire</option>
            <option value="5">Unique</option>
          </SelectCore>
        </div>
      </div>

      <div class="divider my-2">Champs de la ressource</div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="form-control">
          <label class="label"><span class="label-text">Niveau</span></label>
          <InputCore
            class="w-full"
            type="text"
            variant="glass"
            size="sm"
            :color="uiColor"
            :disabled="!isAdmin"
            :placeholder="aggregate.level?.same ? '—' : 'Valeurs différentes'"
            :model-value="form.level"
            @update:model-value="(v) => onChange('level', v)"
          />
        </div>
        <div class="form-control">
          <label class="label"><span class="label-text">Version Dofus</span></label>
          <InputCore
            class="w-full"
            type="text"
            variant="glass"
            size="sm"
            :color="uiColor"
            :disabled="!isAdmin"
            :placeholder="aggregate.dofus_version?.same ? '—' : 'Valeurs différentes'"
            :model-value="form.dofus_version"
            @update:model-value="(v) => onChange('dofus_version', v)"
          />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="form-control">
          <label class="label"><span class="label-text">Prix</span></label>
          <InputCore
            class="w-full"
            type="text"
            variant="glass"
            size="sm"
            :color="uiColor"
            :disabled="!isAdmin"
            :placeholder="aggregate.price?.same ? '—' : 'Valeurs différentes'"
            :model-value="form.price"
            @update:model-value="(v) => onChange('price', v)"
          />
        </div>
        <div class="form-control">
          <label class="label"><span class="label-text">Poids</span></label>
          <InputCore
            class="w-full"
            type="text"
            variant="glass"
            size="sm"
            :color="uiColor"
            :disabled="!isAdmin"
            :placeholder="aggregate.weight?.same ? '—' : 'Valeurs différentes'"
            :model-value="form.weight"
            @update:model-value="(v) => onChange('weight', v)"
          />
        </div>
      </div>

      <div class="form-control">
        <label class="label"><span class="label-text">Image (URL)</span></label>
        <InputCore
          class="w-full"
          type="text"
          variant="glass"
          size="sm"
          :color="uiColor"
          :disabled="!isAdmin"
          :placeholder="aggregate.image?.same ? '—' : 'Valeurs différentes'"
          :model-value="form.image"
          @update:model-value="(v) => onChange('image', v)"
        />
      </div>

      <div class="form-control">
        <label class="label"><span class="label-text">Description</span></label>
        <TextareaCore
          class="w-full min-h-24"
          variant="glass"
          size="sm"
          :color="uiColor"
          :disabled="!isAdmin"
          :placeholder="aggregate.description?.same ? '—' : 'Valeurs différentes'"
          :model-value="form.description"
          @update:model-value="(v) => onChange('description', v)"
        />
        <div class="mt-1 text-xs opacity-70">
          Astuce : si tu modifies un champ et le laisses vide, la valeur sera vidée (mise à null) pour la cible choisie.
        </div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-2 pt-2">
      <Btn size="sm" variant="ghost" @click="resetFromSelection" :disabled="!isAdmin">Réinitialiser</Btn>
      <Btn size="sm" color="primary" @click="applyBulk" :disabled="!canApply">Appliquer</Btn>
    </div>
  </div>
</template>


