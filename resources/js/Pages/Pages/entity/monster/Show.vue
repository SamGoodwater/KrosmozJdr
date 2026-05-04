<script setup>
/**
 * Fiche lecture d’un monstre, vue large.
 */
import { computed } from "vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Monster } from "@/Models/Entity/Monster";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import EntityViewLargeWrapper from "@/Pages/Molecules/entity/shared/EntityViewLargeWrapper.vue";
import MonsterViewLarge from "@/Pages/Molecules/entity/monster/MonsterViewLarge.vue";

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    monster: {
        type: Object,
        required: true,
    },
});

const monster = computed(() => {
    const raw = props.monster || page.props.monster || {};
    return raw instanceof Monster ? raw : new Monster(raw);
});

const characteristicRuntime = computed(() => page.props.characteristicRuntime ?? null);

const creatureName = computed(() => monster.value?.creature?.name || monster.value?.creatureName || "Monstre");

setPageTitle(`Monstre : ${creatureName.value}`);

const goEdit = () => {
    const id = monster.value?.id;
    if (!id) return;
    router.visit(route("entities.monsters.edit", { monster: id }));
};
</script>

<template>
    <Head :title="`Monstre : ${creatureName}`" />

    <Container class="space-y-6 pb-8">
        <EntityViewLargeWrapper :show-back-button="true" back-route="entities.monsters.index">
            <div class="space-y-6">
                <div class="flex justify-end gap-2">
                    <Btn v-if="monster?.can?.update" color="primary" @click="goEdit">
                        <i class="fa-solid fa-pen mr-2" aria-hidden="true"></i>
                        Modifier
                    </Btn>
                </div>

                <MonsterViewLarge
                    :monster="monster"
                    :show-actions="true"
                    :characteristic-runtime="characteristicRuntime"
                />
            </div>
        </EntityViewLargeWrapper>
    </Container>
</template>
