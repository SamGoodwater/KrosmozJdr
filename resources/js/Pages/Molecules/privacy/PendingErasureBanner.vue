<script setup>
/**
 * Bannière affichée quand l'utilisateur a une demande de suppression en cours.
 * Propose d'annuler la demande pour récupérer le compte (tant qu'un admin ne l'a pas supprimé définitivement).
 */
import { computed } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import Btn from '@/Pages/Atoms/action/Btn.vue';

const page = usePage();
const pendingErasure = computed(() => page.props.pending_erasure ?? null);

const form = useForm({});

function formatExpiresAt(iso) {
    if (!iso) return '';
    const d = new Date(iso);
    return d.toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

function recoverAccount() {
    form.post(route('user.privacy.delete.cancel'), {
        preserveScroll: true,
    });
}
</script>

<template>
    <aside
        v-if="pendingErasure"
        class="sticky top-2 z-20 mx-auto mb-4 max-w-4xl rounded-lg border border-warning/50 bg-warning/10 p-4 backdrop-blur"
        aria-live="polite"
        role="alert"
    >
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-warning-content">
                    Tu as demandé la suppression de ton compte.
                </p>
                <p class="mt-1 text-xs text-warning-content/80">
                    La suppression aura lieu le {{ formatExpiresAt(pendingErasure?.expires_at) }}. Tant qu'un administrateur n'a pas effectué la suppression définitive, tu peux annuler ta demande.
                </p>
            </div>
            <Btn
                color="primary"
                size="sm"
                :disabled="form.processing"
                @click="recoverAccount"
            >
                <i class="fa-solid fa-undo mr-1" aria-hidden="true" />
                {{ form.processing ? 'Annulation...' : 'Récupérer mon compte' }}
            </Btn>
        </div>
    </aside>
</template>
