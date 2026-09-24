<script setup>
/**
 * Item Edit Page
 *
 * @description
 * Édition dense d’un équipement : grille fieldSections + toolbar sticky (Liste / Fiche).
 *
 * @props {Object} item - Données de l'item à éditer
 */
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Item } from '@/Models/Entity/Item';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import EffectUsagesManager from '@/Pages/Organismes/entity/EffectUsagesManager.vue';
import ObjectEffectsManager from '@/Pages/Organismes/entity/ObjectEffectsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Collapse from '@/Pages/Atoms/data-display/Collapse.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityListBackButton from '@/Pages/Atoms/action/EntityListBackButton.vue';
import ItemPriceEditSection from '@/Pages/Molecules/entity/item/ItemPriceEditSection.vue';
import {
    buildItemFormFieldsConfig,
    ITEM_FORM_FIELD_SECTIONS_EDIT,
} from '@/Entities/item/item-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    availableResources: {
        type: Array,
        default: () => [],
    },
    /** Types d’équipement (optionnel — sinon champ ID numérique). */
    itemTypes: {
        type: Array,
        default: () => [],
    },
    effectUsages: { type: Array, default: () => [] },
    availableEffects: { type: Array, default: () => [] },
    effectEntityType: { type: String, default: 'item' },
    objectEffects: { type: Array, default: () => [] },
    objectEffectCharacteristics: { type: Array, default: () => [] },
    objectEffectMonsters: { type: Array, default: () => [] },
});

const item = computed(() => {
    const itemData = props.item || page.props.item || {};
    return new Item(itemData);
});

const fieldsConfig = computed(() =>
    buildItemFormFieldsConfig({
        includeReadonlyMeta: true,
        itemTypes: props.itemTypes || [],
    }),
);

const fieldSections = ITEM_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';

setPageTitle(`Modifier l'item : ${item.value.name || 'Nouvel item'}`);

function goToShow() {
    const id = item.value?.id;
    if (!id) return;
    router.visit(route('entities.items.show', { item: id }));
}
</script>

<template>
    <Head :title="`Modifier l'item : ${item?.name || 'Nouvel item'}`" />

    <Container class="space-y-4">
        <div
            class="sticky top-0 z-20 px-3 py-1 bg-glass-3xl backdrop-blur-md border-glass-b-md sm:px-4"
            style="--bg-color: var(--color-base-100)"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-md font-bold text-base-content sm:text-lg">
                        {{ item.name || 'Équipement sans nom' }}
                    </h1>
                    <p class="text-xs text-base-content/60">
                        Édition · ID {{ item.id }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <EntityListBackButton route-name="entities.items.index" />
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
            :entity="item"
            entity-type="item"
            :fields-config="fieldsConfig"
            :is-updating="true"
            :hidden-field-keys="['dofus_version']"
            :field-sections="fieldSections"
            :show-state-toolbar="true"
            :show-access-levels-in-footer="false"
            layout-profile="dense"
            :fixed-footer-actions="true"
            :fixed-footer-inset-class="fixedFooterInsetClass"
            :compact-access-levels="true"
        />

        <ItemPriceEditSection
            v-if="item.id"
            :update-url="route('entities.items.update', { item: item.id })"
            :recalculate-url="route('entities.items.recalculatePrice', { item: item.id })"
            :price-calculated="item.priceCalculated"
            :price-custom="item.priceCustom"
            formula-hint="Bonus de caractéristiques + 150 kamas × niveau + 200 kamas × rareté."
        />

        <Collapse arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Effets &amp; usages</template>
            <template #content>
                <div class="space-y-4">
                    <EffectUsagesManager
                        :effect-usages="effectUsages"
                        :available-effects="availableEffects"
                        :entity-type="effectEntityType"
                        :entity-id="item.id"
                    />

                    <ObjectEffectsManager
                        :object-effects="objectEffects"
                        :object-effect-characteristics="objectEffectCharacteristics"
                        :object-effect-monsters="objectEffectMonsters"
                        :entity-type="effectEntityType"
                        :entity-id="item.id"
                    />
                </div>
            </template>
        </Collapse>

        <Collapse arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Recette de craft</template>
            <template #content>
                <EntityRelationsManager
                    :relations="item.resources || []"
                    :available-items="availableResources"
                    :entity-id="item.id"
                    entity-type="items"
                    relation-type="resources"
                    relation-name="Ressources nécessaires (recette de craft)"
                    :config="{
                        displayFields: ['name', 'description', 'level'],
                        searchFields: ['name', 'description'],
                        pivotFields: ['quantity'],
                        itemLabel: 'ressource',
                        itemLabelPlural: 'ressources'
                    }"
                />
            </template>
        </Collapse>
    </Container>
</template>
