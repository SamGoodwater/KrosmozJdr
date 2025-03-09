<script setup>
import { Link } from '@inertiajs/vue3'
import Btn from '@/Pages/Atoms/actions/Btn.vue'

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
  <div class="space-y-4">
    <h1 class="text-2xl font-bold">{{ section.title }}</h1>

    <div v-html="section.content"></div>

    <div v-if="canEdit || canDelete" class="flex space-x-2">
      <Link v-if="canEdit" :href="route('sections.edit', { section: section.uniqid })">
        <Btn label="Éditer" />
      </Link>

      <Btn v-if="canDelete" label="Supprimer" theme="error" @click="deleteSection" />
    </div>
  </div>
</template>
