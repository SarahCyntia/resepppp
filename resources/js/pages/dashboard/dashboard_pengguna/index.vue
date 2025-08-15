<script setup lang="ts">
import { h, ref, watch } from "vue"
import { useDelete } from "@/libs/hooks"
import Form from "./Form.vue"
import { createColumnHelper } from "@tanstack/vue-table"
import type { Resep } from "@/types"
import { computed } from 'vue'
import axios from "axios"
import { useRouter } from "vue-router"
import Swal from "sweetalert2"

const column = createColumnHelper<Resep>()
const paginateRef = ref<any>(null)
const selected = ref<string>("")
const openForm = ref<boolean>(false)
const isLoading = ref(false)
const searchQuery = ref("")
const selectedCategory = ref("")

const { delete: deleteResep } = useDelete({
  onSuccess: () => {
    paginateRef.value.refetch()
    showNotification("Resep berhasil dihapus!", "success")
  },
})

const showNotification = (message: string, type: 'success' | 'error' | 'info') => {
  console.log(`${type.toUpperCase()}: ${message}`)
}

const columns = [
  column.accessor("id", {
    header: () => h('div', { class: 'table-header-cell' }, [
      h('span', { class: 'header-icon' }, '📋'),
      h('span', 'No')
    ])
  }),
  column.accessor("judul", {
    header: () => h('div', { class: 'table-header-cell' }, [
      h('span', { class: 'header-icon' }, '🍽️'),
      h('span', 'Nama Resep')
    ]),
    cell: (cell) => h('div', { class: 'recipe-title-cell' }, [
      h('span', { class: 'recipe-title-text' }, cell.getValue())
    ])
  }),
  column.accessor("deskripsi", {
    header: () => h('div', { class: 'table-header-cell' }, [
      h('span', { class: 'header-icon' }, '📝'),
      h('span', 'Deskripsi')
    ]),
    cell: (cell) => {
      const description = cell.getValue() || "";
      const truncated = description.length > 80 ? description.substring(0, 80) + "..." : description;
      return h('div', { 
        class: 'description-cell',
        title: description
      }, truncated);
    }
  }),
  column.accessor("bahan", {
    header: () => h('div', { class: 'table-header-cell' }, [
      h('span', { class: 'header-icon' }, '🥘'),
      h('span', 'Bahan')
    ]),
    cell: (cell) => {
      const bahan = cell.getValue() || [];
      const maxShow = 2;
      return h('div', { class: 'ingredients-cell' }, [
        ...bahan.slice(0, maxShow).map((b: any, i: number) =>
          h('div', { class: 'ingredient-item' }, [
            h('span', { class: 'ingredient-bullet' }, '•'),
            h('span', { class: 'ingredient-name' }, b.nama)
          ])
        ),
        bahan.length > maxShow ? h('div', { 
          class: 'more-items-coffee',
          title: `+${bahan.length - maxShow} bahan lainnya`
        }, `+${bahan.length - maxShow} lagi`) : null
      ]);
    },
  }),
  column.accessor("waktu_masak", {
    header: () => h('div', { class: 'table-header-cell' }, [
      h('span', { class: 'header-icon' }, '⏰'),
      h('span', 'Waktu')
    ]),
    cell: (cell) => h('div', { class: 'time-cell' }, [
      h('span', { class: 'time-badge-coffee' }, cell.getValue())
    ])
  }),
  column.accessor("kategori", {
    header: () => h('div', { class: 'table-header-cell' }, [
      h('span', { class: 'header-icon' }, '🏷️'),
      h('span', 'Kategori')
    ]),
    cell: (cell) => {
      const categories = cell.getValue() || [];
      return h('div', { class: 'categories-cell' }, 
        categories.length ? categories.slice(0, 2).map((k: any) => 
          h('span', { class: 'category-tag-coffee' }, k.nama)
        ) : h('span', { class: 'no-category' }, '-')
      );
    }
  }),
  column.accessor("gambar", {
    header: () => h('div', { class: 'table-header-cell' }, [
      h('span', { class: 'header-icon' }, '📷'),
      h('span', 'Foto')
    ]),
    cell: (cell) => {
      const val = cell.getValue();
      return h('div', { class: 'photo-cell' }, [
        h("img", {
          src: val ? `/storage/${val}` : "/img/default-food.png",
          alt: "Foto Resep",
          class: "recipe-photo-coffee",
          onError: (e: any) => {
            e.target.src = "/img/default-food.png";
          }
        })
      ]);
    },
  }),
  column.accessor("id", {
    header: () => h('div', { class: 'table-header-cell' }, [
      h('span', { class: 'header-icon' }, '⚡'),
      h('span', 'Aksi')
    ]),
    cell: (cell) =>
      h("div", { class: "action-buttons-coffee" }, [
        h(
          "button",
          {
            class: "coffee-btn edit-coffee-btn",
            title: "Edit Resep",
            onClick: () => {
              selected.value = cell.getValue();
              openForm.value = true;
            },
          },
          [
            h("i", { class: "la la-edit" }),
            h("span", "Edit")
          ]
        ),
        h(
          "button",
          {
            class: "coffee-btn delete-coffee-btn",
            title: "Hapus Resep",
            onClick: () => {
              if (confirm("Yakin ingin menghapus resep ini?")) {
                deleteResep(`/resep/${cell.getValue()}`);
              }
            }
          },
          [
            h("i", { class: "la la-trash" }),
            h("span", "Hapus")
          ]
        ),
      ]),
  }),
]

