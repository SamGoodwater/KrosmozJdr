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
import { computed } from "vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import { useBulkEditPanel } from "@/Composables/entity/useBulkEditPanel";

const props = defineProps({
  selectedEntities: { type: Array, default: () => [] },
  isAdmin: { type: Boolean, default: false },
  resourceTypes: { type: Array, default: () => [] }, // [{id,name}]
  filteredIds: { type: Array, default: () => [] },
  mode: { type: String, default: "server" }, // server | client
});

const emit = defineEmits(["applied", "clear"]);

const FIELD_META = {
  resource_type_id: { label: "Type de ressource", nullable: true, build: (v) => (v === "" ? null : Number(v)) },
  usable: { label: "Utilisable", nullable: false, build: (v) => v === "1" },
  auto_update: { label: "Auto-update", nullable: false, build: (v) => v === "1" },
  is_visible: { label: "Visibilité", nullable: false, build: (v) => v },
  rarity: { label: "Rareté", nullable: false, build: (v) => Number(v) },

  // Champs additionnels (bulk)
  level: { label: "Niveau", nullable: true, build: (v) => (v === "" ? null : String(v)) },
  price: { label: "Prix", nullable: true, build: (v) => (v === "" ? null : String(v)) },
  weight: { label: "Poids", nullable: true, build: (v) => (v === "" ? null : String(v)) },
  dofus_version: { label: "Version Dofus", nullable: true, build: (v) => (v === "" ? null : String(v)) },
  image: { label: "Image (URL)", nullable: true, build: (v) => (v === "" ? null : String(v)) },
  description: { label: "Description", nullable: true, build: (v) => (v === "" ? null : String(v)) },
};

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
  selectedEntities: props.selectedEntities,
  isAdmin: props.isAdmin,
  fieldMeta: FIELD_META,
  mode: props.mode,
  filteredIds: props.filteredIds,
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
          <input type="radio" class="radio radio-sm" value="selected" v-model="scope" />
          <span class="text-sm">Appliquer à la sélection ({{ ids.length }})</span>
        </label>
        <label
          class="flex items-center gap-2"
          :class="{ 'opacity-60 cursor-not-allowed': mode !== 'client' }"
          :title="mode !== 'client' ? 'Disponible en mode client uniquement (dataset chargé)' : ''"
        >
          <input
            type="radio"
            class="radio radio-sm"
            value="filtered"
            v-model="scope"
            :disabled="mode !== 'client'"
          />
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
        <select
          class="select select-bordered"
          :value="form.resource_type_id"
          :disabled="!isAdmin"
          @change="(e) => onChange('resource_type_id', e)"
        >
          <option value="" disabled hidden>{{ placeholder(aggregate.resource_type_id?.same) }}</option>
          <option value="">—</option>
          <option v-for="t in resourceTypes" :key="t.id" :value="String(t.id)">{{ t.name }}</option>
        </select>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="form-control">
          <label class="label"><span class="label-text">Utilisable</span></label>
          <select class="select select-bordered" :value="form.usable" :disabled="!isAdmin" @change="(e) => onChange('usable', e)">
            <option value="" disabled hidden>{{ placeholder(aggregate.usable?.same) }}</option>
            <option value="1">Oui</option>
            <option value="0">Non</option>
          </select>
        </div>
        <div class="form-control">
          <label class="label"><span class="label-text">Auto-update</span></label>
          <select class="select select-bordered" :value="form.auto_update" :disabled="!isAdmin" @change="(e) => onChange('auto_update', e)">
            <option value="" disabled hidden>{{ placeholder(aggregate.auto_update?.same) }}</option>
            <option value="1">Oui</option>
            <option value="0">Non</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="form-control">
          <label class="label"><span class="label-text">Visibilité</span></label>
          <select class="select select-bordered" :value="form.is_visible" :disabled="!isAdmin" @change="(e) => onChange('is_visible', e)">
            <option value="" disabled hidden>{{ placeholder(aggregate.is_visible?.same) }}</option>
            <option value="guest">Invité</option>
            <option value="user">Utilisateur</option>
            <option value="game_master">Maître de jeu</option>
            <option value="admin">Administrateur</option>
          </select>
        </div>
        <div class="form-control">
          <label class="label"><span class="label-text">Rareté</span></label>
          <select class="select select-bordered" :value="form.rarity" :disabled="!isAdmin" @change="(e) => onChange('rarity', e)">
            <option value="" disabled hidden>{{ placeholder(aggregate.rarity?.same) }}</option>
            <option value="0">Commun</option>
            <option value="1">Peu commun</option>
            <option value="2">Rare</option>
            <option value="3">Très rare</option>
            <option value="4">Légendaire</option>
            <option value="5">Unique</option>
          </select>
        </div>
      </div>

      <div class="divider my-2">Champs de la ressource</div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="form-control">
          <label class="label"><span class="label-text">Niveau</span></label>
          <input
            class="input input-bordered"
            type="text"
            :disabled="!isAdmin"
            :value="form.level"
            :placeholder="aggregate.level?.same ? '—' : 'Valeurs différentes'"
            @input="(e) => onChange('level', e)"
          />
        </div>
        <div class="form-control">
          <label class="label"><span class="label-text">Version Dofus</span></label>
          <input
            class="input input-bordered"
            type="text"
            :disabled="!isAdmin"
            :value="form.dofus_version"
            :placeholder="aggregate.dofus_version?.same ? '—' : 'Valeurs différentes'"
            @input="(e) => onChange('dofus_version', e)"
          />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="form-control">
          <label class="label"><span class="label-text">Prix</span></label>
          <input
            class="input input-bordered"
            type="text"
            :disabled="!isAdmin"
            :value="form.price"
            :placeholder="aggregate.price?.same ? '—' : 'Valeurs différentes'"
            @input="(e) => onChange('price', e)"
          />
        </div>
        <div class="form-control">
          <label class="label"><span class="label-text">Poids</span></label>
          <input
            class="input input-bordered"
            type="text"
            :disabled="!isAdmin"
            :value="form.weight"
            :placeholder="aggregate.weight?.same ? '—' : 'Valeurs différentes'"
            @input="(e) => onChange('weight', e)"
          />
        </div>
      </div>

      <div class="form-control">
        <label class="label"><span class="label-text">Image (URL)</span></label>
        <input
          class="input input-bordered"
          type="text"
          :disabled="!isAdmin"
          :value="form.image"
          :placeholder="aggregate.image?.same ? '—' : 'Valeurs différentes'"
          @input="(e) => onChange('image', e)"
        />
      </div>

      <div class="form-control">
        <label class="label"><span class="label-text">Description</span></label>
        <textarea
          class="textarea textarea-bordered min-h-24"
          :disabled="!isAdmin"
          :value="form.description"
          :placeholder="aggregate.description?.same ? '—' : 'Valeurs différentes'"
          @input="(e) => onChange('description', e)"
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


