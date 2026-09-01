import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  { path:'/login', name:'login', component:()=>import('@/views/LoginView.vue'), meta:{guest:true,title:'Đăng nhập'} },
  { path:'/', component:()=>import('@/layouts/AdminLayout.vue'), meta:{auth:true}, children:[
    { path:'', redirect:'/dashboard' },
    { path:'dashboard', name:'dashboard', component:()=>import('@/views/DashboardView.vue'), meta:{title:'Tổng quan'} },
    { path:'products', name:'products', component:()=>import('@/views/ProductsView.vue'), meta:{title:'Sản phẩm'} },
    { path:'categories', name:'categories', component:()=>import('@/views/CategoriesView.vue'), meta:{title:'Danh mục sản phẩm'} },
    { path:'articles', name:'articles', component:()=>import('@/views/ArticlesView.vue'), meta:{title:'Bài viết'} },
    { path:'article-categories', name:'article-categories', component:()=>import('@/views/ArticleCategoriesView.vue'), meta:{title:'Chuyên mục bài viết'} },
    { path:'orders', name:'orders', component:()=>import('@/views/OrdersView.vue'), meta:{title:'Đơn hàng'} },
    { path:'payments', name:'payments', component:()=>import('@/views/PaymentsView.vue'), meta:{title:'Thanh toán'} },
    { path:'users', name:'users', component:()=>import('@/views/UsersView.vue'), meta:{title:'Khách hàng'} },
    { path:'media', name:'media', component:()=>import('@/views/MediaView.vue'), meta:{title:'Thư viện Media'} },
    { path:'homepage', name:'homepage', component:()=>import('@/views/HomepageSectionsView.vue'), meta:{title:'Bố cục trang chủ'} },
    { path:'menus', name:'menus', component:()=>import('@/views/MenusView.vue'), meta:{title:'Menu điều hướng'} },
  ]},
  { path:'/:pathMatch(.*)*', component:()=>import('@/views/NotFoundView.vue'), meta:{title:'Không tìm thấy'} }
]

const router=createRouter({ history:createWebHistory(import.meta.env.BASE_URL), routes })
router.beforeEach(async(to)=>{
  const auth=useAuthStore(); await auth.initialize()
  if (to.meta.auth && !auth.isAuthenticated) return {name:'login',query:{redirect:to.fullPath}}
  if (to.meta.guest && auth.isAuthenticated) return {name:'dashboard'}
})
router.afterEach((to)=>{ document.title=`${String(to.meta.title||'Admin')} · Ecommerce CMS` })
export default router
