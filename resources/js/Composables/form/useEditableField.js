import { ref, computed, watch } from "vue";
import axios from "axios";

/* Utilisation du composable :

// Exemple d'utilisation du composable :
// const name = useEditableField(user.value.name, {
//     field: 'name',
//     route: route('user.update'),
//     form: form,
//     onSuccess: () => success('Mise à jour réussie'),
//     onError: () => error('Erreur lors de la mise à jour')
// });

*/

/**
 * useEditableField — Composable d'édition réactive pour champs de formulaire
 *
 * Fournit :
 *   - value : valeur courante (ref)
 *   - isModified : booléen (ref) si la valeur a changé
 *   - isPending : booléen (ref) si une MAJ est en attente (debounce)
 *   - reset() : remet la valeur à l'initiale
 *   - update(val) : force la MAJ (optionnel)
 *   - onInput(e) : handler à brancher sur l'input (gère debounce et update)
 *   - onBlur() : handler à brancher sur blur (flush le debounce)
 *   - focus() : méthode pour focus l'input (optionnel)
 *   - lastSavedValue : valeur sauvegardée (ref)
 *
 * Options :
 *   - field : objet field externe (pour compatibilité)
 *   - debounce : délai en ms (défaut 500)
 *   - onUpdate : callback appelé à chaque update effectif
 *
 * @param {any} initialValue - Valeur initiale
 * @param {Object} options - { field, debounce, onUpdate }
 * @returns {Object} API du champ éditable
 */
export default function useEditableField(initialValue, options = {}) {
    const {
        field = null,
        route = null,
        onSuccess = null,
        onError = null,
        debounce = 500,
        onUpdate = null,
    } = options;

    // Si field externe fourni, on délègue tout
    if (field) {
        return field;
    }

    const initialFieldValue = ref(initialValue);
    const currentValue = ref(initialValue);
    const lastSavedValue = ref(initialValue);
    const isModified = computed(
        () => currentValue.value !== initialFieldValue.value,
    );
    const isPending = ref(false);
    let debounceTimeout = null;
    const inputRef = ref(null);

    const reset = () => {
        currentValue.value = initialFieldValue.value;
    };

    const update = async (newValue) => {
        if (!route || isPending.value || newValue === lastSavedValue.value)
            return;

        isPending.value = true;
        try {
            const response = await axios.patch(
                route,
                { [field]: newValue },
                {
                    headers: {
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                },
            );

            if (response.data.success) {
                lastSavedValue.value = newValue;
                if (typeof onSuccess === "function") {
                    onSuccess(response.data);
                }
            }
        } catch (error) {
            currentValue.value = lastSavedValue.value;
            if (typeof onError === "function") {
                onError(error.response?.data);
            }
        } finally {
            isPending.value = false;
        }
    };

    const debouncedUpdate = (newValue) => {
        if (debounceTimeout) {
            clearTimeout(debounceTimeout);
        }
        currentValue.value = newValue;
        debounceTimeout = setTimeout(() => {
            update(newValue);
        }, debounce);
    };

    const onInput = (e) => {
        debouncedUpdate(e.target.value);
    };

    const onBlur = () => {
        if (debounceTimeout) {
            clearTimeout(debounceTimeout);
            debounceTimeout = null;
        }
        update(currentValue.value);
    };

    const focus = () => {
        if (inputRef.value) inputRef.value.focus();
    };

    // Pour compatibilité v-model
    watch(
        () => initialValue,
        (v) => {
            initialFieldValue.value = v;
            currentValue.value = v;
            lastSavedValue.value = v;
        },
    );

    return {
        value: currentValue,
        isModified,
        isPending,
        reset,
        update,
        lastSavedValue: computed(() => lastSavedValue.value),
        onInput,
        onBlur,
        focus,
        inputRef,
    };
}
