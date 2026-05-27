<script setup>
/**
 * Menu d’actions pour une ligne utilisateur (dropdown + clic droit).
 */
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import Dropdown from '@/Pages/Atoms/action/Dropdown.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';

const props = defineProps({
    user: { type: Object, required: true },
    isSuperAdmin: { type: Boolean, default: false },
    isCurrentUser: { type: Boolean, default: false },
});

const emit = defineEmits([
    'open',
    'archive',
    'restore',
    'force-delete',
    'reset-password',
]);

const contextOpen = ref(false);
const contextPosition = ref({ x: 0, y: 0 });
const menuRef = ref(null);

const actions = computed(() => {
    const u = props.user;
    const items = [];

    items.push({
        key: 'open',
        label: 'Ouvrir',
        icon: 'fa-solid fa-arrow-up-right-from-square',
        variant: 'default',
        show: true,
    });

    if (props.isSuperAdmin && !props.isCurrentUser) {
        items.push({
            key: 'reset-password',
            label: 'Réinitialiser le mot de passe',
            icon: 'fa-solid fa-key',
            variant: 'warning',
            show: true,
        });
    }

    if (u?.can?.delete && !u.deleted_at && !props.isCurrentUser) {
        items.push({
            key: 'archive',
            label: 'Archiver',
            icon: 'fa-solid fa-box-archive',
            variant: 'warning',
            show: true,
        });
    }

    if (u?.can?.restore && u.deleted_at && !props.isCurrentUser) {
        items.push({
            key: 'restore',
            label: 'Restaurer',
            icon: 'fa-solid fa-undo',
            variant: 'success',
            show: true,
        });
    }

    if (u?.can?.forceDelete && u.deleted_at && !props.isCurrentUser) {
        items.push({
            key: 'force-delete',
            label: 'Supprimer définitivement',
            icon: 'fa-solid fa-trash',
            variant: 'error',
            show: true,
        });
    }

    return items.filter((item) => item.show);
});

function runAction(key) {
    contextOpen.value = false;
    emit(key);
}

function openContextMenu(event) {
    if (!actions.value.length) {
        return;
    }
    contextPosition.value = { x: event.clientX, y: event.clientY };
    contextOpen.value = true;
    nextTick(() => menuRef.value?.querySelector('button')?.focus?.());
}

function closeContextMenu() {
    contextOpen.value = false;
}

function onDocumentClick() {
    closeContextMenu();
}

function onDocumentKeydown(event) {
    if (event.key === 'Escape') {
        closeContextMenu();
    }
}

onMounted(() => {
    document.addEventListener('click', onDocumentClick);
    document.addEventListener('keydown', onDocumentKeydown);
});

onUnmounted(() => {
    document.removeEventListener('click', onDocumentClick);
    document.removeEventListener('keydown', onDocumentKeydown);
});

watch(contextOpen, (open) => {
    if (open) {
        nextTick(() => menuRef.value?.querySelector('button')?.focus?.());
    }
});

defineExpose({ openContextMenu });
</script>

<template>
    <div class="flex justify-end" data-no-row-select>
        <Dropdown
            v-if="actions.length"
            placement="bottom-end"
            variant="glass"
            size="sm"
            :close-on-content-click="true"
            aria-label="Actions utilisateur"
        >
            <template #trigger>
                <Btn
                    size="xs"
                    color="neutral"
                    variant="ghost"
                    square
                    aria-label="Actions"
                    title="Actions"
                >
                    <i class="fa-solid fa-ellipsis-vertical" aria-hidden="true" />
                </Btn>
            </template>
            <template #content>
                <ul class="menu menu-sm w-52 p-1" role="menu">
                    <li v-for="action in actions" :key="action.key" role="none">
                        <button
                            type="button"
                            role="menuitem"
                            class="flex items-center gap-2"
                            :class="{
                                'text-warning': action.variant === 'warning',
                                'text-success': action.variant === 'success',
                                'text-error': action.variant === 'error',
                            }"
                            @click="runAction(action.key)"
                        >
                            <i class="fa-solid w-4 text-center" :class="action.icon" aria-hidden="true" />
                            {{ action.label }}
                        </button>
                    </li>
                </ul>
            </template>
        </Dropdown>

        <Teleport to="body">
            <div
                v-if="contextOpen"
                class="fixed inset-0 z-[100]"
                @contextmenu.prevent="closeContextMenu"
            >
                <ul
                    ref="menuRef"
                    role="menu"
                    class="menu menu-sm fixed z-[101] w-52 rounded-box border border-base-300 bg-base-100 p-1 shadow-lg"
                    :style="{ top: `${contextPosition.y}px`, left: `${contextPosition.x}px` }"
                    @click.stop
                    @pointerdown.stop
                >
                    <li class="px-2 py-1 text-xs opacity-60 truncate border-b border-base-300 mb-1" aria-hidden="true">
                        {{ user?.name || user?.email }}
                    </li>
                    <li v-for="action in actions" :key="`ctx-${action.key}`" role="none">
                        <button
                            type="button"
                            role="menuitem"
                            class="flex items-center gap-2"
                            :class="{
                                'text-warning': action.variant === 'warning',
                                'text-success': action.variant === 'success',
                                'text-error': action.variant === 'error',
                            }"
                            @click="runAction(action.key)"
                        >
                            <i class="fa-solid w-4 text-center" :class="action.icon" aria-hidden="true" />
                            {{ action.label }}
                        </button>
                    </li>
                </ul>
            </div>
        </Teleport>
    </div>
</template>