const refresh = () => paginateRef.value.refetch()

watch(openForm, (val) => {
  if (!val) {
    selected.value = ""
  }
  window.scrollTo(0, 0)
})

const handleAddRecipe = () => {
  selected.value = ""
  openForm.value = true
}

const searchRecipe = (recipeName: string) => {
  searchQuery.value = recipeName
  // Optional: trigger search or filter
  console.log('Searching for:', recipeName)
}
watch(searchQuery, () => {
  paginateRef.value?.refetch()
})
// Misal di paginateRef.vue / hooks yang ambil data:
// const fetchData = ({ page, pageSize }) => {
//   return axios.get('/resep', {
//     params: {
//       page,
//       search: searchQuery.value, // kirim ke API
//     }
//   })
// }
const filteredData = computed(() => {
  if (!searchQuery.value) return paginateRef.value?.data || []
  return (paginateRef.value?.data || []).filter((item: Resep) =>
    item.judul.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})


const token = localStorage.getItem("token")
const router = useRouter()

const handleAddResep = () => {
  const token = localStorage.getItem("token")
  if (!token) {
    Swal.fire({
      icon: "warning",
      title: "Login Diperlukan",
      text: "Silakan login terlebih dahulu untuk menambahkan resep.",
      showCancelButton: true,
      confirmButtonText: "Login",
      cancelButtonText: "Batal"
    }).then((result) => {
      if (result.isConfirmed) {
        router.push("/sign-in")
      }
      // Kalau dibatalkan, tidak melakukan apa-apa
    })
    return
  }

  openForm.value = true
}

axios.post("/resep", {
  headers: {
    Authorization: `Bearer ${localStorage.getItem("token")}`
  }
})


.then(() => {
  alert("Resep berhasil ditambahkan!")
})
// .catch((err) => {
//   if (err.response?.status === 401) {
//     alert("Sesi login sudah habis, silakan login ulang.")
//   }
// })

</script>

<template>
  <Form :selected="selected" @close="openForm = false" @refresh="refresh" v-if="openForm" />

  <div class="coffee-recipe-container">
    <!-- Coffee Shop Hero Section -->
    <div class="hero-section">
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <div class="hero-text">
          <h1 class="hero-title">
            Resep <span class="highlight-text">Rasa</span>
          </h1>
          <p class="hero-subtitle">
            Koleksi resep terbaik untuk menciptakan cita rasa istimewa di dapur Anda
          </p>
          <button 
            class="hero-cta-btn"
            v-if="!openForm" 
            @click="handleAddResep"
          >
            <span class="btn-icon">👨‍🍳</span>
            Tambah Resep Baru
          </button>
        </div>
      </div>
      
      <!-- Floating Stats -->
      <div class="floating-stats">
        <div class="stat-item">
          <div class="stat-number">{{ paginateRef?.total || 0 }}</div>
          <div class="stat-label">Resep</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <div class="stat-number">{{ Math.ceil((paginateRef?.total || 0) / 10) }}</div>
          <div class="stat-label">Halaman</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <div class="stat-number">⭐ 4.8</div>
          <div class="stat-label">Rating</div>
        </div>
      </div>
    </div>

    <!-- Menu Section -->
    <div class="menu-section">
      <div class="section-header">
        <h2 class="section-title">
          <span class="section-icon">📖</span>
          Daftar Resep Pilihan
        </h2>
        <p class="section-subtitle">Temukan dan kelola resep favorit Anda</p>
      </div>

      <div class="controls-section">
  <div class="search-box-coffee">
    <i class="la la-search search-icon"></i>
    <input
      type="text"
      class="search-input-coffee"
      placeholder="Cari resep..."
      v-model="searchQuery"
    />
  </div>
</div>
      <!-- Popular Recipes Section -->
      <div class="popular-section">
        <div class="popular-header">
          <h3 class="popular-title">Pencarian populer</h3>
          <!-- <span class="popular-time">Diperbarui 03.45</span> -->
        </div>
        
        <div class="popular-grid">
          <div class="recipe-card">
            <div class="recipe-image">
              <img src="https://images.unsplash.com/photo-1565299507177-b0ac66763828?w=400&h=200&fit=crop&crop=center" alt="Perkedel Tahu" />
              <div class="recipe-overlay"></div>
            </div>
            <div class="recipe-info">
              <h4 class="recipe-name">perkedel tahu</h4>
            </div>
          </div>

          <div class="recipe-card" @click="searchRecipe('dadar jagung')">
            <div class="recipe-image">
              <img src="https://images.unsplash.com/photo-1574484284002-952d92456975?w=400&h=200&fit=crop&crop=center" alt="Dadar Jagung" />
              <div class="recipe-overlay"></div>
            </div>
            <div class="recipe-info">
              <h4 class="recipe-name">dadar jagung</h4>
            </div>
          </div>

          <div class="recipe-card" @click="searchRecipe('telur bumbu bali')">
            <div class="recipe-image">
              <img src="https://images.unsplash.com/photo-1582169296469-1f8b1d4de87d?w=400&h=200&fit=crop&crop=center" alt="Telur Bumbu Bali" />
              <div class="recipe-overlay"></div>
            </div>
            <div class="recipe-info">
              <h4 class="recipe-name">telur bumbu bali</h4>
            </div>
          </div>

          <div class="recipe-card" @click="searchRecipe('cumi asam manis')">
            <div class="recipe-image">
              <img src="https://images.unsplash.com/photo-1559847844-d7b4ec2467b0?w=400&h=200&fit=crop&crop=center" alt="Cumi Asam Manis" />
              <div class="recipe-overlay"></div>
            </div>
            <div class="recipe-info">
              <h4 class="recipe-name">cumi asam manis</h4>
            </div>
          </div>

          <div class="recipe-card" @click="searchRecipe('sambal goreng kentang ati ampela')">
            <div class="recipe-image">
              <img src="https://images.unsplash.com/photo-1567620832903-9fc6debc209f?w=400&h=200&fit=crop&crop=center" alt="Sambal Goreng" />
              <div class="recipe-overlay"></div>
            </div>
            <div class="recipe-info">
              <h4 class="recipe-name">sambal goreng kentang ati ampela</h4>
            </div>
          </div>

          <div class="recipe-card" @click="searchRecipe('bobor bayam')">
            <div class="recipe-image">
              <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&h=200&fit=crop&crop=center" alt="Bobor Bayam" />
              <div class="recipe-overlay"></div>
            </div>
            <div class="recipe-info">
              <h4 class="recipe-name">bobor bayam</h4>
            </div>
          </div>

          <div class="recipe-card" @click="searchRecipe('dimsum ayam wortel')">
            <div class="recipe-image">
              <img src="https://images.unsplash.com/photo-1496116218417-1a781b1c416c?w=400&h=200&fit=crop&crop=center" alt="Dimsum Ayam" />
              <div class="recipe-overlay"></div>
            </div>
            <div class="recipe-info">
              <h4 class="recipe-name">dimsum ayam wortel</h4>
            </div>
          </div>

          <div class="recipe-card" @click="searchRecipe('chicken katsu hokben')">
            <div class="recipe-image">
              <img src="https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=400&h=200&fit=crop&crop=center" alt="Chicken Katsu" />
              <div class="recipe-overlay"></div>
            </div>
            <div class="recipe-info">
              <h4 class="recipe-name">chicken katsu hokben</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Import Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

/* Container */
.coffee-recipe-container {
  font-family: 'Poppins', sans-serif;
  background: #0a0a0a;
  min-height: 100vh;
  color: #ffffff;
}

/* Hero Section */
.hero-section {
  position: relative;
  height: 70vh;
  min-height: 500px;
  background: linear-gradient(135deg, 
    rgba(70, 19, 46, 0.95) 0%, 
    rgba(139, 90, 43, 0.95) 50%, 
    rgba(160, 108, 63, 0.95) 100%
  ),
  url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 1080"><defs><pattern id="coffee-pattern" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="2" fill="%23ffffff" fill-opacity="0.1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23coffee-pattern)"/></svg>');
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(218, 139, 255, 0.4);
  z-index: 1;
}

