<template>
  <div class="p-4">
    <h1 class="text-2xl font-bold mb-4">Daftar Resep</h1>

    <!-- FILTER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
      <input
        v-model="search"
        type="text"
        placeholder="Cari resep..."
        class="border rounded px-3 py-2 w-full sm:w-1/2"
      />

      <select v-model="selectedKategori" class="border rounded px-3 py-2 w-full sm:w-1/4">
        <option value="">Semua Kategori</option>
        <option v-for="kategori in kategoriList" :key="kategori" :value="kategori">{{ kategori }}</option>
      </select>
    </div>

    <!-- LIST RESEP -->
    <div v-if="filteredRecipes.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
      <div
        v-for="resep in filteredRecipes"
        :key="resep.id"
        class="p-4 rounded-xl shadow-md border border-gray-200 bg-white hover:shadow-lg transition"
      >
        <h2 class="text-lg font-semibold mb-2">{{ resep.judul }}</h2>
        <p class="text-sm text-gray-600 mb-1">Kategori: {{ resep.kategori }}</p>
        <p class="text-sm text-gray-600 mb-1">Waktu: {{ resep.waktu }}</p>
        <p class="text-sm text-yellow-600 font-bold mb-2">Rating: {{ resep.rating }}</p>
        <p class="text-sm italic text-gray-500 mb-4">Dibuat oleh: {{ resep.creator }}</p>

        <div class="flex gap-2">
          <button @click="lihatDetail(resep)" class="btn btn-primary text-sm">Lihat</button>
          <button @click="shareRecipe(resep)" class="btn btn-secondary text-sm">Bagikan</button>
        </div>
      </div>
    </div>

    <div v-else class="text-center text-gray-500 py-10">Resep tidak ditemukan.</div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import axios from '@/libs/axios'

type Resep = {
  id: number
  title: string
  kategori: string
  waktu: string
  rating: number
  creator: string
}

const recipes = ref<Resep[]>([])
const search = ref('')
const selectedKategori = ref('')

// ✅ Ambil data dari API Laravel
onMounted(async () => {
  try {
    const res = await axios.get('/api/resep') // Ganti URL sesuai endpoint Laravel kamu
    recipes.value = res.data.data ?? res.data
  } catch (err) {
    console.error('Gagal memuat resep:', err)
  }
})

// ✅ Kategori Unik untuk Filter
const kategoriList = computed(() => {
  const set = new Set(recipes.value.map((r) => r.kategori))
  return Array.from(set)
})

// ✅ Filter pencarian + kategori
const filteredRecipes = computed(() => {
  return recipes.value.filter((r) => {
    const matchSearch = r.title.toLowerCase().includes(search.value.toLowerCase())
    const matchKategori = selectedKategori.value === '' || r.kategori === selectedKategori.value
    return matchSearch && matchKategori
  })
})

function lihatDetail(resep: Resep) {
  showToast(`Fitur lihat resep "${resep.title}" masih dalam pengembangan 😊`, 'info')
}

function shareRecipe(resep: Resep) {
  navigator.clipboard.writeText(`Cek resep "${resep.title}" di aplikasi My Recipe!`)
  showToast('Tautan resep disalin ke clipboard!', 'success')
}

function showToast(message: string, type: 'success' | 'info' | 'error') {
  const toast = document.createElement('div')
  toast.textContent = message
  toast.style.position = 'fixed'
  toast.style.bottom = '20px'
  toast.style.right = '20px'
  toast.style.padding = '12px 20px'
  toast.style.borderRadius = '8px'
  toast.style.fontWeight = '500'
  toast.style.color = '#fff'
  toast.style.boxShadow = '0 8px 25px rgba(0,0,0,0.2)'
  toast.style.zIndex = '2000'
  toast.style.opacity = '0'
  toast.style.transition = 'all 0.4s ease'

  switch (type) {
    case 'success':
      toast.style.background = 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)'
      break
    case 'info':
      toast.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
      break
    case 'error':
      toast.style.background = 'linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%)'
      break
  }

  document.body.appendChild(toast)
  requestAnimationFrame(() => {
    toast.style.opacity = '1'
    toast.style.transform = 'translateY(-10px)'
  })

  setTimeout(() => {
    toast.style.opacity = '0'
    toast.style.transform = 'translateY(0px)'
    setTimeout(() => document.body.removeChild(toast), 400)
  }, 2500)
}
</script>

<style scoped>
.btn {
  @apply px-3 py-1 rounded-lg shadow-sm transition hover:brightness-110;
}
.btn-primary {
  @apply bg-blue-600 text-white;
}
.btn-secondary {
  @apply bg-gray-600 text-white;
}
</style>
