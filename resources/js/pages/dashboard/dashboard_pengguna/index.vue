<script setup lang="ts">
import { ref, onMounted } from "vue";
// import Form from "./Form.vue";
import { useDelete } from "@/libs/hooks";
import axios from "@/libs/axios";
import type { Resep } from "@/types";

const openForm = ref(false);
const selected = ref<string | null>(null);
const reseps = ref<Resep[]>([]);

const { delete: deleteResep } = useDelete({
  onSuccess: () => getReseps(),
});

const getReseps = async () => {
  const res = await axios.get("/resep");
  reseps.value = res.data?.data || [];
};

const handleEdit = (uuid: string) => {
  selected.value = uuid;
  openForm.value = true;
};

const handleDelete = (uuid: string) => {
  deleteResep(`/resep/${uuid}`);
};

const closeForm = () => {
  openForm.value = false;
  selected.value = null;
};

onMounted(getReseps);
</script>

<template>
  <Form v-if="openForm" :selected="selected" @close="closeForm" @refresh="getReseps" />

  <div class="card shadow-sm border-0">
    <div class="card-header d-flex align-items-center bg-primary text-white">
      <h3 class="mb-0">📋 Daftar Resep</h3>
      <button class="btn btn-light btn-sm ms-auto" @click="openForm = true">
        Tambah Resep <i class="la la-plus"></i>
      </button>
    </div>

    <div class="card-body">
      <div class="row g-4">
        <div class="col-md-4" v-for="resep in reseps" :key="resep.uuid">
          <div class="card h-100 shadow-sm border">
            <img
              :src="resep.gambar ? `/storage/${resep.gambar}` : '/img/default.png'"
              class="card-img-top"
              alt="Foto Makanan"
              style="height: 200px; object-fit: cover"
            />
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">{{ resep.judul }}</h5>
              <p class="card-text text-muted small">{{ resep.deskripsi }}</p>
              <p class="mb-1">
                <strong>Bahan:</strong>
                <ul class="ps-3 mb-1">
                  <li v-for="b in resep.bahan" :key="b.nama">{{ b.nama }} ({{ b.jumlah }})</li>
                </ul>
              </p>
              <p class="mb-1">
                <strong>Waktu Masak:</strong> {{ resep.waktu_masak }}
              </p>
              <p class="mb-2">
                <strong>Kategori:</strong>
                <span v-if="resep.kategori.length">
                  {{ resep.kategori.map(k => k.nama).join(", ") }}
                </span>
                <span v-else>-</span>
              </p>
              <div class="mt-auto d-flex gap-2">
                <button class="btn btn-sm btn-info w-100" @click="handleEdit(resep.uuid)">
                  <i class="la la-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-danger w-100" @click="handleDelete(resep.uuid)">
                  <i class="la la-trash"></i> Hapus
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-if="!reseps.length" class="text-center text-muted py-5">
          <i class="la la-utensils la-3x"></i>
          <p class="mt-2 mb-0">Belum ada resep ditambahkan.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.card-img-top {
  border-bottom: 1px solid #ddd;
}
.card-body {
  display: flex;
  flex-direction: column;
}
</style>
