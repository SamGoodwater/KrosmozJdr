<script setup>
import { useForm } from '@inertiajs/vue3'
import TextInput from '@/Pages/Atoms/inputs/TextInput.vue'
import Textarea from '@/Pages/Atoms/inputs/Textarea.vue'
import Select from '@/Pages/Atoms/inputs/Select.vue'
import FileInput from '@/Pages/Atoms/inputs/FileInput.vue'
import Btn from '@/Pages/Atoms/actions/Btn.vue'

const props = defineProps({
  section: {
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
  title: props.section.title || '',
  content: props.section.content || '',
  page_id: props.section.page_id || null,
  image: null
})

const submit = () => {
  if (props.isUpdating) {
    form.put(route('sections.update', { section: props.section.uniqid }))
  } else {
    form.post(route('sections.store'))
  }
}
</script>

<template>
  <form @submit.prevent="submit" class="space-y-4">
    <TextInput
      v-model="form.title"
      label="Titre"
      :error="form.errors.title"
    />

    <Textarea
      v-model="form.content"
      label="Contenu"
      :error="form.errors.content"
    />

    <Select
      v-model="form.page_id"
      label="Page associée"
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
