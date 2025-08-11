<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch, computed } from "vue";
import * as Yup from "yup";
import type { Resep } from "@/types";
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
// const emit = defineEmits(["success", "refresh"]);


// const resep = ref<any>({
//   kategori_id: null,
//   alat: [],     // array relasi alat
//   bahan: [],    // array relasi bahan
//   langkah: [],  // array relasi langkah
// });
const resep = ref({
  judul: '',
  deskripsi: '',
  waktu_masak: '',
  kategori: [],
  alatList: [],
  bahanList: [],
  langkahList: [],
});


watch(() => props.selected, (id) => {
  if (id) {
    fetchResep(id);
  }
}, { immediate: true });

function fetchResep(id: string) {
  axios.get(`/resep/${id}`).then(response => {
    const data = response.data.resep;

    resep.value = {
      judul: data.judul || '',
      deskripsi: data.deskripsi || '',
      waktu_masak: data.waktu_masak || '',
      kategori: data.kategori || [],
      alatList: data.alatList?.map(a => ({ nama: a.nama })) || [""],
      bahanList: data.bahanList?.map(b => ({ nama: b.nama })) || [""],
      langkahList: data.langkahList?.map(l => ({ deskripsi: l.deskripsi })) || [""],
      // alatList: data.alatList || [],
      // bahanList: data.alatList || [],
      // langkahList: data.alatList || [],
    };
  }).catch((err) => {
    console.error("Gagal fetch resep:", err);
  });
}
// const daftarAlat = ref<Array<{ id: number; nama: string }>>([]);
// onMounted(async () => {
//   const res = await axios.get("/api/alat");
//   daftarAlat.value = res.data.data; // asumsi response: { data: [ { id: 1, nama: "Wajan" }, ... ] }
// });

// const resep = ref<any>({ kategori_id: null });
const formRef = ref();
const fileTypes = ref(["image/jpeg", "image/png", "image/jpg"]);
const gambar = ref<any>([]);

// const formSchema = Yup.object().shape({
//   judul: Yup.string().required("Nama Resep harus diisi"),
//   waktu_masak: Yup.string().required("Waktu masak harus diisi"),
//   kategori_id: Yup.string().nullable("Kategori harus dipilih"),
//   alatList: Yup.string().required("alat harus diisi"),
//   bahanList: Yup.string().required("bahan harus dipilih"),
//   langkahList: Yup.string().required("langkah harus dipilih"),
// });

const formSchema = Yup.object({
  judul: Yup.string().required("Nama Resep harus diisi"),
  waktu_masak: Yup.string().required("Waktu masak harus diisi"),
  kategori_id: Yup.string().nullable("Kategori harus dipilih"),
  alatList: Yup.array()
    .of(Yup.string().required("Alat tidak boleh kosong"))
    .min(1, "Minimal 1 alat harus diisi"),
  bahanList: Yup.array()
    .of(Yup.string().required("Bahan tidak boleh kosong"))
    .min(1, "Minimal 1 bahan harus diisi"),
  langkahList: Yup.array()
    .of(Yup.string().required("Langkah tidak boleh kosong"))
    .min(1, "Minimal 1 langkah harus diisi"),
});






function getEdit() {
  block(document.getElementById("form-resep"));
  ApiService.get("/resep", props.selected)
    .then(({ data }) => {
      console.log(data);
      resep.value = {
        // penilaian: data.user.penilaian || "",
        judul: data.judul || '',
        deskripsi: data.deskripsi || '',
        waktu_masak: data.waktu_masak || '',
        // kategori: data.kategori || [],
        kategori_id: data.kategori_id || [],
        alatList: data.resep.alat?.map((a: any) => a.nama) || [""],
        bahanList : data.resep.bahan?.map((b: any) => b.nama) || [""],
        langkahList : data.resep.langkah?.map((l: any) => l.deskripsi) || [""],
        // alatList: data.alatList || [],
        // bahanList: data.alatList || [],
        // langkahList: data.alatList || [],
      }
      alatList.value = data.resep.alat?.map((a: any) => a.nama) || [""];
      bahanList.value = data.resep.bahan?.map((b: any) => b.nama) || [""];
      langkahList.value = data.resep.langkah?.map((l: any) => l.deskripsi) || [""];
      console.log(resep.value);

      gambar.value = data.gambar
        ? ["/storage/" + data.gambar]
        : [];

      // resep.value.password = "";
    })
    .catch((err: any) => {
      toast.error(err.response.data.message || "Gagal mengambil data");
    })
    .finally(() => {
      unblock(document.getElementById("form-resep"));
    });
}




