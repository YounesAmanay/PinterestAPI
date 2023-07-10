<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BoardController extends Controller
{

    public function index()
    {
        $user = User::where('id',Auth::id())->first();
        $boards = $user->boards()->with('pins')->orderByDesc('updated_at')->get();
        return response()->json(['boards'=>$boards]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>['required', 'string' , 'max:100'],
            'secret' => ['required' , 'boolean']
        ]);
        if($validator->fails())
        {
            return response()->json($validator->errors() , 422);
        }
        $board = new Board();
        $board->name = $request->name;
        $board->user_id = Auth::id();
        $board->secret = $request->secret;
        $board->save();
        return response()->json(['board' => $board],201);
    }

    public function update(Request $request, Board $board)
    {
        $validator = Validator::make($request->all(),[
            'name'=>['required', 'string' , 'max:100'],
            'secret' => ['required' , 'boolean']
        ]);
        if($validator->fails())
        {
            return response()->json($validator->errors() , 422);
        }
        $board->name = $request->name;
        $board->user_id = Auth::id();
        $board->secret = $request->secret;
        $board->save();
        return response()->json(['message' => 'Board updated successfully!'],201);
    }

    public function destroy(Board $board)
    {
        if($board->user_id !== Auth::id())
        {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $board->delete();
        return response()->json(['message' => 'Board deleted successfully'], 200);
    }
}
