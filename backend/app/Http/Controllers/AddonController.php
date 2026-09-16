<?php

namespace App\Http\Controllers;

use App\Models\Addon;

class AddonController extends Controller
{
    public function index()
    {
        $addons = Addon::orderBy('addon_name')->get();

        return response()->json($addons);
    }
}
