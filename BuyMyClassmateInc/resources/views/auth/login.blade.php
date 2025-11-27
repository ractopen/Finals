@extends('layouts.app')

@section('content')
<div class="card" style="max-width: 400px; margin: 0 auto;">
    <h2 class="text-center mb-4">Login</h2>
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="{{ old('username') }}" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit" class="btn" style="width: 100%;">Login</button>
    </form>
</div>
@endsection
