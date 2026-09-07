<?php

namespace Modules\Rosca\Http\Controllers\Web;

use Illuminate\Routing\Controller;
use Modules\Rosca\Models\Rosca;

class RoscaController extends Controller
{
    public function index()
    {
        $roscas = Rosca::withCount('members')->get();

        return view('rosca::index', compact('roscas'));
    }
}
