<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SeoGeneration;
use Illuminate\Support\Facades\Auth;

class SeoHistoryController extends Controller
{
    // Liste paginée des générations
    public function index()
    {
        $items = SeoGeneration::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.seo.history', compact('items'));
    }

    // Réutiliser une génération passée
    public function reuse(SeoGeneration $generation)
{
    if ($generation->user_id !== Auth::id()) {
        abort(403);
    }

    return redirect()->route('user.projects.seo', [
        'prompt' => $generation->prompt,
        'lang'   => $generation->lang,
    ])->with('prefill', true);
    
}



public function destroy(SeoGeneration $generation)
{
    if ($generation->user_id !== Auth::id()) {
        abort(403);
    }

    $generation->delete();

    return redirect()->route('seo.history.index')
        ->with('success', 'Historique supprimé avec succès.');
}


}
