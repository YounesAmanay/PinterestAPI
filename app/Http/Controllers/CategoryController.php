<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\PinCollection;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return CategoryResource::collection($categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_ids' => 'required|array',
            'category_ids.*' => 'distinct'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $user = User::find(Auth::id());
        $categoryIds = $request->input('category_ids');

        $user->categories()->sync($categoryIds);
        $user->onboarding = true;
        $user->save();

        return response()->json(['message' => 'Categories associated with user successfully.']);
    }

    public function show(Category $category)
    {
        $pins = $category->pins()->get();

        return new PinCollection($pins);
    }
}
