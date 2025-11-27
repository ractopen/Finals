@extends('layouts.app')

@section('content')
<h1 class="mb-4">Admin Dashboard</h1>

@if ($errors->any())
    <div class="alert alert-error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid" style="grid-template-columns: 1fr;">
    <!-- Items Management -->
    <div class="card">
        <h2>Items Management</h2>
        
        <!-- Add Item Form -->
        <div style="background-color: #2c2c2c; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
            <h3>Add New Item</h3>
            <form action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" step="0.01" name="price" required>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" required>
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" name="image">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="2" required></textarea>
                </div>
                <button type="submit" class="btn btn-secondary">Add Item</button>
            </form>
        </div>

        <!-- Items List -->
        <div style="max-height: 400px; overflow-y: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->name }}</td>
                            <td>${{ $item->price }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>
                                @if($item->trashed())
                                    <span style="color: #cf6679;">Deleted</span>
                                @else
                                    <span style="color: #03dac6;">Active</span>
                                @endif
                            </td>
                            <td>
                                @if($item->trashed())
                                    <form action="{{ route('admin.items.update', $item->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="restore" value="1">
                                        <button type="submit" class="btn btn-secondary" style="padding: 2px 5px; font-size: 0.8rem;">Restore</button>
                                    </form>
                                @else
                                    <button onclick="openEditItemModal({{ $item->id }}, {{ json_encode($item->name) }}, {{ $item->price }}, {{ $item->quantity }}, {{ json_encode($item->description) }})" class="btn btn-secondary" style="padding: 2px 5px; font-size: 0.8rem;">Edit</button>
                                    <form action="{{ route('admin.items.destroy', $item->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="padding: 2px 5px; font-size: 0.8rem;">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Orders Management -->
    <div class="card">
        <h2>Orders Management</h2>
        
        <h3>Preparing Orders</h3>
        <div style="max-height: 300px; overflow-y: auto; margin-bottom: 20px;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($preparingOrders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->user ? $order->user->username : 'Unknown' }}</td>
                            <td>${{ $order->total_price }}</td>
                            <td>
                                <div><span style="color: #bbb;">Preparing</span></div>
                                <div style="font-size: 0.8rem; color: #bbb; margin-top: 4px;">
                                    <div>Ordered: {{ $order->created_at->format('M d, H:i') }}</div>
                                </div>
                            </td>
                            <td>
                                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" style="padding: 2px;">
                                        <option value="preparing" selected>Preparing</option>
                                        <option value="shipping">Shipping</option>
                                        <option value="delivered">Delivered</option>
                                    </select>
                                    <button type="submit" class="btn btn-secondary" style="padding: 2px 5px; font-size: 0.8rem;">Update</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h3>Shipping Orders</h3>
        <div style="max-height: 300px; overflow-y: auto; margin-bottom: 20px;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shippingOrders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->user ? $order->user->username : 'Unknown' }}</td>
                            <td>${{ $order->total_price }}</td>
                            <td>
                                <div><span style="color: #bb86fc;">Shipping</span></div>
                                <div style="font-size: 0.8rem; color: #bbb; margin-top: 4px;">
                                    <div>Ordered: {{ $order->created_at->format('M d, H:i') }}</div>
                                    @php
                                        $shippedDate = $order->shipped_at ?? ($order->status == 'shipping' ? $order->updated_at : null);
                                    @endphp
                                    @if($shippedDate)
                                        <div style="color: #bb86fc;">Shipped: {{ $shippedDate->format('M d, H:i') }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" style="padding: 2px;">
                                        <option value="preparing">Preparing</option>
                                        <option value="shipping" selected>Shipping</option>
                                        <option value="delivered">Delivered</option>
                                    </select>
                                    <button type="submit" class="btn btn-secondary" style="padding: 2px 5px; font-size: 0.8rem;">Update</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h3>Delivered Orders</h3>
        <div style="max-height: 300px; overflow-y: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deliveredOrders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->user ? $order->user->username : 'Unknown' }}</td>
                            <td>${{ $order->total_price }}</td>
                            <td>
                                <span style="color: #03dac6;">Delivered</span>
                                <div style="font-size: 0.8rem; color: #bbb; margin-top: 4px;">
                                    <div>Ordered: {{ $order->created_at->format('M d, H:i') }}</div>
                                    @php
                                        $shippedDate = $order->shipped_at ?? ($order->status == 'shipping' ? $order->updated_at : null);
                                        $deliveredDate = $order->delivered_at ?? ($order->status == 'delivered' ? $order->updated_at : null);
                                    @endphp
                                    @if($shippedDate)
                                        <div style="color: #bb86fc;">Shipped: {{ $shippedDate->format('M d, H:i') }}</div>
                                    @endif
                                    @if($deliveredDate)
                                        <div style="color: #03dac6;">Delivered: {{ $deliveredDate->format('M d, H:i') }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" style="padding: 2px;">
                                        <option value="preparing">Preparing</option>
                                        <option value="shipping">Shipping</option>
                                        <option value="delivered" selected>Delivered</option>
                                    </select>
                                    <button type="submit" class="btn btn-secondary" style="padding: 2px 5px; font-size: 0.8rem;">Update</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Users Management -->
    <div class="card">
        <h2>Users Management</h2>
        
        <!-- Add User Form -->
        <div style="background-color: #2c2c2c; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
            <h3>Add New User</h3>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="grid">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required minlength="8">
                    </div>
                </div>
                <div class="form-group">
                    <label style="display: inline-flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="is_admin" style="width: auto; margin: 0;">
                        Is Admin
                    </label>
                </div>
                <button type="submit" class="btn btn-secondary">Add User</button>
            </form>
        </div>

        <div style="max-height: 400px; overflow-y: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->is_admin ? 'Admin' : 'User' }}</td>
                            <td>
                                @if($user->trashed())
                                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="restore" value="1">
                                        <button type="submit" class="btn btn-secondary" style="padding: 2px 5px; font-size: 0.8rem;">Restore</button>
                                    </form>
                                @else
                                    <button onclick="openEditUserModal({{ $user->id }}, {{ json_encode($user->name) }}, {{ json_encode($user->username) }}, {{ json_encode($user->email) }}, {{ $user->is_admin ? 1 : 0 }})" class="btn btn-secondary" style="padding: 2px 5px; font-size: 0.8rem;">Edit</button>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="padding: 2px 5px; font-size: 0.8rem;">Delete</button>
                                    </form>
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
<div id="editItemModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 90%; max-width: 500px; position: relative;">
        <button onclick="closeEditItemModal()" style="position: absolute; top: 10px; right: 10px; background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">&times;</button>
        <h3 class="mb-4">Edit Item</h3>
        <form id="editItemForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" id="editItemName" required>
            </div>
            <div class="form-group">
                <label>Price</label>
                <input type="number" step="0.01" name="price" id="editItemPrice" required>
            </div>
            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity" id="editItemQty" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="editItemDesc" rows="2" required></textarea>
            </div>
            <div class="form-group">
                <label>Image (Optional)</label>
                <input type="file" name="image">
            </div>
            <button type="submit" class="btn btn-secondary" style="width: 100%;">Update Item</button>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 90%; max-width: 500px; position: relative;">
        <button onclick="closeEditUserModal()" style="position: absolute; top: 10px; right: 10px; background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">&times;</button>
        <h3 class="mb-4">Edit User</h3>
        <form id="editUserForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" id="editUserName" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" id="editUserUsername" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="editUserEmail" required>
            </div>
            <div class="form-group">
                <label>Password (Leave blank to keep current)</label>
                <input type="password" name="password" minlength="8">
            </div>
            <div class="form-group">
                <label style="display: inline-flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="is_admin" id="editUserIsAdmin" style="width: auto; margin: 0;">
                    Is Admin
                </label>
            </div>
            <button type="submit" class="btn btn-secondary" style="width: 100%;">Update User</button>
        </form>
    </div>
</div>

<script>
    function openEditItemModal(id, name, price, qty, desc) {
        document.getElementById('editItemModal').style.display = 'flex';
        document.getElementById('editItemForm').action = "/admin/items/" + id;
        document.getElementById('editItemName').value = name;
        document.getElementById('editItemPrice').value = price;
        document.getElementById('editItemQty').value = qty;
        document.getElementById('editItemDesc').value = desc;
    }

    function closeEditItemModal() {
        document.getElementById('editItemModal').style.display = 'none';
    }

    function openEditUserModal(id, name, username, email, isAdmin) {
        document.getElementById('editUserModal').style.display = 'flex';
        document.getElementById('editUserForm').action = "/admin/users/" + id;
        document.getElementById('editUserName').value = name;
        document.getElementById('editUserUsername').value = username;
        document.getElementById('editUserEmail').value = email;
        document.getElementById('editUserIsAdmin').checked = isAdmin == 1;
    }

    function closeEditUserModal() {
        document.getElementById('editUserModal').style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('editItemModal')) {
            closeEditItemModal();
        }
        if (event.target == document.getElementById('editUserModal')) {
            closeEditUserModal();
        }
    }
</script>
@endsection
