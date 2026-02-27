<script setup>
/**
 * LoggedHeaderContainer Molecule (Header Auth - Utilisateur connecté)
 *
 * @description
 * Molécule du design system KrosmozJDR pour l'affichage du header utilisateur connecté.
 * - À placer dans Molecules/header/ (atomicité, séparation des responsabilités)
 * - Utilisé exclusivement par Layouts/Header.vue pour la section droite du header quand l'utilisateur est connecté
 * - Gère l'affichage de l'avatar, du pseudo, du menu utilisateur, des notifications, etc.
 * - N'inclut aucune logique métier globale du header (seulement l'affichage auth)
 * - Utilise les atoms/molecules du design system (Btn, Dropdown, Avatar, Route, etc.)
 *
 * @see Layouts/Header.vue (intégration)
 *
 * @props {Aucune} (récupère l'utilisateur via usePage)
 * @slot default - (non utilisé)
 *
 * @note Ce composant ne doit être utilisé que dans le header principal.
 * @note Respecte la philosophie Atomic Design (niveau molecule, composition d'atoms).
 */
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import Avatar from "@/Pages/Atoms/data-display/Avatar.vue";
import Dropdown from "@/Pages/Atoms/action/Dropdown.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { usePage, router } from "@inertiajs/vue3";
import { ref, watch, computed, onMounted, onUnmounted, inject, nextTick } from "vue";
import { usePermissions } from "@/Composables/permissions/usePermissions";

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
}

const page = usePage();
const user = ref(page.props.auth.user);
const avatar = ref(user.value.avatar);
const pseudo = ref(user.value.name);

watch(
    () => page.props.auth.user,
    (newUser) => {
        if (newUser) {
            user.value = newUser;
            avatar.value = newUser.avatar;
            pseudo.value = newUser.name;
        }
    },
    { deep: true },
);

// Compteur : uniquement les notifications « messages » (BDD), pas les temporaires
const unreadCount = ref(page.props.auth?.notifications_unread_count ?? 0);
watch(
    () => page.props.auth?.notifications_unread_count,
    (count) => { if (count !== undefined) unreadCount.value = count; },
    { immediate: true },
);

// Liste des notifications (centre de notifications)
const notifications = ref([]);
const notificationsLoading = ref(false);

async function fetchNotifications() {
    notificationsLoading.value = true;
    try {
        const res = await fetch(route('notifications.index'), {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        });
        if (!res.ok) throw new Error(res.statusText);
        const json = await res.json();
        if (json.data) notifications.value = json.data;
        if (json.unread_count !== undefined) unreadCount.value = json.unread_count;
    } catch (_) {
        notifications.value = [];
    } finally {
        notificationsLoading.value = false;
    }
}

async function markAsRead(id) {
    const token = getCsrfToken();
    const res = await fetch(route('notifications.markAsRead', { id }), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token || '',
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
    });
    const json = await res.json();
    if (json.success && json.unread_count !== undefined) unreadCount.value = json.unread_count;
    const n = notifications.value.find((x) => x.id === id);
    if (n) n.read_at = new Date().toISOString();
}

async function markAllAsRead() {
    const token = getCsrfToken();
    const res = await fetch(route('notifications.markAllAsRead'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token || '',
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
    });
    const json = await res.json();
    if (json.success) {
        unreadCount.value = 0;
        notifications.value.forEach((n) => { n.read_at = n.read_at || new Date().toISOString(); });
    }
}

function openNotification(item) {
    if (item.url) {
        router.visit(item.url);
    }
    if (!item.read_at) markAsRead(item.id);
}

async function deleteNotification(id) {
    const token = getCsrfToken();
    const res = await fetch(route('notifications.destroy', { id }), {
        method: 'DELETE',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token || '', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
    });
    const json = await res.json();
    if (json.success) {
        notifications.value = notifications.value.filter((n) => n.id !== id);
        if (json.unread_count !== undefined) unreadCount.value = json.unread_count;
    }
}

/** Ouvre la page plein écran du centre de notifications (depuis le popover). */
function goToNotificationCenter() {
    router.visit(route('notifications.index'));
}

// Onglet actif du popover (messages = classiques, temp = temporaires)
const popoverTab = ref('messages');
const notificationStore = inject('notificationStore', null);
const temporaryHistory = computed(() => notificationStore?.temporaryHistory?.value ?? []);

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).catch(() => {});
}

function copyTemporaryItem(t) {
    copyToClipboard(t.message);
    notificationStore?.addNotification({
        message: 'Copié dans le presse-papiers',
        type: 'success',
        duration: 2500,
    });
}

