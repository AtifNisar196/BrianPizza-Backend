<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function getCategories()
    {
        try {
            $status = 1;
            $message = "Success";
            $categories = Category::get();

            if ($categories->isEmpty()) {
                $status = 0;
                $message = "No categories found.";
            }

            $response = [
                'status' => $status,
                'message' => $message,
                'data' => $categories,
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error retrieving categories: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function addCategory(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:100',
                'image' => 'required|image|mimes:jpeg,png,jpg',
            ]);

            if (!$validator->passes()) {
                $response = [
                    'status' => 0,
                    'message' => $validator->errors()->toArray(),
                ];
                return response()->json($response);
            }

            $category = new Category;
            $category->name = $request->name;
            $category->save();

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileNameToStore = time() . '.' . $file->getClientOriginalExtension();

                $destinationPath = 'dashboard/images/categories/' . $category->id;
                $path = $file->storeAs($destinationPath, $fileNameToStore, 'public');

                $category->image = $path;
                $category->save();
            }

            $response = [
                'status' => 1,
                'message' => "Category Created Successfully!",
                'data' => $category,
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error creating category: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function updateCategory($categoryId, Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:100',
                'image' => 'image|mimes:jpeg,png,jpg',
            ]);

            if (!$validator->passes()) {
                $response = [
                    'status' => 0,
                    'message' => $validator->errors()->toArray(),
                ];
                return response()->json($response);
            }

            $category = Category::find($categoryId);

            if (!$category) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Category not found!',
                ]);
            }

            $category->name = $request->name;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileNameToStore = time() . '.' . $file->getClientOriginalExtension();

                $destinationPath = 'dashboard/images/categories/' . $category->id;
                $path = $file->storeAs($destinationPath, $fileNameToStore, 'public');

                $category->image = $path;
                $category->save();
            }

            if ($category->isDirty() || $request->hasFile('image')) {
                $status = 1;
                $message = 'Categories Updated Successfully!';
            } else {
                $status = 0;
                $message = 'No Changes Made!';
            }

            $category->save();

            $response = [
                'status' => $status,
                'message' => $message,
                'data' => $category,
            ];

            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error updating category: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function statusUpdateCategory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:0,1',
                'category_id' => 'required',
            ]);

            if (!$validator->passes()) {
                return response()->json([
                    'status' => 0,
                    'message' => $validator->errors()->toArray(),
                ]);
            }

            $category = Category::find($request->category_id);

            if (!$category) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Category not found!',
                ]);
            }

            if ($category->status != $request->status) {
                $category->status = $request->status;
                $category->save();

                $status = 1;
                $message = 'Category status updated successfully!';
            } else {
                $status = 0;
                $message = 'No Changes Made!';
            }

            $response = [
                'status' => $status,
                'message' => $message,
                'data' => $category,
            ];

            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error updating category status: ' . $th->getMessage());
            return response()->json([
                'status' => 0,
                'message' => $th->getMessage(),
            ]);
        }
    }
}
