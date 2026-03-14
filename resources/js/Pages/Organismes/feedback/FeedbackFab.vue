<script setup>
/**
 * FeedbackFab Organism
 *
 * @description
 * Bouton FAB (Floating Action Button) ouvrant un modal de retour utilisateur.
 * Permet de signaler un bug, une erreur, une suggestion ou autre, avec URL, pseudo et pièce jointe.
 * Envoi par email aux admins.
 *
 * @see docs/00-Project/FEEDBACK_SYSTEM.md
 * @see https://daisyui.com/components/fab/
 */
import { ref, watch, computed, nextTick } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';

const page = usePage();
const modalOpen = ref(false);
const submitting = ref(false);
const formErrors = ref({});
const attachmentInputRef = ref(null);

const form = ref({
    url: '',
    includePseudo: false,
    type: 'bug',
    message: '',
    attachment: null,
});

const TYPE_OPTIONS = [
    { value: 'bug', label: 'Bogue' },
    { value: 'error', label: 'Erreur' },
    { value: 'suggestion', label: 'Suggestion' },
    { value: 'other', label: 'Autre' },
];

const currentUrl = computed(() => {
    if (typeof window !== 'undefined') {
        return window.location.href;
    }
    return '';
});

const authUser = computed(() => page.props.auth?.user ?? null);

watch(modalOpen, async (open) => {
    if (open) {
        form.value.url = currentUrl.value;
        form.value.includePseudo = false;
        form.value.type = 'bug';
        form.value.message = '';
        form.value.attachment = null;
        formErrors.value = {};
        await nextTick();
        clearAttachment();
    }
});

function openModal() {
    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
}

function onFileChange(event) {
    const file = event?.target?.files?.[0];
    form.value.attachment = file ?? null;
}

function clearAttachment() {
    form.value.attachment = null;
    if (attachmentInputRef.value) {
        attachmentInputRef.value.value = '';
    }
}

function submit() {
    if (!form.value.message?.trim()) {
        formErrors.value = { message: 'Le message est requis.' };
        return;
    }

    submitting.value = true;
    formErrors.value = {};

    const formData = new FormData();
    formData.append('message', form.value.message.trim());
    formData.append('type', form.value.type);
    if (form.value.url) formData.append('url', form.value.url);
    if (form.value.includePseudo && authUser.value?.name) {
        formData.append('pseudo', authUser.value.name);
    }
    if (form.value.attachment) formData.append('attachment', form.value.attachment);

    router.post(route('feedback.store'), formData, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            modalOpen.value = false;
        },
        onError: (errors) => {
            const flat = {};
            for (const [key, val] of Object.entries(errors || {})) {
                flat[key] = Array.isArray(val) ? val[0] : val;
            }
            formErrors.value = flat;
        },
        onFinish: () => {
            submitting.value = false;
        },
    });
}
</script>

<template>
    <div class="fixed bottom-4 right-4 z-40">
        <!-- Bouton FAB -->
        <div
            tabindex="0"
            role="button"
            class="btn btn-lg btn-circle btn-primary shadow-lg hover:shadow-xl transition-shadow focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-base-100"
            aria-label="Signaler un problème ou envoyer une suggestion"
            @click="openModal"
            @keydown.enter="openModal"
            @keydown.space.prevent="openModal"
        >
            <Icon source="fa-comment-dots" pack="solid" alt="Feedback" size="lg" />
        </div>

        <!-- Modal formulaire -->
        <Modal
            :open="modalOpen"
            size="lg"
            variant="glass"
            placement="middle-center"
            close-on-esc
            @close="closeModal"
        >
            <template #header>
                <h3 class="text-lg font-bold">Signaler un problème ou faire une suggestion</h3>
            </template>

            <form @submit.prevent="submit" class="space-y-4">
                <!-- Inclure mon pseudo (si connecté) -->
                <div v-if="authUser" class="form-control">
                    <label class="label cursor-pointer justify-start gap-2">
                        <input
                            v-model="form.includePseudo"
                            type="checkbox"
                            class="checkbox checkbox-primary checkbox-sm"
                        />
                        <span class="label-text">Inclure mon pseudo</span>
                    </label>
                </div>

                <!-- Type -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Type de retour</span>
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <label
                            v-for="opt in TYPE_OPTIONS"
                            :key="opt.value"
                            class="label cursor-pointer gap-2 border rounded-lg px-4 py-2 has-[:checked]:border-primary has-[:checked]:bg-primary/10"
                        >
                            <input
                                v-model="form.type"
                                type="radio"
                                :value="opt.value"
                                class="radio radio-primary radio-sm"
                            />
                            <span class="label-text">{{ opt.label }}</span>
                        </label>
                    </div>
                </div>

                <!-- Message -->
                <div class="form-control">
                    <label class="label" for="feedback-message">
                        <span class="label-text">Message</span>
                        <span class="label-text-alt text-error" v-if="formErrors.message">{{ formErrors.message }}</span>
                    </label>
                    <textarea
                        id="feedback-message"
                        v-model="form.message"
                        rows="4"
                        maxlength="2000"
                        class="textarea textarea-bordered w-full"
                        placeholder="Décris le problème ou ta suggestion..."
                        :class="{ 'textarea-error': formErrors.message }"
                    />
                    <label class="label">
                        <span class="label-text-alt">{{ (form.message?.length ?? 0) }}/2000</span>
                    </label>
                </div>

                <!-- Pièce jointe -->
                <div class="form-control">
                    <label class="label" for="feedback-attachment">
                        <span class="label-text">Pièce jointe (optionnel)</span>
                        <span class="label-text-alt text-error" v-if="formErrors.attachment">{{ formErrors.attachment }}</span>
                    </label>
                    <input
                        id="feedback-attachment"
                        ref="attachmentInputRef"
                        type="file"
                        accept=".jpg,.jpeg,.png,.gif,.pdf,.txt"
                        class="file-input file-input-bordered w-full max-w-xs"
                        @change="onFileChange"
                    />
                    <label class="label" v-if="form.attachment">
                        <span class="label-text-alt">{{ form.attachment?.name }}</span>
                        <button
                            type="button"
                            class="label-text-alt link link-hover text-error"
                            @click="clearAttachment"
                        >
                            Supprimer
                        </button>
                    </label>
                    <label class="label">
                        <span class="label-text-alt">Max 2 Mo — Images, PDF ou TXT</span>
                    </label>
                </div>
            </form>

            <template #actions>
                <div class="flex gap-2 justify-end">
                    <Btn color="neutral" variant="ghost" @click="closeModal">Annuler</Btn>
                    <Btn
                        color="primary"
                        :disabled="submitting"
                        @click="submit"
                    >
                        {{ submitting ? 'Envoi...' : 'Envoyer' }}
                    </Btn>
                </div>
            </template>
        </Modal>
    </div>
</template>
