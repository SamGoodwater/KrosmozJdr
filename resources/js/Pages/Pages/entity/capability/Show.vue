<script setup>
/**
 * Fiche lecture d’une capacité (vue large).
 *
 * @props {Object} capability - Payload CapabilityResource
 */
import { computed } from "vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Capability } from "@/Models/Entity/Capability";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import EntityViewFullWrapper from "@/Pages/Molecules/entity/shared/EntityViewFullWrapper.vue";
import CapabilityViewFull from "@/Pages/Molecules/entity/capability/CapabilityViewFull.vue";

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    capability: {
        type: Object,
        required: true,
    },
});

const capability = computed(() => {
    const raw = props.capability || page.props.capability || {};
    return raw instanceof Capability ? raw : new Capability(raw);
});

const characteristicRuntime = computed(() => page.props.characteristicRuntime ?? null);

setPageTitle(`Capacité : ${capability.value.name || "-"}`);

const goEdit = () => {
    if (!capability.value.id) return;
    router.visit(route("entities.capabilities.edit", { capability: capability.value.id }));
};
</script>

<template>
    <Head :title="`Capacité : ${capability?.name || '-'}`" />

    <Container class="space-y-6 pb-8">
        <EntityViewFullWrapper :show-back-button="true" back-route="entities.capabilities.index">
            <div class="space-y-6">
                <div class="flex justify-end gap-2">
                    <Btn v-if="capability?.can?.update" color="primary" @click="goEdit">
                        <i class="fa-solid fa-pen mr-2" aria-hidden="true"></i>
                        Modifier
                    </Btn>
                </div>

                <CapabilityViewFull
                    :capability="capability"
                    title-tag="h1"
                    :show-actions="true"
                    :characteristic-runtime="characteristicRuntime"
                />
            </div>
        </EntityViewFullWrapper>
    </Container>
</template>
