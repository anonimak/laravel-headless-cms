<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // You can use factories to create categories or manually insert them
        // Example of using a factory (assuming you have a CategoryFactory):
        \App\Models\Category::factory()->count(10)->create();

        // Alternatively, you can manually create categories like this:
        // \App\Models\Category::create([
        //     'name' => 'Sample Category',
        //     'slug' => 'sample-category',
        //     'description' => 'This is a sample category description.',
        //     'parent_id' => null, // or a valid parent ID if needed
        //     'created_by' => 1, // Assuming user with ID 1 exists
        //     'updated_by' => 1, // Assuming user with ID 1 exists
        //     'deleted_by' => null, // null for not deleted
        //     'uuid' => \Illuminate\Support\Str::uuid()->toString(),
        // ]);
    }
}
