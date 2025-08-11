<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import ApiService from "@/core/services/ApiService";

const props = defineProps({
  selected: {
    type: String,
    default: null,
  },
});

const emit = defineEmits(["close", "refresh"]);

const resep = ref<any>({ kategori_id: null });
const formRef = ref();
const fileTypes = ref(["image/jpeg", "image/png", "image/jpg"]);
const gambar = ref<any>([]);

const formSchema = Yup.object().shape({
  judul: Yup.string().required("Judul harus diisi"),
  waktu_masak: Yup.string().required("Waktu masak harus diisi"),
  kategori_id: Yup.string().required("Kategori harus dipilih"),
});

function getEdit() {
  block(document.getElementById("form-resep"));
  ApiService.get("resep", props.selected)
    .then(({ data }) => {
      resep.value = data.resep;
      gambar.value = data.resep.gambar ? ["/storage/" + data.resep.gambar] : [];
    })
    .catch((err: any) => {
      toast.error(err.response?.data?.message || "Terjadi kesalahan.");
    })
    .finally(() => {
      unblock(document.getElementById("form-resep"));
    });
}

const kategoriOptions = ref<{ id: number; text: string }[]>([]);
const kategoriBaru = ref("");
const showKategoriInput = ref(false);

function loadKategori() {
  ApiService.get("kategori").then(({ data }) => {
    kategoriOptions.value = data.map((k: any) => ({
      id: k.id,
      text: k.nama,
    }));
  });
}

function tambahKategori() {
  const nama = kategoriBaru.value.trim().toLowerCase();
  const sudahAda = kategoriOptions.value.some(
    (k) => k.text.toLowerCase() === nama
  );

  if (sudahAda) {
    toast.error("Kategori sudah ada.");
    return;
  }

  block(document.getElementById("form-resep"));
  ApiService.post("kategori", { nama: kategoriBaru.value })
    .then(({ data }) => {
      kategoriOptions.value.push({ id: data.id, text: data.nama });
      resep.value.kategori_id = data.id;
      kategoriBaru.value = "";
      showKategoriInput.value = false;
      toast.success("Kategori berhasil ditambahkan.");
    })
    .catch(() => toast.error("Gagal menambahkan kategori"))
    .finally(() => {
      unblock(document.getElementById("form-resep"));
    });
}
onMounted(() => {
  if (props.selected) getEdit();
  loadKategori(); // ← tambahkan ini
});

// const kategoriBaru = ref("");
// const loadingKategori = ref(false);
// const semuaKategori = ref<{ id: string; text: string }[]>([]);

// function tambahKategori() {
//   if (!kategoriBaru.value.trim()) return;
//   loadingKategori.value = true;

//   ApiService.post("kategori", { nama: kategoriBaru.value })
//     .then((res) => {
//       toast.success("Kategori berhasil ditambahkan");
//       semuaKategori.value.push({
//         id: res.data.id,
//         text: res.data.nama,
//       });
//       resep.value.kategori_id = res.data.id;
//       kategoriBaru.value = "";
//     })
//     .catch((err) => {
//       toast.error(err.response?.data?.message || "Gagal menambah kategori");
//     })
//     .finally(() => {
//       loadingKategori.value = false;
//     });
// }

// function loadKategori() {
//   ApiService.get("kategori")
//     .then((res) => {
//       semuaKategori.value = res.data.map((k: any) => ({
//         id: k.id,
//         text: k.nama,
//       }));
//     })
//     .catch(() => {
//       toast.error("Gagal memuat kategori");
//     });
// }

// onMounted(() => {
//   loadKategori();
// });


function submit() {
  const formData = new FormData();
  formData.append("judul", resep.value.judul);
  formData.append("waktu_masak", resep.value.waktu_masak);
  formData.append("kategori_id", resep.value.kategori_id);
  formData.append("bahan", resep.value.bahan || "");
  formData.append("alat", resep.value.alat || "");
  formData.append("cara", resep.value.cara || "");
  if (gambar.value.length) {
    formData.append("gambar", gambar.value[0].file);
  }
  if (props.selected) {
    formData.append("_method", "PUT");
  }

  block(document.getElementById("form-resep"));
  axios({
    method: "post",
    url: props.selected ? `/resep/${props.selected}` : "/resep",
    data: formData,
    headers: {
      "Content-Type": "multipart/form-data",
    },
  })
    .then(() => {
      emit("close");
      emit("refresh");
      toast.success("Resep berhasil disimpan");
      formRef.value.resetForm();
    })
    .catch((err: any) => {
      formRef.value.setErrors(err.response.data.errors);
      toast.error(err.response.data.message);
    })
    .finally(() => {
      unblock(document.getElementById("form-resep"));
    });
}

