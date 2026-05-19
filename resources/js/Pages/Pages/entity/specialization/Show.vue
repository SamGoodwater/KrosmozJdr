<script setup>
import { computed } from "vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Specialization } from "@/Models/Entity/Specialization";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import EntityViewFullWrapper from "@/Pages/Molecules/entity/shared/EntityViewFullWrapper.vue";
import SpecializationViewFull from "@/Pages/Molecules/entity/specialization/SpecializationViewFull.vue";
import SpecializationRelationsByLevel from "@/Pages/Molecules/entity/specialization/SpecializationRelationsByLevel.vue";
import SpecializationWriteMetaPanel from "@/Pages/Molecules/entity/specialization/SpecializationWriteMetaPanel.vue";
import EntitySectionsRenderer from "@/Pages/Organismes/entity/EntitySectionsRenderer.vue";

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    specialization: {
        type: Object,
        required: true,
    },
});

const specialization = computed(() => {
    const raw = props.specialization || page.props.specialization || {};
    return raw instanceof Specialization ? raw : new Specialization(raw);
});

setPageTitle(`Spécialisation : ${specialization.value.name || "-"}`);

const goEdit = () => {
    if (!specialization.value.id) return;
    router.visit(route("entities.specializations.edit", { specialization: specialization.value.id }));
};
</script>

<template>
    <Head :title="`Spécialisation : ${specialization?.name || '-'}`" />

    <Container class="space-y-6 pb-8">
        <EntityViewFullWrapper :show-back-button="true" back-route="entities.specializations.index">
            <div class="space-y-6">
                <div class="flex justify-end gap-2">
                    <Btn v-if="specialization?.can?.update" color="primary" @click="goEdit">
                        <i class="fa-solid fa-pen mr-2" aria-hidden="true"></i>
                        Modifier
                    </Btn>
                </div>

                <SpecializationViewFull :specialization="specialization" :show-actions="true" />

                <SpecializationRelationsByLevel :specialization="specialization" />

                <section class="space-y-3">
                    <h2 class="text-lg font-semibold">Sections</h2>
                    <EntitySectionsRenderer
                        :sections="specialization.sections"
                        empty-message="Aucune section liée à cette spécialisation."
                    />
                </section>

                <SpecializationWriteMetaPanel :specialization="specialization" />
            </div>
        </EntityViewFullWrapper>
    </Container>
</template>
