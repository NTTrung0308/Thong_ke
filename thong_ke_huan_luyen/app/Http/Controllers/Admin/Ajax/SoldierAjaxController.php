<?php

namespace App\Http\Controllers\Admin\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Soldier;
use Illuminate\Http\Request;

class SoldierAjaxController extends Controller
{
    public function quickView(Soldier $soldier)
    {
        $this->authorize('view', $soldier);
        
        $soldier->load(['unit', 'weapons', 'rewards', 'disciplines']);
        
        return response()->json([
            'success' => true,
            'html' => view('backend.search.partials.quick_view', compact('soldier'))->render()
        ]);
    }
}
