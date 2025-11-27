@extends('layouts.app')

@section('content')
    <div style="max-width: 1000px; margin: 0 auto; padding: 40px 20px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;">
            <!-- Image Section -->
            <div style="text-align: center;">
                <img src="{{ $item->image_path }}" alt="{{ $item->name }}" style="max-width: 100%; height: auto; border-radius: 20px;">
            </div>

            <!-- Details Section -->
            <div>
                <h1 style="font-size: 48px; font-weight: 700; margin-bottom: 20px;">{{ $item->name }}</h1>
                <div style="font-size: 24px; font-weight: 600; margin-bottom: 30px;">${{ number_format($item->price, 2) }}</div>
                
                <p style="font-size: 18px; line-height: 1.6; color: #86868b; margin-bottom: 40px;">
                    {{ $item->description }}
                </p>

                <form action="{{ route('cart.add', $item->id) }}" method="POST">
                    @csrf
                    @if($item->quantity > 0)
                        <button type="submit" class="btn" style="font-size: 18px; padding: 15px 40px;">Add to Bag</button>
                    @else
                        <button type="button" class="btn" style="background-color: #86868b; cursor: not-allowed; font-size: 18px; padding: 15px 40px;" disabled>Out of Stock</button>
                    @endif
                </form>

                <div style="margin-top: 30px;">
                    <a href="{{ route('home') }}" style="color: var(--accent-color); text-decoration: none; font-size: 16px;">&larr; Back to Store</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Added to Cart Modal -->
    @if(session('cart_success'))
        <div id="cartModal" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; display:flex; justify-content:center; align-items:center;">
            <div style="background:white; padding:30px; border-radius:18px; width:400px; text-align:center; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
                <div style="font-size: 48px; margin-bottom: 10px;">✅</div>
                <h2 style="margin-bottom: 10px;">Added to Bag</h2>
                <p style="color: #86868b; margin-bottom: 20px;">{{ session('cart_success') }}</p>
                <div style="display:flex; gap:10px; justify-content:center;">
                    <button onclick="document.getElementById('cartModal').style.display='none'" class="btn" style="background: #f5f5f7; color: #1d1d1f;">Continue Shopping</button>
                    <a href="{{ route('cart.view') }}" class="btn">View Bag</a>
                </div>
            </div>
        </div>
    @endif
@endsection
