<script setup>
import { defineProps } from "vue";
import Badge from "@/Pages/Atoms/text/Badge.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";

const props = defineProps({
    role: {
        type: String,
        default: "user",
    },
});
</script>

<template>
    <Tooltip placement="bottom">
        <div>
            <Badge theme="color-auto" :color="props.role" size="md">
                {{ props.role.charAt(0).toUpperCase() + props.role.slice(1) }}
            </Badge>
        </div>
        <template #content>
            <p>Rôle de l'utilisateur·trice</p>
        </template>
    </Tooltip>
</template>
