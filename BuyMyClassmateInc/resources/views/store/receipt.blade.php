@extends('layouts.app')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="text-center mb-4">
        <h1 style="color: #03dac6;">Thank You!</h1>
        <p>Your order has been placed successfully.</p>
    </div>

    <div style="border-bottom: 1px solid #444; padding-bottom: 20px; margin-bottom: 20px;">
        <h3>Order Details</h3>
        <p><strong>Order ID:</strong> #{{ $order->id }}</p>
        <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
        <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
    </div>

    <div style="border-bottom: 1px solid #444; padding-bottom: 20px; margin-bottom: 20px;">
        <h3>Delivery Information</h3>
        <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
        <p><strong>Email:</strong> {{ $order->email }}</p>
        <p><strong>Phone:</strong> {{ $order->phone_number }}</p>
        <p><strong>Address:</strong><br>{{ $order->address }}<br>{{ $order->postal_code }}</p>
    </div>

    <div>
        <h3>Items</h3>
        <table>
            @foreach($order->items as $orderItem)
                <tr>
                    <td>{{ $orderItem->item ? $orderItem->item->name : 'Item (Deleted)' }}</td>
                    <td>x {{ $orderItem->quantity }}</td>
                    <td style="text-align: right;">${{ number_format($orderItem->price * $orderItem->quantity, 2) }}</td>
                </tr>
            @endforeach
            <tr style="border-top: 2px solid #444;">
                <td colspan="2"><strong>Total</strong></td>
                <td style="text-align: right;"><strong>${{ number_format($order->total_price, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('home') }}" class="btn">Continue Shopping</a>
    </div>
</div>
@endsection
