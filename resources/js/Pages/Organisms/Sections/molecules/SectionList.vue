<script setup>
import { Link } from '@inertiajs/vue3'
import Btn from '@/Pages/Atoms/actions/Btn.vue'

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
  <div class="space-y-4">
    <div v-if="canCreate" class="flex justify-end">
      <Link :href="route('sections.create')">
        <Btn label="Créer une section" />
      </Link>
    </div>

    <div class="overflow-x-auto">
      <table class="table w-full">
        <thead>
          <tr>
            <th>Titre</th>
            <th>Page associée</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="section in sections" :key="section.uniqid">
            <td>{{ section.title }}</td>
            <td>{{ section.page?.name }}</td>
            <td>
              <Link :href="route('sections.edit', { section: section.uniqid })">
                <Btn label="Éditer" size="sm" />
              </Link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