.hero-content {
  position: relative;
  z-index: 2;
  text-align: center;
  max-width: 800px;
  padding: 2rem;
}

.hero-title {
  font-size: clamp(3rem, 8vw, 6rem);
  font-weight: 800;
  margin-bottom: 1rem;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
  letter-spacing: -2px;
}

.highlight-text {
  background: linear-gradient(135deg, #D4A574, #F4E4C1);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  position: relative;
}

.hero-subtitle {
  font-size: 1.3rem;
  margin-bottom: 3rem;
  opacity: 0.9;
  line-height: 1.6;
  font-weight: 300;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

.hero-cta-btn {
  background: linear-gradient(135deg, #D4A574, #C19A5B);
  color: #2D1B0E;
  border: none;
  padding: 1.2rem 2.5rem;
  border-radius: 50px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.7rem;
  transition: all 0.4s ease;
  box-shadow: 0 8px 25px rgba(212, 165, 116, 0.3);
  text-transform: uppercase;
  letter-spacing: 1px;
}

.hero-cta-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 35px rgba(212, 165, 116, 0.4);
  background: linear-gradient(135deg, #E6B885, #D4A574);
}

.btn-icon {
  font-size: 1.2rem;
}

/* Floating Stats */
.floating-stats {
  position: absolute;
  bottom: 2rem;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 50px;
  padding: 1rem 2rem;
  display: flex;
  align-items: center;
  gap: 2rem;
  z-index: 2;
}

.stat-item {
  text-align: center;
}

.stat-number {
  font-size: 1.5rem;
  font-weight: 700;
  color: #D4A574;
  margin-bottom: 0.2rem;
}

.stat-label {
  font-size: 0.8rem;
  opacity: 0.8;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.stat-divider {
  width: 1px;
  height: 30px;
  background: rgba(255, 255, 255, 0.3);
}

/* Menu Section */
.menu-section {
  background: hsl(0, 3%, 87%);
  padding: 4rem 2rem;
  position: relative;
}

.section-header {
  text-align: center;
  margin-bottom: 3rem;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  color: #aa5d0b;
}

.section-icon {
  font-size: 2.5rem;
}

.section-subtitle {
  font-size: 1.1rem;
  opacity: 0.7;
  font-weight: 300;
  color: #000000;
}

/* Popular Recipes Section */
.popular-section {
  margin-bottom: 4rem;
}

.popular-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.popular-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #D4A574;
  margin: 0;
}

.popular-time {
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.6);
  font-weight: 400;
}

.popular-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  max-width: 1400px;
  margin: 0 auto;
}

.recipe-card {
  position: relative;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 16px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
}

.recipe-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 35px rgba(212, 165, 116, 0.2);
  border-color: rgba(212, 165, 116, 0.3);
}

