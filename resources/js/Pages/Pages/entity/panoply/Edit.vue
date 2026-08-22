<script setup>
/**
 * Panoply Edit Page
 *
 * @description
 * Édition d’une panoplie : pièces et bonus de set d’abord, puis identité
 * et droits (lecture / écriture) en bas de page.
 *
 * @props {Object} panoply - Données de la panoplie à éditer
 * @props {Array} bonusCharacteristics - Caractéristiques groupe object pour les bonus
 */
import { computed } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Panoply } from "@/Models/Entity/Panoply";
import EntityEditForm from "@/Pages/Organismes/entity/EntityEditForm.vue";
import EntityRelationsManager from "@/Pages/Organismes/entity/EntityRelationsManager.vue";
import PanoplyBonusEditor from "@/Pages/Organismes/entity/PanoplyBonusEditor.vue";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import { getEntityStateOptions, getUserRoleOptions } from "@/Utils/Entity/SharedConstants";

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    panoply: {
        type: Object,
        required: true,
    },
    bonusCharacteristics: {
        type: Array,
        default: () => [],
    },
});

const fieldsConfig = {
    name: {
        type: "text",
        label: "Nom",
        required: true,
        showInCompact: true,
    },
    description: {
        type: "textarea",
        label: "Description",
        required: false,
        showInCompact: false,
    },
    state: {
        type: "select",
        label: "État",
        required: false,
        showInCompact: true,
        options: getEntityStateOptions(),
    },
    read_level: {
        type: "select",
        label: "Lecture (min.)",
        required: false,
        showInCompact: false,
        options: getUserRoleOptions(),
    },
    write_level: {
        type: "select",
        label: "Écriture (min.)",
        required: false,
        showInCompact: false,
        options: getUserRoleOptions(),
    },
};

const panoply = computed(() => {
    const panoplyData = props.panoply || page.props.panoply || {};
    return new Panoply(panoplyData);
});

const linkedItems = computed(() => {
    const raw = panoply.value?.items;
    return Array.isArray(raw) ? raw : [];
});

setPageTitle(`Modifier la panoplie : ${panoply.value.name || "Nouvelle panoplie"}`);
</script>

<template>
    <Head :title="`Modifier la panoplie : ${panoply?.name || 'Nouvelle panoplie'}`" />

    <Container class="space-y-6">
        <Route route="entities.panoplies.index">
            <Btn color="neutral" variant="ghost" size="sm" class="gap-2">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Retour à la liste
            </Btn>
        </Route>

        <section class="rounded-xl border border-base-300/70 bg-base-100/30 p-3 shadow-sm md:p-4">
            <div class="mb-2.5 border-b border-base-300/50 pb-2">
                <h2 class="text-base font-semibold tracking-tight text-base-content">
                    Équipements de la panoplie
                </h2>
                <p class="mt-0.5 text-xs leading-snug text-base-content/70 md:text-sm">
                    Recherchez un équipement dans le catalogue, puis retirez-le si besoin.
                </p>
            </div>
            <EntityRelationsManager
                :relations="linkedItems"
                :entity-id="panoply.id"
                entity-type="panoplies"
                relation-type="items"
                relation-name="Pièces de la panoplie"
                :show-title="false"
                :config="{
                    displayFields: ['name', 'description', 'level'],
                    searchFields: ['name', 'description'],
                    relatedEntityType: 'items',
                    searchApiEntityType: 'items',
                    itemLabel: 'équipement',
                    itemLabelPlural: 'équipements',
                }"
            />
        </section>

        <section class="rounded-xl border border-base-300/70 bg-base-100/30 p-3 shadow-sm md:p-4">
            <div class="mb-2.5 border-b border-base-300/50 pb-2">
                <h2 class="text-base font-semibold tracking-tight text-base-content">
                    Bonus de set
                </h2>
                <p class="mt-0.5 text-xs leading-snug text-base-content/70 md:text-sm">
                    Même principe que les effets d’équipement : une caractéristique et une valeur,
                    groupées par nombre de pièces équipées (2p, 3p, …).
                </p>
            </div>
            <PanoplyBonusEditor
                v-if="panoply.id"
                :panoply-id="panoply.id"
                :bonus="panoply.bonus"
                :characteristics="bonusCharacteristics"
            />
        </section>

        <EntityEditForm
            :entity="panoply"
            entity-type="panoply"
            :fields-config="fieldsConfig"
            :is-updating="true"
        />
    </Container>
</template>
