<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('service_name')->get();

        return response()->json($services);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_name' => ['required', 'string', 'max:255', 'unique:services,service_name'],
            'price_per_kg' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $service = Service::create([
            'service_name' => $validated['service_name'],
            'price_per_kg' => $validated['price_per_kg'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json($service, 201);
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'service_name' => [
                'required',
                'string',
                'max:255',
                'unique:services,service_name,' . $service->id,
            ],
            'price_per_kg' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        $service->update($validated);

        return response()->json($service->fresh());
    }
}