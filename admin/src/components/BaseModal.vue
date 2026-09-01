<script setup lang="ts">
import { X } from 'lucide-vue-next'
withDefaults(defineProps<{open:boolean;title:string;wide?:boolean}>(),{wide:false})
const emit=defineEmits<{close:[]}>()
</script>
<template>
  <Teleport to="body">
    <div v-if="open" class="modal-backdrop" @mousedown.self="emit('close')">
      <section class="modal-card" :class="{wide}">
        <header class="modal-header"><div><slot name="eyebrow"/><h2>{{ title }}</h2></div><button class="icon-btn" @click="emit('close')"><X :size="20"/></button></header>
        <div class="modal-body"><slot/></div>
        <footer v-if="$slots.footer" class="modal-footer"><slot name="footer"/></footer>
      </section>
    </div>
  </Teleport>
</template>
