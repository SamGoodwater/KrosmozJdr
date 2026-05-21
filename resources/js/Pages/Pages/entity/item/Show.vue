<script setup>
/**
 * Fiche lecture d’un équipement (vue large).
 *
 * @props {Object} item - Payload ItemResource
 */
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Item } from '@/Models/Entity/Item';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityViewFullWrapper from '@/Pages/Molecules/entity/shared/EntityViewFullWrapper.vue';
import ItemViewFull from '@/Pages/Molecules/entity/item/ItemViewFull.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
});

const item = computed(() => {
    const raw = props.item || page.props.item || {};
    return raw instanceof Item ? raw : new Item(raw);
});

setPageTitle(`Équipement : ${item.value.name || '-'}`);

const goEdit = () => {
    if (!item.value.id) return;
    router.visit(route('entities.items.edit', { item: item.value.id }));
};
</script>

<template>
    <Head :title="`Équipement : ${item?.name || '-'}`" />

    <Container class="space-y-6 pb-8">
        <EntityViewFullWrapper :show-back-button="true" back-route="entities.items.index">
            <div class="space-y-6">
                <div class="flex justify-end gap-2">
                    <Btn v-if="item?.can?.update" color="primary" @click="goEdit">
                        <i class="fa-solid fa-pen mr-2" aria-hidden="true"></i>
                        Modifier
                    </Btn>
                </div>

                <ItemViewFull :item="item" :show-actions="true" />
            </div>
        </EntityViewFullWrapper>
    </Container>
</template>
