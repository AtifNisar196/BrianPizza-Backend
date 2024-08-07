<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AddonType;
use App\Models\Category;
use App\Models\Product;
use App\Models\VariationType;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public function getAddons(){
        try {
            $status = 1;
            $message = "Success";
            $addons = AddonType::Active()->with('addons')->get();

            if ($addons->isEmpty()) {
                $status = 0;
                $message = "No addons found.";
            }

            $response = [
                'status' => $status,
                'message' => $message,
                'data' => $addons,
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error retrieving addon types with addons: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function getCategories(){
        try {
            $status = 1;
            $message = "Success";
            $categories = Category::Active()->select('name','image')->get();

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

    public function getVariations(){
        try {
            $status = 1;
            $message = "Success";
            $variationTypes = VariationType::Active()
            ->with('variations')
            ->get();

            if ($variationTypes->isEmpty()) {
                $status = 0;
                $message = "No variations found";
            }

            $response = [
                'status' => $status,
                'message' => $message,
                'data' => $variationTypes->map->only('id', 'name', 'variations'),
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error retrieving variation types with variations: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function getCategoryProducts(){
        try {
            $status = 1;
            $message = "Success";
            $categories = Category::Active()->with('products')->get();

            if ($categories->isEmpty()) {
                $status = 0;
                $message = "No categories with products found.";
            }

            $response = [
                'status' => $status,
                'message' => $message,
                'data' => $categories,
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error retrieving categories with products: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function getProducts(){
        try {
            $status = 1;
            $message = "Success";
            $products = Product::where('id', '!=', 1)->Active()->with(['category:id,name,image', 'variations:name,image','addons:name,image,price'])->get();

            if ($products->isEmpty()) {
                $status = 0;
                $message = "No products found.";
            }

            $response = [
                'status' => $status,
                'message' => $message,
                'data' => $products,
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error retrieving products: ' . $th->getMessage());

            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function getProductDetail($productId){
        try {
            $status = 1;
            $message = "Success";
            $product = Product::with(['category:id,name,image', 'variations:name,image', 'addons:name,image,price'])->find($productId);

            if (!isset($product)) {
                $status = 0;
                $message = "No product found.";
            }

            $response = [
                'status' => $status,
                'message' => $message,
                'data' => $product,
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error retrieving product: ' . $th->getMessage());

            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function getFeaturedProducts(){
        try {
            $status = 1;
            $message = "Success";
            $products = Product::active()->featured()->with(['category:id,name,image', 'variations:name,image','addons:name,image,price'])->get();

            if ($products->isEmpty()) {
                $status = 0;
                $message = "No featured products found.";
            }

            $response = [
                'status' => $status,
                'message' => $message,
                'data' => $products,
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error retrieving featured products: ' . $th->getMessage());

            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];

            return response()->json($response, 500);
        }
    }

}
