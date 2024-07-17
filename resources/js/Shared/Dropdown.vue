<script setup>
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { createPopper } from '@popperjs/core'

const props = defineProps({
  placement: {
    type: String,
    default: 'bottom-end'
  },
  autoClose: {
    type: Boolean,
    default: true
  }
})

const show = ref(false)
const dropdown = ref(null)
const button = ref()
let popper = null

onMounted(() => {
  document.addEventListener('keydown', handleEscapeKey)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscapeKey)
  if (popper) {
    popper.destroy()
  }
})

watch(
  () => show.value,
  (isShown) => {
    if (isShown) {
      nextTick(() => {
        popper = createPopper(button.value, dropdown.value, {
          placement: props.placement,
          modifiers: [
            {
              name: 'preventOverflow',
              options: {
                altBoundary: true
              }
            }
          ]
        })
      })
    } else if (popper) {
      setTimeout(() => popper.destroy(), 100)
    }
  }
)

function handleEscapeKey(event) {
  if (event.key === 'Escape') {
    show.value = false
  }
}
</script>
<template>
  <button ref="button" type="button" @click="show = true">
    <slot />
    <teleport v-if="show" to="#dropdown">
      <div>
        <div
          style="position: fixed; top: 0; right: 0; left: 0; bottom: 0; z-index: 99998; background: black; opacity: 0.2"
          @click="show = false"
        />
        <div ref="dropdown" style="position: absolute; z-index: 99999" @click.stop="show = !autoClose">
          <slot name="dropdown" />
        </div>
      </div>
    </teleport>
  </button>
</template>