<script setup lang="ts">import { Inbox } from 'lucide-vue-next';defineProps<{title?:string;description?:string}>()</script>
<template><div class="empty-state"><div class="empty-icon"><Inbox :size="24"/></div><strong>{{title||'Chưa có dữ liệu'}}</strong><p>{{description||'Dữ liệu sẽ xuất hiện tại đây khi được tạo.'}}</p><slot/></div></template>
