@extends('layouts.app')

@section('content')
    <div class="text-center">
        <h1>Your Bag.</h1>
        <p style="font-size: 21px; margin-bottom: 40px;">Free delivery and free returns.</p>
    </div>

    @if($cartItems->isEmpty() && $activeOrders->isEmpty() && $deliveredOrders->isEmpty())
        <div class="card text-center">
            <p>Your bag is empty.</p>
            <a href="{{ route('home') }}" class="btn mt-4">Continue Shopping</a>
        </div>
    @else
        @if(!$cartItems->isEmpty())
            <div class="card mb-4">
                <h2>Items in your bag</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @foreach($cartItems as $cartItem)
                            <tr>
                                <td data-label="Product">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $cartItem->item->image_path }}" alt="{{ $cartItem->item->name }}" style="width: 50px; height: 50px; object-fit: contain;">
                                        <div>
                                            <div style="font-weight: 600;">{{ $cartItem->item->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Price">${{ number_format($cartItem->item->price, 2) }}</td>
                                <td data-label="Quantity">
                                    <input type="number" id="qty-{{ $cartItem->id }}" value="{{ $cartItem->quantity }}" min="1" 
                                           style="width: 60px; padding: 8px;" 
                                           onchange="updateCartItem({{ $cartItem->id }})">
                                </td>
                                <td data-label="Total">$<span id="item-total-{{ $cartItem->id }}">{{ number_format($cartItem->item->price * $cartItem->quantity, 2) }}</span></td>
                                <td>
                                    <form action="{{ route('cart.remove', $cartItem->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="color: var(--link-color); background: none; border: none; cursor: pointer;">Remove</button>
                                    </form>
                                </td>
                            </tr>
                            @php $total += $cartItem->item->price * $cartItem->quantity; @endphp
                        @endforeach
                    </tbody>
                </table>
                
                <div class="flex items-center mt-4" style="border-top: 1px solid var(--border-color); padding-top: 20px; justify-content: flex-end; gap: 20px;">
                    <div style="font-size: 21px; font-weight: 600;">Total: $<span id="cart-total">{{ number_format($total, 2) }}</span></div>
                    <a href="{{ route('checkout') }}" class="btn">Check Out</a>
                </div>
            </div>
        @endif

        @if(!$activeOrders->isEmpty())
            <div class="card mb-4">
                <h2>Active Orders</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Status</th>
                            <th>Timeline</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeOrders as $order)
                            <tr>
                                <td data-label="Order ID">
                                    #{{ $order->id }}
                                    <div style="font-size: 12px; color: #86868b; margin-top: 4px;">
                                        @if($order->status != 'preparing')
                                            Tracking: 12837812378917238921386189236
                                        @else
                                            Tracking: Pending
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Status">
                                    <span style="padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; 
                                        background-color: {{ $order->status == 'shipping' ? '#e8f0fe' : '#fce8e6' }}; 
                                        color: {{ $order->status == 'shipping' ? '#1967d2' : '#c5221f' }};">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td data-label="Timeline">
                                    <div style="font-size: 13px;">
                                        <div style="margin-bottom: 4px;">Ordered: {{ $order->created_at->format('M d, h:i A') }}</div>
                                        @php
                                            $shippedDate = $order->shipped_at ?? ($order->status == 'shipping' ? $order->updated_at : null);
                                        @endphp
                                        @if($shippedDate)
                                            <div style="color: var(--accent-color);">Shipped: {{ $shippedDate->format('M d, h:i A') }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Total" style="text-align: right;">${{ number_format($order->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if(!$deliveredOrders->isEmpty())
            <div class="card">
                <h2>Delivered Orders</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Status</th>
                            <th>Timeline</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveredOrders as $order)
                            <tr>
                                <td data-label="Order ID">#{{ $order->id }}</td>
                                <td data-label="Status">
                                    <span style="padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; background-color: #e6f4ea; color: #137333;">
                                        Delivered
                                    </span>
                                </td>
                                <td data-label="Timeline">
                                    <div style="font-size: 13px;">
                                        <div style="margin-bottom: 4px;">Ordered: {{ $order->created_at->format('M d, h:i A') }}</div>
                                        @php
                                            $shippedDate = $order->shipped_at ?? ($order->status == 'shipping' ? $order->updated_at : null);
                                            $deliveredDate = $order->delivered_at ?? ($order->status == 'delivered' ? $order->updated_at : null);
                                        @endphp
                                        @if($shippedDate || $order->status == 'delivered')
                                            @if($shippedDate)
                                                <div style="color: var(--accent-color); margin-bottom: 4px;">Shipped: {{ $shippedDate->format('M d, h:i A') }}</div>
                                            @endif
                                        @endif
                                        @if($deliveredDate)
                                            <div style="color: var(--success-color);">Delivered: {{ $deliveredDate->format('M d, h:i A') }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Total" style="text-align: right;">${{ number_format($order->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif

    <script>
        function updateCartItem(id) {
            const qty = document.getElementById('qty-' + id).value;
            const token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}';

            fetch('/cart/update/' + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    _method: 'PATCH',
                    quantity: qty
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('item-total-' + id).innerText = data.itemTotal;
                    document.getElementById('cart-total').innerText = data.cartTotal;
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
@endsection
