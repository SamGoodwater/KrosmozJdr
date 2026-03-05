<script setup>
/**
 * Page Centre de notifications.
 * - Onglets : Notifications messages (BDD, lu/archivé/épinglé, copier, supprimer) et Notifications temporaires (toasts de la session, copier, vider).
 * - Lien vers la page Paramètres du compte (onglet Notifications).
 */
import { ref, computed, inject, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import Tab from '@/Pages/Molecules/navigation/Tab.vue';
import TabItem from '@/Pages/Atoms/navigation/TabItem.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Route from '@/Pages/Atoms/action/Route.vue';
import Dropdown from '@/Pages/Atoms/action/Dropdown.vue';
import ScrappingJobNotificationCard from '@/Pages/Molecules/feedback/ScrappingJobNotificationCard.vue';

const page = usePage();
const activeTab = ref('messages');
const showArchived = ref(false);

const notificationStore = inject('notificationStore', null);
const temporaryHistory = computed(() => notificationStore?.temporaryHistory?.value ?? []);

const messages = ref([]);
const messagesMeta = ref({ current_page: 1, last_page: 1, total: 0 });
const messagesLoading = ref(false);
const unreadCount = ref(page.props.unreadCount ?? 0);
const lockPulseNotificationId = ref(null);

function triggerLockPulse(notificationId) {
    if (!notificationId) return;
    lockPulseNotificationId.value = String(notificationId);
    setTimeout(() => {
        if (lockPulseNotificationId.value === String(notificationId)) {
            lockPulseNotificationId.value = null;
        }
    }, 900);
}

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
}

async function fetchMessages() {
    messagesLoading.value = true;
    try {
        const url = new URL(route('notifications.index'));
        url.searchParams.set('per_page', 15);
        url.searchParams.set('page', messagesMeta.value.current_page);
        if (showArchived.value) url.searchParams.set('archived', '1');
        const res = await fetch(url, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
        const json = await res.json();
        if (json.data) messages.value = json.data;
        if (json.unread_count !== undefined) unreadCount.value = json.unread_count;
        if (json.meta) messagesMeta.value = json.meta;
    } catch {
        messages.value = [];
    } finally {
        messagesLoading.value = false;
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).catch(() => {});
}

async function markAsRead(id) {
    const token = getCsrfToken();
    await fetch(route('notifications.markAsRead', { id }), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token || '', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
    });
    const n = messages.value.find((x) => x.id === id);
    if (n) n.read_at = new Date().toISOString();
    unreadCount.value = Math.max(0, unreadCount.value - 1);
}

async function archiveNotification(id) {
    const token = getCsrfToken();
    const res = await fetch(route('notifications.archive', { id }), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token || '', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
    });
    const json = await res.json();
    if (json.success) {
        messages.value = messages.value.filter((n) => n.id !== id);
        if (json.unread_count !== undefined) unreadCount.value = json.unread_count;
    }
}

async function pinNotification(id) {
    const token = getCsrfToken();
    await fetch(route('notifications.pin', { id }), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token || '', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
    });
    fetchMessages();
}

async function unpinNotification(id) {
    const token = getCsrfToken();
    await fetch(route('notifications.unpin', { id }), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token || '', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
    });
    fetchMessages();
}

async function deleteNotification(id) {
    const current = messages.value.find((n) => String(n.id) === String(id));
    if (current?.is_scrapping_job && current?.data?.locked === true) {
        triggerLockPulse(id);
        return;
    }
    const token = getCsrfToken();
    const res = await fetch(route('notifications.destroy', { id }), {
        method: 'DELETE',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token || '', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
    });
    const json = await res.json();
    if (json.success) {
        messages.value = messages.value.filter((n) => n.id !== id);
        if (json.unread_count !== undefined) unreadCount.value = json.unread_count;
    }
}

