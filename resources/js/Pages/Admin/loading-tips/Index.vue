<script setup>
/**
 * Admin — astuces de l’écran de chargement (CRUD sur une page).
 *
 * @description
 * Liste, création, édition et suppression des phrases affichées en bas de
 * l’overlay de chargement. Lien optionnel, mise en avant, durée et activation.
 *
 * @example
 * Inertia::render('Admin/loading-tips/Index', { tips: [...] })
 */
import { ref, computed } from "vue";
import { Head, useForm, usePage, router } from "@inertiajs/vue3";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import AdminArea from "@/Pages/Layouts/AdminArea.vue";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import PageHeader from "@/Pages/Molecules/layout/PageHeader.vue";
import { ACTION } from "@/Utils/atomic-design/actionLabels";
import {
    DEFAULT_LOADING_TIP_DURATION_SECONDS,
    MAX_LOADING_TIP_DURATION_SECONDS,
    MIN_LOADING_TIP_DURATION_SECONDS,
} from "@/Utils/layout/pickLoadingTip";

const notificationStore = useNotificationStore();

defineProps({
    tips: { type: Array, required: true },
});

const emptyForm = () => ({
    body: "",
    url: "",
    featured: false,
    is_active: true,
    duration_seconds: DEFAULT_LOADING_TIP_DURATION_SECONDS,
});

const createForm = useForm(emptyForm());

const editingId = ref(null);
const editForm = useForm(emptyForm());

const startEdit = (row) => {
    editingId.value = row.id;
    editForm.body = row.body || "";
    editForm.url = row.url || "";
    editForm.featured = !!row.featured;
    editForm.is_active = row.is_active !== false;
    editForm.duration_seconds = Number(row.duration_seconds) || DEFAULT_LOADING_TIP_DURATION_SECONDS;
    editForm.clearErrors();
};

const cancelEdit = () => {
    editingId.value = null;
};

const store = () => {
    if (createForm.processing) return;
    createForm.post(route("admin.loading-tips.store"), {
        preserveScroll: true,
        onSuccess: () => {
            createForm.reset();
            createForm.is_active = true;
            createForm.duration_seconds = DEFAULT_LOADING_TIP_DURATION_SECONDS;
            notificationStore.success("Astuce créée.");
        },
    });
};

const update = () => {
    if (!editingId.value) return;
    editForm.patch(route("admin.loading-tips.update", { loading_tip: editingId.value }), {
        preserveScroll: true,
        onSuccess: () => {
            editingId.value = null;
            notificationStore.success("Astuce mise à jour.");
        },
    });
};

const destroy = (id) => {
    if (!id) return;
    if (!window.confirm("Supprimer cette astuce ?")) {
        return;
    }
    router.delete(route("admin.loading-tips.destroy", { loading_tip: id }), {
        preserveScroll: true,
        onSuccess: () => notificationStore.success("Astuce supprimée."),
    });
};

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);

defineOptions({ layout: AdminArea });
</script>

