<script setup>
/**
 * Fiche lecture d’un sort (vue large).
 *
 * @props {Object} spell - Payload SpellResource
 */
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Spell } from '@/Models/Entity/Spell';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityViewFullWrapper from '@/Pages/Molecules/entity/shared/EntityViewFullWrapper.vue';
import SpellViewFull from '@/Pages/Molecules/entity/spell/SpellViewFull.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    spell: {
        type: Object,
        required: true,
    },
});

const spell = computed(() => {
    const raw = props.spell || page.props.spell || {};
    return raw instanceof Spell ? raw : new Spell(raw);
});

/** Quand le backend exposera un payload (même schéma que resolved-stats), les EntityPropertyDisplay l’utiliseront */
const characteristicRuntime = computed(() => page.props.characteristicRuntime ?? null);

setPageTitle(`Sort : ${spell.value.name || '-'}`);

const goEdit = () => {
    if (!spell.value.id) return;
    router.visit(route('entities.spells.edit', { spell: spell.value.id }));
};
</script>

<template>
    <Head :title="`Sort : ${spell?.name || '-'}`" />

    <Container class="space-y-6 pb-8">
        <EntityViewFullWrapper :show-back-button="true" back-route="entities.spells.index">
            <div class="space-y-6">
                <div class="flex justify-end gap-2">
                    <Btn v-if="spell?.can?.update" color="primary" @click="goEdit">
                        <i class="fa-solid fa-pen mr-2" aria-hidden="true"></i>
                        Modifier
                    </Btn>
                </div>

                <SpellViewFull
                    :spell="spell"
                    title-tag="h1"
                    :show-actions="true"
                    :characteristic-runtime="characteristicRuntime"
                />
            </div>
        </EntityViewFullWrapper>
    </Container>
</template>
