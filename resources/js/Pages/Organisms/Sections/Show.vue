<script setup>
import SectionView from '@/Pages/Organisms/Sections/molecules/SectionView.vue'

defineProps({
  section: {
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
  <SectionView :section="section" :canEdit="canEdit" :canDelete="canDelete" />
</template>
