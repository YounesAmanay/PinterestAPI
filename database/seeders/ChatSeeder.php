<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = User::all();

        foreach ($users as $user) {
            $otherUser = $users->where('id', '!=', $user->id)->random();

            // ensure that the chat between these two users doesn't already exist
            while (
                $user->chats()->where('user1_id', $user->id)->where('user2_id', $otherUser->id)->exists()
                || $user->chats()->where('user1_id', $otherUser->id)->where('user2_id', $user->id)->exists()
            ) {
                $otherUser = $users->where('id', '!=', $user->id)->random();
            }

            // create the chat between the two users
            $chat = new Chat();
            $chat->user1_id = $user->id;
            $chat->user2_id = $otherUser->id;
            $chat->save();

            $chat->users()->attach([$user->id, $otherUser->id]); // attach the users to the chat
        }
    }
}
