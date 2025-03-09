<script setup>
import { Link } from '@inertiajs/vue3'
import Btn from '@/Pages/Atoms/actions/Btn.vue'

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
  <div class="space-y-4">
    <h1 class="text-2xl font-bold">{{ page.name }}</h1>

    <div v-html="page.content"></div>

    <div v-if="canEdit || canDelete" class="flex space-x-2">
      <Link v-if="canEdit" :href="route('pages.edit', { page: page.slug })">
        <Btn label="Éditer" />
      </Link>

      <Btn v-if="canDelete" label="Supprimer" theme="error" @click="deletePage" />
    </div>
  </div>
</template>
