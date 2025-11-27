@extends('layouts.app')

@section('content')
    <div class="text-center" style="max-width: 600px; margin: 40px auto;">
        <div class="card">
            <div style="color: var(--success-color); font-size: 48px; margin-bottom: 20px;">✓</div>
            <h1>You're all set.</h1>
            <p style="font-size: 19px; color: #86868b; margin-bottom: 40px;">
                Thanks for your order, {{ Auth::user()->name }}. We'll send a confirmation email to {{ $order->email }}.
            </p>

            <div style="text-align: left; background: #f5f5f7; padding: 20px; border-radius: 12px;">
                <div class="flex justify-between mb-4">
                    <strong>Order Number</strong>
                    <span>#{{ $order->id }}</span>
                </div>
                <div class="flex justify-between mb-4">
                    <strong>Total Paid</strong>
                    <span>${{ number_format($order->total_price, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <strong>Delivery To</strong>
                    <span style="text-align: right;">{{ $order->address }}<br>{{ $order->postal_code }}</span>
                </div>
            </div>

            <a href="{{ route('home') }}" class="btn mt-4">Continue Shopping</a>
        </div>
    </div>
@endsection
