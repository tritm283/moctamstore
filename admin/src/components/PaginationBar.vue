<script setup lang="ts">
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
const p=defineProps<{page:number;lastPage:number;total?:number}>()
const emit=defineEmits<{change:[page:number]}>()
</script>
<template>
<div class="pagination"><span>{{ total ?? 0 }} bản ghi · Trang {{page}}/{{lastPage||1}}</span><div><button class="btn btn-secondary btn-sm" :disabled="page<=1" @click="emit('change',page-1)"><ChevronLeft :size="16"/> Trước</button><button class="btn btn-secondary btn-sm" :disabled="page>=lastPage" @click="emit('change',page+1)">Sau <ChevronRight :size="16"/></button></div></div>
</template>
