import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

/**
 * useInputActions — Composable universel pour la gestion des actions contextuelles sur les champs de saisie
 *
 * Centralise la logique des actions : reset, back, clear, password, copy, toggleEdit, etc.
 * Fournit un tableau d’actions à afficher, les handlers, les états, et les props à transmettre à l’input.
 *
 * @param {Object} options
 * @param {any} options.modelValue - Valeur courante (v-model)
 * @param {string} options.type - Type d’input (text, password, etc.)
 * @param {Array|string|Object} options.actions - Actions à activer (array, string, ou objet)
 * @param {boolean} [options.readonly] - Champ en lecture seule
 * @param {number} [options.debounce] - Délai pour l’apparition de certaines actions (ms)
 * @param {boolean} [options.autofocus] - Autofocus sur le champ
 * @returns {Object} API du composable
 */
export default function useInputActions({
  modelValue,
  type = 'text',
  actions = [],
  readonly = false,
  debounce = 500,
  autofocus = false,
} = {}) {
  // --- ÉTATS ---
  const initialValue = ref(modelValue);
  const currentValue = ref(modelValue);
  const previousValue = ref(modelValue); // Pour l'action "back"
  const isModified = computed(() => currentValue.value !== initialValue.value);
  const isReadonly = ref(readonly);
  const inputRef = ref(null);
  const showReset = ref(false);
  const showBack = ref(false);
  const showPassword = ref(false); // Pour l'action password
  let resetTimeout = null;
  let backTimeout = null;

  // --- HISTORIQUE POUR BACK ---
  watch(currentValue, (newVal, oldVal) => {
    if (oldVal !== newVal) {
      previousValue.value = oldVal;
    }
  });

  // --- SYNC AVEC v-model ---
  watch(
    () => modelValue,
    (v) => {
      currentValue.value = v;
    }
  );

  // --- DÉBOUNCE POUR RESET/BACK ---
  watch(isModified, (val) => {
    if (val) {
      if (resetTimeout) clearTimeout(resetTimeout);
      resetTimeout = setTimeout(() => {
        showReset.value = true;
      }, debounce);
    } else {
      showReset.value = false;
      if (resetTimeout) clearTimeout(resetTimeout);
    }
  });

  watch(() => previousValue.value, (val) => {
    if (val !== initialValue.value) {
      if (backTimeout) clearTimeout(backTimeout);
      backTimeout = setTimeout(() => {
        showBack.value = true;
      }, debounce);
    } else {
      showBack.value = false;
      if (backTimeout) clearTimeout(backTimeout);
    }
  });

  onUnmounted(() => {
    if (resetTimeout) clearTimeout(resetTimeout);
    if (backTimeout) clearTimeout(backTimeout);
  });

  // --- LOGIQUE DES ACTIONS ---
  function parseActions(actions) {
    if (Array.isArray(actions)) {
      return actions.reduce((acc, key) => {
        if (typeof key === 'string') acc[key] = true;
        else if (typeof key === 'object') Object.assign(acc, key);
        return acc;
      }, {});
    } else if (typeof actions === 'object') {
      return { ...actions };
    } else if (typeof actions === 'string') {
      return { [actions]: true };
    }
    return {};
  }
  const actionsConfig = computed(() => parseActions(actions));

  // --- HANDLERS ---
  function reset() {
    currentValue.value = initialValue.value;
  }
  function back() {
    currentValue.value = previousValue.value;
  }
  function clear() {
    currentValue.value = '';
  }
  function togglePassword() {
    showPassword.value = !showPassword.value;
  }
  function copy() {
    if (typeof navigator !== 'undefined' && navigator.clipboard) {
      navigator.clipboard.writeText(currentValue.value ?? '');
    }
  }
  function toggleEdit() {
    isReadonly.value = !isReadonly.value;
  }
  function focus() {
    if (inputRef.value) inputRef.value.focus();
  }

  // --- LOGIQUE D'AFFICHAGE DES ACTIONS ---
  const canReset = computed(() => actionsConfig.value.reset && isModified.value && showReset.value);
  const canBack = computed(() => actionsConfig.value.back && previousValue.value !== initialValue.value && showBack.value);
  const canClear = computed(() => actionsConfig.value.clear && !!currentValue.value);
  const canPassword = computed(() => actionsConfig.value.password && type === 'password');
  const canCopy = computed(() => actionsConfig.value.copy && !!currentValue.value);
  const canToggleEdit = computed(() => actionsConfig.value.toggleEdit);

  // --- TABLEAU D’ACTIONS À AFFICHER ---
  const actionsToDisplay = computed(() => {
    const arr = [];
    if (canReset.value) {
      arr.push({
        key: 'reset',
        icon: 'fa-solid fa-arrow-rotate-left',
        ariaLabel: 'Réinitialiser',
        tooltip: 'Revenir à la valeur initiale',
        visible: true,
        disabled: false,
        onClick: reset,
      });
    }
    if (canBack.value) {
      arr.push({
        key: 'back',
        icon: 'fa-solid fa-rotate-left',
        ariaLabel: 'Annuler la dernière modification',
        tooltip: 'Annuler la dernière modification',
        visible: true,
        disabled: false,
        onClick: back,
      });
    }
    if (canClear.value) {
      arr.push({
        key: 'clear',
        icon: 'fa-solid fa-xmark',
        ariaLabel: 'Vider le champ',
        tooltip: 'Vider le champ',
        visible: true,
        disabled: false,
        onClick: clear,
      });
    }
    if (canCopy.value) {
      arr.push({
        key: 'copy',
        icon: 'fa-solid fa-copy',
        ariaLabel: 'Copier le contenu',
        tooltip: 'Copier le contenu',
        visible: true,
        disabled: false,
        onClick: copy,
      });
    }
    if (canPassword.value) {
      arr.push({
        key: 'password',
        icon: showPassword.value ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye',
        ariaLabel: showPassword.value ? 'Masquer le mot de passe' : 'Afficher le mot de passe',
        tooltip: showPassword.value ? 'Masquer le mot de passe' : 'Afficher le mot de passe',
        visible: true,
        disabled: false,
        onClick: togglePassword,
      });
    }
    if (canToggleEdit.value) {
      arr.push({
        key: 'toggleEdit',
        icon: isReadonly.value ? 'fa-solid fa-pen' : 'fa-solid fa-eye',
        ariaLabel: isReadonly.value ? 'Passer en édition' : 'Passer en lecture seule',
        tooltip: isReadonly.value ? 'Passer en édition' : 'Passer en lecture seule',
        visible: true,
        disabled: false,
        onClick: toggleEdit,
      });
    }
    return arr;
  });

  // --- PROPS À TRANSMETTRE À L’INPUT ---
  const inputProps = computed(() => {
    let effectiveType = type;
    if (type === 'password' && showPassword.value) {
      effectiveType = 'text';
    }
    return {
      type: effectiveType,
      readonly: isReadonly.value,
      value: currentValue.value,
      ref: inputRef,
      autofocus,
    };
  });

  // --- EXPOSE ---
  return {
    currentValue,
    initialValue,
    previousValue,
    isModified,
    isReadonly,
    inputRef,
    focus,
    actionsToDisplay,
    inputProps,
    reset,
    back,
    clear,
    togglePassword,
    copy,
    toggleEdit,
  };
} 