function clearTemporaryHistory() {
    notificationStore?.clearTemporaryHistory();
}

// Raccourci clavier : Alt+N ouvre le popover notifications
const NOTIFICATIONS_SHORTCUT = 'n';
const notificationsDropdownRef = ref(null);
const popoverListRef = ref(null);

function handleNotificationsShortcut(e) {
    if ((e.altKey || e.metaKey) && e.key.toLowerCase() === NOTIFICATIONS_SHORTCUT) {
        e.preventDefault();
        notificationsDropdownRef.value?.open?.();
        nextTick(() => {
            const list = popoverListRef.value;
            if (list) {
                list.setAttribute('tabindex', '-1');
                list.focus();
                const first = list.querySelector('button');
                first?.focus();
            }
        });
    }
}

function handlePopoverKeydown(e) {
    const list = popoverListRef.value;
    if (!list) return;
    const focusable = [...list.querySelectorAll('button[data-keyboard-item]')];
    if (focusable.length === 0) return;
    const idx = focusable.indexOf(document.activeElement);
    const current = focusable[idx];

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        const next = focusable[Math.min(idx + 1, focusable.length - 1)];
        next?.focus();
        return;
    }
    if (e.key === 'ArrowUp') {
        e.preventDefault();
        const prev = focusable[Math.max(idx - 1, 0)];
        prev?.focus();
        return;
    }
    if (e.key === 'Delete' || e.key === 'Backspace') {
        if (!current) return;
        e.preventDefault();
        const id = current.dataset.notificationId;
        const tempId = current.dataset.tempId;
        if (id) {
            deleteNotification(Number(id));
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
        if (!current) return;
        e.preventDefault();
        const message = current.dataset.message;
        if (message) {
            copyToClipboard(message);
            notificationStore?.addNotification?.({
                message: 'Copié dans le presse-papiers',
                type: 'success',
                duration: 2500,
            });
        }
    }
}

function formatTemporaryDate(createdAt) {
    if (createdAt == null) return '';
    const d = new Date(createdAt);
    if (Number.isNaN(d.getTime())) return '';
    return formatNotificationDate(d.toISOString());
}

function formatNotificationDate(iso) {
    if (!iso) return '';
    const d = new Date(iso);
    const now = new Date();
    const diffMs = now - d;
    const diffM = Math.floor(diffMs / 60000);
    if (diffM < 1) return "À l'instant";
    if (diffM < 60) return `Il y a ${diffM} min`;
    const diffH = Math.floor(diffM / 60);
    if (diffH < 24) return `Il y a ${diffH} h`;
    const diffD = Math.floor(diffH / 24);
    if (diffD < 7) return `Il y a ${diffD} j`;
    return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
}

onMounted(() => {
    fetchNotifications();
    document.addEventListener('keydown', handleNotificationsShortcut);
});
onUnmounted(() => {
    document.removeEventListener('keydown', handleNotificationsShortcut);
});

// Vérifier si l'utilisateur est admin ou super_admin
const { canAccess } = usePermissions();

// Vérifier si l'utilisateur est game_master, admin ou super_admin
const canManagePages = computed(() => canAccess('pagesManager'));

