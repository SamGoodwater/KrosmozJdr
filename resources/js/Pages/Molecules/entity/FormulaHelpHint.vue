<script setup>
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { FORMULA_TOOLTIP_TEXT } from '@/Utils/entity/formula-help';

const props = defineProps({
    placement: {
        type: String,
        default: 'bottom-start',
    },
});
</script>

<template>
    <Tooltip :content="FORMULA_TOOLTIP_TEXT" :placement="props.placement">
        <button
            type="button"
            class="inline-flex items-center gap-2 text-sm text-primary-200 hover:text-primary-100 transition-colors"
        >
            <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
            Aide formules
        </button>
    </Tooltip>
</template>

