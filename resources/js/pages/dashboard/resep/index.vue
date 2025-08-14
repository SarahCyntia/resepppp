<script setup lang="ts">
import { h, ref, watch } from "vue"
import { useDelete } from "@/libs/hooks"
import Form from "./Form.vue" // ganti sesuai lokasi Form resep kamu
import { createColumnHelper } from "@tanstack/vue-table"
import type { Resep } from "@/types" // pastikan ada tipe `Resep`

const column = createColumnHelper<Resep>()
const paginateRef = ref<any>(null)
const selected = ref<string>("")
const openForm = ref<boolean>(false)

const { delete: deleteResep } = useDelete({
  onSuccess: () => paginateRef.value.refetch(),
})

const columns = [
  column.accessor("id", {
    header: 'No'
  }),
  column.accessor("judul", {
    header: "Judul Resep",
  }),
  column.accessor("deskripsi", {
    header: "Deskripsi",
  }),
  column.accessor("bahan", {
    header: "Bahan-bahan",
    cell: (cell) => {
      const bahan = cell.getValue() || [];
      return h('div', {}, bahan.map((b: any, i: number) =>
        h('div', {}, `${i + 1}. ${b.nama}`)
      ));
    },
  }),
  // column.accessor("bahan", {
  //   header: "Bahan-bahan",
  //   cell: (cell) => {
  //     const bahan = cell.getValue() || [];
  //     return h('div', {}, bahan.map((b: any, i: number) =>
  //       h('div', {}, `${i + 1}. ${b.nama} (${b.jumlah})`)
  //     ));
  //   },
  // }),

  // ✅ Tampilkan alat-alat
  column.accessor("alat", {
    header: "Alat-alat",
    cell: (cell) => {
      const alat = cell.getValue() || [];
      return h('div', {}, alat.map((a: any, i: number) =>
        h('div', {}, `${i + 1}. ${a.nama}`)
      ));
    },
  }),

  // ✅ Tampilkan langkah-langkah
  column.accessor("langkah", {
    header: "Langkah-langkah",
    cell: (cell) => {
      const langkah = cell.getValue() || [];
      return h('div', {}, langkah.map((l: any, i: number) =>
        h('div', {}, `${i + 1}. ${l.deskripsi}`)
      ));
    },
  }),
  column.accessor("waktu_masak", {
    header: "Waktu Masak",
  }),
  column.accessor("kategori", {
    header: "Kategori",
    cell: (cell) => cell.getValue()?.map((k: any) => k.nama).join(", ") ?? "-",
  }),

  column.accessor("gambar", {

    header: "Foto",
    cell: (cell) => {
      const val = cell.getValue();
      console.log("Photo path:", val);
      return h("img", {
        src: val ? `/storage/${val}` : "/img/default.png",
        alt: "Foto Makanan",
        class: "img-thumbnail",
        style: "width: 50px; height: 50px;",
      });
    },
  }),


  // column.accessor("uuid", {
  //   header: "Aksi",
  //   cell: (cell) =>
  //     h("div", { class: "d-flex gap-2" }, [
  //       h(
  //         "button",
  //         {
  //           class: "btn btn-sm btn-icon btn-info",
  //           onClick: () => {
  //             selected.value = cell.getValue()
  //             openForm.value = true
  //           },
  //         },
  //         h("i", { class: "la la-pencil fs-2" })
  //       ),
  //       h(
  //         "button",
  //         {
  //           class: "btn btn-sm btn-icon btn-danger",
  //           onClick: () => deleteResep(`/resep/${cell.getValue()}`),
  //         },
  //         h("i", { class: "la la-trash fs-2" })
  //       ),
  //     ]),
  // }),
  column.accessor("id", {
    header: "Aksi",
    cell: (cell) =>
      h("div", { class: "d-flex gap-2" }, [
        h(
          "button",
          {
            class: "btn btn-sm btn-icon btn-info",
            onClick: () => {
              console.log("Edit diklik, ID:", cell.getValue()); // ⬅️ DI SINI
              selected.value = cell.getValue();
              openForm.value = true;
            },
          },
          h("i", { class: "la la-pencil fs-2" })
        ),
        h(
          "button",
          {
            class: "btn btn-sm btn-icon btn-danger",
            onClick: () =>
              deleteResep(`/resep/${cell.getValue()}`),
          },
          h("i", { class: "la la-trash fs-2" })
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
</script>

<template>
  <Form :selected="selected" @close="openForm = false" @refresh="refresh" v-if="openForm" />

  <div class="card">
    <div class="card-header align-items-center">
      <h2 class="mb-0">📋 Daftar Resep</h2>
      <button type="button" class="btn btn-sm btn-primary ms-auto" v-if="!openForm" @click="openForm = true">
        Tambah Resep <i class="la la-plus"></i>
      </button>
    </div>
    <div class="card-body">
      <paginate ref="paginateRef" id="table-resep" url="/resep" :columns="columns" />
    </div>
  </div>
</template>
