<script setup>
import { Link } from '@inertiajs/vue3'
import Btn from '@/Pages/Atoms/actions/Btn.vue'

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
  <div class="space-y-4">
    <div v-if="canCreate" class="flex justify-end">
      <Link :href="route('pages.create')">
        <Btn label="Créer une page" />
      </Link>
    </div>

    <div class="overflow-x-auto">
      <table class="table w-full">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Slug</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="page in pages" :key="page.uniqid">
            <td>{{ page.name }}</td>
            <td>{{ page.slug }}</td>
            <td>
              <Link :href="route('pages.edit', { page: page.slug })">
                <Btn label="Éditer" size="sm" />
              </Link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
