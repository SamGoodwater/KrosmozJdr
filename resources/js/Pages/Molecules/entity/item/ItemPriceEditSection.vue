<script setup>
/**
 * Bloc prix (édition item) : montant calculé (lecture seule), ajustement personnalisé (signé), total (lecture seule).
 *
 * @props {number} itemId - Identifiant de l'item
 * @props {number|null} priceCalculated - Part calculée (kamas entiers), peut être null si non calculée
 * @props {number|null} priceCustom - Ajustement (peut être négatif pour baisser le total)
 */
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
import Btn from '@/Pages/Atoms/action/Btn.vue';

const props = defineProps({
    itemId: { type: Number, required: true },
    priceCalculated: { type: Number, default: null },
    priceCustom: { type: Number, default: null },
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
    form.patch(route('entities.items.update', { item: props.itemId }), {
        preserveScroll: true,
        onSuccess: () => notifications.success('Prix enregistré.'),
    });
}
</script>

<template>
    <div class="card bg-base-200 shadow-sm border border-base-300">
        <div class="card-body gap-4">
            <h2 class="card-title text-lg">Prix (kamas)</h2>
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
            <div class="card-actions justify-end">
                <Btn type="button" color="primary" :disabled="form.processing" @click="submit">
                    Enregistrer le prix
                </Btn>
            </div>
        </div>
    </div>
</template>
