<?php 

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resep;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'nilai' => 'required|integer|min:1|max:5',
        ]);

        $resep = Resep::findOrFail($id);
        $rating = Rating::updateOrCreate(
            ['user_id' => Auth::id(), 'resep_id' => $id],
            ['nilai' => $request->nilai]
        );

        return response()->json(['message' => 'Rating berhasil disimpan', 'data' => $rating]);
    }
}