.recipe-image {
  position: relative;
  height: 160px;
  overflow: hidden;
}

.recipe-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: all 0.3s ease;
}

.recipe-card:hover .recipe-image img {
  transform: scale(1.1);
}

.recipe-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(180deg, 
    transparent 0%, 
    rgba(0, 0, 0, 0.3) 50%, 
    rgba(45, 27, 14, 0.8) 100%
  );
  z-index: 1;
}

.recipe-info {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 1.5rem;
  z-index: 2;
}

.recipe-name {
  font-size: 1rem;
  font-weight: 600;
  color: #ffffff;
  margin: 0;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
  line-height: 1.3;
  text-transform: lowercase;
}

.recipe-card:hover .recipe-name {
  color: #D4A574;
}
.controls-section {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 2rem;
  margin-bottom: 3rem;
  flex-wrap: wrap;
}

.search-box-coffee {
  position: relative;
  width: 350px;
}

.search-icon {
  position: absolute;
  left: 1.2rem;
  top: 50%;
  transform: translateY(-50%);
  color: #000000;
  font-size: 1.2rem;
  z-index: 2;
}

.search-input-coffee {
  width: 100%;
  padding: 1rem 1.2rem 1rem 3rem;
  background: hwb(0 8% 92% / 0.05);
  border: 2px solid #0000004d;
  border-radius: 25px;
  color: hwb(0 0% 100%);
  font-size: 0.95rem;
  outline: none;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.search-input-coffee:focus {
  border-color: #D4A574;
  box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.2);
  background: rgba(255, 255, 255, 0.08);
}

