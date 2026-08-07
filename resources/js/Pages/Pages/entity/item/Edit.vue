<script setup>
/**
 * Item Edit Page
 * 
 * @description
 * Page d'édition d'un item via le formulaire d'entité générique.
 * 
 * @props {Object} item - Données de l'item à éditer
 */
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Item } from '@/Models/Entity/Item';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import EffectUsagesManager from '@/Pages/Organismes/entity/EffectUsagesManager.vue';
import ObjectEffectsManager from '@/Pages/Organismes/entity/ObjectEffectsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Collapse from '@/Pages/Atoms/data-display/Collapse.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Route from '@/Pages/Atoms/action/Route.vue';
import ItemPriceEditSection from '@/Pages/Molecules/entity/item/ItemPriceEditSection.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    item: {
        type: Object,
        required: true
    },
    availableResources: {
        type: Array,
        default: () => []
    },
    effectUsages: { type: Array, default: () => [] },
    availableEffects: { type: Array, default: () => [] },
    effectEntityType: { type: String, default: 'item' },
    objectEffects: { type: Array, default: () => [] },
    objectEffectCharacteristics: { type: Array, default: () => [] },
    objectEffectMonsters: { type: Array, default: () => [] },
});

// Configuration des champs pour les items
const fieldsConfig = {
    name: { 
        type: 'text', 
        label: 'Nom', 
        required: true, 
        showInCompact: true 
    },
    description: { 
        type: 'textarea', 
        label: 'Description', 
        required: false, 
        showInCompact: false 
    },
    level: { 
        type: 'number', 
        label: 'Niveau', 
        required: false, 
        showInCompact: true 
    },
    rarity: { 
        type: 'select', 
        label: 'Rareté', 
        required: false, 
        showInCompact: true,
        options: [
            { value: 'common', label: 'Commun' },
            { value: 'uncommon', label: 'Peu commun' },
            { value: 'rare', label: 'Rare' },
            { value: 'epic', label: 'Épique' },
            { value: 'legendary', label: 'Légendaire' }
        ]
    },
    image: { 
        type: 'file', 
        label: 'Image', 
        required: false, 
        showInCompact: false 
    }
};

// Créer une instance de modèle Item
const item = computed(() => {
    const itemData = props.item || page.props.item || {};
    return new Item(itemData);
});

setPageTitle(`Modifier l'item : ${item.value.name || 'Nouvel item'}`);
</script>

<template>
    <Head :title="`Modifier l'item : ${item?.name || 'Nouvel item'}`" />
    
    <Container class="space-y-6">
        <Route route="entities.items.index">
            <Btn color="neutral" variant="ghost" size="sm" class="gap-2">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Retour à la liste
            </Btn>
        </Route>

        <EntityEditForm
            :entity="item"
            entity-type="item"
            :fields-config="fieldsConfig"
            :is-updating="true"
        />

        <ItemPriceEditSection
            v-if="item.id"
            :item-id="item.id"
            :price-calculated="item.priceCalculated"
            :price-custom="item.priceCustom"
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

