<script setup>
import { computed } from "vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";

const props = defineProps({
    item: { type: Object, required: true },
    compact: { type: Boolean, default: false },
});

const emit = defineEmits(["open", "delete", "copy", "cancel"]);

const data = computed(() => props.item?.data || {});
const progress = computed(() => data.value?.progress || null);
const status = computed(() => String(data.value?.status || "running"));
const locked = computed(() => data.value?.locked === true);
const backendJobId = computed(() => data.value?.meta?.job_id ?? null);
const canCancel = computed(() => {
    return locked.value && !!backendJobId.value && (status.value === "running" || status.value === "cancelling");
});

const statusLabel = computed(() => {
    if (status.value === "success") return "Terminé";
    if (status.value === "error") return "Erreur";
    if (status.value === "cancelled") return "Annulé";
    if (status.value === "cancelling") return "Annulation...";
    return "En cours";
});

const statusColorClass = computed(() => {
    if (status.value === "success") return "text-success";
    if (status.value === "error") return "text-error";
    if (status.value === "cancelled") return "text-warning";
    return "text-info";
});

const progressPercent = computed(() => {
    const p = Number(progress.value?.percent);
    if (!Number.isFinite(p)) return 0;
    return Math.max(0, Math.min(100, p));
});

function openItem() {
    emit("open", props.item);
}

function deleteItem(e) {
    e?.stopPropagation?.();
    emit("delete", props.item);
}

function copyItem(e) {
    e?.stopPropagation?.();
    emit("copy", props.item);
}

function cancelItem(e) {
    e?.stopPropagation?.();
    emit("cancel", props.item);
}
</script>

<template>
    <div
        class="relative rounded-lg border border-base-content/15 bg-base-200/90 backdrop-blur-sm p-3 shadow-sm"
        :class="compact ? 'text-sm' : ''"
    >
        <button
            type="button"
            class="w-full text-left pr-20"
            @click="openItem"
        >
            <div class="flex items-center gap-2 mb-1">
                <Icon source="fa-gears" pack="solid" size="sm" alt="" class="text-primary" />
                <span class="font-semibold">Job scrapping</span>
                <span class="text-xs font-medium" :class="statusColorClass">{{ statusLabel }}</span>
            </div>
            <p class="line-clamp-2 text-base-content">{{ item.message }}</p>
            <p v-if="item.data?.run_id" class="mt-1 text-xs text-base-content/60">run_id={{ item.data.run_id }}</p>
            <div v-if="progress" class="mt-2">
                <div class="w-full h-1.5 rounded-full bg-base-300 overflow-hidden">
                    <div class="h-full bg-primary transition-all duration-200" :style="{ width: `${progressPercent}%` }" />
                </div>
                <p class="text-xs text-base-content/70 mt-1">
                    {{ progress.label || progress.phase || 'Progression' }}
                    <template v-if="Number.isFinite(Number(progress.done)) && Number.isFinite(Number(progress.total)) && Number(progress.total) > 0">
                        · {{ progress.done }}/{{ progress.total }} ({{ progressPercent }}%)
                    </template>
                </p>
            </div>
        </button>

        <div class="absolute top-2 right-2 flex items-center gap-1">
            <Btn
                v-if="canCancel"
                variant="ghost"
                size="xs"
                color="error"
                aria-label="Annuler le job"
                :disabled="status === 'cancelling'"
                @click="cancelItem"
            >
                {{ status === 'cancelling' ? 'Annulation...' : 'Annuler' }}
            </Btn>
            <Btn
                variant="ghost"
                size="xs"
                circle
                aria-label="Copier la notification"
                @click="copyItem"
            >
                <Icon source="fa-copy" pack="regular" size="xs" alt="" />
            </Btn>
            <Btn
                v-if="!locked"
                variant="ghost"
                size="xs"
                circle
                aria-label="Supprimer la notification"
                @click="deleteItem"
            >
                <Icon source="fa-times" pack="solid" size="xs" alt="" />
            </Btn>
        </div>
    </div>
</template>
