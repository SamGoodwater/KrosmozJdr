<script setup>
import SectionForm from '@/Pages/Organisms/Sections/molecules/SectionForm.vue'

defineProps({
  pages: {
    type: Array,
    default: () => []
  }
})
</script>

<template>
  <SectionForm :pages="pages" />
</template>
