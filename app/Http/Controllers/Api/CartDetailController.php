<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\CartDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartDetailResource;

class CartDetailController extends Controller
{
    // method GET
    public function index()
    {
        $cart_details = CartDetail::get();
        if ($cart_details->count() > 0) {
            return response()->json([
                'message' => 'Get cart detail success',
                'data' => CartDetailResource::collection($cart_details)
            ], 200);
        } else {
            return response()->json(['message' => 'No record available'], 200);
        }
    }

    // method GET CartDetail by cart_id
    public function getCartDetailByCartId($cart_id)
    {
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

    // method GET all CartDetail by customer_id
    public function getAllCartDetailByCustomerId($customer_id)
    {
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

    // method GET CartDetails with is_checked_out = false
    public function getNotOrderedCartDetailByCustomerId()
    {
        $uncheckedCartDetails = CartDetail::where('is_checked_out', false)->get();

        if ($uncheckedCartDetails->count() > 0) {
            return response()->json([
                'message' => 'Get unchecked cart details successfully',
                'data' => CartDetailResource::collection($uncheckedCartDetails)
            ], 200);
        } else {
            return response()->json(['message' => 'No unchecked cart details found'], 404);
        }
    }


    // method GET Detail with cart_detail_id
    public function show($cart_detail_id)
    {
        try {
            $cart_detail = CartDetail::where('cart_detail_id', $cart_detail_id)->first();
            if (!$cart_detail) {
                return response()->json([
                    'message' => 'Cart detail not found',
                    'cart_detail_id' => $cart_detail_id
                ], 404);
            }

            return response()->json([
                'message' => 'Get cart detail success with cart_detail_id',
                'data' => new CartDetailResource($cart_detail)
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to get cart detail information', [
                'error' => $e->getMessage(),
                'cart_detail_id' => $cart_detail_id
            ]);

            return response()->json([
                'message' => 'Failed to get cart detail information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // method DELETE a cart item by cart_detail_id
    public function deleteItemInCart($cart_detail_id)
    {
        $cartDetail = CartDetail::find($cart_detail_id);

        if (!$cartDetail) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        $cart_id = $cartDetail->cart_id;

        $cartDetail->delete();

        $cartTotal = CartDetail::where('cart_id', $cart_id)->sum('total_price');
        $cart = Cart::find($cart_id);

        if ($cart) {
            $cart->total_price = $cartTotal;
            $cart->save();
        }

        return response()->json(['message' => 'Item removed from cart and total price updated successfully'], 200);
    }
}
