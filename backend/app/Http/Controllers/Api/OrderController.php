<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('items.product')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_postal_code' => 'required|string',
            'shipping_phone' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        $cart = Cart::with('items.product')->where('user_id', $request->user()->id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Panier vide'], 400);
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            foreach ($cart->items as $item) {
                if ($item->product->stock < $item->quantity) {
                    DB::rollBack();
                    return response()->json(['message' => 'Stock insuffisant pour ' . $item->product->name], 400);
                }
                $subtotal += $item->price * $item->quantity;
            }

            $tax = $subtotal * 0.20;
            $shippingCost = 50;
            $total = $subtotal + $tax + $shippingCost;

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $request->user()->id,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_postal_code' => $request->shipping_postal_code,
                'shipping_phone' => $request->shipping_phone,
                'payment_method' => $request->payment_method,
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->price * $item->quantity,
                ]);

                $product = Product::find($item->product_id);
                $product->stock -= $item->quantity;
                $product->save();
            }

            $cart->items()->delete();

            DB::commit();

            return response()->json($order->load('items.product'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erreur lors de la création de la commande'], 500);
        }
    }

    public function show($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,shipped,delivered,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return response()->json($order);
    }
}
