<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use Illuminate\Http\Request;

class AddonController extends Controller
{
    public function index()
    {
        $addons = Addon::orderBy('addon_name')->get();

        return response()->json($addons);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'addon_name' => ['required', 'string', 'max:255', 'unique:addons,addon_name'],
            'addon_price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $addon = Addon::create([
            'addon_name' => $validated['addon_name'],
            'addon_price' => $validated['addon_price'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json($addon, 201);
    }

    public function update(Request $request, Addon $addon)
    {
        $validated = $request->validate([
            'addon_name' => [
                'required',
                'string',
                'max:255',
                'unique:addons,addon_name,' . $addon->id,
            ],
            'addon_price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        $addon->update($validated);

        return response()->json($addon->fresh());
    }
}