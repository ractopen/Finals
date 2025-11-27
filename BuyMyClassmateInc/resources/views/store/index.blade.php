@extends('layouts.app')

@section('content')
<h1 class="mb-4">Classmates for Sale</h1>
<div class="grid">
    @foreach($items as $item)
        <div class="card">
            @if($item->image_path)
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" style="width: 100%; height: 200px; object-fit: cover; border-radius: 4px; margin-bottom: 10px;">
            @else
                <div style="width: 100%; height: 200px; background-color: #333; display: flex; align-items: center; justify-content: center; border-radius: 4px; margin-bottom: 10px;">
                    <span>No Image</span>
                </div>
            @endif
            <h3>{{ $item->name }}</h3>
            <p style="color: #bbb; font-size: 0.9rem; margin-bottom: 10px;">{{ Str::limit($item->description, 100) }}</p>
            <div class="flex justify-between items-center">
                <span style="font-size: 1.2rem; font-weight: bold; color: #bb86fc;">${{ number_format($item->price, 2) }}</span>
                <span style="font-size: 0.8rem; color: #888;">Qty: {{ $item->quantity }}</span>
            </div>
            <div class="mt-4 flex gap-2">
                @auth
                    @if($item->quantity > 0)
                        <button onclick="openModal({{ $item->id }}, {{ json_encode($item->name) }}, {{ json_encode($item->description) }}, {{ $item->price }}, {{ $item->quantity }}, '{{ $item->image_path ? asset('storage/' . $item->image_path) : '' }}', false)" class="btn" style="flex: 1;">Add to Cart</button>
                        <button onclick="openModal({{ $item->id }}, {{ json_encode($item->name) }}, {{ json_encode($item->description) }}, {{ $item->price }}, {{ $item->quantity }}, '{{ $item->image_path ? asset('storage/' . $item->image_path) : '' }}', true)" class="btn btn-secondary" style="flex: 1;">Buy Now</button>
                    @else
                        <button class="btn btn-danger" disabled style="width: 100%; opacity: 0.5; cursor: not-allowed;">Out of Stock</button>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn" style="flex: 1; text-align: center;">Login to Buy</a>
                @endauth
            </div>
        </div>
    @endforeach
</div>

<!-- Modal -->
<div id="itemModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 90%; max-width: 500px; position: relative; max-height: 90vh; overflow-y: auto;">
        <button onclick="closeModal()" style="position: absolute; top: 10px; right: 10px; background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">&times;</button>
        
        <img id="modalImage" src="" alt="Item Image" style="width: 100%; height: 200px; object-fit: cover; border-radius: 4px; margin-bottom: 15px; display: none;">
        <h2 id="modalTitle" class="mb-4"></h2>
        <p id="modalDescription" class="mb-4" style="color: #bbb;"></p>
        <p class="mb-4" style="font-size: 1.2rem; color: #bb86fc;">Price: $<span id="modalPrice"></span></p>
        
        <form id="modalForm" method="POST">
            @csrf
            <div class="form-group">
                <label>Quantity (Max: <span id="modalMaxQty"></span>)</label>
                <input type="number" name="quantity" id="modalQty" value="1" min="1" required>
            </div>
            <div id="buyNowInputContainer"></div>
            <button type="submit" class="btn" style="width: 100%;" id="modalSubmitBtn">Confirm</button>
        </form>
    </div>
</div>

<script>
    function openModal(id, name, description, price, maxQty, imageUrl, isBuyNow) {
        document.getElementById('itemModal').style.display = 'flex';
        document.getElementById('modalTitle').innerText = name;
        document.getElementById('modalDescription').innerText = description;
        document.getElementById('modalPrice').innerText = price.toFixed(2);
        document.getElementById('modalMaxQty').innerText = maxQty;
        
        const imgElement = document.getElementById('modalImage');
        if (imageUrl) {
            imgElement.src = imageUrl;
            imgElement.style.display = 'block';
        } else {
            imgElement.style.display = 'none';
        }
        
        const qtyInput = document.getElementById('modalQty');
        qtyInput.max = maxQty;
        qtyInput.value = 1;
        
        const form = document.getElementById('modalForm');
        form.action = "/cart/add/" + id;
        
        const buyNowContainer = document.getElementById('buyNowInputContainer');
        const submitBtn = document.getElementById('modalSubmitBtn');
        
        if (isBuyNow) {
            buyNowContainer.innerHTML = '<input type="hidden" name="buy_now" value="1">';
            submitBtn.innerText = "Buy Now";
            submitBtn.className = "btn btn-secondary";
        } else {
            buyNowContainer.innerHTML = '';
            submitBtn.innerText = "Add to Cart";
            submitBtn.className = "btn";
        }
    }

    function closeModal() {
        document.getElementById('itemModal').style.display = 'none';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('itemModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endsection
