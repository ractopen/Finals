<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apple Store</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <nav class="navbar">
        <div class="container nav-content">
            <a href="{{ route('home') }}" class="logo">
                <svg viewBox="0 0 170 170" width="20" height="20">
                    <path fill="currentColor" d="M150.37 130.25c-2.45 5.66-5.35 10.87-8.71 15.66-4.58 6.53-8.33 11.05-11.22 13.56-4.48 4.12-9.28 6.23-14.42 6.35-3.69 0-8.14-1.05-13.32-3.18-5.197-2.12-9.973-3.17-14.34-3.17-4.58 0-9.492 1.05-14.746 3.17-5.262 2.13-9.501 3.24-12.742 3.35-4.929.21-9.842-1.96-14.746-6.52-3.13-2.73-7.045-7.41-11.735-14.04-5.032-7.08-9.169-15.29-12.41-24.65-3.471-10.11-5.211-19.9-5.211-29.378 0-10.857 2.346-20.221 7.045-28.1 6.855-11.872 17.206-17.801 31.063-17.801 9.848 0 18.816 3.769 26.903 11.309 3.965 3.661 7.245 5.497 9.84 5.497 2.836 0 6.239-2.018 10.222-6.059 6.779-6.878 15.658-10.318 26.654-10.318 5.197 0 10.582.883 16.154 2.653 19.102 5.321 29.577 18.975 31.433 21.02-1.448 1.136-2.976 2.488-4.581 4.058-8.995 8.792-13.501 19.761-13.501 32.902 0 11.713 3.961 21.807 11.886 30.292 1.258 1.363 2.595 2.621 4.015 3.775-2.476 6.47-5.049 11.4-7.717 14.78zM129.85 27.6c-7.226-8.533-11.209-19.111-11.949-31.735 10.685.839 19.985 5.91 27.89 15.215 7.016 8.25 11.126 18.54 12.332 30.865-1.352.154-2.66.231-3.926.231-9.609 0-17.747-4.89-24.347-14.576z" />
                </svg>
            </a>
            <div class="nav-links">
                <a href="{{ route('home') }}">Store</a>
                @auth
                    <a href="{{ route('cart.view') }}">Cart</a>
                    @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}">Admin</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="inline-form">
                        @csrf
                        <button type="submit" class="nav-btn">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container content">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <p>Copyright © {{ date('Y') }} Viray Inc. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
