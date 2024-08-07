<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductAddon;
use App\Models\OrderProductVariation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'suburb' => $request->order_type == 1 ? 'required|string' : 'nullable|string',
            'address' => $request->order_type == 1 ? 'required|string' : 'nullable|string',
            'order_type' => 'required|integer',
            'timings' => 'required|string',
            'payment_method' => 'required|string',
            'comments' => 'nullable|string',
            'products' => 'required|array',
            'products.*.product_id' => 'required|integer',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric',
            'products.*.addons' => 'sometimes|array',
            'products.*.addons.*.addon_id' => 'required|integer',
            'products.*.addons.*.price' => 'required|numeric',
            'products.*.variations' => 'sometimes|array',
            'products.*.variations.*.variation_id' => 'required|integer',
            'products.*.variations.*.price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 0,
                'message' => $validator->errors()->toArray(),
            ];
            return response()->json($response);
        }

        try {
            $order = new Order;
            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->phone = $request->phone;
            $order->suburb = $request->suburb;
            $order->address = $request->address;
            $order->order_type = $request->order_type;
            $order->timings = $request->timings;
            $order->payment_method = $request->payment_method;
            $order->comments = $request->comments;
            $order->save();

            $subTotal = 0;

            if ($request->has('products') && is_array($request->products)) {
                foreach ($request->products as $product) {
                    $orderProduct = new OrderProduct;
                    $orderProduct->order_id = $order->id;
                    $orderProduct->product_id = $product['product_id'];
                    $orderProduct->quantity = $product['quantity'];
                    $orderProduct->price = $product['price'];
                    $orderProduct->total = $product['quantity'] * $product['price'];
                    $orderProduct->save();

                    $subTotal += $orderProduct->total;

                    if (isset($product['addons']) && is_array($product['addons'])) {
                        foreach ($product['addons'] as $addon) {
                            $orderProductAddon = new OrderProductAddon;
                            $orderProductAddon->order_product_id = $orderProduct->id;
                            $orderProductAddon->addon_id = $addon['addon_id'];
                            $orderProductAddon->price = $addon['price'];
                            $orderProductAddon->save();

                            $subTotal += $orderProductAddon->price;
                        }
                    }

                    if (isset($product['variations']) && is_array($product['variations'])) {
                        foreach ($product['variations'] as $variation) {
                            $orderProductVariation = new OrderProductVariation;
                            $orderProductVariation->order_product_id = $orderProduct->id;
                            $orderProductVariation->variation_id = $variation['variation_id'];
                            $orderProductVariation->price = $variation['price'];
                            $orderProductVariation->save();

                            $subTotal += $orderProductVariation->price;
                        }
                    }
                }

                $tax = $subTotal * 0.06;
                $order->sub_total = $subTotal;
                $order->tax = $tax;
                $order->total = $subTotal + $tax;
                $order->save();

                $cart = Cart::where('session_id', $request->session_id)->first();
                foreach ($cart->items as $item) {
                    $item->addons()->delete();
                    $item->variations()->delete();
                    $item->delete();
                }
                $cart->delete();

            }

            $response = [
                'status' => 1,
                'message' => "Order Placed Successfully!",
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error creating an order: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function checkAvailability(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'timings' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => $validator->errors()->toArray(),
                ]);
            }

            $date = Carbon::now()->format('Y-m-d');
            $order = Order::where('timings', $request->timings)
                ->whereDate('created_at', $date)
                ->first();

            $status = 0;
            $message = "Timeslot Not Available";

            if (!$order) {
                $status = 1;
                $message = "Timeslot Available";
            }

            $response = [
                'status' => $status,
                'message' => $message,
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error checking availability: ' . $th->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'An error occurred while checking availability.',
            ]);
        }
    }


}
