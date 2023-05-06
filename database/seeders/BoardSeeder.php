<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;


class BoardSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Get all users
        $users = User::all();

        // Create 10 boards
        for ($i = 0; $i < 10; $i++) {
            // Choose a random user
            $user =User::find($i+1);

            // Create a new board with a random name
            $board = new Board();
            $board->name = $faker->sentence(3);
            $board->user_id = $user->id;
            $board->save();
        }
    }
}
