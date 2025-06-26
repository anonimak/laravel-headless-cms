<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // You can use factories to create posts or manually insert them
        // Example of using a factory (assuming you have a PostFactory):
        \App\Models\Post::factory()->count(10)->create();

        // Alternatively, you can manually create posts like this:
        // \App\Models\Post::create([
        //     'title' => 'Sample Post',
        //     'slug' => 'sample-post',
        //     'body' => 'This is a sample post body.',
        //     'status' => 'published',
        //     'created_by' => 1, // Assuming user with ID 1 exists
        // ]);
    }
}
