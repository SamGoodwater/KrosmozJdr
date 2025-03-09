<script setup>
import SectionForm from '@/Pages/Organisms/Sections/molecules/SectionForm.vue'

defineProps({
  section: {
    type: Object,
    required: true
  },
  pages: {
    type: Array,
    default: () => []
  }
})
</script>

<template>
  <SectionForm :section="section" :pages="pages" :isUpdating="true" />
</template>
