<script setup>
/**
 * Page Scrapping (wrapper)
 *
 * @description
 * La logique de scrapping de la page `/scrapping` est centralisée dans `ScrappingDashboard`.
 * Accès réservé aux admins, protégé par confirmation du mot de passe (ConfirmPasswordModal).
 */
import { ref } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";

import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import ConfirmPasswordModal from "@/Pages/Molecules/action/ConfirmPasswordModal.vue";
import ScrappingDashboard from "@/Pages/Organismes/scrapping/ScrappingDashboard.vue";

const { setPageTitle } = usePageTitle();
onMounted(() => setPageTitle("Gestion du Scrapping"));

const page = usePage();
const scrappingUnlocked = ref(Boolean(page.props.auth?.password_recently_confirmed));
const showConfirmModal = ref(false);

function onPasswordConfirmed() {
    scrappingUnlocked.value = true;
}

// Après une grosse refonte, HMR peut laisser un module incohérent en mémoire.
if (import.meta?.hot) {
    import.meta.hot.accept(() => {
        import.meta.hot.invalidate();
    });
}
</script>

<template>
    <Head title="Gestion du Scrapping" />

    <Container class="space-y-6 pb-8">
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Gestion du Scrapping</h1>
                <p class="text-primary-200 mt-2">Importez des données depuis DofusDB vers KrosmozJDR</p>
            </div>
            <div v-if="scrappingUnlocked" class="flex flex-wrap gap-2">
                <a
                    :href="route('admin.scrapping-mappings.index')"
                    class="btn btn-ghost btn-sm"
                >
                    Mapping entités
                </a>
                <a
                    :href="route('admin.dofusdb-effect-mappings.index')"
                    class="btn btn-ghost btn-sm"
                >
                    Mapping effets DofusDB
                </a>
            </div>
        </div>

        <!-- Porte d'accès : confirmation mot de passe requise -->
        <div
            v-if="!scrappingUnlocked"
            class="rounded-lg border border-warning/40 bg-warning/10 p-6 text-center space-y-4"
        >
            <p class="text-warning-content">
                Cette section est réservée aux administrateurs et protégée. Confirme ton mot de passe pour accéder au scrapping.
            </p>
            <Btn color="primary" @click="showConfirmModal = true">
                Accéder au scrapping
            </Btn>
        </div>

        <ScrappingDashboard v-else />

        <ConfirmPasswordModal
            v-model:open="showConfirmModal"
            title="Accéder au scrapping"
            message="Cette section permet d'importer des données depuis DofusDB. Entre ton mot de passe pour confirmer ton identité."
            confirm-label="Accéder"
            @confirmed="onPasswordConfirmed"
        />
    </Container>
</template>
