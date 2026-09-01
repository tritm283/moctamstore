<script setup lang="ts">
const config = useRuntimeConfig()
const health = ref('Đang kiểm tra API...')

onMounted(async () => {
  try {
    await $fetch('/up')
    health.value = 'Laravel API đang hoạt động'
  } catch {
    health.value = 'Không kết nối được Laravel API'
  }
})
</script>

<template>
  <main class="page">
    <section class="card">
      <p class="eyebrow">E-COMMERCE STOREFRONT</p>
      <h1>NuxtJS Website</h1>
      <p>
        Đây là storefront NuxtJS độc lập. Website khách hàng chạy tại <strong>/</strong>,
        còn VueJS Admin chạy tại <strong>/admin/</strong> trên cùng một domain.
      </p>
      <p class="status">{{ health }}</p>
      <p class="api">Public API: <code>{{ config.public.apiBase }}</code></p>
      <a class="admin" href="/admin/">Mở trang quản trị</a>
    </section>
  </main>
</template>

<style>
:root { font-family: Inter, ui-sans-serif, system-ui, sans-serif; color: #172033; background: #f5f7fb; }
* { box-sizing: border-box; }
body { margin: 0; }
.page { min-height: 100vh; display: grid; place-items: center; padding: 24px; }
.card { width: min(720px, 100%); padding: 42px; background: white; border: 1px solid #e7eaf0; border-radius: 20px; box-shadow: 0 18px 55px rgba(20,32,58,.08); }
.eyebrow { font-size: 12px; font-weight: 800; letter-spacing: .14em; color: #64748b; }
h1 { font-size: clamp(34px, 7vw, 58px); margin: 8px 0 16px; line-height: 1; }
p { line-height: 1.7; }
.status { margin-top: 22px; font-weight: 700; }
.api code { background: #f1f5f9; padding: 4px 7px; border-radius: 7px; }
.admin { display: inline-block; margin-top: 12px; padding: 12px 18px; border-radius: 10px; background: #172033; color: white; text-decoration: none; font-weight: 700; }
</style>
