<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $items = Item::withTrashed()->get();
        $users = User::withTrashed()->get();
        
        $allOrders = Order::with('user')->withTrashed()->latest()->get();
        $preparingOrders = $allOrders->where('status', 'preparing');
        $shippingOrders = $allOrders->where('status', 'shipping');
        $deliveredOrders = $allOrders->where('status', 'delivered');

        return view('admin.dashboard', compact('items', 'users', 'preparingOrders', 'shippingOrders', 'deliveredOrders'));
    }

    // Items CRUD
    public function storeItem(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'image_path' => 'nullable',
        ]);

        Item::create($validated);
        return back()->with('success', 'Item created successfully');
    }

    public function updateItem(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'image_path' => 'nullable',
        ]);

        $item->update($validated);
        return back()->with('success', 'Item updated successfully');
    }

    public function destroyItem(Item $item)
    {
        $item->delete();
        return back()->with('success', 'Item deleted successfully');
    }

    // Users CRUD
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'is_admin' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);
        return back()->with('success', 'User created successfully');
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'is_admin' => 'boolean',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);
        return back()->with('success', 'User updated successfully');
    }

    public function destroyUser(User $user)
    {
        $user->delete();
        return back()->with('success', 'User deleted successfully');
    }

    // Orders
    public function updateOrder(Request $request, Order $order)
    {
        $updateData = ['status' => $request->status];

        if ($request->status == 'shipping' && is_null($order->shipped_at)) {
            $updateData['shipped_at'] = now();
        } elseif ($request->status == 'delivered' && is_null($order->delivered_at)) {
            $updateData['delivered_at'] = now();
            if (is_null($order->shipped_at)) {
                $updateData['shipped_at'] = now();
            }
        }

        $order->update($updateData);
        return back()->with('success', 'Order status updated');
    }
}