// function getEdit() {
//   block(document.getElementById("form-resep"));
//   ApiService.get("resep", props.selected)
//     .then(({ data }) => {
//       resep.value = data.resep;
//       gambar.value = data.resep.gambar ? ["/storage/" + data.resep.gambar] : [];
//       // alatList.value = data.resep.alat?.map((a: any) => a.nama) || [""];
//       // Jika alat memang array of string:
//       alatList.value = data.resep.alat || [""];
//       // Jika alat array of object:
//       alatList.value = data.resep.alat?.map((a: any) => a.nama) || [""];
//       bahanList.value = data.resep.bahan?.map((b: any) => b.nama) || [""];
//       langkahList.value = data.resep.langkah?.map((l: any) => l.nama) || [""];

//     })
//     .catch((err: any) => {
//       toast.error(err.response?.data?.message || "Terjadi kesalahan.");
//     })
//     .finally(() => {
//       unblock(document.getElementById("form-resep"));
//     });
//   }


// const tambahAlat = () => {
//   const last = alatList.value[alatList.value.length - 1];
//   if (last && last.trim() !== "") {
//     alatList.value.push("");
//   }
// };

// const hapusAlat = (index: number) => {
//   if (alatList.value.length > 1) {
//     alatList.value.splice(index, 1);
//   } else {
//     // Kosongkan saja input pertama, tidak dihapus
//     alatList.value[0] = "";
//   }
// };
const alatList = ref<string[]>([""]); // default satu input


const tambahAlat = () => {
  const last = alatList.value[alatList.value.length - 1];
  if (last && last.trim() !== "") {
    alatList.value.push("");
  }
};

const hapusAlat = (index: number) => {
  if (alatList.value.length > 1) {
    alatList.value.splice(index, 1);
  } else {
    alatList.value[0] = "";
  }
};

const bahanList = ref<string[]>([""]);
const tambahBahan = () => {
  const last = bahanList.value[bahanList.value.length - 1];
  if (last && last.trim() !== "") {
    bahanList.value.push("");
  }
};

const hapusBahan = (index: number) => {
  if (bahanList.value.length > 1) {
    bahanList.value.splice(index, 1);
  } else {
    bahanList.value[0] = "";
  }
};
const langkahList = ref<string[]>([""]);
const tambahLangkah = () => {
  const last = langkahList.value[langkahList.value.length - 1];
  if (last && last.trim() !== "") {
    langkahList.value.push("");
  }
};

const hapusLangkah = (index: number) => {
  if (langkahList.value.length > 1) {
    langkahList.value.splice(index, 1);
  } else {
    langkahList.value[0] = "";
  }
};

// const semuaTerisi = computed(() =>
//   alatList.value.every((a) => a.trim() !== "")
// );

// const simpan = () => {
//   if (!semuaTerisi.value) {
//     toast.warning("Pastikan semua alat sudah diisi.");
//     return;
//   }
//   // lanjut simpan ke backend
// }


// const bahanList = ref<string[]>([""]); // default satu input
// const tambahBahan = () => {
//   bahanList.value.push("");
// };
// const hapusBahan = (index: number) => {
//   if (bahanList.value.length > 1) {
//     bahanList.value.splice(index, 1);
//   }
// };

const kategoriOptions = ref<{ id: number; text: string }[]>([]);
const kategoriBaru = ref("");
const showKategoriInput = ref(false);

