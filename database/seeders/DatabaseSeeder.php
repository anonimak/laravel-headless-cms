<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
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

        $this->call([
            CategorySeeder::class,
            PostSeeder::class,
            PageSeeder::class,
        ]);

        // Setelah semua seeder selesai, panggil scout:import
        if (app()->environment(['local', 'development'])) {
            $this->command->info('Importing models into Scout...');

            Artisan::call('scout:import', ['model' => Category::class]);
            $this->command->line('Product model imported successfully.');

            Artisan::call('scout:import', ['model' => Post::class]);
            $this->command->line('Post model imported successfully.');

            $this->command->info('All models imported.');
        }
    }
}
