<?php

namespace App\Http\Controllers;

use App\Http\Resources\PinCollection;
use App\Http\Resources\UserResource;
use App\Models\Pin;
use App\Models\Search;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index()
    {
        $user = User::find(Auth::id());
        $history = $user->searches()->latest()->take(8)->get();
        return response()->json(['history' => $history]);
    }

    public function search(Request $request)
    {
        $query = $request->input('q');

        // Get all pins
        $pins = Pin::all();

        // Calculate similarity score for each pin
        $pins = $pins->map(function ($pin) use ($query) {
            similar_text($query, $pin->title, $titleSimilarity);
            similar_text($query, $pin->description, $descriptionSimilarity);
            $pin->similarity = max($titleSimilarity, $descriptionSimilarity);
            return $pin;
        });

        // Sort pins by similarity score
        $pins = $pins->sortByDesc('similarity');

        // Store the search query in the searches table
        Search::create([
            'query' => $query,
            'user_id' => $request->user()->id
        ]);

        return new PinCollection($pins);
    }


    function getSuggestions(Request $request)
    {
        $query = $request->q;
        $users = User::search($query)->take(10)->get();
        $pins = Pin::search($query)->take(10)->get();

        $results = collect();
        foreach ($users as $user) {
            $results->push([
                'type' => 'user',
                'user' => new UserResource($user),
            ]);
        }
        foreach ($pins as $pin) {
            $results->push([
                'type' => 'pin',
                'title' => $pin->title
            ]);
        }

        return $results->sortByDesc('similarity')->take(10);
    }
}
