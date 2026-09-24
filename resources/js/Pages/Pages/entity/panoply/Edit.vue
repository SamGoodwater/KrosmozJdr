<script setup>
/**
 * Panoply Edit Page
 *
 * @description
 * Édition dense d’une panoplie : identité dense + pièces / bonus en collapses.
 *
 * @props {Object} panoply - Données de la panoplie à éditer
 * @props {Array} bonusCharacteristics - Caractéristiques groupe object pour les bonus
 */
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Panoply } from '@/Models/Entity/Panoply';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import PanoplyBonusEditor from '@/Pages/Organismes/entity/PanoplyBonusEditor.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Collapse from '@/Pages/Atoms/data-display/Collapse.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityListBackButton from '@/Pages/Atoms/action/EntityListBackButton.vue';
import LevelBadge from '@/Pages/Molecules/data-display/LevelBadge.vue';
import {
    buildPanoplyFormFieldsConfig,
    PANOPLY_FORM_FIELD_SECTIONS_EDIT,
} from '@/Entities/panoply/panoply-form-config';

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

const fieldsConfig = computed(() =>
    buildPanoplyFormFieldsConfig({ includeReadonlyMeta: true }),
);
const fieldSections = PANOPLY_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';

const panoply = computed(() => {
    const panoplyData = props.panoply || page.props.panoply || {};
    return new Panoply(panoplyData);
});

const linkedItems = computed(() => {
    const raw = panoply.value?.items;
    return Array.isArray(raw) ? raw : [];
});

const levelValue = computed(() => {
    const lv = panoply.value?.level;
    if (lv === null || lv === undefined || lv === '') {
        return null;
    }
    const n = Number(lv);
    return Number.isFinite(n) ? n : null;
});

setPageTitle(`Modifier la panoplie : ${panoply.value.name || 'Nouvelle panoplie'}`);

function goToShow() {
    const id = panoply.value?.id;
    if (!id) return;
    router.visit(route('entities.panoplies.show', { panoply: id }));
}
</script>

<template>
    <Head :title="`Modifier la panoplie : ${panoply?.name || 'Nouvelle panoplie'}`" />

    <Container class="space-y-4">
        <div
            class="sticky top-0 z-20 px-3 py-1 bg-glass-3xl backdrop-blur-md border-glass-b-md sm:px-4"
            style="--bg-color: var(--color-base-100)"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="truncate text-md font-bold text-base-content sm:text-lg">
                            {{ panoply.name || 'Panoplie sans nom' }}
                        </h1>
                        <LevelBadge
                            v-if="levelValue != null"
                            :level="levelValue"
                            size="xs"
                            class="shrink-0"
                        />
                    </div>
                    <p class="text-xs text-base-content/60">
                        Édition · ID {{ panoply.id }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <EntityListBackButton route-name="entities.panoplies.index" />
                    <Btn
                        color="neutral"
                        variant="outline"
                        size="xs"
                        type="button"
                        class="gap-1.5"
                        @click="goToShow"
                    >
                        <i class="fa-solid fa-book-open" aria-hidden="true"></i>
                        Fiche
                    </Btn>
                </div>
            </div>
        </div>

        <EntityEditForm
            :entity="panoply"
            entity-type="panoply"
            :fields-config="fieldsConfig"
            :is-updating="true"
            :field-sections="fieldSections"
            :show-state-toolbar="true"
            :show-access-levels-in-footer="false"
            layout-profile="dense"
            :fixed-footer-actions="true"
            :fixed-footer-inset-class="fixedFooterInsetClass"
            :compact-access-levels="true"
        />

        <Collapse arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Équipements de la panoplie</template>
            <template #content>
                <div class="space-y-4">
                    <p class="text-xs text-base-content/60">
                        Recherchez un équipement dans le catalogue, puis retirez-le si besoin.
                        Le niveau du set est celui de la pièce la plus élevée.
                    </p>
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
                </div>
            </template>
        </Collapse>

        <Collapse arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Bonus de set</template>
            <template #content>
                <div class="space-y-4">
                    <p class="text-xs text-base-content/60">
                        Même principe que les effets d’équipement : une caractéristique et une valeur,
                        groupées par nombre de pièces équipées (2p, 3p, …).
                    </p>
                    <PanoplyBonusEditor
                        v-if="panoply.id"
                        :panoply-id="panoply.id"
                        :bonus="panoply.bonus"
                        :characteristics="bonusCharacteristics"
                    />
                </div>
            </template>
        </Collapse>
    </Container>
</template>
