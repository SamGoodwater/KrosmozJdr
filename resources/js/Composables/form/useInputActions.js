import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

/**
 * useInputActions — Composable universel pour la gestion des actions contextuelles sur les champs de saisie
 *
 * Centralise la logique des actions : reset, back, clear, password, copy, toggleEdit, etc.
 * Fournit un tableau d'actions à afficher, les handlers, les états, et les props à transmettre à l'input.
 *
 * @param {Object} options
 * @param {any} options.modelValue - Valeur courante (v-model)
 * @param {string} options.type - Type d'input (text, password, etc.)
 * @param {Array|string|Object} options.actions - Actions à activer (array, string, ou objet)
 * @param {boolean} [options.readonly] - Champ en lecture seule
 * @param {number} [options.delay] - Délai pour l'apparition de certaines actions (ms)
 * @param {boolean} [options.autofocus] - Autofocus sur le champ
 * @param {Object} [options.actionOptions] - Options personnalisées pour les actions
 * @returns {Object} API du composable
 */

// --- CONFIGURATION DES ACTIONS ---
const ACTIONS_CONFIGURATION = {
  reset: {
    compatibility: ['input', 'textarea', 'select', 'file', 'range', 'rating', 'checkbox', 'radio', 'toggle', 'filter'],
    options: {
      delay: 1000, // délai avant de pouvoir refaire l'action
      autofocus: false, // autofocus sur le champ
      destroy: false, // détruire l'action après l'utilisation
      notify: false, // notifier l'utilisateur après l'utilisation
      confirm: false, // demander confirmation avant l'action
      confirmMessage: 'Êtes-vous sûr de vouloir réinitialiser ce champ ?',
    },
    icon: 'fa-solid fa-arrow-rotate-left',
    size: 'auto', // dépend de l'input
    color: "neutral",
    variant: "ghost",
    ariaLabel: 'Réinitialiser',
    tooltip: 'Revenir à la valeur initiale',
    actionKey: 'reset',
  },
  back: {
    compatibility: ['input', 'textarea', 'select', 'file', 'range', 'rating', 'checkbox', 'radio', 'toggle', 'filter'],
    options: {
      delay: 500, // délai avant de pouvoir refaire l'action
      autofocus: false, // autofocus sur le champ
      destroy: false, // détruire l'action après l'utilisation
      notify: false, // notifier l'utilisateur après l'utilisation
      confirm: false, // demander confirmation avant l'action
      confirmMessage: 'Êtes-vous sûr de vouloir annuler la dernière modification ?',
    },
    icon: 'fa-solid fa-rotate-left',
    size: 'auto', // dépend de l'input
    color: "neutral",
    variant: "ghost",
    ariaLabel: 'Annuler la dernière modification',
    tooltip: 'Annuler la dernière modification',
    actionKey: 'back',
  },
  clear: {
    compatibility: ['input', 'textarea', 'select', 'file', 'range', 'rating'],
    options: {
      delay: 1000, // délai avant de pouvoir refaire l'action
      autofocus: false, // autofocus sur le champ
      destroy: false, // détruire l'action après l'utilisation
      notify: false, // notifier l'utilisateur après l'utilisation
      confirm: false, // demander confirmation avant l'action
      confirmMessage: 'Êtes-vous sûr de vouloir vider ce champ ?',
    },
    icon: 'fa-solid fa-xmark',
    size: 'auto', // dépend de l'input
    color: "neutral",
    variant: "ghost",
    ariaLabel: 'Vider le champ',
    tooltip: 'Vider le champ',
    actionKey: 'clear',
  },
  copy: {
    compatibility: ['input', 'textarea', 'select', 'file', 'range', 'rating'],
    options: {
      delay: 1000, // délai avant de pouvoir refaire l'action
      autofocus: false, // autofocus sur le champ
      destroy: false, // détruire l'action après l'utilisation
      notify: {
        message: 'Le contenu du champ a été copié dans le presse-papiers',
        type: 'success',
        icon: 'fa-solid fa-copy',
        duration: 2000,
      }, // notifier l'utilisateur après l'utilisation
      confirm: false, // demander confirmation avant l'action
      confirmMessage: '',
    },
    icon: 'fa-solid fa-copy',
    size: 'auto', // dépend de l'input
    color: "neutral",
    variant: "ghost",
    ariaLabel: 'Copier le contenu',
    tooltip: 'Copier le contenu',
    actionKey: 'copy',
  },
  password: {
    compatibility: ['password'],
    options: {
      delay: 100, // délai avant de pouvoir refaire l'action
      autofocus: false, // autofocus sur le champ
      destroy: false, // détruire l'action après l'utilisation
      notify: false, // notifier l'utilisateur après l'utilisation
      confirm: false, // demander confirmation avant l'action
      confirmMessage: '',
    },
    icon: 'fa-solid fa-eye', // sera dynamique
    size: 'auto', // dépend de l'input
    color: "neutral",
    variant: "ghost",
    ariaLabel: 'Afficher le mot de passe', // sera dynamique
    tooltip: 'Afficher le mot de passe', // sera dynamique
    actionKey: 'password',
  },
  edit: {
    compatibility: ['input', 'textarea', 'select', 'file', 'range', 'rating', 'checkbox', 'radio', 'toggle', 'filter'],
    options: {
      delay: 100, // délai avant de pouvoir refaire l'action
      autofocus: true, // autofocus sur le champ
      destroy: false, // détruire l'action après l'utilisation
      notify: false, // notifier l'utilisateur après l'utilisation
      confirm: false, // demander confirmation avant l'action
      confirmMessage: '',
    },
    icon: 'fa-solid fa-pen', // sera dynamique
    size: 'auto', // dépend de l'input
    color: "success",
    variant: "ghost",
    ariaLabel: 'Passer en édition', // sera dynamique
    tooltip: 'Passer en édition', // sera dynamique
    actionKey: 'edit',
  },
  lock: {
    compatibility: ['input', 'textarea', 'select', 'file', 'range', 'rating', 'checkbox', 'radio', 'toggle', 'filter'],
    options: {
      delay: 100, // délai avant de pouvoir refaire l'action
      autofocus: true, // autofocus sur le champ
      destroy: false, // détruire l'action après l'utilisation
      notify: false, // notifier l'utilisateur après l'utilisation
      confirm: false, // demander confirmation avant l'action
      confirmMessage: '',
    },
    icon: 'fa-solid fa-unlock', // sera dynamique
    size: 'auto', // dépend de l'input
    color: "neutral",
    variant: "ghost",
    ariaLabel: 'Activer le champ', // sera dynamique
    tooltip: 'Activer le champ', // sera dynamique
    actionKey: 'lock',
  },
};