.search-input-coffee::placeholder {
  color: rgba(255, 255, 255, 0.5);
}

.category-select-coffee {
  padding: 1rem 1.5rem;
  background: rgba(255, 255, 255, 0.05);
  border: 2px solid rgba(212, 165, 116, 0.3);
  border-radius: 25px;
  color: #ffffff;
  font-size: 0.95rem;
  cursor: pointer;
  outline: none;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
  min-width: 200px;
}

.category-select-coffee:focus {
  border-color: #D4A574;
}

.category-select-coffee option {
  background: #2D1B0E;
  color: #ffffff;
}

/* Table Container */
.table-container-coffee {
  max-width: 1400px;
  margin: 0 auto;
  background: rgba(255, 255, 255, 0.03);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

/* Table Styles */
:deep(.coffee-table) {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  background: transparent;
}

:deep(.coffee-table thead) {
  background: linear-gradient(135deg, 
    rgba(212, 165, 116, 0.15),
    rgba(193, 154, 91, 0.15)
  );
}

:deep(.coffee-table th) {
  padding: 1.5rem 1.2rem;
  text-align: left;
  font-weight: 600;
  color: #D4A574;
  border-bottom: 2px solid rgba(212, 165, 116, 0.2);
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 1px;
}

:deep(.coffee-table td) {
  padding: 1.2rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  color: #ffffff;
  font-size: 0.9rem;
}

:deep(.coffee-table tbody tr) {
  transition: all 0.3s ease;
  background: #3a1d5005;
}

:deep(.coffee-table tbody tr:hover) {
  background: #ec3ebb1a;
  transform: scale(1.001);
}

/* Table Cell Styles */
.table-header-cell {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
}

.header-icon {
  font-size: 1rem;
}

.recipe-title-cell {
  font-weight: 600;
  color: #D4A574;
  font-size: 1rem;
}

.description-cell {
  color: rgba(255, 255, 255, 0.8);
  line-height: 1.4;
  max-width: 200px;
}

.ingredients-cell {
  max-width: 180px;
}

.ingredient-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.3rem;
  font-size: 0.85rem;
}