<template>
    <Head title="Astuces de chargement" />

    <Container class="space-y-8 pb-8 max-w-5xl">
        <PageHeader title="Astuces de chargement">
            <template #subtitle>
                Phrases courtes affichées en bas de l’écran de chargement. Les
                astuces mises en avant apparaissent plus souvent. La durée
                contrôle le temps de lecture (hors fondu). Un lien optionnel
                s’ouvre dans un nouvel onglet.
            </template>
            <template #primary>
                <Btn
                    type="submit"
                    form="loading-tip-create-form"
                    color="primary"
                    size="sm"
                    :disabled="createForm.processing"
                >
                    <i :class="ACTION.create.icon" class="mr-1.5" aria-hidden="true"></i>
                    {{ createForm.processing ? ACTION.create.processing : ACTION.create.label }}
                </Btn>
            </template>
        </PageHeader>

        <p v-if="flashSuccess" class="text-sm text-success">
            {{ flashSuccess }}
        </p>

        <div class="rounded-box border border-base-300 bg-base-100/80 p-4 space-y-4">
            <h2 class="text-lg font-semibold text-primary-100">Nouvelle astuce</h2>
            <form id="loading-tip-create-form" class="grid gap-3 md:grid-cols-2" @submit.prevent="store">
                <div class="md:col-span-2">
                    <InputField
                        v-model="createForm.body"
                        label="Phrase"
                        :error="createForm.errors.body"
                        required
                        maxlength="200"
                        placeholder="Ex. N’hésite pas à faire des retours…"
                    />
                </div>
                <InputField
                    v-model="createForm.url"
                    label="Lien (optionnel)"
                    :error="createForm.errors.url"
                    type="url"
                    placeholder="https://…"
                />
                <InputField
                    v-model="createForm.duration_seconds"
                    label="Durée (secondes)"
                    :error="createForm.errors.duration_seconds"
                    type="number"
                    :min="MIN_LOADING_TIP_DURATION_SECONDS"
                    :max="MAX_LOADING_TIP_DURATION_SECONDS"
                    required
                    helper="Temps de lecture avant le fondu de sortie (2–30 s)."
                />
                <div class="flex flex-wrap items-end gap-6 pb-1 md:col-span-2">
                    <label class="flex items-center gap-2 text-sm">
                        <input v-model="createForm.featured" type="checkbox" class="checkbox checkbox-sm" />
                        Mise en avant
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input v-model="createForm.is_active" type="checkbox" class="checkbox checkbox-sm" />
                        Active
                    </label>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto rounded-box border border-base-300 bg-base-100">
            <table class="table table-zebra table-pin-rows">
                <thead>
                    <tr class="bg-base-300/70 text-primary-200">
                        <th class="font-semibold">Phrase</th>
                        <th class="font-semibold">Lien</th>
                        <th class="w-20 text-center font-semibold">Durée</th>
                        <th class="w-28 text-center font-semibold">Avant</th>
                        <th class="w-24 text-center font-semibold">Active</th>
                        <th class="w-40 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="row in tips" :key="row.id">
                        <tr v-if="editingId !== row.id" class="border-b border-base-300/30 align-middle">
                            <td class="font-medium text-primary-100 max-w-md">
                                <span class="line-clamp-2" :title="row.body">{{ row.body }}</span>
                            </td>
                            <td class="text-sm text-primary-300 max-w-xs">
                                <a
                                    v-if="row.url"
                                    :href="row.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="link link-hover line-clamp-1 break-all"
                                >
                                    {{ row.url }}
                                </a>
                                <span v-else>—</span>
                            </td>
                            <td class="text-center text-sm tabular-nums text-primary-200">
                                {{ row.duration_seconds }}s
                            </td>
                            <td class="text-center text-sm">
                                <span v-if="row.featured" class="badge badge-primary badge-sm">Oui</span>
                                <span v-else class="text-primary-400">—</span>
                            </td>
                            <td class="text-center text-sm">
                                <span v-if="row.is_active" class="badge badge-success badge-sm">Oui</span>
                                <span v-else class="badge badge-ghost badge-sm">Non</span>
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    <Btn variant="ghost" size="xs" @click="startEdit(row)">
                                        {{ ACTION.edit.label }}
                                    </Btn>
                                    <Btn variant="ghost" color="error" size="xs" @click="destroy(row.id)">
                                        {{ ACTION.delete.label }}
                                    </Btn>
                                </div>
                            </td>
                        </tr>
                        <tr v-else :key="`edit-${row.id}`" class="bg-base-200/40">
                            <td colspan="6">
                                <form class="grid gap-3 md:grid-cols-2 py-2" @submit.prevent="update">
                                    <div class="md:col-span-2">
                                        <InputField
                                            v-model="editForm.body"
                                            label="Phrase"
                                            :error="editForm.errors.body"
                                            required
                                            maxlength="200"
                                        />
                                    </div>
                                    <InputField
                                        v-model="editForm.url"
                                        label="Lien (optionnel)"
                                        :error="editForm.errors.url"
                                        type="url"
                                    />
                                    <InputField
                                        v-model="editForm.duration_seconds"
                                        label="Durée (secondes)"
                                        :error="editForm.errors.duration_seconds"
                                        type="number"
                                        :min="MIN_LOADING_TIP_DURATION_SECONDS"
                                        :max="MAX_LOADING_TIP_DURATION_SECONDS"
                                        required
                                    />
                                    <div class="flex flex-wrap items-end gap-6 pb-1 md:col-span-2">
                                        <label class="flex items-center gap-2 text-sm">
                                            <input v-model="editForm.featured" type="checkbox" class="checkbox checkbox-sm" />
                                            Mise en avant
                                        </label>
                                        <label class="flex items-center gap-2 text-sm">
                                            <input v-model="editForm.is_active" type="checkbox" class="checkbox checkbox-sm" />
                                            Active
                                        </label>
                                    </div>
                                    <div class="md:col-span-2 flex justify-end gap-2">
                                        <Btn variant="ghost" size="sm" @click="cancelEdit">
                                            {{ ACTION.close.label }}
                                        </Btn>
                                        <Btn type="submit" color="primary" size="sm" :disabled="editForm.processing">
                                            {{ editForm.processing ? ACTION.save.processing : ACTION.save.label }}
                                        </Btn>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    </template>
                    <tr v-if="!tips.length">
                        <td colspan="6" class="text-center py-8 text-primary-400 italic">Aucune astuce</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </Container>
</template>
