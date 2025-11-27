@extends('layouts.app')

@section('content')
<div class="black-page">
    <h1 style="font-size: 3rem; margin-bottom: 2rem;">Put Payment Process Here</h1>
    <p class="mb-4">Order ID: #{{ $order->id }}</p>
    <p class="mb-4">Total: ${{ number_format($order->total_price, 2) }}</p>
    
    <form action="{{ route('payment.confirm', $order->id) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-secondary" style="font-size: 1.2rem; padding: 15px 30px;">Complete Payment</button>
    </form>
    
    <a href="{{ route('checkout') }}" class="btn btn-danger mt-4">Go Back</a>
</div>
@endsection
