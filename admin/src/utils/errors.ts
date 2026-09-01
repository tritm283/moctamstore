import axios from 'axios'
export function errorMessage(error: unknown): string {
  if (axios.isAxiosError(error)) {
    const data = error.response?.data as any
    if (data?.errors) {
      const first = Object.values(data.errors).flat()[0]
      if (first) return String(first)
    }
    if (data?.message) return String(data.message)
  }
  return error instanceof Error ? error.message : 'Có lỗi xảy ra. Vui lòng thử lại.'
}
