<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class MessageSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        Chat::all()->each(function ($chat) use ($faker) {
            $user1 = $chat->user1;
            $user2 = $chat->user2;

            // Generate messages from user1
            for ($i = 0; $i < 20; $i++) {
                $message = new Message();
                $message->chat_id = $chat->id;
                $message->sender_id = $user1->id;
                $message->receiver_id = $user2->id;
                $message->content = $faker->sentence();
                $message->sender_vue = true;
                $message->receiver_vue = true;
                $message->is_read = false;
                $message->created_at = $faker->dateTimeBetween('-2 weeks', 'now');
                $message->save();
            }

            // Generate messages from user2
            for ($i = 0; $i < 20; $i++) {
                $message = new Message();
                $message->chat_id = $chat->id;
                $message->sender_id = $user2->id;
                $message->receiver_id = $user1->id;
                $message->content = $faker->sentence();
                $message->sender_vue = true;
                $message->receiver_vue = true;
                $message->is_read = false;
                $message->created_at = $faker->dateTimeBetween('-2 weeks', 'now');
                $message->save();
            }
        });
    }
}
