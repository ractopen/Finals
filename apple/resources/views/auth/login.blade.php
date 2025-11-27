@extends('layouts.app')

@section('content')
    <div style="max-width: 400px; margin: 40px auto;">
        <div class="card">
            <h2>Sign in to Apple Store</h2>
            
            @if($errors->any())
                <div style="color: var(--danger-color); margin-bottom: 20px; font-size: 14px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" style="width: 100%;">
                @csrf
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required autofocus>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                
                <button type="submit" class="btn" style="width: 100%; margin-top: 20px;">Sign In</button>
            </form>
            
            <div style="margin-top: 20px; font-size: 14px;">
                Don't have an Apple ID? <a href="{{ route('register') }}">Create yours now.</a>
            </div>
        </div>
    </div>
@endsection
