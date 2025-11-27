@extends('layouts.app')

@section('content')
    <div style="max-width: 400px; margin: 40px auto;">
        <div class="card">
            <h2>Create your Apple ID</h2>
            
            @if($errors->any())
                <div style="color: var(--danger-color); margin-bottom: 20px; font-size: 14px;">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" style="width: 100%;">
                @csrf
                <div class="form-group">
                    <input type="text" name="name" placeholder="Full Name" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="name@example.com" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
                </div>
                
                <button type="submit" class="btn" style="width: 100%; margin-top: 20px;">Create Apple ID</button>
            </form>
            
            <div style="margin-top: 20px; font-size: 14px;">
                Already have an Apple ID? <a href="{{ route('login') }}">Sign in.</a>
            </div>
        </div>
    </div>
@endsection
