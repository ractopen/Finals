<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;

class StoreController extends Controller
{
    public function index()
    {
        $items = Item::all();
        return view('store.index', compact('items'));
    }

    public function show($id)
    {
        $item = Item::findOrFail($id);
        return view('store.show', compact('item'));
    }

    public function viewCart()
    {
        $cartItems = CartItem::where('user_id', Auth::id())->with('item')->get();
        $pastOrders = Order::where('user_id', Auth::id())->with('orderItems.item')->latest()->get();
        
        $activeOrders = $pastOrders->where('status', '!=', 'delivered');
        $deliveredOrders = $pastOrders->where('status', 'delivered');

        return view('store.cart', compact('cartItems', 'activeOrders', 'deliveredOrders'));
    }

    public function addToCart(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        
        if ($item->quantity <= 0) {
            return back()->with('error', 'This item is currently out of stock.');
        }
        
        $cartItem = CartItem::where('user_id', Auth::id())
                            ->where('item_id', $id)
                            ->first();

        if ($cartItem) {
            $cartItem->quantity += 1;
            $cartItem->save();
        } else {
            CartItem::create([
                'user_id' => Auth::id(),
                'item_id' => $id,
                'quantity' => 1,
            ]);
        }

        return back()->with('cart_success', $item->name . ' added to bag');
    }

    public function updateCartItem(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::where('user_id', Auth::id())->findOrFail($id);
        $cartItem->update(['quantity' => $request->quantity]);

        if ($request->wantsJson()) {
            $cartTotal = CartItem::where('user_id', Auth::id())->get()->sum(function($item) {
                return $item->quantity * $item->item->price;
            });

            return response()->json([
                'success' => true,
                'itemTotal' => number_format($cartItem->quantity * $cartItem->item->price, 2),
                'cartTotal' => number_format($cartTotal, 2),
            ]);
        }

        return back();
    }

    public function removeFromCart($id)
    {
        CartItem::where('user_id', Auth::id())->where('id', $id)->delete();
        return back();
    }

    public function checkout()
    {
        $cartItems = CartItem::where('user_id', Auth::id())->with('item')->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.view');
        }
        $total = $cartItems->sum(fn($item) => $item->quantity * $item->item->price);
        return view('store.checkout', compact('cartItems', 'total'));
    }

    public function processOrder(Request $request)
    {
        $request->validate([
            'address' => 'required',
            'postal_code' => 'required',
            'phone_number' => 'required',
            'email' => 'required|email',
        ]);

        $cartItems = CartItem::where('user_id', Auth::id())->with('item')->get();
        
        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Cart is empty');
        }

        $total = $cartItems->sum(fn($item) => $item->quantity * $item->item->price);

        $order = Order::create([
            'user_id' => Auth::id(),
            'total_price' => $total,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'status' => 'preparing',
        ]);

        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'item_id' => $cartItem->item_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->item->price,
            ]);
            
            // Stock is unlimited, so we do not decrement
            // $cartItem->item->decrement('quantity', $cartItem->quantity);
        }

        CartItem::where('user_id', Auth::id())->delete();

        return redirect()->route('receipt', $order);
    }

    public function receipt(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        return view('store.receipt', compact('order'));
    }
}
