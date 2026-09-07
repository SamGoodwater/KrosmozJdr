<script setup>
/**
 * Fiche lecture d’un PNJ, vue large.
 */
import { computed } from "vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Npc } from "@/Models/Entity/Npc";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import EntityViewFullWrapper from "@/Pages/Molecules/entity/shared/EntityViewFullWrapper.vue";
import NpcViewFull from "@/Pages/Molecules/entity/npc/NpcViewFull.vue";

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    npc: {
        type: Object,
        required: true,
    },
});

const npc = computed(() => {
    const raw = props.npc || page.props.npc || {};
    return raw instanceof Npc ? raw : new Npc(raw);
});

const characteristicRuntime = computed(() => page.props.characteristicRuntime ?? null);

const creatureName = computed(() => npc.value?.creature?.name || npc.value?.name || "PNJ");

setPageTitle(`PNJ : ${creatureName.value}`);

const goEdit = () => {
    const id = npc.value?.id;
    if (!id) return;
    router.visit(route("entities.npcs.edit", { npc: id }));
};
</script>

<template>
    <Head :title="`PNJ : ${creatureName}`" />

    <Container class="space-y-6 pb-8">
        <EntityViewFullWrapper :show-back-button="true" back-route="entities.npcs.index">
            <div class="space-y-6">
                <div class="flex justify-end gap-2">
                    <Btn v-if="npc?.can?.update" color="primary" @click="goEdit">
                        <i class="fa-solid fa-pen mr-2" aria-hidden="true"></i>
                        Modifier
                    </Btn>
                </div>

                <NpcViewFull
                    :npc="npc"
                    :show-actions="true"
                    :characteristic-runtime="characteristicRuntime"
                />
            </div>
        </EntityViewFullWrapper>
    </Container>
</template>
