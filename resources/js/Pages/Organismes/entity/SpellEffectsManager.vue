<script setup>
/**
 * SpellEffectsManager — Gestion des effets d'un sort (liste avec type, valeurs, durée, cible).
 * Envoi via PATCH entities.spells.updateEffects.
 */
import { ref, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectFieldNative from '@/Pages/Molecules/data-input/SelectFieldNative.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';

const TARGET_SCOPE_OPTIONS = [
    { value: 'enemy', label: 'Ennemi' },
    { value: 'ally', label: 'Allié' },
    { value: 'self', label: 'Soi' },
    { value: 'cell', label: 'Case' },
    { value: 'zone', label: 'Zone' },
];

const props = defineProps({
    spell: { type: Object, required: true },
    availableSpellEffectTypes: { type: Array, default: () => [] },
});

function emptyEffect(order = 0) {
    return {
        id: null,
        spell_effect_type_id: '',
        value_min: null,
        value_max: null,
        dice_num: null,
        dice_side: null,
        duration: null,
        target_scope: 'enemy',
        zone_shape: '',
        dispellable: true,
        order,
        raw_description: '',
        summon_monster_id: null,
    };
}

function effectFromSpellEffect(e) {
    return {
        id: e.id,
        spell_effect_type_id: e.spell_effect_type_id,
        value_min: e.value_min ?? null,
        value_max: e.value_max ?? null,
        dice_num: e.dice_num ?? null,
        dice_side: e.dice_side ?? null,
        duration: e.duration ?? null,
        target_scope: e.target_scope || 'enemy',
        zone_shape: e.zone_shape ?? '',
        dispellable: e.dispellable ?? true,
        order: e.order ?? 0,
        raw_description: e.raw_description ?? '',
        summon_monster_id: e.summon_monster_id ?? null,
    };
}

const effectTypesOptions = ref(
    props.availableSpellEffectTypes.map((t) => ({ value: t.id, label: `${t.name} (${t.slug})` }))
);

const effects = ref(
    (props.spell?.spellEffects || []).length
        ? (props.spell.spellEffects || []).map((e, i) => ({ ...effectFromSpellEffect(e), order: e.order ?? i }))
        : [emptyEffect(0)]
);

watch(
    () => props.spell?.spellEffects,
    (list) => {
        if (!list || list.length === 0) {
            effects.value = [emptyEffect(0)];
            return;
        }
        effects.value = list.map((e, i) => ({ ...effectFromSpellEffect(e), order: e.order ?? i }));
    },
    { deep: true }
);

function addEffect() {
    const nextOrder = effects.value.length ? Math.max(...effects.value.map((e) => e.order), 0) + 1 : 0;
    effects.value.push(emptyEffect(nextOrder));
}

function removeEffect(index) {
    effects.value.splice(index, 1);
    if (effects.value.length === 0) {
        effects.value.push(emptyEffect(0));
    }
}

function buildPayload() {
    return effects.value
        .filter((e) => e.spell_effect_type_id != null && e.spell_effect_type_id !== '')
        .map((e, i) => ({
        id: e.id || undefined,
        spell_effect_type_id: Number(e.spell_effect_type_id) || null,
        value_min: e.value_min != null && e.value_min !== '' ? Number(e.value_min) : null,
        value_max: e.value_max != null && e.value_max !== '' ? Number(e.value_max) : null,
        dice_num: e.dice_num != null && e.dice_num !== '' ? Number(e.dice_num) : null,
        dice_side: e.dice_side != null && e.dice_side !== '' ? Number(e.dice_side) : null,
        duration: e.duration != null && e.duration !== '' ? Number(e.duration) : null,
        target_scope: e.target_scope || 'enemy',
        zone_shape: e.zone_shape || null,
        dispellable: Boolean(e.dispellable),
        order: i,
        raw_description: e.raw_description || null,
        summon_monster_id: e.summon_monster_id != null && e.summon_monster_id !== '' ? Number(e.summon_monster_id) : null,
    }));
}

const form = useForm({ spell_effects: buildPayload() });

function submit() {
    form.spell_effects = buildPayload();
    form.patch(route('entities.spells.updateEffects', props.spell.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Container class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Effets du sort</h2>
            <Btn size="sm" variant="primary" @click="addEffect">
                <i class="fa-solid fa-plus mr-1" /> Ajouter un effet
            </Btn>
        </div>
        <p class="text-sm text-base-content/70">
            Définissez les effets que produit ce sort (dégâts, soins, états, etc.). Chaque ligne correspond à un effet.
        </p>

        <div class="space-y-4">
            <div
                v-for="(eff, index) in effects"
                :key="index"
                class="card card-compact bg-base-200/60 border border-base-300"
            >
                <div class="card-body gap-3">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-sm">Effet {{ index + 1 }}</span>
                        <Btn
                            size="xs"
                            variant="ghost"
                            class="text-error"
                            :disabled="effects.length <= 1"
                            @click="removeEffect(index)"
                        >
                            <i class="fa-solid fa-trash" />
                        </Btn>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="sm:col-span-2">
                            <SelectFieldNative
                                v-model="eff.spell_effect_type_id"
                                label="Type d'effet"
                                :options="effectTypesOptions"
                            />
                        </div>
                        <SelectFieldNative
                            v-model="eff.target_scope"
                            label="Cible"
                            :options="TARGET_SCOPE_OPTIONS"
                        />
                        <InputField
                            v-model="eff.duration"
                            label="Durée (tours)"
                            type="number"
                            min="0"
                            placeholder="—"
                        />
                        <InputField
                            v-model="eff.value_min"
                            label="Valeur min"
                            type="number"
                            placeholder="—"
                        />
                        <InputField
                            v-model="eff.value_max"
                            label="Valeur max"
                            type="number"
                            placeholder="—"
                        />
                        <InputField
                            v-model="eff.dice_num"
                            label="Dés (nb)"
                            type="number"
                            min="0"
                            placeholder="—"
                        />
                        <InputField
                            v-model="eff.dice_side"
                            label="Dés (faces)"
                            type="number"
                            min="0"
                            placeholder="—"
                        />
                        <div class="flex items-center gap-2 sm:col-span-2">
                            <input
                                v-model="eff.dispellable"
                                type="checkbox"
                                class="checkbox checkbox-sm"
                            />
                            <span class="label-text">Dispensable</span>
                        </div>
                        <div class="sm:col-span-2">
                            <InputField
                                v-model="eff.raw_description"
                                label="Description brute"
                                placeholder="Texte libre (optionnel)"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-2">
            <button
                type="button"
                class="btn btn-primary"
                :disabled="form.processing"
                @click="submit"
            >
                {{ form.processing ? 'Enregistrement…' : 'Enregistrer les effets' }}
            </button>
            <p v-if="form.recentlySuccessful" class="text-sm text-success">Effets enregistrés.</p>
        </div>
    </Container>
</template>
