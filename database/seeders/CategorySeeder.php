<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Nature', 'Travel', 'Food', 'Art', 'Fashion', 'Fitness', 'Music', 'Sports',
            'Pets', 'Technology', 'Education', 'Books', 'Movies', 'Cars', 'Architecture',
            'Design', 'Gardening', 'DIY', 'Beauty', 'Business'
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
            ]);
        }
    }
}
