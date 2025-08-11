<?php 

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resep;
use App\Models\Komentar;
use Illuminate\Support\Facades\Auth;

class KomentarController extends Controller
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'isi' => 'required|string',
        ]);

        $resep = Resep::findOrFail($id);
        $komentar = $resep->komentar()->create([
            'user_id' => Auth::id(),
            'isi' => $request->isi
        ]);

        return response()->json(['message' => 'Komentar ditambahkan', 'data' => $komentar->load('user')]);
    }
}
