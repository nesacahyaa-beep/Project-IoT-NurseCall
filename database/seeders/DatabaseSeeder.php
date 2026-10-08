<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

            \App\Models\Room::insert([
            ['code' => 'R101', 'name' => '101', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'R102', 'name' => '102', 'created_at' => now(), 'updated_at' => now()],
        ]);

        \App\Models\Item::insert([
            ['name' => 'Sarung tangan', 'unit' => 'box', 'stock' => 10, 'min_stock' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Infus set',     'unit' => 'pcs', 'stock' => 20, 'min_stock' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
        }
}
