<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class StoreController extends Controller
{
    public function index()
    {
        $items = Cache::remember('items_list', 60, function () {
            return Item::all();
        });
        return view('store.index', compact('items'));
    }

    public function addToCart(Request $request, $itemId)
    {
        $item = Item::findOrFail($itemId);
        
        $quantity = $request->input('quantity', 1);

        if ($item->quantity < $quantity) {
            return back()->with('error', 'Not enough stock.');
        }

        $cartItem = CartItem::where('user_id', Auth::id())
                            ->where('item_id', $itemId)
                            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
        } else {
            CartItem::create([
                'user_id' => Auth::id(),
                'item_id' => $itemId,
                'quantity' => $quantity,
            ]);
        }

        if ($request->has('buy_now')) {
            return redirect()->route('checkout');
        }

        return back()->with('success', 'Added to cart.');
    }

    public function viewCart()
    {
        $cartItems = CartItem::with('item')->where('user_id', Auth::id())->get();
        
        // Check for soft deleted items
        foreach ($cartItems as $cartItem) {
            if (!$cartItem->item) {
                // Item was hard deleted or relationship failed, but if soft deleted it should still be retrievable if we used withTrashed in relationship or check manually.
                // However, standard belongsTo won't return soft deleted unless we say so.
                // Let's check if it exists in trashed.
                $trashedItem = Item::withTrashed()->find($cartItem->item_id);
                if ($trashedItem && $trashedItem->trashed()) {
                    $cartItem->is_removed = true;
                }
            }
        }

        $allOrders = Order::where('user_id', Auth::id())->with('items.item')->latest()->get();
        $activeOrders = $allOrders->where('status', '!=', 'delivered');
        $deliveredOrders = $allOrders->where('status', 'delivered');

        return view('store.cart', compact('cartItems', 'activeOrders', 'deliveredOrders'));
    }

    public function updateCartItem(Request $request, $id)
    {
        $cartItem = CartItem::where('user_id', Auth::id())->findOrFail($id);
        $cartItem->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Cart updated.');
    }

    public function removeCartItem($id)
    {
        CartItem::where('user_id', Auth::id())->findOrFail($id)->delete();
        return back()->with('success', 'Item removed from cart.');
    }

    public function checkout(Request $request)
    {
        $query = CartItem::with('item')->where('user_id', Auth::id());
        
        if ($request->has('selected_items')) {
            $query->whereIn('id', $request->selected_items);
        }

        $cartItems = $query->get();

        if ($cartItems->isEmpty()) {
            return redirect('/cart')->with('error', 'No items selected for checkout.');
        }
        
        $selectedItems = $request->selected_items;

        return view('store.checkout', compact('cartItems', 'selectedItems'));
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'address' => 'required',
            'postal_code' => 'required',
            'phone_number' => 'required',
            'email' => 'required|email',
        ]);

        $query = CartItem::with('item')->where('user_id', Auth::id());
        
        if ($request->has('selected_items')) {
            $query->whereIn('id', $request->selected_items);
        }

        $cartItems = $query->get();
        
        if ($cartItems->isEmpty()) {
             return redirect('/cart')->with('error', 'No items to process.');
        }

        $total = 0;

        foreach ($cartItems as $cartItem) {
            if ($cartItem->item) {
                $total += $cartItem->item->price * $cartItem->quantity;
            }
        }

        $order = Order::create([
            'user_id' => Auth::id(),
            'status' => 'preparing',
            'total_price' => $total,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
        ]);

        foreach ($cartItems as $cartItem) {
            if ($cartItem->item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $cartItem->item_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->item->price,
                ]);
                
                // Decrement stock
                $cartItem->item->decrement('quantity', $cartItem->quantity);
            }
        }

        // Clear processed items from cart
        if ($request->has('selected_items')) {
            CartItem::where('user_id', Auth::id())->whereIn('id', $request->selected_items)->delete();
        } else {
            CartItem::where('user_id', Auth::id())->delete();
        }

        return redirect()->route('payment.show', ['order' => $order->id]);
    }

    public function showPayment($orderId)
    {
        $order = Order::findOrFail($orderId);
        if ($order->user_id != Auth::id()) {
            abort(403);
        }
        return view('store.payment', compact('order'));
    }

    public function confirmPayment($orderId)
    {
        $order = Order::findOrFail($orderId);
        if ($order->user_id != Auth::id()) {
            abort(403);
        }
        // In a real app, we'd verify payment here.
        return redirect()->route('receipt', ['order' => $order->id]);
    }

    public function receipt($orderId)
    {
        $order = Order::with('items.item')->findOrFail($orderId);
        if ($order->user_id != Auth::id()) {
            abort(403);
        }
        return view('store.receipt', compact('order'));
    }
}
