<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bean & Bites POS')</title>
    <link rel="stylesheet" href="/css/dashboard.css">
    
    @if(Request::is('products'))
        <link rel="stylesheet" href="/css/products.css">
    @endif

    @if(Request::is('pos'))
        <link rel="stylesheet" href="/css/pos.css">
    @endif
    
    @if(Request::is('reports'))
        <link rel="stylesheet" href="/css/reports.css">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <header class="main-navbar">
        <div class="nav-branding">
            BEAN & BITES POS
        </div>
        
        <nav class="nav-links">
            <a href="/dashboard" class="{{ Request::is('dashboard') ? 'active' : '' }}">
                <img src="/background_assets/Dashboard.svg" alt="" class="nav-icon"> DASHBOARD
            </a>
            <a href="/products" class="{{ Request::is('products') ? 'active' : '' }}">
                <img src="/background_assets/Product.svg" alt="" class="nav-icon"> PRODUCTS
            </a>
            <a href="/pos" class="{{ Request::is('pos') ? 'active' : '' }}">
                <img src="/background_assets/POS.svg" alt="" class="nav-icon"> POS
            </a>
            <a href="/reports" class="{{ Request::is('reports') ? 'active' : '' }}">
                <img src="/background_assets/Report.svg" alt="" class="nav-icon"> REPORTS
            </a>
        </nav>

        <div class="nav-user-profile">
            <span class="employee-name">{{ Auth::user()->username }}</span>
            <div class="user-avatar-circle">
                <img src="/background_assets/2x2.jpg" alt="Profile">
            </div>
            <a href="/logout" class="logout-link">
                <i class="fa-solid fa-right-from-bracket"></i> LOGOUT
            </a>
        </div>
    </header>

    <main class="content-wrapper">
        @yield('content')
    </main>

</body>
</html>