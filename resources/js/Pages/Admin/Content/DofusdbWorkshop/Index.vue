<script setup>
/**
 * Atelier DofusDB — import de masse (admin, confirmation mot de passe).
 */
import { ref, onMounted } from "vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { WORKSHOP_MODES, workshopModeFromUrl } from "@/utils/scrapping/workshopMode";
import AdminArea from "@/Pages/Layouts/AdminArea.vue";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import ConfirmPasswordModal from "@/Pages/Molecules/action/ConfirmPasswordModal.vue";
import ScrappingDashboard from "@/Pages/Organismes/scrapping/ScrappingDashboard.vue";

defineOptions({ layout: AdminArea });

defineProps({
    entityChoices: { type: Array, default: () => [] },
    catalogTypeChoices: { type: Array, default: () => [] },
    consoleJob: { type: Object, default: null },
});

const { setPageTitle } = usePageTitle();
onMounted(() => setPageTitle("Import DofusDB"));

const page = usePage();
const unlocked = ref(Boolean(page.props.auth?.password_recently_confirmed));
const showConfirmModal = ref(false);
const workshopMode = ref(workshopModeFromUrl(page.url));

function onPasswordConfirmed() {
    unlocked.value = true;
}
</script>

<template>
    <Head title="Import DofusDB" />

    <Container class="space-y-6 pb-8">
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Import DofusDB</h1>
                <p class="text-primary-200 mt-2">
                    Recherche, import et mise à jour de masse. La maj unitaire se fait depuis chaque fiche.
                </p>
            </div>
            <div v-if="unlocked" class="flex flex-wrap gap-2">
                <Link :href="route('admin.scrapping-mappings.index')" class="btn btn-ghost btn-sm">
                    Mapping champs
                </Link>
                <Link :href="route('admin.dofusdb-effect-mappings.index')" class="btn btn-ghost btn-sm">
                    Mapping effets
                </Link>
            </div>
        </div>

        <div
            v-if="!unlocked"
            class="rounded-box border border-warning/40 bg-warning/10 p-6 text-center space-y-4"
        >
            <p class="text-warning-content">
                Cette section est réservée aux administrateurs. Confirme ton mot de passe pour accéder à l’atelier.
            </p>
            <Btn color="primary" @click="showConfirmModal = true">Accéder à l’atelier</Btn>
        </div>

        <template v-else>
            <div class="flex flex-wrap gap-2" role="tablist" aria-label="Mode d’import">
                <Btn
                    v-for="mode in WORKSHOP_MODES"
                    :key="mode.value"
                    size="sm"
                    :color="workshopMode === mode.value ? 'primary' : undefined"
                    :variant="workshopMode === mode.value ? undefined : 'outline'"
                    @click="workshopMode = mode.value"
                >
                    {{ mode.label }}
                </Btn>
            </div>

            <ScrappingDashboard :workshop-mode="workshopMode" />
        </template>

        <ConfirmPasswordModal
            v-model:open="showConfirmModal"
            title="Accéder à l’atelier DofusDB"
            message="Cette section permet d’importer des données depuis DofusDB en masse. Entre ton mot de passe pour confirmer ton identité."
            confirm-label="Accéder"
            @confirmed="onPasswordConfirmed"
        />
    </Container>
</template>
