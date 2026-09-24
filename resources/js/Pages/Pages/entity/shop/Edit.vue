<script setup>
/**
 * Shop Edit Page
 *
 * @description
 * Édition dense d’un hôtel de vente : grille fieldSections + inventaire en collapses.
 */
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Shop } from '@/Models/Entity/Shop';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Collapse from '@/Pages/Atoms/data-display/Collapse.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityListBackButton from '@/Pages/Atoms/action/EntityListBackButton.vue';
import {
    buildShopFormFieldsConfig,
    SHOP_FORM_FIELD_SECTIONS_EDIT,
} from '@/Entities/shop/shop-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    shop: {
        type: Object,
        required: true,
    },
    availableItems: {
        type: Array,
        default: () => [],
    },
    availableConsumables: {
        type: Array,
        default: () => [],
    },
    availableResources: {
        type: Array,
        default: () => [],
    },
});

const fieldsConfig = computed(() =>
    buildShopFormFieldsConfig({ includeReadonlyMeta: true }),
);
const fieldSections = SHOP_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';

const shop = computed(() => {
    const shopData = props.shop || page.props.shop || {};
    return new Shop(shopData);
});

setPageTitle(`Modifier la hotel de vente : ${shop.value.name || 'Nouvelle hotel de vente'}`);

function goToShow() {
    const id = shop.value?.id;
    if (!id) return;
    router.visit(route('entities.shops.show', { shop: id }));
}
</script>

<template>
    <Head :title="`Modifier la hotel de vente : ${shop?.name || 'Nouvelle hotel de vente'}`" />

    <Container class="space-y-4">
        <div
            class="sticky top-0 z-20 px-3 py-1 bg-glass-3xl backdrop-blur-md border-glass-b-md sm:px-4"
            style="--bg-color: var(--color-base-100)"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-md font-bold text-base-content sm:text-lg">
                        {{ shop.name || 'Hôtel de vente sans nom' }}
                    </h1>
                    <p class="text-xs text-base-content/60">
                        Édition · ID {{ shop.id }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <EntityListBackButton route-name="entities.shops.index" />
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
            :entity="shop"
            entity-type="shop"
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
            <template #title>Inventaire (objets, conso, ressources)</template>
            <template #content>
                <div class="space-y-4">
                    <EntityRelationsManager
                        :relations="shop.items || []"
                        :available-items="availableItems"
                        :entity-id="shop.id"
                        entity-type="shops"
                        relation-type="items"
                        relation-name="Objets vendus dans la hotel de vente"
                        :config="{
                            displayFields: ['name', 'description', 'level'],
                            searchFields: ['name', 'description'],
                            pivotFields: ['quantity', 'price', 'comment'],
                            itemLabel: 'objet',
                            itemLabelPlural: 'objets'
                        }"
                    />

                    <EntityRelationsManager
                        :relations="shop.consumables || []"
                        :available-items="availableConsumables"
                        :entity-id="shop.id"
                        entity-type="shops"
                        relation-type="consumables"
                        relation-name="Consommables vendus dans la hotel de vente"
                        :config="{
                            displayFields: ['name', 'description', 'level'],
                            searchFields: ['name', 'description'],
                            pivotFields: ['quantity', 'price', 'comment'],
                            itemLabel: 'consommable',
                            itemLabelPlural: 'consommables'
                        }"
                    />

                    <EntityRelationsManager
                        :relations="shop.resources || []"
                        :available-items="availableResources"
                        :entity-id="shop.id"
                        entity-type="shops"
                        relation-type="resources"
                        relation-name="Ressources vendues dans la hotel de vente"
                        :config="{
                            displayFields: ['name', 'description', 'level'],
                            searchFields: ['name', 'description'],
                            pivotFields: ['quantity', 'price', 'comment'],
                            itemLabel: 'ressource',
                            itemLabelPlural: 'ressources'
                        }"
                    />
                </div>
            </template>
        </Collapse>
    </Container>
</template>
