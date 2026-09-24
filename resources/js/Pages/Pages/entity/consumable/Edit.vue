<script setup>
/**
 * Consumable Edit Page
 *
 * @description
 * Édition dense d’un consommable : grille fieldSections + toolbar sticky.
 */
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Consumable } from '@/Models/Entity/Consumable';
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
    buildConsumableFormFieldsConfig,
    CONSUMABLE_FORM_FIELD_SECTIONS_EDIT,
} from '@/Entities/consumable/consumable-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    consumable: {
        type: Object,
        required: true,
    },
    availableConsumableTypes: {
        type: Array,
        default: () => [],
    },
    availableResources: {
        type: Array,
        default: () => [],
    },
    effectUsages: { type: Array, default: () => [] },
    availableEffects: { type: Array, default: () => [] },
    effectEntityType: { type: String, default: 'consumable' },
    objectEffects: { type: Array, default: () => [] },
    objectEffectCharacteristics: { type: Array, default: () => [] },
    objectEffectMonsters: { type: Array, default: () => [] },
});

const consumable = computed(() => {
    const data = props.consumable || page.props.consumable || {};
    return new Consumable(data);
});

const fieldsConfig = computed(() =>
    buildConsumableFormFieldsConfig({
        includeReadonlyMeta: true,
        consumableTypes: props.availableConsumableTypes || [],
    }),
);

const fieldSections = CONSUMABLE_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';

setPageTitle(`Modifier le consommable : ${consumable.value.name || 'Sans nom'}`);

function goToShow() {
    const id = consumable.value?.id;
    if (!id) return;
    router.visit(route('entities.consumables.show', { consumable: id }));
}
</script>

<template>
    <Head :title="`Modifier le consommable : ${consumable?.name || 'Sans nom'}`" />

    <Container class="space-y-4">
        <div
            class="sticky top-0 z-20 px-3 py-1 bg-glass-3xl backdrop-blur-md border-glass-b-md sm:px-4"
            style="--bg-color: var(--color-base-100)"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-md font-bold text-base-content sm:text-lg">
                        {{ consumable.name || 'Consommable sans nom' }}
                    </h1>
                    <p class="text-xs text-base-content/60">
                        Édition · ID {{ consumable.id }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <EntityListBackButton route-name="entities.consumables.index" />
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
            :entity="consumable"
            entity-type="consumable"
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

        <ItemPriceEditSection
            v-if="consumable.id"
            :update-url="route('entities.consumables.update', { consumable: consumable.id })"
            :recalculate-url="route('entities.consumables.recalculatePrice', { consumable: consumable.id })"
            :price-calculated="consumable.priceCalculated"
            :price-custom="consumable.priceCustom"
            :allow-recalculate="(consumable.state ?? consumable._data?.state) !== 'playable'"
            formula-hint="Somme des prix des ressources de la recette. Sans recette, le total vient du prix personnalisé (barème JDR)."
        />

        <Collapse arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Recette de craft</template>
            <template #content>
                <EntityRelationsManager
                    :relations="consumable.resources || []"
                    :available-items="availableResources"
                    :entity-id="consumable.id"
                    entity-type="consumables"
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

        <Collapse arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Effets &amp; usages</template>
            <template #content>
                <div class="space-y-4">
                    <EffectUsagesManager
                        :effect-usages="effectUsages"
                        :available-effects="availableEffects"
                        :entity-type="effectEntityType"
                        :entity-id="consumable.id"
                    />
                    <ObjectEffectsManager
                        :object-effects="objectEffects"
                        :object-effect-characteristics="objectEffectCharacteristics"
                        :object-effect-monsters="objectEffectMonsters"
                        :entity-type="effectEntityType"
                        :entity-id="consumable.id"
                    />
                </div>
            </template>
        </Collapse>
    </Container>
</template>
