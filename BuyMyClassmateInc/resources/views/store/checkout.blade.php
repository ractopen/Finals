@extends('layouts.app')

@section('content')
<h1 class="mb-4">Checkout</h1>

<div class="grid">
    <div class="card">
        <h3>Delivery Details</h3>
        <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
            @csrf
            @if(isset($selectedItems))
                @foreach($selectedItems as $id)
                    <input type="hidden" name="selected_items[]" value="{{ $id }}">
                @endforeach
            @endif
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ Auth::user()->email }}" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea name="address" id="address" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="postal_code">Postal Code</label>
                <input type="text" name="postal_code" id="postal_code" required>
            </div>
        </form>
    </div>

    <div class="card">
        <h3>Order Summary</h3>
        <table style="font-size: 0.9rem;">
            @php $total = 0; @endphp
            @foreach($cartItems as $cartItem)
                @if($cartItem->item)
                    <tr>
                        <td>{{ $cartItem->item->name }} x {{ $cartItem->quantity }}</td>
                        <td style="text-align: right;">${{ number_format($cartItem->item->price * $cartItem->quantity, 2) }}</td>
                    </tr>
                    @php $total += $cartItem->item->price * $cartItem->quantity; @endphp
                @endif
            @endforeach
            <tr style="border-top: 2px solid #444;">
                <td><strong>Total</strong></td>
                <td style="text-align: right;"><strong>${{ number_format($total, 2) }}</strong></td>
            </tr>
        </table>
        <button type="submit" form="checkout-form" class="btn mt-4" style="width: 100%;">Place Order</button>
    </div>
</div>
@endsection
