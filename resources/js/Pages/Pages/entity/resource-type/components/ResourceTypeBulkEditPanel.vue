<script setup>
/**
 * ResourceTypeBulkEditPanel
 *
 * @description
 * Panneau d'édition en masse pour ResourceType (decision, usable, is_visible).
 * Règle UX:
 * - Si toutes les lignes sélectionnées ont la même valeur -> on l'affiche.
 * - Sinon -> champ vide avec placeholder "Valeurs différentes".
 * - À la validation, on applique uniquement les champs effectivement modifiés.
 *
 * @props {Array} selectedEntities - ResourceTypes sélectionnés (objets bruts ou modèles).
 * @props {Boolean} isAdmin - Autorise l'édition.
 *
 * @emit applied - quand la MAJ a réussi (le parent peut refresh/clear sélection).
 * @emit clear - demander au parent de vider la sélection.
 */
import { computed } from "vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import { useBulkEditPanel } from "@/Composables/entity/useBulkEditPanel";

const props = defineProps({
  selectedEntities: { type: Array, default: () => [] },
  isAdmin: { type: Boolean, default: false },
  mode: { type: String, default: "client" },
  filteredIds: { type: Array, default: () => [] },
});

const emit = defineEmits(["applied", "clear"]);

const FIELD_META = {
  decision: { label: "Statut", nullable: false, build: (v) => v },
  usable: { label: "Utilisable", nullable: false, build: (v) => v === "1" },
  is_visible: { label: "Visibilité", nullable: false, build: (v) => v },
};

const {
  showList,
  ids,
  selectedList,
  aggregate,
  form,
  dirty,
  placeholder,
  onChange,
  canApply,
  resetFromSelection,
  buildPayload,
} = useBulkEditPanel({
  selectedEntities: props.selectedEntities,
  isAdmin: props.isAdmin,
  fieldMeta: FIELD_META,
  mode: props.mode,
  filteredIds: props.filteredIds,
});

const panelTitle = computed(() => {
  return ids.value.length <= 1 ? "Édition" : "Édition en masse";
});

const applyBulk = async () => {
  if (!canApply.value) return;
  emit("applied", buildPayload());
};
</script>

<template>
  <div class="rounded-lg border border-base-300 bg-base-200 p-4 space-y-4 max-h-[calc(100vh-7rem)] overflow-y-auto">
    <div class="flex items-start justify-between gap-3">
      <div>
        <div class="text-lg font-bold text-primary-100">{{ panelTitle }}</div>
        <div class="text-sm text-primary-300">
          {{ ids.length }} types sélectionnés
        </div>
      </div>
      <Btn size="sm" variant="ghost" @click="$emit('clear')" title="Vider la sélection">
        Effacer
      </Btn>
    </div>

    <div class="rounded-md border border-base-300 bg-base-100 p-3">
      <div class="flex items-center justify-between gap-3">
        <div class="text-sm font-semibold">Sélection</div>
        <Btn size="xs" variant="ghost" @click="showList = !showList">
          {{ showList ? "Masquer" : "Afficher" }}
        </Btn>
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

    <div v-if="!isAdmin" class="text-sm text-warning">
      Tu dois être administrateur pour modifier en masse.
    </div>

    <div class="space-y-3">
      <div class="form-control">
        <label class="label"><span class="label-text">Statut</span></label>
        <select class="select select-bordered" :value="form.decision" :disabled="!isAdmin" @change="(e) => onChange('decision', e)">
          <option value="" disabled hidden>{{ placeholder(aggregate.decision?.same) }}</option>
          <option value="pending">En attente</option>
          <option value="allowed">Utilisé</option>
          <option value="blocked">Non utilisé</option>
        </select>
      </div>

      <div class="form-control">
        <label class="label"><span class="label-text">Utilisable</span></label>
        <select class="select select-bordered" :value="form.usable" :disabled="!isAdmin" @change="(e) => onChange('usable', e)">
          <option value="" disabled hidden>{{ placeholder(aggregate.usable?.same) }}</option>
          <option value="1">Oui</option>
          <option value="0">Non</option>
        </select>
      </div>

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
    </div>

    <div class="flex items-center justify-end gap-2 pt-2">
      <Btn size="sm" variant="ghost" @click="resetFromSelection" :disabled="!isAdmin">
        Réinitialiser
      </Btn>
      <Btn size="sm" color="primary" @click="applyBulk" :disabled="!canApply">
        Appliquer
      </Btn>
    </div>
  </div>
</template>