// Fonction de déconnexion
const logout = () => {
    router.post(route('logout'));
};
</script>
<template>
    <div class="flex justify-end">
        <!-- Notifications : résumé + accès au centre -->
        <div class="flex items-center mr-6 max-sm:mr-4">
            <Dropdown ref="notificationsDropdownRef" placement="bottom-end" :close-on-content-click="false">
                <template #trigger>
                    <div class="indicator" @click="fetchNotifications">
                        <span
                            v-if="unreadCount > 0"
                            class="indicator-item badge badge-sm bg-primary text-primary-content rounded-full min-w-5"
                        >
                            {{ unreadCount > 99 ? '99+' : unreadCount }}
                        </span>
                        <Btn variant="link" color="neutral" circle aria-label="Notifications (Alt+N pour ouvrir)" title="Notifications (Alt+N)">
                            <Icon source="fa-bell" alt="Notifications" size="lg" pack="regular" />
                        </Btn>
                    </div>
                </template>
                <template #content>
                    <div class="flex flex-col min-w-80 max-w-sm max-h-96 overflow-hidden">
                        <div class="flex items-center justify-between px-3 py-2 border-glass-b-sm">
                            <span class="font-semibold text-sm text-base-content">Notifications</span>
                            <Btn
                                v-if="popoverTab === 'messages' && unreadCount > 0"
                                variant="ghost"
                                size="xs"
                                content="Tout marquer comme lu"
                                @click="markAllAsRead"
                            />
                        </div>
                        <div
                            ref="popoverListRef"
                            class="overflow-y-auto min-h-24 max-h-64 flex-1 outline-none"
                            tabindex="-1"
                            role="listbox"
                            aria-label="Liste des notifications"
                            @keydown="handlePopoverKeydown"
                        >
                            <!-- Vue par défaut : notifications classiques -->
                            <template v-if="popoverTab === 'messages'">
                                <p v-if="notificationsLoading" class="text-sm text-base-content/60 p-4 text-center">
                                    Chargement…
                                </p>
                                <template v-else-if="notifications.length === 0">
                                    <p class="text-sm text-base-content/60 p-4 text-center">
                                        Aucune notification
                                    </p>
                                </template>
                                <template v-else>
                                    <div
                                        v-for="(item, idx) in notifications"
                                        :key="item.id"
                                        class="relative group"
                                        :class="idx > 0 ? 'border-glass-t-xs' : ''"
                                    >
                                        <button
                                            type="button"
                                            role="option"
                                            data-keyboard-item
                                            :data-notification-id="item.id"
                                            :data-message="item.message"
                                            class="w-full text-left px-3 py-2.5 pr-8 text-sm flex flex-col gap-0.5 transition-colors hover:bg-base-200/50"
                                            @click="openNotification(item)"
                                        >
                                            <span class="line-clamp-2 text-base-content">{{ item.message }}</span>
                                            <span class="text-xs text-base-content/50">{{ formatNotificationDate(item.created_at) }}</span>
                                        </button>
                                        <Btn
                                            type="button"
                                            variant="ghost"
                                            size="xs"
                                            circle
                                            class="absolute top-1.5 right-1 opacity-40 hover:opacity-100 transition-opacity"
                                            aria-label="Supprimer la notification"
                                            @click.stop="deleteNotification(item.id)"
                                        >
                                            <Icon source="fa-times" pack="solid" size="xs" alt="" />
                                        </Btn>
                                    </div>
                                </template>
                            </template>
                            <!-- Vue Temporaires : flèche retour + liste -->
                            <template v-else>
                                <button
                                    type="button"
                                    role="option"
                                    class="w-full flex items-center gap-2 px-3 py-2 text-sm text-base-content/80 hover:text-base-content hover:bg-base-200/50 transition-colors border-glass-b-xs"
                                    @click.prevent="popoverTab = 'messages'"
                                >
                                    <Icon source="fa-arrow-left" pack="solid" size="sm" alt="" />
                                    <span>Retour</span>
                                </button>
                                <template v-if="temporaryHistory.length === 0">
                                    <p class="text-sm text-base-content/60 p-4 text-center">
                                        Aucune notification temporaire
                                    </p>
                                </template>
                                <template v-else>
                                    <div
                                        v-for="(t, idx) in temporaryHistory"
                                        :key="t.id"
                                        class="relative group"
                                        :class="idx > 0 ? 'border-glass-t-xs' : ''"
                                    >
                                        <button
                                            type="button"
                                            role="option"
                                            data-keyboard-item
                                            :data-temp-id="t.id"
                                            :data-message="t.message"
                                            class="w-full text-left px-3 py-2.5 pr-8 text-sm flex items-center justify-between gap-2 transition-colors hover:bg-base-200/50"
                                            @click="copyTemporaryItem(t)"
                                        >
                                            <span class="line-clamp-2 flex-1 min-w-0 text-base-content">{{ t.message }}</span>
                                            <span class="text-xs text-base-content/50 shrink-0">{{ formatTemporaryDate(t.createdAt) }}</span>
                                            <Btn
                                                variant="ghost"
                                                size="xs"
                                                circle
                                                aria-label="Copier"
                                                class="shrink-0"
                                                @click.stop="copyTemporaryItem(t)"
                                            >
                                                <Icon source="fa-copy" pack="regular" size="sm" alt="" />
                                            </Btn>
                                        </button>
                                        <Btn
                                            type="button"
                                            variant="ghost"
                                            size="xs"
                                            circle
                                            class="absolute top-1.5 right-1 opacity-40 hover:opacity-100 transition-opacity"
                                            aria-label="Supprimer la notification"
                                            @click.stop="notificationStore?.removeFromTemporaryHistory?.(t.id)"
                                        >
                                            <Icon source="fa-times" pack="solid" size="xs" alt="" />
                                        </Btn>
                                    </div>
                                </template>
                            </template>
                        </div>
                        <div class="border-glass-t-sm px-2 py-2 flex items-center justify-between gap-2">
                            <Btn
                                variant="ghost"
                                size="sm"
                                content="Voir plus"
                                class="btn-sm"
                                @click="goToNotificationCenter"
                            />
                            <template v-if="popoverTab === 'messages'">
                                <Tooltip
                                    content="Toasts affichés pendant la session (non enregistrés)."
                                    placement="top"
                                >
                                    <button
                                        type="button"
                                        class="text-xs text-base-content/60 hover:text-base-content transition-colors flex items-center gap-1"
                                        aria-label="Voir les notifications temporaires"
                                        @click.prevent="popoverTab = 'temp'"
                                    >
                                        <span>Temporaire</span>
                                        <Icon source="fa-circle-question" pack="regular" size="sm" alt="" />
                                    </button>
                                </Tooltip>
                            </template>
                            <Btn
                                v-else-if="temporaryHistory.length > 0"
                                variant="ghost"
                                size="sm"
                                circle
                                aria-label="Vider la liste des notifications temporaires"
                                title="Vider la liste"
                                class="btn-sm shrink-0"
                                @click="clearTemporaryHistory"
                            >
                                <Icon source="fa-trash" pack="regular" size="sm" alt="" />
                            </Btn>
                        </div>
                    </div>
                </template>
            </Dropdown>
        </div>
        <!-- Mon compte -->
        <div class="flex flex-col text-right">
            <Dropdown :close-on-content-click="false">
                <template #trigger>
                    <Btn color="neutral" variant="ghost">
                        <div class="flex items-center gap-2">
                            <Avatar
                                :src="user.avatar"
                                :label="user.name"
                                :alt="user.name"
                                size="md"
                            />
                            <span>{{ user.name.charAt(0).toUpperCase() + user.name.slice(1) }}</span>
                        </div>
                    </Btn>
                </template>
                <template #content>
                    <div class="flex flex-col items-start gap-2">
                        <Route route="user.show" class="w-full">
                            <Btn
                                variant="ghost"
                                size="md"
                                content="Mon compte"
                            />
                        </Route>
                        <span class="border-glass-b-sm w-full h-px"></span>
                        <template v-if="canAccess('adminPanel') || canAccess('effectsAdmin')">
                            <div class="w-full">
                                <p class="text-xs text-subtitle/60 px-2 py-1 font-semibold text-center">Administration</p>
                                <Route v-if="canAccess('adminPanel')" route="admin.characteristics.index" class="w-full">
                                    <Btn variant="ghost" size="md" class="w-full justify-start">
                                        <Icon source="fa-sliders" pack="solid" size="sm" alt="Caractéristiques" class="mr-2"/>
                                        <span>Caractéristiques</span>
                                    </Btn>
                                </Route>
                                <Route v-if="canAccess('effectsAdmin')" route="admin.effects.index" class="w-full">
                                    <Btn variant="ghost" size="md" class="w-full justify-start">
                                        <Icon source="fa-bolt" pack="solid" size="sm" alt="Effets" class="mr-2"/>
                                        <span>Effets</span>
                                    </Btn>
                                </Route>
                                <Route v-if="canAccess('adminPanel') && canAccess('scrapping')" route="scrapping.index" class="w-full">
                                    <Btn variant="ghost" size="md" class="w-full justify-start">
                                        <Icon source="fa-magnifying-glass" pack="solid" size="sm" alt="Scrapping" class="mr-2"/>
                                        <span>Scrapping</span>
                                    </Btn>
                                </Route>
                                <Route v-if="canAccess('adminPanel')" route="user.index" class="w-full">
                                    <Btn variant="ghost" size="md" class="w-full justify-start">
                                        <Icon source="fa-users" pack="solid" size="sm" alt="Utilisateurs" class="mr-2"/>
                                        <span>Utilisateurs</span>
                                    </Btn>
                                </Route>
                            </div>
                            <span class="border-glass-b-sm w-full h-px"></span>
                        </template>
                        <template v-if="canManagePages">
                            <div class="w-full">
                                <p v-if="!canAccess('adminPanel')" class="text-xs text-subtitle/60 px-2 py-1 font-semibold text-center">Gestion</p>
                                <Route route="pages.index" class="w-full">
                                    <Btn variant="ghost" size="md" class="w-full justify-start">
                                        <Icon source="fa-file-lines" pack="solid" size="sm" alt="Pages" class="mr-2"/>
                                        <span>Pages</span>
                                    </Btn>
                                </Route>
                            </div>
                            <span v-if="!canAccess('adminPanel')" class="border-glass-b-sm w-full h-px"></span>
                        </template>
                        <Btn
                            variant="ghost"
                            size="sm"
                            content="Se déconnecter"
                            @click="logout"
                        />
                    </div>
                </template>
            </Dropdown>
        </div>
    </div>
</template>
