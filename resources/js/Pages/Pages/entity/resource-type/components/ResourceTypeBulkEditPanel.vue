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
import { computed, reactive, ref, watch } from "vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";

const props = defineProps({
  selectedEntities: { type: Array, default: () => [] },
  isAdmin: { type: Boolean, default: false },
});

const emit = defineEmits(["applied", "clear"]);

const getId = (e) => (e && typeof e.id !== "undefined" ? Number(e.id) : null);
const getName = (e) => {
  if (!e) return "";
  if (typeof e?._data !== "undefined") return String(e.name ?? e._data?.name ?? "");
  return String(e?.name ?? "");
};

const showList = ref(false);

const valuesOf = (key) => {
  return (props.selectedEntities || [])
    .map((e) => (typeof e?._data !== "undefined" ? e[key] ?? e._data?.[key] : e?.[key]))
    .map((v) => (typeof v === "boolean" ? (v ? 1 : 0) : v));
};

const allSame = (arr) => {
  if (!arr.length) return { same: true, value: null };
  const first = arr[0];
  for (const v of arr) {
    // Comparaison stricte (string/number)
    if (String(v ?? "") !== String(first ?? "")) return { same: false, value: null };
  }
  return { same: true, value: first ?? null };
};

const aggregate = computed(() => {
  const decision = allSame(valuesOf("decision"));
  const usable = allSame(valuesOf("usable"));
  const isVisible = allSame(valuesOf("is_visible"));
  return {
    decision,
    usable,
    isVisible,
  };
});

const form = reactive({
  decision: "",
  usable: "",
  is_visible: "",
});

const dirty = reactive({
  decision: false,
  usable: false,
  is_visible: false,
});

const resetFromSelection = () => {
  form.decision = aggregate.value.decision.same ? String(aggregate.value.decision.value ?? "") : "";
  form.usable = aggregate.value.usable.same ? String(aggregate.value.usable.value ?? "") : "";
  form.is_visible = aggregate.value.isVisible.same ? String(aggregate.value.isVisible.value ?? "") : "";
  dirty.decision = false;
  dirty.usable = false;
  dirty.is_visible = false;
};

watch(
  () => props.selectedEntities.map(getId).join(","),
  () => resetFromSelection(),
  { immediate: true }
);

const placeholderDecision = computed(() =>
  aggregate.value.decision.same ? "Choisir…" : "Valeurs différentes"
);
const placeholderUsable = computed(() =>
  aggregate.value.usable.same ? "Choisir…" : "Valeurs différentes"
);
const placeholderVisible = computed(() =>
  aggregate.value.isVisible.same ? "Choisir…" : "Valeurs différentes"
);

const canApply = computed(() => {
  if (!props.isAdmin) return false;
  const anyDirty = dirty.decision || dirty.usable || dirty.is_visible;
  if (!anyDirty) return false;

  // Si dirty, une valeur doit être choisie
  if (dirty.decision && !form.decision) return false;
  if (dirty.usable && !form.usable) return false;
  if (dirty.is_visible && !form.is_visible) return false;
  return true;
});

const ids = computed(() => (props.selectedEntities || []).map(getId).filter(Boolean));

const selectedList = computed(() => {
  return (props.selectedEntities || [])
    .map((e) => ({ id: getId(e), name: getName(e) }))
    .filter((x) => x.id);
});

const panelTitle = computed(() => {
  return ids.value.length <= 1 ? "Édition" : "Édition en masse";
});

const onChangeDecision = (e) => {
  dirty.decision = true;
  form.decision = e?.target?.value ?? "";
};
const onChangeUsable = (e) => {
  dirty.usable = true;
  form.usable = e?.target?.value ?? "";
};
const onChangeVisible = (e) => {
  dirty.is_visible = true;
  form.is_visible = e?.target?.value ?? "";
};

const getCsrfToken = () => document.querySelector("meta[name=\"csrf-token\"]")?.getAttribute("content");

const applyBulk = async () => {
  if (!canApply.value) return;
  const csrf = getCsrfToken();
  if (!csrf) return;

  const payload = { ids: ids.value };
  if (dirty.decision) payload.decision = form.decision;
  if (dirty.usable) payload.usable = form.usable === "1";
  if (dirty.is_visible) payload.is_visible = form.is_visible;

  emit("applied", payload);
};
</script>

<template>
  <div class="rounded-lg border border-base-300 bg-base-200 p-4 space-y-4">
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
        <select class="select select-bordered" :value="form.decision" :disabled="!isAdmin" @change="onChangeDecision">
          <option value="" disabled hidden>{{ placeholderDecision }}</option>
          <option value="pending">En attente</option>
          <option value="allowed">Utilisé</option>
          <option value="blocked">Non utilisé</option>
        </select>
      </div>

      <div class="form-control">
        <label class="label"><span class="label-text">Utilisable</span></label>
        <select class="select select-bordered" :value="form.usable" :disabled="!isAdmin" @change="onChangeUsable">
          <option value="" disabled hidden>{{ placeholderUsable }}</option>
          <option value="1">Oui</option>
          <option value="0">Non</option>
        </select>
      </div>

      <div class="form-control">
        <label class="label"><span class="label-text">Visibilité</span></label>
        <select class="select select-bordered" :value="form.is_visible" :disabled="!isAdmin" @change="onChangeVisible">
          <option value="" disabled hidden>{{ placeholderVisible }}</option>
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


