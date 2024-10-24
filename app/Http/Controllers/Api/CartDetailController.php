<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartDetailResource;
use App\Models\CartDetail;
use Illuminate\Http\Request;

class CartDetailController extends Controller
{
    // method GET
    public function index() {
        $cart_details = CartDetail::get();
        if ($cart_details->count() > 0) {
            return response()->json([
                // 'message' => 'Get cart detail success',
                'data' => CartDetailResource::collection($cart_details)
            ], 200);
        }
        else {
            return response()->json(['message' => 'No record available'], 200);
        }
    }

    // method GET CartDetail by cart_id
    public function getCartDetailByCartId($cart_id) {
        $cart_details = CartDetail::where('cart_id', $cart_id)->get();

        if ($cart_details->count() > 0) {
            return response()->json([
                'message' => 'Get cart details by cart_id successfully',
                'data' => CartDetailResource::collection($cart_details)
            ], 200);
        } else {
            return response()->json(['message' => 'No cart details found for this cart_id'], 404);
        }
    }

    // method GET CartDetail by customer_id
    public function getCartDetailByCustomerId($customer_id) {
        $cart_details = CartDetail::where('customer_id', $customer_id)->get();

        if ($cart_details->count() > 0) {
            return response()->json([
                'message' => 'Get cart details by customer_id successfully',
                'data' => CartDetailResource::collection($cart_details)
            ], 200);
        } else {
            return response()->json(['message' => 'No cart details found for this customer_id'], 404);
        }
    }

    // method GET Detail
    public function show(CartDetail $cart_detail) {
        return new CartDetailResource($cart_detail);
    }
}
