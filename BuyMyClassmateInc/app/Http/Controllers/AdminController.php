<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;

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

    // Items
    public function storeItem(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'description' => 'required',
            'image' => 'nullable|image',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
        }

        Item::create([
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'image_path' => $path,
        ]);

        return back()->with('success', 'Item created successfully.');
    }

    public function updateItem(Request $request, $id)
    {
        $item = Item::withTrashed()->findOrFail($id);
        
        if ($request->has('restore')) {
            $item->restore();
            return back()->with('success', 'Item restored.');
        }

        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'description' => 'required',
        ]);

        $item->update($request->only(['name', 'price', 'quantity', 'description']));

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
            $item->update(['image_path' => $path]);
        }

        return back()->with('success', 'Item updated.');
    }

    public function destroyItem($id)
    {
        Item::findOrFail($id)->delete();
        return back()->with('success', 'Item deleted.');
    }

    // Users
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'is_admin' => $request->has('is_admin'),
        ]);

        return back()->with('success', 'User created successfully.');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($request->has('restore')) {
            $user->restore();
            return back()->with('success', 'User restored.');
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'username' => 'required|unique:users,username,'.$id,
        ]);

        $user->update($request->only(['name', 'email', 'username']));
        
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:8',
            ]);
            $user->update([
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            ]);
        }

        if ($request->has('is_admin')) {
            $user->is_admin = $request->boolean('is_admin');
            $user->save();
        }

        return back()->with('success', 'User updated.');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->is_admin) {
            return back()->with('error', 'Cannot delete admin user.');
        }

        $user->delete();
        return back()->with('success', 'User deleted.');
    }

    // Orders
    public function updateOrder(Request $request, $id)
    {
        $order = Order::withTrashed()->findOrFail($id);
        
        if ($request->has('restore')) {
            $order->restore();
            return back()->with('success', 'Order restored.');
        }

        $updateData = ['status' => $request->status];

        if ($request->status == 'shipping' && is_null($order->shipped_at)) {
            $updateData['shipped_at'] = now();
        } elseif ($request->status == 'delivered' && is_null($order->delivered_at)) {
            $updateData['delivered_at'] = now();
            // Ensure shipped_at is set if jumping straight to delivered
            if (is_null($order->shipped_at)) {
                $updateData['shipped_at'] = now();
            }
        }

        $order->update($updateData);
        return back()->with('success', 'Order status updated.');
    }

    public function destroyOrder($id)
    {
        Order::findOrFail($id)->delete();
        return back()->with('success', 'Order deleted.');
    }
}
