<script setup lang="ts">import type {Component} from 'vue';defineProps<{label:string;value:string|number;hint?:string;icon:Component}>()</script>
<template><div class="stat-card"><div class="stat-icon"><component :is="icon" :size="22"/></div><div><span>{{label}}</span><strong>{{value}}</strong><small v-if="hint">{{hint}}</small></div></div></template>