.ingredient-bullet {
  color: #D4A574;
  font-weight: bold;
  font-size: 1.2rem;
}

.ingredient-name {
  color: rgba(255, 255, 255, 0.9);
}

.more-items-coffee {
  color: #D4A574;
  font-size: 0.8rem;
  font-weight: 500;
  background: #67297626;
  padding: 0.2rem 0.6rem;
  border-radius: 12px;
  cursor: help;
  margin-top: 0.3rem;
  display: inline-block;
}

.time-badge-coffee {
  background: linear-gradient(135deg, #D4A574, #C19A5B);
  color: #2D1B0E;
  padding: 0.4rem 1rem;
  border-radius: 15px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.categories-cell {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
}

.category-tag-coffee {
  background: rgba(212, 165, 116, 0.2);
  color: #D4A574;
  padding: 0.3rem 0.8rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  border: 1px solid rgba(212, 165, 116, 0.3);
}

.no-category {
  color: rgba(255, 255, 255, 0.4);
  font-style: italic;
}

.recipe-photo-coffee {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 12px;
  border: 2px solid rgba(212, 165, 116, 0.3);
  transition: all 0.3s ease;
}

.recipe-photo-coffee:hover {
  transform: scale(1.1);
  border-color: #D4A574;
  box-shadow: 0 8px 20px rgba(212, 165, 116, 0.3);
}

/* Action Buttons */
.action-buttons-coffee {
  display: flex;
  gap: 0.5rem;
}

.coffee-btn {
  display: flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.6rem 1rem;
  border: none;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.edit-coffee-btn {
  background: linear-gradient(135deg, #4CAF50, #45a049);
  color: white;
}

.edit-coffee-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(76, 175, 80, 0.3);
}

.delete-coffee-btn {
  background: linear-gradient(135deg, #f44336, #da190b);
  color: white;
}

.delete-coffee-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(244, 67, 54, 0.3);
}

/* Responsive Design */
@media (max-width: 1200px) {
  .coffee-recipe-container {
    padding: 0;
  }
  
  .menu-section {
    padding: 3rem 1rem;
  }
}

@media (max-width: 768px) {
  .hero-title {
    font-size: 3rem;
  }
  
  .hero-subtitle {
    font-size: 1.1rem;
  }
  
  .floating-stats {
    bottom: 1rem;
    gap: 1rem;
    padding: 0.8rem 1.5rem;
  }
  
  .controls-section {
    flex-direction: column;
    gap: 1rem;
  }
  
  .search-box-coffee {
    width: 100%;
    max-width: 400px;
  }
  
  .category-select-coffee {
    width: 100%;
    max-width: 400px;
  }
  
  .section-title {
    font-size: 2rem;
    flex-direction: column;
    gap: 0.5rem;
  }
  
  :deep(.coffee-table th),
  :deep(.coffee-table td) {
    padding: 1rem 0.8rem;
  }
  
  .action-buttons-coffee {
    flex-direction: column;
    gap: 0.3rem;
  }
}

@media (max-width: 480px) {
  .hero-content {
    padding: 1rem;
  }
  
  .menu-section {
    padding: 2rem 1rem;
  }
  
  .floating-stats {
    flex-direction: column;
    gap: 1rem;
    padding: 1rem;
  }
  
  .stat-divider {
    width: 30px;
    height: 1px;
  }
}

/* Animation */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.menu-section {
  animation: fadeInUp 0.8s ease-out;
}

.hero-content > * {
  animation: fadeInUp 0.8s ease-out;
}

.floating-stats {
  animation: fadeInUp 1s ease-out 0.3s both;
}
</style>