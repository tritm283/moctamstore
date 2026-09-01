import { defineStore } from 'pinia'
import { api } from '@/services/api'
import { TOKEN_KEY } from '@/services/http'
import type { User } from '@/types/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({ user: null as User | null, initialized: false, loading: false }),
  getters: { isAuthenticated: (s) => !!s.user && !!localStorage.getItem(TOKEN_KEY) },
  actions: {
    async initialize() {
      if (this.initialized) return
      const token = localStorage.getItem(TOKEN_KEY)
      if (!token) { this.initialized = true; return }
      try { this.user = await api.me() } catch { localStorage.removeItem(TOKEN_KEY); this.user = null }
      finally { this.initialized = true }
    },
    async login(email:string,password:string) {
      this.loading = true
      try {
        const data:any = await api.login(email,password)
        localStorage.setItem(TOKEN_KEY,data.token)
        this.user = data.user
        this.initialized = true
      } finally { this.loading = false }
    },
    async logout() {
      try { await api.logout() } catch {}
      localStorage.removeItem(TOKEN_KEY); this.user=null; this.initialized=true
    }
  }
})
