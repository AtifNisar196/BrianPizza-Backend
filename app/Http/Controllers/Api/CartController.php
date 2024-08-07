<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CartItemAddon;
use App\Models\CartItemVariation;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{

    public function getCart(Request $request)
    {
        try {
            $status = 1;
            $message = "Success";
            $carts = Cart::where('session_id', $request->session_id)
                ->with([
                    'items.product',
                    'items.addons.addonCart.addon',
                    'items.variations.variationCart.variation'
                ])
                ->get();

            if ($carts->isEmpty()) {
                $status = 0;
                $message = "No products in cart found.";
            }

            $response = [
                'status' => $status,
                'message' => $message,
                'data' => $carts,
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error retrieving products from cart: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string',
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'price' => 'required',
            'addons' => 'sometimes|array',
            'addons.*.id' => 'required_with:addons',
            'addons.*.price' => 'required_with:addons',
            'variations' => 'sometimes|array',
            'variations.*.id' => 'required_with:variations',
            'variations.*.price' => 'required_with:variations',
        ]);

        if (!$validator->passes()) {
            $response = [
                'status' => 0,
                'message' => $validator->errors()->toArray(),
            ];
            return response()->json($response);
        }

        try {
            $cart = Cart::firstOrCreate(['session_id' => $request->session_id]);

            // Check if a similar cart item exists
            $existingCartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $request->product_id)
                ->get();

            $found = false;

            foreach ($existingCartItem as $item) {
                // Check if addons and variations match
                $existingAddons = $item->addons->pluck('product_addon_id')->sort()->values()->toArray();
                $existingVariations = $item->variations->pluck('product_variation_id')->sort()->values()->toArray();

                $requestAddons = isset($request->addons) ? collect($request->addons)->pluck('id')->sort()->values()->toArray() : [];
                $requestVariations = isset($request->variations) ? collect($request->variations)->pluck('id')->sort()->values()->toArray() : [];

                if ($existingAddons == $requestAddons && $existingVariations == $requestVariations) {
                    // If both addons and variations match, update the quantity
                    $item->quantity += $request->quantity;
                    $item->save();
                    $found = true;
                    break;
                }
            }

            if (!$found) {

                $finalTotal = 0;
                // Create new cart item
                $cartItem = new CartItem;
                $cartItem->cart_id = $cart->id;
                $cartItem->product_id = $request->product_id;
                $cartItem->quantity = $request->quantity;
                $cartItem->price = $request->price;
                $cartItem->sub_total = $request->quantity * $request->price;
                $cartItem->save();

                $finalTotal += $cartItem->sub_total;

                // Save addons
                if ($request->has('addons') && is_array($request->addons)) {
                    foreach ($request->addons as $addon) {
                        $cartItemAddon = new CartItemAddon;
                        $cartItemAddon->cart_item_id = $cartItem->id;
                        $cartItemAddon->product_addon_id = $addon['id'];
                        $cartItemAddon->price = $addon['price'];
                        $cartItemAddon->save();

                        $finalTotal += $cartItemAddon->price;
                    }
                }

                // Save variations
                if ($request->has('variations') && is_array($request->variations)) {
                    foreach ($request->variations as $variation) {
                        $cartItemVariation = new CartItemVariation;
                        $cartItemVariation->cart_item_id = $cartItem->id;
                        $cartItemVariation->product_variation_id = $variation['id'];
                        $cartItemVariation->price = $variation['price'];
                        $cartItemVariation->save();

                        $finalTotal += $cartItemVariation->price;
                    }
                }

                $cartItem->total = $finalTotal;
                $cartItem->save();
            }

            $response = [
                'status' => 1,
                'message' => $found ? "Product quantity updated in cart" : "Product added to cart",
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error adding product to cart: ' . $th);
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function clearCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 0,
                'message' => $validator->errors()->toArray(),
            ];
            return response()->json($response);
        }

        try {
            $cart = Cart::where('session_id', $request->session_id)->first();

            if (!$cart) {
                $response = [
                    'status' => 0,
                    'message' => 'Cart not found.',
                ];
                return response()->json($response);
            }

            // Delete all cart items, addons, and variations associated with the cart
            foreach ($cart->items as $item) {
                $item->addons()->delete();
                $item->variations()->delete();
                $item->delete();
            }

            // Optionally delete the cart itself
            $cart->delete();

            $response = [
                'status' => 1,
                'message' => 'Cart cleared successfully.',
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error clearing cart: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function removeCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string',
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 0,
                'message' => $validator->errors()->toArray(),
            ];
            return response()->json($response);
        }

        try {
            $cart = Cart::where('session_id', $request->session_id)->first();

            if (!$cart) {
                $response = [
                    'status' => 0,
                    'message' => 'Cart not found.',
                ];
                return response()->json($response);
            }

            // Check if the cart has only one item
            if ($cart->items->count() === 1) {
                // If the cart has only one item, clear the entire cart
                foreach ($cart->items as $item) {
                    $item->addons()->delete();
                    $item->variations()->delete();
                    $item->delete();
                }
                $cart->delete();

                $response = [
                    'status' => 1,
                    'message' => 'Cart cleared successfully.',
                ];
                return response()->json($response);
            }

            // If the cart has more than one item, find and delete the specific product
            $cartItem = $cart->items()->where('product_id', $request->product_id)->first();

            if (!$cartItem) {
                $response = [
                    'status' => 0,
                    'message' => 'Product not found in the cart.',
                ];
                return response()->json($response);
            }

            $cartItem->addons()->delete();
            $cartItem->variations()->delete();
            $cartItem->delete();

            $response = [
                'status' => 1,
                'message' => 'Product removed from the cart.',
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error removing product from cart: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function quantityUpdateCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string',
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 0,
                'message' => $validator->errors()->toArray(),
            ];
            return response()->json($response);
        }

        try {
            $cart = Cart::where('session_id', $request->session_id)->first();

            if (!$cart) {
                $response = [
                    'status' => 0,
                    'message' => 'Cart not found.',
                ];
                return response()->json($response);
            }

            // If the cart has more than one item, find and delete the specific product
            $cartItem = $cart->items()->where('product_id', $request->product_id)->first();
            $addonsTotal = $cartItem->addons()->sum('price');
            $variationsTotal = $cartItem->variations()->sum('price');

            if (!$cartItem) {
                $response = [
                    'status' => 0,
                    'message' => 'Product not found in the cart.',
                ];
                return response()->json($response);
            }

            $cartItem->quantity = $request->quantity;
            $cartItem->sub_total = $request->quantity * $cartItem->price;
            $cartItem->total = $cartItem->sub_total + $addonsTotal + $variationsTotal;
            $cartItem->save();

            $response = [
                'status' => 1,
                'message' => 'Product quantity updated.',
                'data' => $cartItem
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error removing product from cart: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

}