export default function useInputActions({
  modelValue,
  type = 'text',
  actions = [],
  readonly = false,
  disabled = false,
  delay = 500,
  autofocus = false,
  actionOptions = {},
  emit = null, // Ajout du paramètre emit
} = {}) {
  // --- ÉTATS RÉACTIFS ---
  const initialValue = ref(modelValue);
  const currentValue = ref(modelValue);
  const previousValue = ref(modelValue);
  const isReadonly = ref(readonly);
  const isDisabled = ref(disabled);
  const inputRef = ref(null);
  const showPassword = ref(false);
  
  // --- ÉTATS D'AFFICHAGE AVEC DÉBOUNCE ---
  const showReset = ref(false);
  const showBack = ref(false);
  
  // --- TIMERS ---
  let resetTimeout = null;
  let backTimeout = null;

  // --- COMPUTED OPTIMISÉS ---
  const isModified = computed(() => currentValue.value !== initialValue.value);
  const hasPreviousValue = computed(() => previousValue.value !== initialValue.value);

  // --- SYNC AVEC v-model ---
  watch(
    () => modelValue,
    (newVal) => {
      if (newVal !== currentValue.value) {
        currentValue.value = newVal;
      }
    },
    { immediate: true }
  );

  // --- ÉMISSION DES CHANGEMENTS (NOUVEAU) ---
  watch(currentValue, (newVal, oldVal) => {
    if (oldVal !== newVal && emit && typeof emit === 'function') {
      emit('update:modelValue', newVal);
    }
  });

  // --- GESTION DE L'HISTORIQUE ---
  watch(currentValue, (newVal, oldVal) => {
    if (oldVal !== newVal && oldVal !== undefined) {
      previousValue.value = oldVal;
    }
  });

  // --- DÉBOUNCE POUR RESET/BACK ---
  watch(isModified, (val) => {
    if (resetTimeout) clearTimeout(resetTimeout);
    
    if (val) {
      resetTimeout = setTimeout(() => {
        showReset.value = true;
      }, delay);
    } else {
      showReset.value = false;
    }
  });

  watch(hasPreviousValue, (val) => {
    if (backTimeout) clearTimeout(backTimeout);
    
    if (val) {
      backTimeout = setTimeout(() => {
        showBack.value = true;
      }, delay);
    } else {
      showBack.value = false;
    }
  });

  // --- NETTOYAGE ---
  onUnmounted(() => {
    if (resetTimeout) clearTimeout(resetTimeout);
    if (backTimeout) clearTimeout(backTimeout);
  });

  // --- PARSING DES ACTIONS ---
  function parseActions(actions) {
    if (Array.isArray(actions)) {
      return actions.reduce((acc, action) => {
        if (typeof action === 'string') {
          acc[action] = {};
        } else if (typeof action === 'object') {
          const { key, ...options } = action;
          acc[key] = options;
        }
        return acc;
      }, {});
    } else if (typeof actions === 'object') {
      return actions;
    } else if (typeof actions === 'string') {
      return { [actions]: {} };
    }
    return {};
  }

  const actionsConfig = computed(() => parseActions(actions));

  // --- FONCTIONS UTILITAIRES ---
  function focus() {
    if (inputRef.value) inputRef.value.focus()
  }

  // --- MAPPING DES ACTIONS ---
  const actionHandlers = {
    reset: () => {
      if (emit && typeof emit === 'function') {
        emit('action:reset', initialValue.value)
      }
      currentValue.value = initialValue.value
      showReset.value = false
    },
    back: () => {
      if (emit && typeof emit === 'function') {
        emit('action:back', previousValue.value)
      }
      currentValue.value = previousValue.value
      showBack.value = false
    },
    clear: () => {
      if (emit && typeof emit === 'function') {
        emit('action:clear')
      }
      currentValue.value = ''
    },
    password: () => {
      showPassword.value = !showPassword.value
      if (emit && typeof emit === 'function') {
        emit('action:togglePassword', showPassword.value)
      }
    },
    copy: async () => {
      try {
        await navigator.clipboard.writeText(currentValue.value)
        if (emit && typeof emit === 'function') {
          emit('action:copy', currentValue.value)
        }
      } catch (err) {
        console.error('Erreur lors de la copie:', err)
      }
    },
    edit: () => {
      isReadonly.value = !isReadonly.value
      if (emit && typeof emit === 'function') {
        emit('action:toggleEdit', isReadonly.value)
      }
    },
    lock: () => {
      isDisabled.value = !isDisabled.value
      if (emit && typeof emit === 'function') {
        emit('action:toggleDisabled', isDisabled.value)
      }
    }
  }

  // --- VÉRIFICATION DE COMPATIBILITÉ ---
  function isActionCompatible(actionKey, inputType) {
    const action = ACTIONS_CONFIGURATION[actionKey];
    if (!action) return false;
    
    return action.compatibility.includes(inputType.toLowerCase());
  }

  // --- FUSION DES OPTIONS ---
  function mergeActionOptions(actionKey, userOptions = {}) {
    const defaultOptions = ACTIONS_CONFIGURATION[actionKey]?.options || {};
    const globalOptions = actionOptions[actionKey] || {};
    
    return {
      ...defaultOptions,
      ...globalOptions,
      ...userOptions,
    };
  }

  // --- LOGIQUE D'AFFICHAGE OPTIMISÉE ---
  const actionConditions = computed(() => ({
    reset: actionsConfig.value.reset && isModified.value && showReset.value,
    back: actionsConfig.value.back && hasPreviousValue.value && showBack.value,
    clear: actionsConfig.value.clear && !!currentValue.value,
    copy: actionsConfig.value.copy && !!currentValue.value,
    password: actionsConfig.value.password && type === 'password',
    edit: actionsConfig.value.edit,
    lock: actionsConfig.value.lock,
  }));

  // --- GÉNÉRATION DES ACTIONS DYNAMIQUES ---
  function getDynamicActionProps(actionKey) {
    const action = ACTIONS_CONFIGURATION[actionKey];
    if (!action) return null;

    // Propriétés dynamiques selon l'état
    const dynamicProps = {
      password: {
        icon: showPassword.value ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye',
        ariaLabel: showPassword.value ? 'Masquer le mot de passe' : 'Afficher le mot de passe',
        tooltip: showPassword.value ? 'Masquer le mot de passe' : 'Afficher le mot de passe',
      },
      edit: {
        icon: isReadonly.value ? 'fa-solid fa-pen' : 'fa-solid fa-eye',
        ariaLabel: isReadonly.value ? 'Passer en édition' : 'Passer en lecture seule',
        tooltip: isReadonly.value ? 'Passer en édition' : 'Passer en lecture seule',
      },
      lock: {
        icon: isDisabled.value ? 'fa-solid fa-lock' : 'fa-solid fa-unlock',
        ariaLabel: isDisabled.value ? 'Activer le champ' : 'Désactiver le champ',
        tooltip: isDisabled.value ? 'Activer le champ' : 'Désactiver le champ',
      },
    };

    return {
      ...action,
      ...(dynamicProps[actionKey] || {}),
    };
  }

  // --- TABLEAU D'ACTIONS À AFFICHER ---
  const actionsToDisplay = computed(() => {
    const conditions = actionConditions.value;
    const actions = [];

    Object.entries(actionsConfig.value).forEach(([actionKey, userOptions]) => {
      if (conditions[actionKey] && isActionCompatible(actionKey, type)) {
        const actionProps = getDynamicActionProps(actionKey);
        const mergedOptions = mergeActionOptions(actionKey, userOptions);
        
        const action = {
          key: actionKey,
          icon: actionProps.icon,
          ariaLabel: actionProps.ariaLabel,
          tooltip: actionProps.tooltip,
          color: userOptions.color || actionProps.color,
          variant: userOptions.variant || actionProps.variant,
          size: userOptions.size || actionProps.size,
          visible: true,
          disabled: false,
          onClick: actionHandlers[actionKey],
          options: mergedOptions,
        };
        
        actions.push(action);
      }
    });

    return actions;
  });

  // --- PROPS À TRANSMETTRE À L'INPUT ---
  const inputProps = computed(() => {
    let effectiveType = type;
    if (type === 'password' && showPassword.value) {
      effectiveType = 'text';
    }
    
    return {
      type: effectiveType,
      readonly: isReadonly.value,
      disabled: isDisabled.value,
      value: currentValue.value,
      ref: inputRef,
      autofocus,
    };
  });

  // --- GESTIONNAIRE D'ÉVÉNEMENT INPUT ---
  const handleInput = (event) => {
    const newValue = event.target.value;
    currentValue.value = newValue;
  };

  // --- API EXPOSÉE ---
  return {
    // États réactifs
    currentValue,
    initialValue,
    previousValue,
    isModified,
    isReadonly,
    isDisabled,
    inputRef,
    showPassword,
    
    // Actions à afficher
    actionsToDisplay,
    
    // Props pour l'input
    inputProps,
    
    // Gestionnaire d'événement
    handleInput,
    
    // Méthodes
    focus,
    
    // Utilitaires
    isActionCompatible,
    mergeActionOptions,
    getDynamicActionProps,
  };
} 