function loadKategori() {
  ApiService.get("kategori").then(({ data }) => {
    kategoriOptions.value = data.map((k: any) => ({
      id: k.kategori_id,
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
  ApiService.post("/kategori", { nama: kategoriBaru.value })
    .then(({ data }) => {
      kategoriOptions.value.push({ id: data.kategori.kategori_id, text: data.kategori.nama });
      resep.value.kategori_id = data.kategori.kategori_id;
      console.log("Kategori Options", kategoriOptions.value);
      console.log("resep.value.kategori_id", resep.value);
      kategoriBaru.value = "";
      showKategoriInput.value = false;
      toast.success("Kategori berhasil ditambahkan.");
    })
    .catch(() => toast.error("Gagal menambahkan kategori"))
    .finally(() => {
      unblock(document.getElementById("form-resep"));
    });
}


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

  // Kirim sebagai JSON string array
  formData.append("alat", JSON.stringify(alatList.value));
  formData.append("bahan", JSON.stringify(bahanList.value));
  formData.append("langkah", JSON.stringify(langkahList.value));

  // if (gambar.value.length) {
  //   formData.append("gambar", gambar.value[0].file);
  // }
  if (gambar.value.length && gambar.value[0]?.file) {
    formData.append("gambar", gambar.value[0].file);
  }
  if (props.selected) {
    // formData.append("_method", "PUT");
    formData.append("_method", props.selected ? "PUT" : "POST");

  }

  // formData.append("judul", resep.value.judul);
  // formData.append("waktu_masak", resep.value.waktu_masak);
  // formData.append("kategori_id", resep.value.kategori_id);
  // formData.append("bahan", resep.value.bahan || "");
  // formData.append("alat", resep.value.alat || "");
  // formData.append("cara", resep.value.cara || "");
  // if (gambar.value.length) {
  //   formData.append("gambar", gambar.value[0].file);
  // }
  // if (props.selected) {
  //   formData.append("_method", "PUT");
  // }

  block(document.getElementById("form-resep"));
  axios({
    method: "post",
    url: props.selected ? `/resep/${props.selected}` : "/resep/store",
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
  if (props.selected) {
    getEdit(); // 🔴 Ini hanya jalan kalau `props.selected` SUDAH ADA
  }
});

// onMounted(() => {
//   if (props.selected) {
//     console.log("Selected id_resep:", props.selected);
//     getEdit();

//   }
// });
onMounted(() => {
  if (props.selected) getEdit();
  loadKategori(); // ← tambahkan ini
});
// watch(
//   () => props.selected,
//   () => {
//     if (props.selected) getEdit();
//   }
// );

watch(() => props.selected, (newVal) => {
  if (newVal) {
    console.log("props.selected berubah jadi:", newVal); // ← HARUS MUNCUL!
    getEdit();
  }
});

</script>


<template>
  <!-- <ResepForm :selected="selectedId" @success="loadResep" /> -->
  <VForm class="form card mb-10" @submit="submit" :validation-schema="formSchema" id="form-resep" ref="formRef">
    <div class="card-header align-items-center">
      <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Resep</h2>
      <button type="button" class="btn btn-sm btn-light-danger ms-auto" @click="emit('close')">
        Batal
        <i class="la la-times-circle p-0"></i>
      </button>
    </div>

    <div class="card-body">
      <!-- Judul -->
      <div class="mb-5">
        <label class="form-label fw-bold required">Nama Resep</label>
        <Field class="form-control" name="judul" v-model="resep.judul" />
        <ErrorMessage name="judul" class="text-danger" />
      </div>

      <!-- Waktu Masak -->
      <div class="mb-5">
        <label class="form-label fw-bold required">Waktu Masak</label>
        <Field class="form-control" name="waktu_masak" v-model="resep.waktu_masak" />
        <ErrorMessage name="waktu_masak" class="text-danger" />
      </div>

      <!-- Kategori -->
      <div class="mb-5">
        <label class="form-label fw-bold required">Kategori</label>
        <select class="form-select" v-model="resep.kategori_id" name="kategori_id">
          <option value="" disabled>Pilih kategori</option>
          <option v-for="kategori in kategoriOptions" :key="kategori.id" :value="resep.kategori_id">
            {{ kategori.text }}
          </option>
        </select>
        <ErrorMessage name="kategori_id" class="text-danger" />

        <div class="mt-2">
          <button class="btn btn-sm btn-light-primary" type="button" @click="showKategoriInput = !showKategoriInput">
            + Tambah Kategori Baru
          </button>
        </div>

        <div v-if="showKategoriInput" class="mt-2 d-flex gap-2">
          <input type="text" v-model="kategoriBaru" class="form-control" placeholder="Tambah kategori" />
          <button class="btn btn-sm btn-success" type="button" @click="tambahKategori">
            Simpan
          </button>
        </div>
      </div>

      <!-- Alat -->
      <div class="mb-5">
        <label class="form-label">🛠️ Alat Masak</label>

        <div v-for="(alat, index) in alatList" :key="index" class="d-flex gap-2 mb-2">
          <input type="text" class="form-control" v-model="alatList[index]" placeholder="Contoh: Wajan, Panci..." />
          <button type="button" class="btn btn-sm btn-danger" @click="hapusAlat(index)"
            v-if="alatList.length > 1 && alat.trim() !== ''">
            <i class="la la-times"></i>
          </button>
        </div>

        <button class="btn btn-sm btn-primary" @click="tambahAlat" :disabled="!alatList[alatList.length - 1]?.trim()">
          <i class="la la-plus"></i>Tambah Alat
        </button>
      </div>


      <div class="mb-5">
        <label class="form-label">🥕 Bahan-bahan</label>

        <div v-for="(bahan, index) in bahanList" :key="index" class="d-flex gap-2 mb-2">
          <input type="text" class="form-control" v-model="bahanList[index]" placeholder="Cabai, Bawang merah...." />
          <button type="button" class="btn btn-sm btn-danger" @click="hapusBahan(index)"
            v-if="bahanList.length > 1 && bahan.trim() !== ''">
            <i class="la la-times"></i>
          </button>
        </div>

        <button class="btn btn-sm btn-primary" @click="tambahBahan"
          :disabled="!bahanList[bahanList.length - 1]?.trim()">
          <i class="la la-plus"></i>Tambah Bahan
        </button>
      </div>

      <div class="mb-5">
        <label class="form-label">👣 Langkah-langkah</label>

        <div v-for="(langkah, index) in langkahList" :key="index" class="d-flex gap-2 mb-2">
          <input type="text" class="form-control" v-model="langkahList[index]" placeholder="Siapkan......" />
          <button type="button" class="btn btn-sm btn-danger" @click="hapusLangkah(index)"
            v-if="langkahList.length > 1 && langkah.trim() !== ''">
            <i class="la la-times"></i>
          </button>
        </div>

        <button class="btn btn-sm btn-primary" @click="tambahLangkah"
          :disabled="!langkahList[langkahList.length - 1]?.trim()">
          <i class="la la-plus"></i>Tambah Langkah
        </button>
      </div>


      <!-- <div class="mb-5">
        <label class="form-label">🥕 Bahan-bahan</label>
        <div v-for="(bahan, index) in bahanList" :key="index" class="d-flex gap-2 mb-2">
          <input type="text" class="form-control" v-model="bahanList[index]"
            placeholder="Contoh: Cabai, Bawang merah..." />
          <button class="btn btn-sm btn-danger" @click="hapusBahan(index)">
            <i class="la la-times"></i>
          </button>
        </div>
        <button class="btn btn-sm btn-primary" @click="tambahBahan">
          <i class="la la-plus"></i>Tambah Bahan
        </button>
      </div> -->

      <!-- Langkah -->
      <!-- <div class="mb-5">
        <label class="form-label fw-bold">Alat</label>
        <div v-if="resep.alat?.length === 0" class="text-muted fst-italic">
          Belum ada alat ditambahkan
        </div>
        <div>
          <Field class="form-control" name="alat" v-model="resep.alat" />
        </div>
        <button
          class="btn btn-sm btn-primary mt-2"
          @click.prevent="resep.alat.push({ nama: '' })"
        >
          + Tambah Alat
        </button>
      </div> -->

      <!-- Bahan -->
      <!-- <div class="mb-5">
        <label class="form-label fw-bold">Bahan</label>
        <div>
          <Field class="form-control" name="bahan" v-model="resep.bahan" />
        </div>
        <button
          class="btn btn-sm btn-primary mt-2"
          @click.prevent="resep.bahan.push({ nama: '' })"
        >
          + Tambah Bahan
        </button>
      </div> -->


      <!-- Gambar -->
      <div class="mb-5">
        <label class="form-label fw-bold">Gambar</label>
        <file-upload :files="gambar" :accepted-file-types="fileTypes" required
          @updatefiles="(file) => (gambar = file)" />
        <ErrorMessage name="gambar" class="text-danger" />
      </div>
    </div>
    <ErrorMessage name="judul" class="text-danger" />
    <ErrorMessage name="waktu_masak" class="text-danger" />
    <ErrorMessage name="kategori_id" class="text-danger" />
    <ErrorMessage name="alat" class="text-danger" />
    <ErrorMessage name="bahan" class="text-danger" />
    <ErrorMessage name="langkah" class="text-danger" />
    <!-- Footer -->
    <div class="card-footer d-flex">
      <button type="submit" class="btn btn-primary btn-sm ms-auto">Simpan</button>
    </div>
  </VForm>
</template>
