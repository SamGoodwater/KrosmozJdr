<script setup>
import PageForm from '@/Pages/Organisms/Pages/molecules/PageForm.vue'

defineProps({
  page: {
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
  <PageForm :page="page" :pages="pages" :isUpdating="true" />
</template>
