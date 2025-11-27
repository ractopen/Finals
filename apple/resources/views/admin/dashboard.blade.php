@extends('layouts.app')

@section('content')
    <div class="text-center mb-4">
        <h1>Admin Dashboard</h1>
    </div>

    @if(session('success'))
        <div style="background: #e6f4ea; color: #137333; padding: 15px; border-radius: 12px; margin-bottom: 20px; text-align: center;">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid" style="grid-template-columns: 1fr; gap: 40px;">
        
        <!-- Orders Section -->
        <div class="card">
            <h2>Order Management</h2>
            
            <div style="width: 100%; text-align: left;">
                <h3 style="margin-top: 20px; color: #86868b;">Preparing Orders</h3>
                @if($preparingOrders->isEmpty())
                    <p style="color: #86868b; font-size: 14px;">No orders to prepare.</p>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($preparingOrders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->user ? $order->user->username : 'Unknown' }}</td>
                                    <td>${{ number_format($order->total_price, 2) }}</td>
                                    <td>
                                        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="shipping">
                                            <button type="submit" class="btn" style="font-size: 12px; padding: 4px 12px;">Ship Order</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <h3 style="margin-top: 40px; color: #86868b;">Shipping Orders</h3>
                @if($shippingOrders->isEmpty())
                    <p style="color: #86868b; font-size: 14px;">No orders currently shipping.</p>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Timestamps</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shippingOrders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->user ? $order->user->username : 'Unknown' }}</td>
                                    <td style="font-size: 12px;">
                                        <div>Ord: {{ $order->created_at->format('M d H:i') }}</div>
                                        @if($order->shipped_at)
                                            <div style="color: var(--accent-color);">Shp: {{ $order->shipped_at->format('M d H:i') }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="delivered">
                                            <button type="submit" class="btn" style="font-size: 12px; padding: 4px 12px; background-color: var(--success-color);">Mark Delivered</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <h3 style="margin-top: 40px; color: #86868b;">Delivered Orders</h3>
                @if($deliveredOrders->isEmpty())
                    <p style="color: #86868b; font-size: 14px;">No delivered orders.</p>
                @else
                    <div style="max-height: 300px; overflow-y: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Delivered At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deliveredOrders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->user ? $order->user->username : 'Unknown' }}</td>
                                        <td>
                                            @if($order->delivered_at)
                                                {{ $order->delivered_at->format('M d, Y H:i') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Items Management -->
        <div class="card">
            <h2>Products</h2>
            <details style="width: 100%; margin-bottom: 20px;">
                <summary style="cursor: pointer; color: var(--accent-color); margin-bottom: 10px;">Add New Product</summary>
                
                <div style="margin-bottom: 15px; padding: 15px; background: #f5f5f7; border-radius: 12px;">
                    <label style="font-size: 12px; font-weight: 600; color: #86868b;">Quick Template</label>
                    <select id="productTemplate" onchange="applyTemplate()" style="margin-top: 5px;">
                        <option value="">Select a template...</option>
                        <option value='{"name":"iPhone 15","desc":"New camera. New design. Newphoria.","price":"799.00","img":"https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pink-select-202309?wid=512&hei=512&fmt=png-alpha&.v=1692923774125"}'>iPhone 15</option>
                        <option value='{"name":"iPhone 15 Plus","desc":"Huge screen. Huge battery.","price":"899.00","img":"https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-plus-green-select-202309?wid=512&hei=512&fmt=png-alpha&.v=1692923774125"}'>iPhone 15 Plus</option>
                        <option value='{"name":"iPhone 14","desc":"As amazing as ever.","price":"699.00","img":"https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-blue-select-202209?wid=512&hei=512&fmt=png-alpha&.v=1661978428190"}'>iPhone 14</option>
                        <option value='{"name":"iPhone 13","desc":"A total powerhouse.","price":"599.00","img":"https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-13-midnight-select-2021?wid=512&hei=512&fmt=png-alpha&.v=1629907844000"}'>iPhone 13</option>
                        <option value='{"name":"iPhone SE","desc":"Serious power. Value price.","price":"429.00","img":"https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-se-midnight-select-202203?wid=512&hei=512&fmt=png-alpha&.v=1646070493725"}'>iPhone SE</option>
                    </select>
                </div>

                <form action="{{ route('admin.items.store') }}" method="POST">
                    @csrf
                    <div class="form-group"><input type="text" id="p_name" name="name" placeholder="Product Name" required></div>
                    <div class="form-group"><input type="text" id="p_desc" name="description" placeholder="Description" required></div>
                    <div class="form-group"><input type="text" id="p_img" name="image_path" placeholder="Image URL" required></div>
                    <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div class="form-group"><input type="number" id="p_price" step="0.01" name="price" placeholder="Price" required></div>
                        <div class="form-group"><input type="number" name="quantity" placeholder="Stock" required></div>
                    </div>
                    <button type="submit" class="btn">Add Product</button>
                </form>
            </details>

            <script>
                function applyTemplate() {
                    const select = document.getElementById('productTemplate');
                    const data = JSON.parse(select.value);
                    
                    if(data) {
                        document.getElementById('p_name').value = data.name;
                        document.getElementById('p_desc').value = data.desc;
                        document.getElementById('p_price').value = data.price;
                        document.getElementById('p_img').value = data.img;
                    }
                }
            </script>

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>${{ number_format($item->price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>
                                <button onclick="openEditItemModal({{ json_encode($item) }})" style="color: var(--accent-color); background: none; border: none; cursor: pointer; margin-right: 10px;">Edit</button>
                                <form action="{{ route('admin.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete this item?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="color: var(--danger-color); background: none; border: none; cursor: pointer;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Users Management -->
        <div class="card">
            <h2>Users</h2>
            <details style="width: 100%; margin-bottom: 20px;">
                <summary style="cursor: pointer; color: var(--accent-color); margin-bottom: 10px;">Add New User</summary>
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="form-group"><input type="text" name="name" placeholder="Full Name" required></div>
                    <div class="form-group"><input type="text" name="username" placeholder="Username" required></div>
                    <div class="form-group"><input type="email" name="email" placeholder="Email" required></div>
                    <div class="form-group"><input type="password" name="password" placeholder="Password" required></div>
                    <div class="form-group">
                        <select name="is_admin">
                            <option value="0">Regular User</option>
                            <option value="1">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn">Add User</button>
                </form>
            </details>

            <div style="max-height: 400px; overflow-y: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->is_admin ? 'Admin' : 'User' }}</td>
                                <td>
                                    <button onclick="openEditUserModal({{ json_encode($user) }})" style="color: var(--accent-color); background: none; border: none; cursor: pointer; margin-right: 10px;">Edit</button>
                                    @if(Auth::id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Delete this user?');" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="color: var(--danger-color); background: none; border: none; cursor: pointer;">Delete</button>
                                        </form>
                                    @else
                                        <span style="color: #86868b;">(You)</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <!-- Edit Item Modal -->
    <div id="editItemModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
        <div style="background:white; padding:20px; border-radius:12px; width:400px; max-width:90%;">
            <h3>Edit Product</h3>
            <form id="editItemForm" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group"><input type="text" id="edit_name" name="name" placeholder="Product Name" required></div>
                <div class="form-group"><input type="text" id="edit_desc" name="description" placeholder="Description" required></div>
                <div class="form-group"><input type="text" id="edit_img" name="image_path" placeholder="Image URL" required></div>
                <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div class="form-group"><input type="number" id="edit_price" step="0.01" name="price" placeholder="Price" required></div>
                    <div class="form-group"><input type="number" id="edit_qty" name="quantity" placeholder="Stock" required></div>
                </div>
                <div style="display:flex; gap:10px; margin-top:15px;">
                    <button type="submit" class="btn">Update</button>
                    <button type="button" class="btn" onclick="closeEditItemModal()" style="background:#86868b;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
        <div style="background:white; padding:20px; border-radius:12px; width:400px; max-width:90%;">
            <h3>Edit User</h3>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group"><input type="text" id="edit_user_name" name="name" placeholder="Full Name" required></div>
                <div class="form-group"><input type="text" id="edit_user_username" name="username" placeholder="Username" required></div>
                <div class="form-group"><input type="email" id="edit_user_email" name="email" placeholder="Email" required></div>
                <div class="form-group"><input type="password" name="password" placeholder="New Password (leave blank to keep)"></div>
                <div class="form-group">
                    <select id="edit_user_admin" name="is_admin">
                        <option value="0">Regular User</option>
                        <option value="1">Admin</option>
                    </select>
                </div>
                <div style="display:flex; gap:10px; margin-top:15px;">
                    <button type="submit" class="btn">Update</button>
                    <button type="button" class="btn" onclick="closeEditUserModal()" style="background:#86868b;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditItemModal(item) {
            document.getElementById('editItemModal').style.display = 'flex';
            document.getElementById('editItemForm').action = '/admin/items/' + item.id;
            document.getElementById('edit_name').value = item.name;
            document.getElementById('edit_desc').value = item.description;
            document.getElementById('edit_img').value = item.image_path;
            document.getElementById('edit_price').value = item.price;
            document.getElementById('edit_qty').value = item.quantity;
        }

        function closeEditItemModal() {
            document.getElementById('editItemModal').style.display = 'none';
        }

        function openEditUserModal(user) {
            document.getElementById('editUserModal').style.display = 'flex';
            document.getElementById('editUserForm').action = '/admin/users/' + user.id;
            document.getElementById('edit_user_name').value = user.name;
            document.getElementById('edit_user_username').value = user.username;
            document.getElementById('edit_user_email').value = user.email;
            document.getElementById('edit_user_admin').value = user.is_admin ? 1 : 0;
        }

        function closeEditUserModal() {
            document.getElementById('editUserModal').style.display = 'none';
        }
    </script>
@endsection
