<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import {
  LayoutDashboard, Package, Tags, Newspaper, FolderTree, ShoppingBag, CreditCard,
  Users, Images, PanelsTopLeft, Menu as MenuIcon, LogOut, PanelLeftClose, PanelLeftOpen, Search
} from 'lucide-vue-next'
const auth=useAuthStore(); const router=useRouter(); const collapsed=ref(false); const mobileOpen=ref(false)
const nav=[
  {to:'/dashboard',label:'Tổng quan',icon:LayoutDashboard},
  {section:'Bán hàng'},
  {to:'/orders',label:'Đơn hàng',icon:ShoppingBag},
  {to:'/payments',label:'Thanh toán',icon:CreditCard},
  {to:'/products',label:'Sản phẩm',icon:Package},
  {to:'/categories',label:'Danh mục SP',icon:Tags},
  {section:'Nội dung'},
  {to:'/articles',label:'Bài viết',icon:Newspaper},
  {to:'/article-categories',label:'Chuyên mục',icon:FolderTree},
  {to:'/media',label:'Media',icon:Images},
  {section:'Khách hàng & giao diện'},
  {to:'/users',label:'Khách hàng',icon:Users},
  {to:'/homepage',label:'Trang chủ',icon:PanelsTopLeft},
  {to:'/menus',label:'Menu',icon:MenuIcon},
]
async function logout(){await auth.logout();router.replace('/login')}
</script>
<template>
<div class="admin-shell" :class="{collapsed}">
  <aside class="sidebar" :class="{mobileOpen}">
    <div class="brand"><div class="brand-mark">EC</div><div class="brand-copy"><strong>Ecommerce CMS</strong><span>Admin Console</span></div></div>
    <nav class="nav-list">
      <template v-for="(item,i) in nav" :key="i">
        <div v-if="item.section" class="nav-section">{{item.section}}</div>
        <router-link v-else :to="item.to!" class="nav-item" @click="mobileOpen=false"><component :is="item.icon" :size="19"/><span>{{item.label}}</span></router-link>
      </template>
    </nav>
    <div class="sidebar-bottom"><button class="nav-item nav-button" @click="logout"><LogOut :size="19"/><span>Đăng xuất</span></button></div>
  </aside>
  <div v-if="mobileOpen" class="mobile-overlay" @click="mobileOpen=false"/>
  <main class="main-area">
    <header class="topbar">
      <div class="topbar-left"><button class="icon-btn desktop-toggle" @click="collapsed=!collapsed"><PanelLeftOpen v-if="collapsed" :size="20"/><PanelLeftClose v-else :size="20"/></button><button class="icon-btn mobile-toggle" @click="mobileOpen=!mobileOpen"><MenuIcon :size="21"/></button><div class="global-search"><Search :size="17"/><span>Quản trị hệ thống</span></div></div>
      <div class="admin-profile"><div class="avatar">{{auth.user?.full_name?.charAt(0)?.toUpperCase()||'A'}}</div><div><strong>{{auth.user?.full_name||'Administrator'}}</strong><span>{{auth.user?.email}}</span></div></div>
    </header>
    <section class="content-wrap"><router-view/></section>
  </main>
</div>
</template>