onMounted(() => {
  if (props.selected) getEdit();
});

watch(
  () => props.selected,
  () => {
    if (props.selected) getEdit();
  }
);
</script>

<template>
  <VForm class="form card mb-10" @submit="submit" :validation-schema="formSchema" id="form-resep" ref="formRef">
    <div class="card-header align-items-center">
      <h2 class="mb-0">{{ props.selected ? "Edit" : "Tambah" }} Resep</h2>
      <button type="button" class="btn btn-sm btn-light-danger ms-auto" @click="emit('close')">
        Batal <i class="la la-times-circle p-0"></i>
      </button>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col-md-6 mb-7">
          <label class="form-label required">Judul Resep</label>
          <Field name="judul" class="form-control form-control-solid" v-model="resep.judul"
            placeholder="Masukkan judul resep" />
          <ErrorMessage name="judul" class="text-danger" />
        </div>

        <div class="col-md-6 mb-7">
          <label class="form-label required">Waktu Masak</label>
          <Field name="waktu_masak" class="form-control form-control-solid" v-model="resep.waktu_masak"
            placeholder="Contoh: 30 menit" />
          <ErrorMessage name="waktu_masak" class="text-danger" />
        </div>


        <div class="col-md-6 mb-7">
          <label class="form-label required">Kategori</label>
          <Field name="kategori_id" type="hidden" v-model="resep.kategori_id">
            <select2 placeholder="Pilih kategori" class="form-select-solid" :options="kategoriOptions"
              v-model="resep.kategori_id" />
          </Field>
          <ErrorMessage name="kategori_id" class="text-danger" />

          <button type="button" class="btn btn-sm btn-light-primary mt-2"
            @click="showKategoriInput = !showKategoriInput">
            {{ showKategoriInput ? "Batal" : "Tambah Kategori Baru" }}
          </button>

          <div v-if="showKategoriInput" class="mt-3">
            <input type="text" class="form-control form-control-sm mb-2" v-model="kategoriBaru"
              placeholder="Masukkan nama kategori baru" />
            <button class="btn btn-sm btn-primary" @click="tambahKategori">
              Simpan Kategori
            </button>
          </div>
        </div>

        <!-- <div class="col-md-6 mb-7">
          <label class="form-label required">Kategori</label>
          <Field name="kategori_id" type="hidden" v-model="resep.kategori_id">
            <select2 placeholder="Pilih kategori" class="form-select-solid" :options="semuaKategori"
              v-model="resep.kategori_id" />
          </Field>
          <div class="d-flex align-items-center gap-2 mt-2">
            <input v-model="kategoriBaru" type="text" class="form-control form-control-sm"
              placeholder="Kategori baru..." />
            <button type="button" class="btn btn-sm btn-light-primary" @click="tambahKategori"
              :disabled="loadingKategori">
              Tambah
            </button>
          </div>
          <ErrorMessage name="kategori_id" class="text-danger" />
        </div> -->

        <div class="col-md-6 mb-7">
          <label class="form-label">Gambar Resep</label>
          <file-upload :files="gambar" :accepted-file-types="fileTypes" v-on:updatefiles="(file) => (gambar = file)" />
        </div>

        <div class="col-12 mb-7">
          <label class="form-label">Bahan</label>
          <textarea class="form-control form-control-solid" v-model="resep.bahan" rows="3"
            placeholder="Tulis bahan-bahan..."></textarea>
        </div>

        <div class="col-12 mb-7">
          <label class="form-label">Alat</label>
          <textarea class="form-control form-control-solid" v-model="resep.alat" rows="2"
            placeholder="Tulis alat-alat yang dibutuhkan..."></textarea>
        </div>

        <div class="col-12 mb-7">
          <label class="form-label">Cara Membuat</label>
          <textarea class="form-control form-control-solid" v-model="resep.cara" rows="4"
            placeholder="Tulis langkah-langkah pembuatan..."></textarea>
        </div>
      </div>
    </div>

    <div class="card-footer d-flex">
      <button type="submit" class="btn btn-primary btn-sm ms-auto">
        Simpan Resep
      </button>
    </div>
  </VForm>
</template>
