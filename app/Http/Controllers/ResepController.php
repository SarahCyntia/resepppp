<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResepRequest;
use App\Http\Requests\UpdateResepRequest;
use App\Models\Resep;
use App\Models\Kategori;
use App\Models\Tag;
use App\Models\Bahan;
use App\Models\Langkah;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ResepController extends Controller
{
    // app/Http/Controllers/ResepController.php
// public function index()
// {
//     $resep = Resep::with('kategori', 'user')->get()->map(function ($r) {
//         return [
//             'id' => $r->id,
//             'title' => $r->judul,
//             'kategori' => $r->kategori->nama ?? 'Tidak ada',
//             'waktu' => $r->waktu,
//             'rating' => $r->rating,
//             'creator' => $r->user->name ?? 'Tidak diketahui',
//         ];
//     });

    //     return response()->json($resep);
// }

    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Resep::with(['user', 'kategori', 'tag'])
            ->when($request->search, function ($query, $search) {
                $query->where('judul', 'like', "%$search%")
                    ->orWhere('deskripsi', 'like', "%$search%")
                    ->orWhere('bahan', 'like', "%$search%")
                    ->orWhere('alat', 'like', "%$search%")
                    ->orWhere('langkah', 'like', "%$search%")
                    ->orWhere('waktu_masak', 'like', "%$search%");
            })
            ->latest()
            ->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);
        $reseps = Resep::with(['bahan', 'alat', 'langkah'])->paginate(10);
        return response()->json($reseps);

        // return response()->json($data);
    }

    // public function show(Resep $resep)
    // {
    //     $resep->load(['user', 'kategori', 'tag', 'alat', 'bahan', 'langkah']);
    //     return response()->json(['resep' => $resep]);
    // }

    // public function show($id)
    // {
    //     $resep = Resep::findOrFail($id);
    //     $kategori = Kategori::where('kategori_id', $id)->orderBy('created_at')->get();

    //     return response()->json([
    //         'resep' => $resep,
    //         'kategori' => $kategori,
    //     ]);
    // }
    public function show(Resep $resep)
    {
        // Load relasi yang diperlukan
        $resep->load([
            'kategori', // tidak perlu 'resep.kategori'
            'alat',
            'bahan',
            'langkah',
            'tag',
            'komentar',
            'rating',
            'favorit'
        ]);

        return response()->json([
            'resep' => [
                'judul' => $resep->judul,
                'deskripsi' => $resep->deskripsi,
                'waktu_masak' => $resep->waktu_masak,
                'gambar' => $resep->gambar,
                'kategori' => $resep->kategori_id, // ambil nama-nya langsung
                'rating' => $resep->averageRating(),
                'alat' => $resep->alat,
                'bahan' => $resep->bahan,
                'langkah' => $resep->langkah,
                'komentar' => $resep->komentar,
                'tag' => $resep->tag,
                'favorit' => $resep->favorit,
                'rating_count' => $resep->rating->count(),
                'rating_average' => $resep->averageRating(),
            ],
        ]);
    }


    // public function store(Request $request)
    // {
    //     $validated = $request->validated();
    //     $validated['user_id'] = Auth::id();

    //     if ($request->hasFile('gambar')) {
    //         $validated['gambar'] = $request->file('gambar')->store('gambar', 'public');
    //     }

    //     $resep = Resep::create($validated);

    //     if ($request->kategori_id) {
    //         $resep->kategori()->attach($request->kategori_id);
    //     }

    //     if ($request->tag_id) {
    //         $resep->tag()->attach($request->tag_id);
    //     }

    //     if ($request->alat) {
    //         foreach ($request->alat as $a) {
    //             $resep->alat()->create(['nama' => $a['nama']]);
    //         }
    //     }

    //     if ($request->bahan) {
    //         foreach ($request->bahan as $b) {
    //             $resep->bahan()->create(["nama" => $b['nama'], "jumlah" => $b['jumlah']]);
    //         }
    //     }

    //     if ($request->langkah) {
    //         foreach ($request->langkah as $l) {
    //             $resep->langkah()->create(["deskripsi" => $l['deskripsi'], "urutan" => $l['urutan']]);
    //         }
    //     }

    //     return response()->json(['success' => true, 'resep' => $resep->load(['kategori', 'tag', 'alat', 'bahan', 'langkah'])]);
    // }

    public function store(Request $request)
{
    $validated = $request->validate([
        'judul' => 'required|string|max:255',
        'kategori_id' => 'nullable|exists:kategori,kategori_id',
        'kategori_nama' => 'nullable|string|max:255',
        'deskripsi' => 'nullable|string',
        'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'waktu_masak' => 'nullable|string',
        'bahan' => 'required|array|min:1',
        'alat' => 'required|array|min:1',
        'langkah' => 'required|array|min:1',
    ]);

    if ($request->hasFile('gambar')) {
        $validated['gambar'] = $request->file('gambar')->store('resep', 'public');
    }

    DB::beginTransaction();
    try {
        $kategoriId = $validated['kategori_id'] ?? null;

        if (!$kategoriId && !empty($validated['kategori_nama'])) {
            $kategori = Kategori::firstOrCreate(['nama' => $validated['kategori_nama']]);
            $kategoriId = $kategori->id;
        }

        $resep = Resep::create([
            'judul' => $validated['judul'],
            'kategori_id' => $validated['kategori_id'] ,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'gambar' => $validated['gambar'] ?? null,
            'waktu_masak' => $validated['waktu_masak'] ?? null,
            'user_id' => Auth::id(),
        ]);

        foreach ($validated['bahan'] as $nama) {
            if (!empty(trim($nama))) {
                $resep->bahan()->create(['nama' => $nama]);
            }
        }

        foreach ($validated['alat'] as $nama) {
            if (!empty(trim($nama))) {
                $resep->alat()->create(['nama' => $nama]);
            }
        }

        foreach ($validated['langkah'] as $i => $deskripsi) {
            if (!empty(trim($deskripsi))) {
                $resep->langkah()->create([
                    'urutan' => $i + 1,
                    'deskripsi' => $deskripsi,
                ]);
            }
        }

        DB::commit();
        return response()->json([
            'message' => 'Resep berhasil ditambahkan',
            'resep' => $resep->load(['kategori', 'bahan', 'alat', 'langkah']),
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error($e);
        return response()->json([
            'message' => 'Gagal menyimpan resep',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'judul' => 'required|string|max:255',
    //         // 'kategori_id' => 'nullable|exists:kategori,id',
    //         'kategori_id' => 'nullable|exists:kategori,id_kategori',
    //         'kategori_nama' => 'nullable|string|max:255',

    //         'deskripsi' => 'nullable|string',
    //         'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //         'waktu_masak' => 'nullable|string',

    //         'bahan' => 'required|array|min:1',
    //         'alat' => 'required|array|min:1',
    //         'langkah' => 'required|array|min:1',

    //         // 'bahan' => 'required|array',
    //         // 'alat' => 'required|array',
    //         // 'langkah' => 'required|array',
    //     ]);

    //     // Simpan gambar jika ada
    //     if ($request->hasFile('gambar')) {
    //         $validated['gambar'] = $request->file('gambar')->store('resep', 'public');
    //     }

    //     DB::beginTransaction();
    //     try {
    //         // Cek kategori: pakai ID atau nama baru
    //         $kategoriId = $validated['kategori_id'] ?? null;

    //         if (!$kategoriId && !empty($validated['kategori_nama'])) {
    //             $kategori = Kategori::firstOrCreate(['nama' => $validated['kategori_nama']]);
    //             $kategoriId = $kategori->id;
    //         }

    //         // Simpan resep utama
    //         $resep = Resep::create([
    //             'judul' => $validated['judul'],
    //             'kategori_id' => $kategoriId,
    //             'deskripsi' => $validated['deskripsi'] ?? null,
    //             'gambar' => $validated['gambar'] ?? null,
    //             'waktu_masak' => $validated['waktu_masak'] ?? null,
    //             'user_id' => Auth::id(),
    //         ]);

    //         // Simpan bahan
    //         foreach ($validated['bahan'] as $nama) {
    //             if (is_string($nama) && trim($nama) !== '') {
    //                 $resep->bahan()->create(['nama' => $nama]);
    //             }
    //         }

    //         // Simpan alat
    //         foreach ($validated['alat'] as $nama) {
    //             if (is_string($nama) && trim($nama) !== '') {
    //                 $resep->alat()->create(['nama' => $nama]);
    //             }
    //         }

    //         // Simpan langkah dengan urutan
    //         foreach ($validated['langkah'] as $i => $deskripsi) {
    //             if (is_string($deskripsi) && trim($deskripsi) !== '') {
    //                 $resep->langkah()->create([
    //                     'urutan' => $i + 1,
    //                     'deskripsi' => $deskripsi,
    //                 ]);
    //             }
    //         }

    //         DB::commit();
    //         return response()->json([
    //             'message' => 'Resep berhasil ditambahkan',
    //             'resep' => $resep->load(['kategori', 'bahan', 'alat', 'langkah']),
    //         ], 201);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'message' => 'Gagal menyimpan resep',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }



    public function update(Request $request, Resep $resep)
{

    Log::info('Updating resep with ID: ' . $resep);

    $validated = $request->validate([
        'judul' => 'required|string',
        'waktu_masak' => 'required|string',
        'kategori_id' => 'required|exists:kategori,kategori_id',
        'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'alatList' => 'nullable|array',
        'alatList.*.nama' => 'required|string',
        'bahanList' => 'nullable|array',    
        'bahanList.*.nama' => 'required|string',
        'langkahList' => 'nullable|array',
        'langkahList.*.deskripsi' => 'required|string',
    ]);

    // Update gambar jika ada
    if ($request->hasFile('gambar')) {
        $path = $request->file('gambar')->store('resep', 'public');
        $validated['gambar'] = $path;
    }

    // Update resep utama
    $resep->update($validated);

    // Update alatList
if ($request->has('alat')) {
    $resep->alatList()->delete();
    foreach ($request->alat as $nama) {
        $resep->alatList()->create(['nama' => $nama]);
    }
}

// Update bahanList
if ($request->has('bahan')) {
    $resep->bahanList()->delete();
    foreach ($request->bahan as $nama) {
        $resep->bahanList()->create(['nama' => $nama]);
    }
}

// Update langkahList
if ($request->has('langkah')) {
    $resep->langkahList()->delete();
    foreach ($request->langkah as $index => $deskripsi) {
        $resep->langkahList()->create([
            'deskripsi' => $deskripsi,
            'urutan' => $index + 1
        ]);
    }
}



    return response()->json([
        'message' => 'Resep berhasil diperbarui',
        'resep' => $resep->load(['alatList', 'bahanList', 'langkahList'])
    ]);
}

    //ini sebelumnya
    // public function update(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'judul' => 'required|string|max:255',
    //         'kategori_id' => 'nullable|exists:kategori,id',
    //         'kategori_nama' => 'nullable|string|max:255',

    //         'deskripsi' => 'nullable|string',
    //         'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //         'waktu_masak' => 'nullable|string',

    //         'bahan' => 'required|array',
    //         'alat' => 'required|array',
    //         'langkah' => 'required|array',
    //     ]);

    //     $resep = Resep::with(['bahan', 'alat', 'langkah'])->findOrFail($id);

    //     DB::beginTransaction();
    //     try {
    //         // Update gambar jika ada file baru
    //         if ($request->hasFile('gambar')) {
    //             if ($resep->gambar && Storage::disk('public')->exists($resep->gambar)) {
    //                 Storage::disk('public')->delete($resep->gambar);
    //             }

    //             $validated['gambar'] = $request->file('gambar')->store('resep', 'public');
    //         }

    //         // Cek kategori
    //         $kategoriId = $validated['kategori_id'] ?? null;
    //         if (!$kategoriId && !empty($validated['kategori_nama'])) {
    //             $kategori = Kategori::firstOrCreate(['nama' => $validated['kategori_nama']]);
    //             $kategoriId = $kategori->id;
    //         }

    //         // Update data utama resep
    //         $resep->update([
    //             'judul' => $validated['judul'],
    //             'kategori_id' => $kategoriId,
    //             'deskripsi' => $validated['deskripsi'] ?? null,
    //             'gambar' => $validated['gambar'] ?? $resep->gambar,
    //             'waktu_masak' => $validated['waktu_masak'] ?? null,
    //         ]);

    //         // Hapus semua data bahan, alat, langkah sebelumnya
    //         $resep->bahan()->delete();
    //         $resep->alat()->delete();
    //         $resep->langkah()->delete();

    //         // Tambahkan data bahan baru
    //         foreach ($validated['bahan'] as $nama) {
    //             if (is_string($nama) && trim($nama) !== '') {
    //                 $resep->bahan()->create(['nama' => $nama]);
    //             }
    //         }

    //         // Tambahkan data alat baru
    //         foreach ($validated['alat'] as $nama) {
    //             if (is_string($nama) && trim($nama) !== '') {
    //                 $resep->alat()->create(['nama' => $nama]);
    //             }
    //         }

    //         // Tambahkan data langkah baru
    //         foreach ($validated['langkah'] as $i => $deskripsi) {
    //             if (is_string($deskripsi) && trim($deskripsi) !== '') {
    //                 $resep->langkah()->create([
    //                     'urutan' => $i + 1,
    //                     'deskripsi' => $deskripsi,
    //                 ]);
    //             }
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'message' => 'Resep berhasil diperbarui',
    //             'resep' => $resep->load(['kategori', 'bahan', 'alat', 'langkah']),
    //         ], 200);

    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'message' => 'Gagal memperbarui resep',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }


    // public function store(Request $request)
// {
//      $validated = $request->validate([
//         'judul'         => 'required|string|max:255',
//         // 'user_id'  => 'required|exists:users,id',
//         'kategori_id'  => 'required|exists:kategori,id',
//         'kategori_nama' => 'nullable|string|max:255', // tambahkan ini
//         'deskripsi'    => 'nullable|string',
//         'gambar'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
//         'waktu_masak'    => 'nullable|string',

    //         'bahan'        => 'required|array',
//         'bahan.*.nama' => 'required|string',

    //         'alat'         => 'required|array',
//         'alat.*.nama'  => 'required|string',

    //         'langkah'             => 'required|array',
//         'langkah.*.urutan'    => 'required|integer',
//         'langkah.*.deskripsi' => 'required|string',
//     ]);

    //     // Simpan foto jika ada
//     if ($request->hasFile('foto')) {
//         $validated['foto'] = $request->file('foto')->store('resep', 'public');
//     }

    //     DB::beginTransaction();
//     try {
//         $resep = Resep::create([
//     'judul'        => $validated['judul'],
//     'kategori_id'  => $validated['kategori_id'],
//     'deskripsi'    => $validated['deskripsi'] ?? null,
//     'gambar'       => $validated['foto'] ?? null,
//     'waktu_masak'  => $validated['waktu_masak'] ?? null,
//     'user_id'      => Auth::id(),
//     ]);
//     $kategoriId = $validated['kategori_id'] ?? null;

    //     if (!$kategoriId && !empty($validated['kategori_nama'])) {
//         $kategori = Kategori::firstOrCreate(['nama' => $validated['kategori_nama']]);
//         $kategoriId = $kategori->id;
//     }

    //         // $resep = Resep::create([
//         //     'judul'        => $validated['nama'],
//         //     'kategori_id' => $validated['kategori_id'],
//         //     'deskripsi'   => $validated['deskripsi'] ?? null,
//         //     'gambar'        => $validated['foto'] ?? null,
//         //     'waktu_masak'   => $validated['waktu_masak'] ?? null,
//         // ]);

    //         // Simpan bahan
//         foreach ($validated['bahan'] as $item) {
//             $resep->bahan()->create([
//                 // 'resep_id' => $item['resep_id'],
//                 'nama' => $item['nama'],
//                 'jumlah' => $item['jumlah'],
//             ]);
//         }

    //         // Simpan alat
//         foreach ($validated['alat'] as $item) {
//             $resep->alat()->create([
//                 // 'resep_id' => $item['resep_id'],
//                 'nama' => $item['nama'],
//             ]);
//         }

    //         // Simpan langkah
//         foreach ($validated['langkah'] as $item) {
//             $resep->langkah()->create([
//                 // 'resep_id' => $item['resep_id'],
//                 'deskripsi' => $item['deskripsi'],
//                 'urutan'    => $item['urutan'],
//             ]);
//         }

    //         DB::commit();
//         return response()->json([
//             'message' => 'Resep berhasil ditambahkan',
//             'resep'   => $resep->load(['kategori', 'bahan', 'alat', 'langkah']),
//         ], 201);
//     } catch (\Exception $e) {
//         DB::rollBack();
//         return response()->json([
//             'message' => 'Gagal menyimpan resep',
//             'error' => $e->getMessage(),
//         ], 500);
//     }
// }


    // public function update(Request $request, Resep $resep)
    // {
    //     $validated = $request->validated();

    //     if ($request->hasFile('gambar')) {
    //         if ($resep->gambar) {
    //             Storage::disk('public')->delete($resep->gambar);
    //         }
    //         $validated['gambar'] = $request->file('gambar')->store('gambar', 'public');
    //     }

    //     $resep->update($validated);

    //     $resep->kategori()->sync($request->kategori_id ?? []);
    //     $resep->tag()->sync($request->tag_id ?? []);

    //     $resep->alat()->delete();
    //     foreach ($request->alat ?? [] as $a) {
    //         $resep->alat()->create(['nama' => $a['nama']]);
    //     }

    //     $resep->bahan()->delete();
    //     foreach ($request->bahan ?? [] as $b) {
    //         $resep->bahan()->create(['nama' => $b['nama'], 'jumlah' => $b['jumlah']]);
    //     }

    //     $resep->langkah()->delete();
    //     foreach ($request->langkah ?? [] as $l) {
    //         $resep->langkah()->create(['deskripsi' => $l['deskripsi'], 'urutan' => $l['urutan']]);
    //     }

    //     return response()->json(['success' => true, 'resep' => $resep->load(['kategori', 'tag', 'alat', 'bahan', 'langkah'])]);
    // }

    public function destroy(Resep $resep)
    {
        if ($resep->gambar) {
            Storage::disk('public')->delete($resep->gambar);
        }

        $resep->kategori()->detach();
        $resep->tag()->detach();
        $resep->alat()->delete();
        $resep->bahan()->delete();
        $resep->langkah()->delete();
        $resep->delete();

        return response()->json(['success' => true]);
    }
}
