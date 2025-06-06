<script setup>
import PageList from '@/Pages/Organisms/Pages/molecules/PageList.vue'

defineProps({
  pages: {
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
  <PageList :pages="pages" :canCreate="canCreate" />
</template>
