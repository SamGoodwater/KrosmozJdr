import { ref, computed } from "vue";
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

export default function useEditableField(initialValue, options = {}) {
    const {
        field = null,
        route = null,
        onSuccess = null,
        onError = null,
    } = options;

    const initialFieldValue = ref(initialValue);
    const currentValue = ref(initialValue);
    const lastSavedValue = ref(initialValue);

    const isModified = computed(
        () => currentValue.value !== initialFieldValue.value,
    );
    const isPending = ref(false);

    const reset = () => {
        currentValue.value = initialFieldValue.value;
    };

    const update = async (newValue) => {
        if (
            !field ||
            !route ||
            isPending.value ||
            newValue === lastSavedValue.value
        )
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

    return {
        value: currentValue,
        isModified,
        isPending,
        reset,
        update,
        lastSavedValue: computed(() => lastSavedValue.value),
    };
}
