<script setup>
/**
 * Admin Effects — Liste à gauche, panneau création/édition à droite.
 * Gestion des sous-effets (ordre, scope, paramètres). Bouton « Ajouter un degré ».
 */
import { watch, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import Main from '@/Pages/Layouts/Main.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectFieldNative from '@/Pages/Molecules/data-input/SelectFieldNative.vue';

const { setPageTitle } = usePageTitle();

const props = defineProps({
    effects: { type: Array, required: true },
    selected: { type: [Object, String], default: null },
    options: {
        type: Object,
        default: () => ({ effect_groups: [], sub_effects: [], scopes: [] }),
    },
});

defineOptions({ layout: Main });
setPageTitle('Effets');

function buildFormData(selected) {
    if (!selected || selected === 'new') {
        return {
            name: '',
            slug: '',
            description: '',
            effect_group_id: '',
            degree: '',
            effect_sub_effects: [],
        };
    }
    return {
        name: selected.name ?? '',
        slug: selected.slug ?? '',
        description: selected.description ?? '',
        effect_group_id: selected.effect_group_id ?? '',
        degree: selected.degree ?? '',
        effect_sub_effects: (selected.sub_effects || []).map((s) => ({
            sub_effect_id: s.id,
            order: s.order ?? 0,
            scope: s.scope ?? 'general',
            value_min: s.value_min ?? '',
            value_max: s.value_max ?? '',
            dice_num: s.dice_num ?? '',
            dice_side: s.dice_side ?? '',
            params: s.params ?? null,
        })),
    };
}

const form = useForm(buildFormData(props.selected));
const duplicateForm = useForm({});

watch(
    () => props.selected,
    (s) => {
        const data = buildFormData(s);
        form.name = data.name;
        form.slug = data.slug;
        form.description = data.description;
        form.effect_group_id = data.effect_group_id;
        form.degree = data.degree;
        form.effect_sub_effects = data.effect_sub_effects;
    },
    { immediate: true }
);

function addSubEffect() {
    const firstId = props.options.sub_effects?.[0]?.id;
    if (!firstId) return;
    form.effect_sub_effects.push({
        sub_effect_id: firstId,
        order: form.effect_sub_effects.length,
        scope: 'general',
        value_min: '',
        value_max: '',
        dice_num: '',
        dice_side: '',
        params: null,
    });
}

function removeSubEffect(index) {
    form.effect_sub_effects.splice(index, 1);
    form.effect_sub_effects.forEach((row, i) => {
        row.order = i;
    });
}

function subEffectLabel(subEffectId) {
    const s = props.options.sub_effects?.find((e) => e.id === subEffectId);
    return s ? `${s.slug} (${s.type_slug})` : `#${subEffectId}`;
}

function submit() {
    if (props.selected === 'new') {
        const payload = {
            name: form.name || null,
            slug: form.slug || null,
            description: form.description || null,
            effect_group_id: form.effect_group_id ? Number(form.effect_group_id) : null,
            degree: form.degree !== '' && form.degree != null ? Number(form.degree) : null,
        };
        form.transform(() => payload).post(route('admin.effects.store'));
        return;
    }
    if (props.selected?.id) {
        const payload = {
            name: form.name || null,
            slug: form.slug || null,
            description: form.description || null,
            effect_group_id: form.effect_group_id ? Number(form.effect_group_id) : null,
            degree: form.degree !== '' && form.degree != null ? Number(form.degree) : null,
            effect_sub_effects: form.effect_sub_effects.map((row, i) => ({
                sub_effect_id: Number(row.sub_effect_id),
                order: i,
                scope: row.scope || 'general',
                value_min: row.value_min !== '' && row.value_min != null ? Number(row.value_min) : null,
                value_max: row.value_max !== '' && row.value_max != null ? Number(row.value_max) : null,
                dice_num: row.dice_num !== '' && row.dice_num != null ? Number(row.dice_num) : null,
                dice_side: row.dice_side !== '' && row.dice_side != null ? Number(row.dice_side) : null,
                params: row.params || null,
            })),
        };
        form.transform(() => payload).patch(route('admin.effects.update', props.selected.id));
    }
}

function duplicateDegree() {
    if (!props.selected?.id) return;
    duplicateForm.post(route('admin.effects.duplicate-degree', props.selected.id));
}

function destroy() {
    if (!props.selected?.id) return;
    if (confirm('Supprimer cet effet ?')) {
        form.delete(route('admin.effects.destroy', props.selected.id));
    }
}
</script>

<template>
    <Head title="Effets" />
    <div class="flex h-full min-h-0 w-full">
        <aside class="flex w-64 shrink-0 flex-col border-r border-base-300 bg-base-200/50 overflow-y-auto">
            <div class="p-3">
                <div class="font-semibold text-base-content">Effets</div>
                <p class="mt-1 text-xs text-base-content/70">
                    Conteneurs de sous-effets. Degré = puissance (sorts). Niveau géré sur l'usage (entité).
                </p>
            </div>
            <nav class="flex flex-col gap-0.5 p-2">
                <Link
                    :href="route('admin.effects.create')"
                    class="rounded-lg px-3 py-2 text-left text-sm font-medium transition-colors hover:bg-base-300 border-l-4 border-transparent"
                    :class="selected === 'new' ? 'bg-primary text-primary-content' : ''"
                >
                    + Nouvel effet
                </Link>
                <Link
                    v-for="e in effects"
                    :key="e.id"
                    :href="route('admin.effects.show', e.id)"
                    class="flex items-center gap-2 rounded-lg px-3 py-2 text-left transition-colors border-l-4 border-transparent"
                    :class="selected?.id === e.id ? 'bg-primary text-primary-content' : 'hover:bg-base-300'"
                >
                    <span class="truncate">{{ e.name || e.slug || 'Effet #' + e.id }}</span>
                    <span v-if="e.degree != null" class="text-xs opacity-70 shrink-0">d{{ e.degree }}</span>
                </Link>
            </nav>
        </aside>

        <main class="min-w-0 flex-1 overflow-y-auto p-6">
            <template v-if="selected">
                <h1 class="mb-2 text-2xl font-bold">
                    {{ selected === 'new' ? 'Nouvel effet' : (selected.name || selected.slug || 'Effet') }}
                </h1>
                <p class="mb-6 text-sm text-base-content/70">
                    Nom, description optionnelle, groupe et degré. Liste des sous-effets avec ordre, contexte (Général / Combat / Hors combat) et paramètres.
                </p>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="card bg-base-100 shadow">
                        <div class="card-body">
                            <h2 class="card-title text-lg">Définition</h2>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <InputField v-model="form.name" label="Nom" name="name" />
                                <InputField v-model="form.slug" label="Slug" name="slug" helper="Optionnel, unique." />
                                <div class="sm:col-span-2">
                                    <InputField v-model="form.description" label="Description (aperçu)" name="description" type="textarea" />
                                </div>
                                <SelectFieldNative
                                    v-model="form.effect_group_id"
                                    label="Groupe d'effets"
                                    name="effect_group_id"
                                    :options="[{ value: '', label: '— Aucun —' }, ...(options.effect_groups || [])]"
                                />
                                <InputField v-model="form.degree" label="Degré" name="degree" type="number" helper="1, 2, 3… pour sorts." />
                            </div>
                        </div>
                    </div>

                    <div class="card bg-base-100 shadow">
                        <div class="card-body">
                            <div class="flex items-center justify-between">
                                <h2 class="card-title text-lg">Sous-effets</h2>
                                <button type="button" class="btn btn-sm btn-outline" @click="addSubEffect">
                                    Ajouter un sous-effet
                                </button>
                            </div>
                            <div v-if="form.effect_sub_effects.length === 0" class="text-sm text-base-content/70 py-4">
                                Aucun sous-effet. Cliquez sur « Ajouter un sous-effet ».
                            </div>
                            <div v-else class="space-y-4">
                                <div
                                    v-for="(row, index) in form.effect_sub_effects"
                                    :key="index"
                                    class="flex flex-wrap items-end gap-3 rounded-lg border border-base-300 bg-base-200/30 p-3"
                                >
                                    <div class="min-w-[180px] flex-1">
                                        <label class="label text-xs">Sous-effet</label>
                                        <select
                                            v-model="row.sub_effect_id"
                                            class="select select-bordered select-sm w-full"
                                            required
                                        >
                                            <option
                                                v-for="s in options.sub_effects"
                                                :key="s.id"
                                                :value="s.id"
                                            >
                                                {{ s.slug }} ({{ s.type_slug }})
                                            </option>
                                        </select>
                                    </div>
                                    <div class="w-28">
                                        <label class="label text-xs">Contexte</label>
                                        <select v-model="row.scope" class="select select-bordered select-sm w-full">
                                            <option
                                                v-for="sc in options.scopes"
                                                :key="sc.value"
                                                :value="sc.value"
                                            >
                                                {{ sc.label }}
                                            </option>
                                        </select>
                                    </div>
                                    <InputField
                                        v-model="row.value_min"
                                        label="Min"
                                        type="number"
                                        class="w-20"
                                    />
                                    <InputField
                                        v-model="row.value_max"
                                        label="Max"
                                        type="number"
                                        class="w-20"
                                    />
                                    <InputField
                                        v-model="row.dice_num"
                                        label="Dés n"
                                        type="number"
                                        class="w-16"
                                    />
                                    <InputField
                                        v-model="row.dice_side"
                                        label="Faces"
                                        type="number"
                                        class="w-16"
                                    />
                                    <button
                                        type="button"
                                        class="btn btn-ghost btn-sm btn-square text-error"
                                        title="Retirer"
                                        @click="removeSubEffect(index)"
                                    >
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">
                            {{ form.processing ? 'Enregistrement…' : (selected === 'new' ? 'Créer' : 'Enregistrer') }}
                        </button>
                        <button
                            v-if="selected?.id"
                            type="button"
                            class="btn btn-outline"
                            :disabled="duplicateForm.processing"
                            @click="duplicateDegree"
                        >
                            Ajouter un degré
                        </button>
                        <button
                            v-if="selected?.id"
                            type="button"
                            class="btn btn-ghost btn-error"
                            :disabled="form.processing"
                            @click="destroy"
                        >
                            Supprimer
                        </button>
                        <p v-if="form.recentlySuccessful" class="text-sm text-success">Enregistré.</p>
                    </div>
                </form>
            </template>
            <template v-else>
                <p class="text-base-content/70">
                    Sélectionnez un effet ou créez-en un nouveau.
                </p>
            </template>
        </main>
    </div>
</template>
