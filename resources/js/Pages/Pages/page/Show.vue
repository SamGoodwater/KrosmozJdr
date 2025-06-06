<script setup>
import PageView from '@/Pages/Organisms/Pages/molecules/PageView.vue'

defineProps({
  page: {
    type: Object,
    required: true
  },
  canEdit: {
    type: Boolean,
    default: false
  },
  canDelete: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <PageView :page="page" :canEdit="canEdit" :canDelete="canDelete" />
</template>
