<script setup>
import { useForm } from '@inertiajs/vue3'
import TextInput from '@/Pages/Atoms/inputs/TextInput.vue'
import Textarea from '@/Pages/Atoms/inputs/Textarea.vue'
import Select from '@/Pages/Atoms/inputs/Select.vue'
import FileInput from '@/Pages/Atoms/inputs/FileInput.vue'
import Btn from '@/Pages/Atoms/actions/Btn.vue'

const props = defineProps({
  page: {
    type: Object,
    default: () => ({})
  },
  isUpdating: {
    type: Boolean,
    default: false
  },
  pages: {
    type: Array,
    default: () => []
  }
})

const form = useForm({
  name: props.page.name || '',
  slug: props.page.slug || '',
  content: props.page.content || '',
  is_public: props.page.is_public || false,
  page_id: props.page.page_id || null,
  image: null
})

const submit = () => {
  if (props.isUpdating) {
    form.put(route('pages.update', { page: props.page.uniqid }))
  } else {
    form.post(route('pages.store'))
  }
}
</script>

<template>
  <form @submit.prevent="submit" class="space-y-4">
    <TextInput
      v-model="form.name"
      label="Nom de la page"
      :error="form.errors.name"
    />

    <Textarea
      v-model="form.content"
      label="Contenu"
      :error="form.errors.content"
    />

    <Select
      v-model="form.page_id"
      label="Page parente"
      :options="pages.map(p => ({ value: p.uniqid, label: p.name }))"
      :error="form.errors.page_id"
    />

    <FileInput
      v-model="form.image"
      label="Image"
      :error="form.errors.image"
    />

    <Btn type="submit" :disabled="form.processing">
      {{ isUpdating ? 'Mettre à jour' : 'Créer' }}
    </Btn>
  </form>
</template>
