<script setup>
/**
 * Atelier DofusDB — import de masse (admin, confirmation mot de passe).
 */
import { ref } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { WORKSHOP_MODES, workshopModeFromUrl } from "@/utils/scrapping/workshopMode";
import AdminArea from "@/Pages/Layouts/AdminArea.vue";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import PageHeader from "@/Pages/Molecules/layout/PageHeader.vue";
import ConfirmPasswordModal from "@/Pages/Molecules/action/ConfirmPasswordModal.vue";
import ScrappingDashboard from "@/Pages/Organismes/scrapping/ScrappingDashboard.vue";
import { ACTION } from "@/Utils/atomic-design/actionLabels";

defineOptions({ layout: AdminArea });

defineProps({
    entityChoices: { type: Array, default: () => [] },
    catalogTypeChoices: { type: Array, default: () => [] },
    consoleJob: { type: Object, default: null },
});

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
        <PageHeader
            title="Import DofusDB"
            subtitle="Recherche, import et mise à jour de masse. La maj unitaire se fait depuis chaque fiche."
        >
            <template v-if="unlocked" #actions>
                <Route :href="route('admin.scrapping-mappings.index')" class="no-underline">
                    <Btn variant="ghost" size="sm" type="button">Mapping champs</Btn>
                </Route>
                <Route :href="route('admin.dofusdb-effect-mappings.index')" class="no-underline">
                    <Btn variant="ghost" size="sm" type="button">Mapping effets</Btn>
                </Route>
            </template>
            <template v-if="!unlocked" #primary>
                <Btn color="primary" size="sm" type="button" @click="showConfirmModal = true">
                    <i :class="ACTION.confirm.icon" class="mr-1.5" aria-hidden="true"></i>
                    {{ ACTION.confirm.label }}
                </Btn>
            </template>
            <template v-if="unlocked" #tabs>
                <div class="flex flex-wrap gap-2" role="tablist" aria-label="Mode d’import">
                    <Btn
                        v-for="mode in WORKSHOP_MODES"
                        :key="mode.value"
                        size="sm"
                        role="tab"
                        :aria-selected="workshopMode === mode.value"
                        :color="workshopMode === mode.value ? 'primary' : undefined"
                        :variant="workshopMode === mode.value ? undefined : 'outline'"
                        @click="workshopMode = mode.value"
                    >
                        {{ mode.label }}
                    </Btn>
                </div>
            </template>
        </PageHeader>

        <div
            v-if="!unlocked"
            class="rounded-box border border-warning/40 bg-warning/10 p-6 text-center"
        >
            <p class="text-warning-content">
                Cette section est réservée aux administrateurs. Confirme ton mot de passe (bouton « Confirmer » en
                haut) pour accéder à l’atelier.
            </p>
        </div>

        <ScrappingDashboard v-else :workshop-mode="workshopMode" />

        <ConfirmPasswordModal
            v-model:open="showConfirmModal"
            title="Accéder à l’atelier DofusDB"
            message="Cette section permet d’importer des données depuis DofusDB en masse. Entre ton mot de passe pour confirmer ton identité."
            :confirm-label="ACTION.confirm.label"
            @confirmed="onPasswordConfirmed"
        />
    </Container>
</template>
