<script setup>
/**
 * Fiche lecture d’un consommable (vue large).
 *
 * @props {Object} consumable - Payload ConsumableResource
 */
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Consumable } from '@/Models/Entity/Consumable';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityViewFullWrapper from '@/Pages/Molecules/entity/shared/EntityViewFullWrapper.vue';
import ConsumableViewFull from '@/Pages/Molecules/entity/consumable/ConsumableViewFull.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    consumable: {
        type: Object,
        required: true,
    },
});

const consumable = computed(() => {
    const raw = props.consumable || page.props.consumable || {};
    return raw instanceof Consumable ? raw : new Consumable(raw);
});

setPageTitle(`Consommable : ${consumable.value.name || '-'}`);

const goEdit = () => {
    if (!consumable.value.id) return;
    router.visit(route('entities.consumables.edit', { consumable: consumable.value.id }));
};
</script>

<template>
    <Head :title="`Consommable : ${consumable?.name || '-'}`" />

    <Container class="space-y-6 pb-8">
        <EntityViewFullWrapper :show-back-button="true" back-route="entities.consumables.index">
            <div class="space-y-6">
                <div class="flex justify-end gap-2">
                    <Btn v-if="consumable?.can?.update" color="primary" @click="goEdit">
                        <i class="fa-solid fa-pen mr-2" aria-hidden="true"></i>
                        Modifier
                    </Btn>
                </div>

                <ConsumableViewFull :consumable="consumable" :show-actions="true" />
            </div>
        </EntityViewFullWrapper>
    </Container>
</template>
