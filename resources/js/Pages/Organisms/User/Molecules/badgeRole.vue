<script setup>
import { defineProps } from "vue";
import Badge from "@/Pages/Atoms/text/Badge.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { getRoleTranslation, getRoleColor } from "@/Utils/Roles";
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
            <Badge :color="getRoleColor(props.role)" size="md">
                {{ getRoleTranslation(props.role)}}
            </Badge>
        </div>
        <template #content>
            <p>Rôle de l'utilisateur·trice</p>
        </template>
    </Tooltip>
</template>