async function cancelScrappingJobNotification(item) {
    const token = getCsrfToken();
    const jobId = item?.data?.meta?.job_id;
    if (!jobId) return;
    try {
        await fetch(`/api/scrapping/jobs/${encodeURIComponent(jobId)}/cancel`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token || '', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            body: JSON.stringify({}),
        });
        await fetch(route('notifications.scrapping.update', { id: item.id }), {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token || '', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            body: JSON.stringify({
                status: 'cancelled',
                message: 'Scrapping annulé depuis le centre de notifications.',
                progress: { ...(item?.data?.progress || {}) },
            }),
        });
        fetchMessages();
    } catch {
        notificationStore?.addNotification?.({ message: "Impossible d'annuler le job pour le moment.", type: 'error', duration: 3000 });
    }
}

function openMessage(item) {
    if (item.url) router.visit(item.url);
    if (!item.read_at) markAsRead(item.id);
}

function formatDate(iso) {
    if (iso == null || iso === '') return '';
    const d = new Date(iso);
    if (Number.isNaN(d.getTime())) return '';
    return d.toLocaleString('fr-FR', { dateStyle: 'short', timeStyle: 'short' });
}

function copyMessageContent(item) {
    const text = [item.message, item.url].filter(Boolean).join('\n');
    copyToClipboard(text);
}

function copyTemporaryContent(t) {
    copyToClipboard(t.message);
    notificationStore?.addNotification?.({ message: 'Copié dans le presse-papiers', type: 'success', duration: 2500 });
}

function handleCentreListKeydown(e) {
    const list = e.currentTarget;
    const focusable = [...list.querySelectorAll('[data-keyboard-item]')];
    if (focusable.length === 0) return;
    const idx = focusable.indexOf(document.activeElement);

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        focusable[Math.min(idx + 1, focusable.length - 1)]?.focus();
        return;
    }
    if (e.key === 'ArrowUp') {
        e.preventDefault();
        focusable[Math.max(idx - 1, 0)]?.focus();
        return;
    }
    if (e.key === 'Delete' || e.key === 'Backspace') {
        const el = document.activeElement;
        if (!el || focusable.indexOf(el) === -1) return;
        e.preventDefault();
        const notifId = el.dataset.notificationId;
        const tempId = el.dataset.tempId;
        if (notifId) {
            const notif = messages.value.find((n) => String(n.id) === String(notifId));
            if (notif?.is_scrapping_job && notif?.data?.locked === true) {
                triggerLockPulse(notifId);
                return;
            }
            deleteNotification(notifId);
            const nextIdx = Math.min(idx, focusable.length - 2);
            focusable[nextIdx >= 0 ? nextIdx : 0]?.focus();
        } else if (tempId) {
            notificationStore?.removeFromTemporaryHistory?.(Number(tempId));
            const nextIdx = Math.min(idx, focusable.length - 2);
            focusable[nextIdx >= 0 ? nextIdx : 0]?.focus();
        }
        return;
    }
    if (e.key === 'c' && (e.ctrlKey || e.metaKey)) {
        const el = document.activeElement;
        if (!el || focusable.indexOf(el) === -1) return;
        e.preventDefault();
        const message = el.dataset.message;
        if (message) {
            copyToClipboard(message);
            notificationStore?.addNotification?.({ message: 'Copié dans le presse-papiers', type: 'success', duration: 2500 });
        }
    }
}

function clearTemporaryHistory() {
    notificationStore?.clearTemporaryHistory();
}

onMounted(() => { fetchMessages(); });
</script>

