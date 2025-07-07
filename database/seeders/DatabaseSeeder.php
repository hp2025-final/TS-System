<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users only if they don't exist
        if (!User::where('email', 'admin@tspos.com')->exists()) {
            User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@tspos.com',
                'role' => 'admin'
            ]);
            $this->command->info('Created admin user: admin@tspos.com');
        } else {
            $this->command->info('Admin user already exists: admin@tspos.com');
        }

        if (!User::where('email', 'staff@tspos.com')->exists()) {
            User::factory()->create([
                'name' => 'Staff User',
                'email' => 'staff@tspos.com',
                'role' => 'staff'
            ]);
            $this->command->info('Created staff user: staff@tspos.com');
        } else {
            $this->command->info('Staff user already exists: staff@tspos.com');
        }

        // Seed collections and dresses
        $this->call([
            CollectionSeeder::class,
            DressSeeder::class,
            DressItemSeeder::class,
        ]);
    }
}
