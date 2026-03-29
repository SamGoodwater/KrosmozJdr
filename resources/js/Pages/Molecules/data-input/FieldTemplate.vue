<script setup>
import { computed } from 'vue'
import InputLabel from '@/Pages/Atoms/data-input/InputLabel.vue'
import Validator from '@/Pages/Atoms/data-input/Validator.vue'
import Helper from '@/Pages/Atoms/data-input/Helper.vue'
import Btn from '@/Pages/Atoms/action/Btn.vue'

const props = defineProps({
  // API du composable useInputField
  containerClasses: { type: String, required: true },
  labelConfig: { type: Object, required: true },
  inputAttrs: { type: Object, required: true },
  listeners: { type: Object, required: true },
  inputRef: { type: [Object, null], default: null }, // Accept ref or null
  actionsToDisplay: { type: Array, required: true },
  styleProperties: { type: Object, required: true },
  validationState: { type: [String, null], default: null }, // Accept String or null
  validationMessage: { type: [String, null], default: null }, // Accept String or null
  helper: { type: [String, Object], default: '' },
  /** @type {'input'|'textarea'|'select'|string} — Ajuste l’alignement flex et la position des actions (textarea = plusieurs lignes). */
  inputType: { type: String, default: 'input' },
})

// Classes dynamiques pour le bloc principal selon le type d'input
const mainBlockClasses = computed(() => {
  const baseClasses = 'relative'
  
  // Types d'inputs avec taille fixe (pas de flex-1)
  const fixedSizeTypes = ['checkbox', 'radio', 'toggle', 'rating']
  
  if (fixedSizeTypes.includes(props.inputType)) {
    return baseClasses
  }
  
  // Types d'inputs avec taille dynamique (avec flex-1)
  return `${baseClasses} flex-1`
})
</script> 

<template>
  <div :class="containerClasses">
    <!-- 🔼 Label au-dessus -->
    <InputLabel
      v-if="labelConfig.top || $slots.labelTop"
      :value="labelConfig.top"
      :for="inputAttrs.id"
      :color="styleProperties.labelColor"
      :size="styleProperties.labelSize"
    >
      <slot name="labelTop" />
    </InputLabel>

    <div
      class="relative flex w-full"
      :class="props.inputType === 'textarea' ? 'items-start' : 'items-center'"
    >
      <!-- ⬅️ Label à gauche -->
      <InputLabel
        v-if="labelConfig.start || $slots.labelStart"
        :value="labelConfig.start"
        :for="inputAttrs.id"
        :color="styleProperties.labelColor"
        :size="styleProperties.labelSize"
        class="mr-2"
      >
        <slot name="labelStart" />
      </InputLabel>

      <!-- 🧱 Bloc principal : input + actions -->
      <div :class="mainBlockClasses">
        <!-- Slot pour le composant Core spécifique -->
        <slot 
          name="core" 
          :input-attrs="inputAttrs"
          :listeners="listeners"
          :input-ref="inputRef"
        />

        <!-- 🎯 Actions overStart -->
        <div
          v-if="$slots.overStart"
          class="absolute left-2 z-10 flex gap-1"
          :class="inputType === 'textarea' ? 'top-2' : 'top-1/2 -translate-y-1/2'"
        >
          <slot name="overStart" />
        </div>

        <!-- 🎯 Actions overEnd + contextuelles -->
        <div
          v-if="$slots.overEnd || actionsToDisplay.length"
          class="absolute right-2 z-10 flex items-center gap-1"
          :class="inputType === 'textarea' ? 'top-2' : 'top-1/2 -translate-y-1/2'"
        >
          <slot name="overEnd" />
          <Btn
            v-for="action in actionsToDisplay"
            :key="action.key"
            :variant="action.variant"
            :color="action.color"
            :size="action.size"
            circle
            :aria-label="action.ariaLabel"
            :title="action.tooltip"
            :disabled="action.disabled"
            tabindex="0"
            @click.stop="action.onClick"
          >
            <i :class="action.icon" class="text-sm"></i>
          </Btn>
        </div>
      </div>

      <!-- ➡️ Label à droite -->
      <InputLabel
        v-if="labelConfig.end || $slots.labelEnd"
        :value="labelConfig.end"
        :for="inputAttrs.id"
        :color="styleProperties.labelColor"
        :size="styleProperties.labelSize"
        class="ml-2"
      >
        <slot name="labelEnd" />
      </InputLabel>
    </div>

    <!-- 🔽 Label en-dessous -->
    <InputLabel
      v-if="labelConfig.bottom || $slots.labelBottom"
      :value="labelConfig.bottom"
      :for="inputAttrs.id"
      :color="styleProperties.labelColor"
      :size="styleProperties.labelSize"
      class="mt-1"
    >
      <slot name="labelBottom" />
    </InputLabel>

    <!-- ⚠️ Validation -->
    <div v-if="validationState && validationMessage" class="mt-1">
      <slot name="validator">
        <Validator
          :state="validationState"
          :message="validationMessage"
          :color="styleProperties.validationColor"
        />
      </slot>
    </div>

    <!-- 💡 Helper -->
    <div v-if="helper || $slots.helper" class="mt-1">
      <slot name="helper">
        <Helper :value="helper" :color="styleProperties.helperColor" />
      </slot>
    </div>
  </div>
</template>
