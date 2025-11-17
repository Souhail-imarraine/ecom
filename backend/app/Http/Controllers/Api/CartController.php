<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::with(['items.product.images'])->where('user_id', $request->user()->id)->first();
        
        if (!$cart) {
            $cart = Cart::create(['user_id' => $request->user()->id]);
        }

        return response()->json([
            'cart' => $cart,
            'total' => $cart->getTotal(),
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        
        if ($product->stock < $request->quantity) {
            return response()->json(['message' => 'Stock insuffisant'], 400);
        }

        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]);
        }

        return response()->json(['message' => 'Produit ajouté au panier']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::findOrFail($id);
        
        if ($cartItem->cart->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        if ($cartItem->product->stock < $request->quantity) {
            return response()->json(['message' => 'Stock insuffisant'], 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json(['message' => 'Panier mis à jour']);
    }

    public function remove($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Produit retiré du panier']);
    }
}
