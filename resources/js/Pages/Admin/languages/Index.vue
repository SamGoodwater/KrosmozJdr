<script setup>
/**
 * Admin — référentiel des langues (CRUD sur une page).
 */
import { ref, computed } from "vue";
import { Head, useForm, usePage, router } from "@inertiajs/vue3";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import AdminArea from "@/Pages/Layouts/AdminArea.vue";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import PageHeader from "@/Pages/Molecules/layout/PageHeader.vue";
import LanguageChip from "@/Pages/Molecules/entity/language/LanguageChip.vue";
import { ACTION } from "@/Utils/atomic-design/actionLabels";

const notificationStore = useNotificationStore();

defineProps({
    languages: { type: Array, required: true },
});

const createForm = useForm({
    name: "",
    description: "",
    color: "#6b7280",
});

const editingId = ref(null);
const editForm = useForm({
    name: "",
    description: "",
    color: "#6b7280",
});

const startEdit = (row) => {
    editingId.value = row.id;
    editForm.name = row.name || "";
    editForm.description = row.description || "";
    editForm.color = row.color && /^#[0-9A-Fa-f]{6}$/.test(row.color) ? row.color : "#6b7280";
    editForm.clearErrors();
};

const cancelEdit = () => {
    editingId.value = null;
};

const store = () => {
    if (createForm.processing) return;
    createForm.post(route("admin.languages.store"), {
        preserveScroll: true,
        onSuccess: () => {
            createForm.reset("name", "description");
            createForm.color = "#6b7280";
            notificationStore.success("Langue créée.");
        },
    });
};

const update = () => {
    if (!editingId.value) return;
    editForm.patch(route("admin.languages.update", { language: editingId.value }), {
        preserveScroll: true,
        onSuccess: () => {
            editingId.value = null;
            notificationStore.success("Langue mise à jour.");
        },
    });
};

const destroy = (id) => {
    if (!id) return;
    if (!window.confirm("Supprimer cette langue ? Les liaisons classes / monstres seront retirées.")) {
        return;
    }
    router.delete(route("admin.languages.destroy", { language: id }), {
        preserveScroll: true,
        onSuccess: () => notificationStore.success("Langue supprimée."),
    });
};

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);

defineOptions({ layout: AdminArea });
</script>

<template>
    <Head title="Langues" />

    <Container class="space-y-8 pb-8 max-w-5xl">
        <PageHeader title="Langues">
            <template #subtitle>
                Référentiel des langues (noms, description, couleur d’affichage en
                <code class="text-xs font-mono">#RRGGBB</code>). Utilisé pour les classes et les monstres.
            </template>
            <template #primary>
                <Btn
                    type="submit"
                    form="language-create-form"
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
            <h2 class="text-lg font-semibold text-primary-100">Nouvelle langue</h2>
            <form id="language-create-form" class="grid gap-3 md:grid-cols-2" @submit.prevent="store">
                <InputField
                    v-model="createForm.name"
                    label="Nom"
                    :error="createForm.errors.name"
                    required
                />
                <div class="flex flex-col gap-1">
                    <span class="text-sm font-medium">Couleur (hex)</span>
                    <div class="flex items-center gap-2">
                        <input
                            v-model="createForm.color"
                            type="color"
                            class="h-10 w-14 cursor-pointer rounded border border-base-300 bg-base-200 p-0.5"
                        />
                        <input
                            v-model="createForm.color"
                            type="text"
                            class="input input-bordered input-sm font-mono flex-1"
                            pattern="#[0-9A-Fa-f]{6}"
                            maxlength="7"
                        />
                    </div>
                    <span v-if="createForm.errors.color" class="text-xs text-error">{{ createForm.errors.color }}</span>
                </div>
                <div class="md:col-span-2">
                    <label class="flex flex-col gap-1">
                        <span class="text-sm font-medium">Description (tooltip)</span>
                        <textarea
                            v-model="createForm.description"
                            class="textarea textarea-bordered textarea-sm w-full min-h-[72px]"
                            placeholder="Optionnel"
                        />
                        <span v-if="createForm.errors.description" class="text-xs text-error">{{ createForm.errors.description }}</span>
                    </label>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto rounded-box border border-base-300 bg-base-100">
            <table class="table table-zebra table-pin-rows">
                <thead>
                    <tr class="bg-base-300/70 text-primary-200">
                        <th class="w-20">Aperçu</th>
                        <th class="font-semibold">Nom</th>
                        <th class="font-semibold">Description</th>
                        <th class="font-semibold font-mono text-xs">Couleur</th>
                        <th class="w-40 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="row in languages" :key="row.id">
                        <tr v-if="editingId !== row.id" class="border-b border-base-300/30 align-middle">
                            <td>
                                <LanguageChip :language="row" />
                            </td>
                            <td class="font-medium text-primary-100">{{ row.name }}</td>
                            <td class="text-sm text-primary-200 max-w-md">
                                <span class="line-clamp-2" :title="row.description || ''">{{ row.description || "—" }}</span>
                            </td>
                            <td class="font-mono text-xs text-primary-300">{{ row.color }}</td>
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
                            <td colspan="5">
                                <form class="grid gap-3 md:grid-cols-2 py-2" @submit.prevent="update">
                                    <InputField
                                        v-model="editForm.name"
                                        label="Nom"
                                        :error="editForm.errors.name"
                                        required
                                    />
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-medium">Couleur</span>
                                        <div class="flex items-center gap-2">
                                            <input
                                                v-model="editForm.color"
                                                type="color"
                                                class="h-10 w-14 cursor-pointer rounded border border-base-300 bg-base-200 p-0.5"
                                            />
                                            <input
                                                v-model="editForm.color"
                                                type="text"
                                                class="input input-bordered input-sm font-mono flex-1"
                                                pattern="#[0-9A-Fa-f]{6}"
                                                maxlength="7"
                                            />
                                        </div>
                                        <span v-if="editForm.errors.color" class="text-xs text-error">{{ editForm.errors.color }}</span>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="flex flex-col gap-1">
                                            <span class="text-sm font-medium">Description</span>
                                            <textarea
                                                v-model="editForm.description"
                                                class="textarea textarea-bordered textarea-sm w-full min-h-[72px]"
                                            />
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
                    <tr v-if="!languages.length">
                        <td colspan="5" class="text-center py-8 text-primary-400 italic">Aucune langue</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </Container>
</template>
