<script setup>
/**
 * ConfirmPasswordModal — Modale de confirmation par mot de passe
 *
 * @description
 * Modale réutilisable pour protéger les actions sensibles (export RGPD, suppression de compte,
 * téléchargement de données, actions admin, etc.). Valide le mot de passe via l'API avant
 * d'émettre l'événement confirmed avec le mot de passe (pour les actions qui en ont besoin).
 *
 * @example
 * // Action protégée sans besoin du mot de passe (session suffit)
 * <ConfirmPasswordModal v-model:open="showModal" @confirmed="doExport" />
 *
 * // Action protégée qui nécessite le mot de passe (ex: suppression)
 * <ConfirmPasswordModal v-model:open="showModal" @confirmed="(pwd) => submitDelete(pwd)" />
 */
import { ref, watch, nextTick } from 'vue';
import axios from 'axios';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';

const props = defineProps({
    /** Titre de la modale */
    title: { type: String, default: 'Confirmer ton mot de passe' },
    /** Message explicatif */
    message: {
        type: String,
        default: 'Cette action est sensible. Entre ton mot de passe pour continuer.',
    },
    /** Label du bouton de confirmation */
    confirmLabel: { type: String, default: 'Confirmer' },
});

const emit = defineEmits(['update:open', 'confirmed', 'cancel']);

const open = defineModel('open', { type: Boolean, default: false });
const password = ref('');
const error = ref(null);
const loading = ref(false);
const passwordInputRef = ref(null);

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

async function submit() {
    error.value = null;
    if (!password.value.trim()) {
        error.value = 'Le mot de passe est requis.';
        return;
    }
    loading.value = true;
    try {
        const { data } = await axios.post(route('user.password.confirm'), { password: password.value }, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        if (data?.confirmed) {
            const pwd = password.value;
            reset();
            open.value = false;
            emit('confirmed', pwd);
        } else {
            error.value = 'Une erreur est survenue.';
        }
    } catch (err) {
        const msg = err?.response?.data?.errors?.password
            ?? err?.response?.data?.message
            ?? 'Le mot de passe est incorrect.';
        error.value = Array.isArray(msg) ? msg[0] : msg;
    } finally {
        loading.value = false;
    }
}

function reset() {
    password.value = '';
    error.value = null;
}

function close() {
    reset();
    open.value = false;
    emit('cancel');
}

watch(open, (isOpen) => {
    if (isOpen) {
        reset();
        nextTick(() => passwordInputRef.value?.$el?.querySelector('input')?.focus());
    }
});
</script>

<template>
    <Modal
        :open="open"
        size="sm"
        placement="middle-center"
        :close-on-esc="!loading"
        :close-on-outside-click="!loading"
        @close="close"
    >
        <Container class="p-6 space-y-4">
            <h2 class="text-lg font-semibold">
                {{ title }}
            </h2>
            <p class="text-sm text-content-500">
                {{ message }}
            </p>
            <InputField
                ref="passwordInputRef"
                v-model="password"
                type="password"
                autocomplete="current-password"
                label="Mot de passe"
                :validation="error ? { state: 'error', message: error } : null"
                placeholder="••••••••"
                @keyup.enter="submit"
            />
            <div class="flex justify-end gap-2">
                <Btn variant="ghost" :disabled="loading" @click="close">
                    Annuler
                </Btn>
                <Btn color="primary" :disabled="loading" @click="submit">
                    {{ loading ? 'Vérification…' : confirmLabel }}
                </Btn>
            </div>
        </Container>
    </Modal>
</template>
