<?php 

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Resep;

class FavoritController extends Controller
{
    public function toggle($id)
    {
        $resep = Resep::findOrFail($id);
        $user = Auth::user();

        $isFavorit = $user->favorit()->where('resep_id', $id)->exists();

        if ($isFavorit) {
            $user->favorit()->detach($id);
            return response()->json(['message' => 'Dihapus dari favorit']);
        } else {
            $user->favorit()->attach($id);
            return response()->json(['message' => 'Ditambahkan ke favorit']);
        }
    }
}