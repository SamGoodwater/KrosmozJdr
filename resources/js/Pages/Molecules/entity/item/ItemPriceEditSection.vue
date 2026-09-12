<script setup>
/**
 * Bloc prix (édition item / consommable) : calculé, ajustement, total, actualisation.
 *
 * @props {string} updateUrl - Route PATCH de l’entité
 * @props {string} recalculateUrl - Route POST de recalcul
 * @props {number|null} priceCalculated - Part calculée (kamas)
 * @props {number|null} priceCustom - Ajustement signé
 * @props {string} formulaHint - Rappel de la formule
 */
import { computed, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
import EditActionDock from '@/Pages/Molecules/action/EditActionDock.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';

const props = defineProps({
    updateUrl: { type: String, required: true },
    recalculateUrl: { type: String, required: true },
    priceCalculated: { type: Number, default: null },
    priceCustom: { type: Number, default: null },
    formulaHint: { type: String, default: '' },
});

const notifications = useNotificationStore();

const form = useForm({
    price_custom: props.priceCustom ?? null,
});

watch(
    () => props.priceCustom,
    (v) => {
        form.price_custom = v ?? null;
    }
);

const calculatedLabel = computed(() => {
    const v = props.priceCalculated;
    if (v === null || v === undefined) {
        return '—';
    }
    return String(Math.round(Number(v)));
});

const customNumeric = computed(() => {
    const v = form.price_custom;
    if (v === null || v === undefined || v === '') {
        return 0;
    }
    const n = Number(v);
    return Number.isFinite(n) ? Math.round(n) : 0;
});

const totalPreview = computed(() => {
    const calc = props.priceCalculated != null ? Math.round(Number(props.priceCalculated)) : 0;
    return Math.max(0, calc + customNumeric.value);
});

function onCustomInput(event) {
    const raw = event.target.value;
    form.price_custom = raw === '' || raw === null ? null : parseInt(raw, 10);
}

function submit() {
    form.patch(props.updateUrl, {
        preserveScroll: true,
        onSuccess: () => notifications.success('Prix enregistré.'),
    });
}

function recalculate() {
    router.post(
        props.recalculateUrl,
        {},
        {
            preserveScroll: true,
            onSuccess: () => notifications.success('Prix recalculé.'),
        }
    );
}
</script>

<template>
    <div class="card bg-base-200 shadow-sm border border-base-300">
        <div class="card-body gap-4">
            <h2 class="card-title text-lg">Prix (kamas)</h2>
            <p v-if="formulaHint" class="text-sm text-base-content/70">{{ formulaHint }}</p>
            <div class="grid gap-3 sm:grid-cols-1 md:grid-cols-3">
                <label class="form-control w-full">
                    <span class="label-text font-medium">Prix calculé</span>
                    <input type="text" class="input input-bordered w-full bg-base-300/50" readonly :value="calculatedLabel" />
                </label>
                <label class="form-control w-full">
                    <span class="label-text font-medium">Prix personnalisé</span>
                    <input
                        type="number"
                        class="input input-bordered w-full"
                        step="1"
                        :value="form.price_custom === null || form.price_custom === undefined ? '' : form.price_custom"
                        @input="onCustomInput"
                    />
                    <span class="label-text-alt text-base-content/60">Valeur entière ; peut être négative.</span>
                </label>
                <label class="form-control w-full">
                    <span class="label-text font-medium">Prix total</span>
                    <input type="text" class="input input-bordered w-full bg-base-300/50" readonly :value="String(totalPreview)" />
                </label>
            </div>
            <div class="card-actions justify-end gap-2">
                <Btn type="button" color="neutral" variant="outline" size="sm" @click="recalculate">
                    Actualiser le prix
                </Btn>
                <EditActionDock
                    primary-label="Enregistrer le prix"
                    processing-label="Enregistrement..."
                    :processing="form.processing"
                    :show-secondary="false"
                    :secondary-actions="[]"
                    :fixed-on-desktop="false"
                    @primary="submit"
                />
            </div>
        </div>
    </div>
</template>
