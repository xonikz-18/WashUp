<?php

namespace Database\Seeders;

use App\Models\Addon;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate([
            'username' => 'admin',
        ], [
            'password' => Hash::make('password'),
        ]);

        Service::firstOrCreate([
            'service_name' => 'Wash Only',
        ], [
            'price_per_kg' => 25.00,
        ]);

        Service::firstOrCreate([
            'service_name' => 'Wash & Dry',
        ], [
            'price_per_kg' => 50.00,
        ]);

        Addon::firstOrCreate([
            'addon_name' => 'Fabric Conditioner',
        ], [
            'addon_price' => 15.00,
        ]);

        Addon::firstOrCreate([
            'addon_name' => 'Extra Drying',
        ], [
            'addon_price' => 20.00,
        ]);
    }
}
