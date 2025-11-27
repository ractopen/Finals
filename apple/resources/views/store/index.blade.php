@extends('layouts.app')

@section('content')
    <div class="text-center">
        <h1>Store. <span style="color: #86868b;">The best way to buy the products you love.</span></h1>
    </div>

    <div class="grid">
        @foreach($items as $item)
            <div class="card">
                <h3 class="product-title">{{ $item->name }}</h3>
                <div class="product-price">From ${{ number_format($item->price, 2) }}</div>
                
                <div style="margin-bottom: 20px;">
                    <a href="{{ route('item.show', $item->id) }}" style="font-size: 14px;">Learn more ></a>
                </div>

                <a href="{{ route('item.show', $item->id) }}">
                    <img src="{{ $item->image_path }}" alt="{{ $item->name }}" class="product-image" style="cursor: pointer;">
                </a>

                <form action="{{ route('cart.add', $item->id) }}" method="POST">
                    @csrf
                    @if($item->quantity > 0)
                        <button type="submit" class="btn">Buy</button>
                    @else
                        <button type="button" class="btn" style="background-color: #86868b; cursor: not-allowed;" disabled>Out of Stock</button>
                    @endif
                </form>
            </div>
        @endforeach
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
