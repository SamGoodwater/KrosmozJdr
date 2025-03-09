<script setup>
import SectionList from '@/Pages/Organisms/Sections/molecules/SectionList.vue'

defineProps({
  sections: {
    type: Array,
    required: true
  },
  canCreate: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <SectionList :sections="sections" :canCreate="canCreate" />
</template>