<template>
    <div class="container mx-auto px-4 py-6 max-w-4xl">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold">Centre de notifications</h1>
            <Route :href="route('user.settings') + '#notifications'" class="btn btn-ghost btn-sm gap-2">
                <Icon source="fa-cog" pack="solid" size="sm" alt="" />
                Paramètres de notification
            </Route>
        </div>

        <Tab variant="lift" size="md" class="mb-4">
            <TabItem
                :active="activeTab === 'messages'"
                label="Notifications"
                icon="fa-envelope"
                @click.prevent="activeTab = 'messages'"
            />
            <TabItem
                :active="activeTab === 'temp'"
                label="Notifications temporaires"
                icon="fa-clock-rotate-left"
                class="tab-sm text-base-content/70"
                @click.prevent="activeTab = 'temp'"
            />
        </Tab>

        <!-- Vue Notifications (classiques) -->
        <div v-show="activeTab === 'messages'" class="space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <label class="label cursor-pointer gap-2">
                    <input
                        v-model="showArchived"
                        type="checkbox"
                        class="checkbox checkbox-sm"
                        @change="fetchMessages"
                    />
                    <span class="label-text">Voir les archivées</span>
                </label>
            </div>
            <p v-if="messagesLoading" class="text-sm text-base-content/60 py-4">Chargement…</p>
            <template v-else-if="messages.length === 0">
                <p class="text-sm text-base-content/60 py-4">Aucune notification.</p>
            </template>
            <template v-else>
                <div
                    ref="messagesListRef"
                    class="outline-none"
                    tabindex="-1"
                    role="listbox"
                    aria-label="Liste des notifications"
                    @keydown="handleCentreListKeydown"
                >
                <ul class="space-y-2">
                    <li
                        v-for="item in messages"
                        :key="item.id"
                        class="relative border border-base-300 rounded-lg p-3 flex flex-col gap-2"
                        :class="{ 'bg-primary/5': !item.read_at }"
                    >
                        <template v-if="item.is_scrapping_job">
                            <ScrappingJobNotificationCard
                                :item="item"
                                :lock-pulse="lockPulseNotificationId === String(item.id)"
                                @open="openMessage(item)"
                                @copy="copyMessageContent(item)"
                                @cancel="cancelScrappingJobNotification(item)"
                                @delete="deleteNotification(item.id)"
                            />
                        </template>
                        <template v-else>
                        <Btn
                            variant="ghost"
                            size="xs"
                            circle
                            class="absolute top-2 right-2 opacity-40 hover:opacity-100 transition-opacity"
                            aria-label="Supprimer la notification"
                            @click.stop="deleteNotification(item.id)"
                        >
                            <Icon source="fa-times" pack="solid" size="xs" alt="" />
                        </Btn>
                        <div class="flex items-start justify-between gap-2 pr-6">
                            <button
                                type="button"
                                role="option"
                                data-keyboard-item
                                :data-notification-id="item.id"
                                :data-message="item.message"
                                class="text-left flex-1 min-w-0"
                                @click="openMessage(item)"
                            >
                                <span class="line-clamp-2">{{ item.message }}</span>
                                <span class="text-xs text-base-content/50">{{ formatDate(item.created_at) }}</span>
                            </button>
                            <div class="flex items-center gap-1 shrink-0">
                                <Btn
                                    variant="ghost"
                                    size="xs"
                                    circle
                                    aria-label="Copier"
                                    @click.stop="copyMessageContent(item)"
                                >
                                    <Icon source="fa-copy" pack="regular" size="sm" alt="" />
                                </Btn>
                                <Dropdown placement="bottom-end">
                                    <template #trigger>
                                        <Btn variant="ghost" size="xs" circle aria-label="Actions">
                                            <Icon source="fa-ellipsis-vertical" pack="solid" size="sm" alt="" />
                                        </Btn>
                                    </template>
                                    <template #content>
                                        <div class="flex flex-col p-1">
                                            <Btn
                                                v-if="item.pinned_at"
                                                variant="ghost"
                                                size="sm"
                                                class="justify-start gap-2"
                                                @click="unpinNotification(item.id)"
                                            >
                                                <Icon source="fa-thumbtack" pack="solid" size="sm" alt="" />
                                                Désépingler
                                            </Btn>
                                            <Btn
                                                v-else
                                                variant="ghost"
                                                size="sm"
                                                class="justify-start gap-2"
                                                @click="pinNotification(item.id)"
                                            >
                                                <Icon source="fa-thumbtack" pack="solid" size="sm" alt="" />
                                                Épingler
                                            </Btn>
                                            <Btn
                                                variant="ghost"
                                                size="sm"
                                                class="justify-start gap-2"
                                                @click="archiveNotification(item.id)"
                                            >
                                                <Icon source="fa-archive" pack="solid" size="sm" alt="" />
                                                Archiver
                                            </Btn>
                                            <Btn
                                                variant="ghost"
                                                size="sm"
                                                color="error"
                                                class="justify-start gap-2"
                                                @click="deleteNotification(item.id)"
                                            >
                                                <Icon source="fa-trash" pack="solid" size="sm" alt="" />
                                                Supprimer
                                            </Btn>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                        <div v-if="item.pinned_at" class="flex items-center gap-1 text-xs text-primary">
                            <Icon source="fa-thumbtack" pack="solid" size="xs" alt="" />
                            Épinglée
                        </div>
                        </template>
                    </li>
                </ul>
                <div v-if="messagesMeta.last_page > 1" class="flex justify-center gap-2 pt-4">
                    <Btn
                        variant="outline"
                        size="sm"
                        :disabled="messagesMeta.current_page <= 1"
                        @click="messagesMeta.current_page--; fetchMessages();"
                    >
                        Précédent
                    </Btn>
                    <span class="self-center text-sm">{{ messagesMeta.current_page }} / {{ messagesMeta.last_page }}</span>
                    <Btn
                        variant="outline"
                        size="sm"
                        :disabled="messagesMeta.current_page >= messagesMeta.last_page"
                        @click="messagesMeta.current_page++; fetchMessages();"
                    >
                        Suivant
                    </Btn>
                </div>
                </div>
            </template>
        </div>

        <!-- Vue Notifications temporaires -->
        <div v-show="activeTab === 'temp'" class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2 border-glass-b-sm pb-3">
                <button
                    type="button"
                    class="flex items-center gap-2 text-sm text-base-content/80 hover:text-base-content transition-colors"
                    @click.prevent="activeTab = 'messages'"
                >
                    <Icon source="fa-arrow-left" pack="solid" size="sm" alt="" />
                    <span>Retour aux notifications</span>
                </button>
                <Btn
                    variant="outline"
                    size="sm"
                    content="Vider la liste"
                    @click="clearTemporaryHistory"
                />
            </div>
            <p class="text-sm text-base-content/60">
                Les notifications temporaires sont les toasts affichés pendant ta session. Elles ne sont pas enregistrées.
            </p>
            <template v-if="temporaryHistory.length === 0">
                <p class="text-sm text-base-content/60 py-4">Aucune notification temporaire.</p>
            </template>
            <div
                v-else
                ref="tempListRef"
                class="outline-none"
                tabindex="-1"
                role="listbox"
                aria-label="Liste des notifications temporaires"
                @keydown="handleCentreListKeydown"
            >
                <ul class="space-y-2">
                    <li
                        v-for="t in temporaryHistory"
                        :key="t.id"
                        class="relative border border-base-300 rounded-lg p-3 flex items-center justify-between gap-2"
                    >
                        <Btn
                            variant="ghost"
                            size="xs"
                            circle
                            class="absolute top-2 right-2 opacity-40 hover:opacity-100 transition-opacity shrink-0"
                            aria-label="Supprimer la notification"
                            @click.stop="notificationStore?.removeFromTemporaryHistory?.(t.id)"
                        >
                            <Icon source="fa-times" pack="solid" size="xs" alt="" />
                        </Btn>
                        <button
                            type="button"
                            role="option"
                            data-keyboard-item
                            :data-temp-id="t.id"
                            :data-message="t.message"
                            class="text-left flex-1 min-w-0 flex items-center justify-between gap-2 pr-8"
                            @click="copyTemporaryContent(t)"
                        >
                            <span class="line-clamp-2 flex-1 min-w-0">{{ t.message }}</span>
                            <span class="text-xs text-base-content/50 shrink-0">{{ formatDate(t.createdAt) }}</span>
                        </button>
                        <Btn
                            variant="ghost"
                            size="xs"
                            circle
                            aria-label="Copier"
                            @click="copyTemporaryContent(t)"
                        >
                            <Icon source="fa-copy" pack="regular" size="sm" alt="" />
                        </Btn>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
