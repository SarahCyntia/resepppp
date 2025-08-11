<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Resep;
use App\Models\Order;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KategoriController extends Controller
{

    public function index(Request $request, Resep $id)
    {
        
        

        $data = Kategori::get();

        return response()->json($data);

    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'nama' => 'required|string|unique:kategori,nama',
    ]);

    $kategori = Kategori::create($validated);

    return response()->json([
        'message' => 'Kategori berhasil ditambahkan',
        'kategori' => $kategori
    ]);
}

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'nama' => 'required|string',
    //     ]);

    //     $resep = Resep::findOrFail($request->id);

    //     // Tambahkan entri baru ke riwayat_pengiriman (asumsinya array atau teks log)
    //     $existingKategori = $resep->kategori_resep ?? [];

    //     if (!is_array($existingKategori)) {
    //         $existingKategori = explode("\n", $existingKategori); // fallback kalau disimpan sebagai string
    //     }

    //     // Tambahkan riwayat baru ke array
    //     $existingKategori[] = $request->riwayat;

    //     // Simpan ke database, bisa berupa JSON atau string tergantung field-nya
    //     $resep->kategori_resep = json_encode($existingKategori);
    //     $resep->save();

    //     return response()->json(['message' => 'Kategori berhasil ditambahkan.']);
    // }


   public function updateKategoriResep(Request $request, $resepId)
{
    $request->validate([
        'kategori_id' => 'required|array',
        'kategori_id.*' => 'exists:kategori,id', // validasi setiap ID
    ]);

    $resep = Resep::findOrFail($resepId);

    // Sinkronisasi kategori (menghapus yang lama dan mengganti dengan yang baru)
    $resep->kategori()->sync($request->kategori_ids);

    return response()->json([
        'message' => 'Kategori pada resep berhasil diperbarui.',
        'kategori_terkait' => $resep->kategori()->get()
    ]);
}


    // public function store(Request $request, $id)
    // {
    //     $request->validate([
    //         'kategori' => 'required|string',
    //     ]);

    //     $resep = Resep::findOrFail($id);

    //     // Tambahkan entri baru ke riwayat_pengiriman (asumsinya array atau teks log)
    //     $existingKategori = $resep->kategori_resep ?? [];

    //     if (!is_array($existingKategori)) {
    //         $existingKategori = explode("\n", $existingKategori); // fallback kalau disimpan sebagai string
    //     }

    //     // Tambahkan riwayat baru ke array
    //     $existingKategori[] = $request->riwayat;

    //     // Simpan ke database, bisa berupa JSON atau string tergantung field-nya
    //     $resep->kategori_resep = json_encode($existingKategori); 
    //     $resep->save();

    //     return response()->json(['message' => 'Kategori berhasil ditambahkan.']);
    // }

    public function kategori()
    {
        return $this->belongsTo(Resep::class, 'kategori', 'kategori');
    }
    // public function show($kategori)
    // {
    //     $resep = Resep::where('kategori', $kategori)->firstOrFail();
    //     $kategori = Kategori::where('kategori_id', $kategori)->orderBy('created_at')->get();

    //     return response()->json([
    //         'resep' => $resep,
    //         'kategori' => $kategori,
    //     ]);
    // } 

    public function show($id)
    {
        if (!is_numeric($id)) {
            return response()->json(['message' => 'ID tidak valid'], 400);
        }

        $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        return response()->json($kategori);
    }

    // metode lainnya seperti index, store, update, destroy...


//     public function show(Resep $resep)
// {
//     // Load relasi yang diperlukan
//     $resep->load([
//         'kategori', // tidak perlu 'resep.kategori'
//         'alat',
//         'bahan',
//         'langkah',
//         'tag',
//         'komentar',
//         'rating',
//         'favorit'
//     ]);

//     return response()->json([
//         'resep' => [
//             'judul' => $resep->judul,
//             'deskripsi' => $resep->deskripsi,
//             'waktu_masak' => $resep->waktu_masak,
//             'gambar' => $resep->gambar,
//             'kategori' => $resep->kategori->pluck('nama'), // ambil nama-nya langsung
//             'rating' => $resep->averageRating(),
//             'alat' => $resep->alat,
//             'bahan' => $resep->bahan,
//             'langkah' => $resep->langkah,
//             'komentar' => $resep->komentar,
//             'tag' => $resep->tag,
//             'favorit' => $resep->favorit,
//             'rating_count' => $resep->rating->count(),
//             'rating_average' => $resep->averageRating(),
//         ],
//     ]);
// }

    
}
// $data = Resep::with('kategori')->get();
// return response()->json($data);
