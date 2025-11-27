@extends('layouts.app')

@section('content')
<h1 class="mb-4">Your Cart</h1>

@if($cartItems->isEmpty())
    <p>Your cart is empty. <a href="{{ route('home') }}">Go shopping</a>.</p>
@else
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;"><input type="checkbox" id="select-all" onclick="toggleSelectAll()"></th>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach($cartItems as $cartItem)
                    <tr id="cart-row-{{ $cartItem->id }}">
                        <td>
                            @if(!isset($cartItem->is_removed) && $cartItem->item)
                                <input type="checkbox" name="selected_items[]" value="{{ $cartItem->id }}" class="item-checkbox" onclick="updateCheckoutButton()">
                            @endif
                        </td>
                        <td>
                            @if(isset($cartItem->is_removed) && $cartItem->is_removed)
                                <span style="color: #cf6679;">Item removed from store</span>
                            @elseif($cartItem->item)
                                {{ $cartItem->item->name }}
                            @else
                                <span style="color: #cf6679;">Item unavailable</span>
                            @endif
                        </td>
                        <td>
                            @if(isset($cartItem->is_removed) || !$cartItem->item)
                                -
                            @else
                                ${{ number_format($cartItem->item->price, 2) }}
                            @endif
                        </td>
                        <td>
                            @if(isset($cartItem->is_removed) || !$cartItem->item)
                                -
                            @else
                                <form action="{{ route('cart.update', $cartItem->id) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $cartItem->quantity }}" min="1" style="width: 60px; padding: 5px; border-radius: 4px; border: 1px solid #444; background: #222; color: #fff;">
                                    <button type="submit" class="btn" style="padding: 5px 10px; font-size: 0.8rem;">Update</button>
                                </form>
                            @endif
                        </td>
                        <td>
                            @if(isset($cartItem->is_removed) || !$cartItem->item)
                                -
                            @else
                                ${{ number_format($cartItem->item->price * $cartItem->quantity, 2) }}
                                @php $total += $cartItem->item->price * $cartItem->quantity; @endphp
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('cart.remove', $cartItem->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8rem;">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="flex justify-between items-center mt-4">
            <h3>Total: $<span id="cart-total">{{ number_format($total, 2) }}</span></h3>
            @if($total > 0)
                <form action="{{ route('checkout') }}" method="GET" id="checkout-form" onsubmit="prepareCheckout(event)">
                    <div id="checkout-hidden-inputs"></div>
                    <button type="submit" class="btn" id="checkout-btn" disabled>Checkout Selected</button>
                </form>
            @endif
        </div>
    </div>

    <script>
        function toggleSelectAll() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateCheckoutButton();
        }

        function updateCheckoutButton() {
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            const checkoutBtn = document.getElementById('checkout-btn');
            if (checkoutBtn) {
                checkoutBtn.disabled = checkboxes.length === 0;
                checkoutBtn.innerText = checkboxes.length > 0 ? `Checkout Selected (${checkboxes.length})` : 'Checkout Selected';
            }
        }

        function prepareCheckout(event) {
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            const container = document.getElementById('checkout-hidden-inputs');
            container.innerHTML = '';
            
            if (checkboxes.length === 0) {
                event.preventDefault();
                alert('Please select at least one item to checkout.');
                return;
            }

            checkboxes.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_items[]';
                input.value = cb.value;
                container.appendChild(input);
            });
        }
    </script>
@endif



<h2 class="mb-4 mt-8">Active Orders</h2>
@if($activeOrders->isEmpty())
    <p>No active orders.</p>
@else
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Tracking ID</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Items</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activeOrders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>
                            @if($order->status != 'preparing')
                                <span style="font-family: monospace; color: #bbb;">12837812378917238921386189236</span>
                            @else
                                <span style="color: #666;">Pending</span>
                            @endif
                        </td>
                        <td>${{ number_format($order->total_price, 2) }}</td>
                        <td>
                            <div style="margin-bottom: 5px;">
                                <span style="
                                    padding: 2px 6px; 
                                    border-radius: 4px; 
                                    font-size: 0.8rem;
                                    background-color: {{ $order->status == 'shipping' ? '#bb86fc' : '#cf6679' }};
                                    color: #000;
                                ">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <div style="font-size: 0.8rem; color: #bbb;">
                                <div>Ordered: {{ $order->created_at->format('M d, h:i A') }}</div>
                                @php
                                    $shippedDate = $order->shipped_at ?? ($order->status == 'shipping' ? $order->updated_at : null);
                                @endphp
                                @if($shippedDate)
                                    <div style="color: #bb86fc;">Shipped: {{ $shippedDate->format('M d, h:i A') }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.9rem;">
                                @foreach($order->items as $orderItem)
                                    <li>
                                        {{ $orderItem->item ? $orderItem->item->name : 'Item Removed' }} 
                                        (x{{ $orderItem->quantity }})
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<h2 class="mb-4 mt-8">Delivered Orders</h2>
@if($deliveredOrders->isEmpty())
    <p>No delivered orders yet.</p>
@else
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Tracking ID</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Items</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deliveredOrders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td style="font-family: monospace; color: #bbb;">12837812378917238921386189236</td>
                        <td>${{ number_format($order->total_price, 2) }}</td>
                        <td>
                            <div style="margin-bottom: 5px;">
                                <span style="
                                    padding: 2px 6px; 
                                    border-radius: 4px; 
                                    font-size: 0.8rem;
                                    background-color: #03dac6;
                                    color: #000;
                                ">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <div style="font-size: 0.8rem; color: #bbb;">
                                <div>Ordered: {{ $order->created_at->format('M d, h:i A') }}</div>
                                @php
                                    $shippedDate = $order->shipped_at ?? ($order->status == 'shipping' ? $order->updated_at : null);
                                    $deliveredDate = $order->delivered_at ?? ($order->status == 'delivered' ? $order->updated_at : null);
                                @endphp
                                @if($shippedDate || $order->status == 'delivered')
                                    {{-- If delivered, we might not have shipped date if it was skipped, so only show if we have it or if status is shipping --}}
                                    @if($shippedDate)
                                        <div style="color: #bb86fc;">Shipped: {{ $shippedDate->format('M d, h:i A') }}</div>
                                    @endif
                                @endif
                                @if($deliveredDate)
                                    <div style="color: #03dac6;">Delivered: {{ $deliveredDate->format('M d, h:i A') }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.9rem;">
                                @foreach($order->items as $orderItem)
                                    <li>
                                        {{ $orderItem->item ? $orderItem->item->name : 'Item Removed' }} 
                                        (x{{ $orderItem->quantity }})
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
