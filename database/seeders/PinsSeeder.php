<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\Category;
use App\Models\Pin;
use App\Models\User;
use Faker\Core\File;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Faker\Factory as Faker;

class PinSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        Storage::makeDirectory('public/pins');

        for ($i = 0; $i < 11; $i++) {
            $pin = new Pin();
            $pin->user_id = User::inRandomOrder()->first()->id;
            $pin->category_id = Category::inRandomOrder()->first()->id;
            $pin->board_id = $pin->user->boards()->inRandomOrder()->first()->id; // Assigning a random board from the user's boards
            $pin->title = $faker->sentence(3);
            $pin->description = $faker->sentence(10);

            // Generate and store the image
            $image = storage_path('app/pin.png');
            $path = Storage::putFileAs('public/pins', new \Illuminate\Http\File($image), uniqid() . '.png');
            $pin->pin = basename($path);
            $pin->save();
        }

    }
}
