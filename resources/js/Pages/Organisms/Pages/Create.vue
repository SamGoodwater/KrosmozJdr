<script setup>
import PageForm from '@/Pages/Organisms/Pages/molecules/PageForm.vue'

defineProps({
  pages: {
    type: Array,
    default: () => []
  }
})
</script>

<template>
  <PageForm :pages="pages" />
</template>
