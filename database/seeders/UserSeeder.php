<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create 10 users
        for ($i = 0; $i < 10; $i++) {
            $faker = Faker::create();
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password')
            ]);

            // Set a profile image for 2 users
                $image = storage_path('app/Darkinism.jpg');
                $path = Storage::putFileAs('public/profiles', new \Illuminate\Http\File($image), uniqid() . '.png');
                $user->profile = $image;
                $user->save();
        }
    }
}
