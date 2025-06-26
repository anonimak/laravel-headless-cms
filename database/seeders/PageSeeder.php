<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // You can use factories to create pages or manually insert them
        // Example of using a factory (assuming you have a PageFactory):
        \App\Models\Page::factory()->count(10)->create();

        // Alternatively, you can manually create pages like this:
        // \App\Models\Page::create([
        //     'title' => 'Sample Page',
        //     'slug' => 'sample-page',
        //     'body' => 'This is a sample page body.',
        //     'status' => 'published',
        //     'created_by' => 1, // Assuming user with ID 1 exists
        // ]);
    }
}
