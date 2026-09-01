import { http } from './http'
import type { ApiResponse, Article, ArticleCategory, Category, DashboardStats, HomepageSection, Media, Menu, Order, Paginated, Payment, Product, User } from '@/types/api'

const unwrap = <T>(p: Promise<{data:ApiResponse<T>}>) => p.then(r=>r.data.data)
export const api = {
  login: (email:string,password:string) => unwrap(http.post('/auth/login',{email,password,device_name:'vue-admin'})),
  me: () => unwrap<User>(http.get('/auth/me')),
  logout: () => unwrap(http.post('/auth/logout')),
  dashboard: () => unwrap<DashboardStats>(http.get('/dashboard')),

  categories: (params={}) => unwrap<Paginated<Category>>(http.get('/categories',{params})),
  saveCategory: (payload:any,id?:number) => unwrap<Category>(id?http.put(`/categories/${id}`,payload):http.post('/categories',payload)),
  deleteCategory: (id:number) => unwrap(http.delete(`/categories/${id}`)),

  products: (params={}) => unwrap<Paginated<Product>>(http.get('/products',{params})),
  product: (id:number) => unwrap<Product>(http.get(`/products/${id}`)),
  saveProduct: (payload:any,id?:number) => unwrap<Product>(id?http.put(`/products/${id}`,payload):http.post('/products',payload)),
  deleteProduct: (id:number) => unwrap(http.delete(`/products/${id}`)),
  uploadProductImage: (id:number,file:File) => {const f=new FormData();f.append('file',file);return unwrap<Product>(http.post(`/products/${id}/image`,f))},

  articleCategories: (params={}) => unwrap<Paginated<ArticleCategory>>(http.get('/article-categories',{params})),
  saveArticleCategory: (payload:any,id?:number) => unwrap<ArticleCategory>(id?http.put(`/article-categories/${id}`,payload):http.post('/article-categories',payload)),
  deleteArticleCategory: (id:number) => unwrap(http.delete(`/article-categories/${id}`)),

  articles: (params={}) => unwrap<Paginated<Article>>(http.get('/articles',{params})),
  article: (id:number) => unwrap<Article>(http.get(`/articles/${id}`)),
  saveArticle: (payload:any,id?:number) => unwrap<Article>(id?http.put(`/articles/${id}`,payload):http.post('/articles',payload)),
  deleteArticle: (id:number) => unwrap(http.delete(`/articles/${id}`)),
  uploadArticleThumbnail: (id:number,file:File) => {const f=new FormData();f.append('file',file);return unwrap<Article>(http.post(`/articles/${id}/thumbnail`,f))},

  orders: (params={}) => unwrap<Paginated<Order>>(http.get('/orders',{params})),
  order: (id:number) => unwrap<Order>(http.get(`/orders/${id}`)),
  orderStatus: (id:number,status:string) => unwrap<Order>(http.patch(`/orders/${id}/status`,{status})),

  payments: (params={}) => unwrap<Paginated<Payment>>(http.get('/payments',{params})),
  payment: (id:number) => unwrap<Payment>(http.get(`/payments/${id}`)),
  paymentStatus: (id:number,payload:any) => unwrap<Payment>(http.patch(`/payments/${id}/status`,payload)),

  users: (params={}) => unwrap<Paginated<User>>(http.get('/users',{params})),
  user: (id:number) => unwrap<User>(http.get(`/users/${id}`)),
  updateUser: (id:number,payload:any) => unwrap<User>(http.patch(`/users/${id}`,payload)),

  media: (params={}) => unwrap<Paginated<Media>>(http.get('/media',{params})),
  uploadMedia: (file:File) => {const f=new FormData();f.append('file',file);return unwrap<Media>(http.post('/media',f))},
  deleteMedia: (id:number) => unwrap(http.delete(`/media/${id}`)),

  sections: () => unwrap<HomepageSection[]>(http.get('/homepage-sections')),
  saveSection: (payload:any,id?:number) => unwrap<HomepageSection>(id?http.put(`/homepage-sections/${id}`,payload):http.post('/homepage-sections',payload)),
  deleteSection: (id:number) => unwrap(http.delete(`/homepage-sections/${id}`)),
  reorderSections: (items:{section_id:number;position:number}[]) => unwrap(http.post('/homepage-sections/reorder',{items})),

  menus: () => unwrap<{tree:Menu[];flat:Menu[]}>(http.get('/menus')),
  saveMenu: (payload:any,id?:number) => unwrap<Menu>(id?http.put(`/menus/${id}`,payload):http.post('/menus',payload)),
  deleteMenu: (id:number) => unwrap(http.delete(`/menus/${id}`)),
  reorderMenus: (items:{menu_id:number;parent_id:number|null;position:number}[]) => unwrap(http.post('/menus/reorder',{items})),
}
