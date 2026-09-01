import axios from 'axios'

export const TOKEN_KEY = 'ecommerce_admin_token'
export const http = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api/admin/v1',
  timeout: 30000,
  headers: { Accept: 'application/json' },
})
http.interceptors.request.use((config) => {
  const token = localStorage.getItem(TOKEN_KEY)
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})
http.interceptors.response.use(r=>r, error => {
  if (error.response?.status === 401) {
    localStorage.removeItem(TOKEN_KEY)
    const base = import.meta.env.BASE_URL || '/'
    if (!location.pathname.endsWith('/login')) location.assign(`${base}login`)
  }
  return Promise.reject(error)
})
