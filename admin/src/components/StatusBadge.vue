<script setup lang="ts">
const p=defineProps<{value:string|boolean}>()
const labels:Record<string,string>={pending:'Chờ xử lý',confirmed:'Đã xác nhận',processing:'Đang xử lý',shipping:'Đang giao',completed:'Hoàn thành',cancelled:'Đã hủy',success:'Thành công',failed:'Thất bại',draft:'Bản nháp',published:'Đã đăng',archived:'Lưu trữ',true:'Đang bật',false:'Đang tắt',admin:'Admin',customer:'Khách hàng'}
const tone=(v:string)=>['completed','success','published','true'].includes(v)?'success':['cancelled','failed','false'].includes(v)?'danger':['pending','draft'].includes(v)?'warning':['shipping','processing','confirmed'].includes(v)?'info':'neutral'
</script>
<template><span class="badge" :class="`badge-${tone(String(value))}`"><span class="badge-dot"/>{{labels[String(value)]||value}}</span></template>
