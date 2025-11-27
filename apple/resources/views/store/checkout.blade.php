@extends('layouts.app')

@section('content')
    <div style="max-width: 600px; margin: 0 auto;">
        <div class="text-center mb-4">
            <h1>Checkout</h1>
        </div>

        <div class="card">
            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 20px; margin-bottom: 20px;">
                <div class="flex justify-between" style="font-size: 21px; font-weight: 600;">
                    <span>Total to Pay</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>
            </div>

            <form action="{{ route('checkout.process') }}" method="POST">
                @csrf
                <h3>Where should we send your order?</h3>
                
                <div class="form-group">
                    <label>Full Address</label>
                    <textarea name="address" rows="3" required placeholder="123 Apple St, Cupertino, CA"></textarea>
                </div>

                <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Postal Code</label>
                        <input type="text" name="postal_code" required placeholder="95014">
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone_number" required placeholder="(555) 123-4567">
                    </div>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="{{ Auth::user()->email }}" required>
                </div>

                <button type="submit" class="btn" style="width: 100%; margin-top: 20px; font-size: 17px; padding: 12px;">Place Order</button>
            </form>
        </div>
    </div>
@endsection
