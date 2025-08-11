<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Bahan;
use App\Models\Resep;
use App\Models\Kategori;
use App\Models\Langkah;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReseppController extends Controller
{
    public function get(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => Resep::with(['user', 'kategori', 'tag', 'bahan', 'langkah'])
                ->when($request->kategori_id, fn ($q) => $q->whereHas('kategori', fn ($qr) => $qr->where('kategori.id', $request->kategori_id)))
                ->when($request->tag_id, fn ($q) => $q->whereHas('tag', fn ($qr) => $qr->where('tag.id', $request->tag_id)))
                ->get()
        ]);
    }

    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Resep::with(['user', 'kategori', 'tag'])
            ->when($request->search, function ($q, $search) {
                $q->where('judul', 'like', "%$search%")
                    ->orWhere('deskripsi', 'like', "%$search%");
            })
            ->latest()
            ->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'waktu_masak' => 'required|string|max:50',
            'gambar' => 'nullable|image',
            'kategori' => 'array',
            'tag' => 'array',
            'bahan' => 'array',
            'langkah' => 'array',
        ]);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('resep', 'public');
        }

        $validated['user_id'] = auth()->id();
        $resep = Resep::create([
            "deskripsi" => $request->deskripsi,
        ]);

        $bahan = Bahan::creat([
            "resep_id" => $request->resep_id,
            "nama" => $request->nama,
            "jumlah" => $request->jumlah,
        ]);
        $alat = Alat::creat([
            "resep_id" => $request->resep_id,
            "nama" => $request->nama,
        ]);
        $langkah = Langkah::creat([
            "resep_id" => $request->resep_id,
            "deskripsi" => $request->deskripsi,
            "urutan" => $request->urutan,
        ]);

        // Kategori & Tag
        $resep->kategori()->sync($request->kategori);
        $resep->tag()->sync($request->tag);

        // Bahan
        foreach ($request->bahan ?? [] as $item) {
            $resep->bahan()->create([
                'nama' => $item['nama'],
                'jumlah' => $item['jumlah'] ?? null
            ]);
        }

        // Langkah
        foreach ($request->langkah ?? [] as $i => $step) {
            $resep->langkah()->create([
                'deskripsi' => $step['deskripsi'],
                'urutan' => $i + 1,
                'gambar' => $step['gambar'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'resep' => $resep->load(['kategori', 'tag', 'bahan', 'langkah']),
        ]);
    }

    public function show(Resep $resep)
    {
        $resep->load(['user', 'kategori', 'tag', 'bahan', 'langkah', 'komentar.user', 'rating.user']);
        return response()->json([
            'resep' => $resep,
        ]);
    }

    public function update(Request $request, Resep $resep)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'waktu_masak' => 'required|string|max:50',
            'gambar' => 'nullable|image',
            'kategori' => 'array',
            'tag' => 'array',
            'bahan' => 'array',
            'langkah' => 'array',
        ]);

        if ($request->hasFile('gambar')) {
            if ($resep->gambar) {
                Storage::disk('public')->delete($resep->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('resep', 'public');
        }

        $resep->update($validated);

        // Sync kategori dan tag
        $resep->kategori()->sync($request->kategori);
        $resep->tag()->sync($request->tag);

        // Reset bahan & langkah
        $resep->bahan()->delete();
        $resep->langkah()->delete();

        foreach ($request->bahan ?? [] as $item) {
            $resep->bahan()->create([
                'nama' => $item['nama'],
                'jumlah' => $item['jumlah'] ?? null
            ]);
        }

        foreach ($request->langkah ?? [] as $i => $step) {
            $resep->langkah()->create([
                'deskripsi' => $step['deskripsi'],
                'urutan' => $i + 1,
                'gambar' => $step['gambar'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'resep' => $resep->load(['kategori', 'tag', 'bahan', 'langkah']),
        ]);
    }

    public function destroy(Resep $resep)
    {
        if ($resep->gambar) {
            Storage::disk('public')->delete($resep->gambar);
        }

        $resep->bahan()->delete();
        $resep->langkah()->delete();
        $resep->kategori()->detach();
        $resep->tag()->detach();
        $resep->delete();

        return response()->json(['success' => true]);
    }
}
