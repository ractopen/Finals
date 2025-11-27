@extends('layouts.app')

@section('content')
<div class="card" style="max-width: 400px; margin: 0 auto;">
    <h2 class="text-center mb-4">Register</h2>
    <form action="{{ route('register') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
        </div>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="{{ old('username') }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
        </div>
        <div class="form-group">
            <label for="password">Password (Min 8 chars)</label>
            <input type="password" name="password" id="password" required minlength="8">
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>
        </div>
        <button type="submit" class="btn" style="width: 100%;">Register</button>
    </form>
</div>
@endsection
