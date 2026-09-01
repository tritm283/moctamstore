export function money(value: string | number | null | undefined) {
  const n = Number(value || 0)
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND', maximumFractionDigits: 0 }).format(n)
}
export function dateTime(value?: string | null) {
  if (!value) return '—'
  const d = new Date(value)
  return Number.isNaN(d.getTime()) ? value : new Intl.DateTimeFormat('vi-VN', { dateStyle: 'short', timeStyle: 'short' }).format(d)
}
export function fileSize(bytes?: number | null) {
  if (!bytes) return '—'
  const units = ['B','KB','MB','GB']; let n=bytes; let i=0
  while(n>=1024 && i<units.length-1){n/=1024;i++}
  return `${n.toFixed(i===0?0:1)} ${units[i]}`
}
export function slugify(input:string) {
  return input.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/đ/g,'d').replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'')
}
