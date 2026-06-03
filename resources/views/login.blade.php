<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bean N' Bites - Login</title>
    <link rel="stylesheet" href="/css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="login-container">
        <div class="brand-side">
            <div class="brand-content">
                <div class="mini-logo-container">
                    <img src="/background_assets/2x2.jpg" alt="Bean N' Bites Logo" class="brand-logo-img">
                </div>
                <h1>BEAN<br>N' BITES</h1>
                <p class="subtitle">POINT OF SALE SYSTEM</p>
                <div class="line-decorator"></div>
            </div>
        </div>

        <div class="form-side">
            <div class="form-content">
                <div class="welcome-logo-container">
                    <img src="/background_assets/2x2.jpg" alt="Bean N' Bites Welcome Logo" class="form-logo-img">
                </div>
                <h2>Welcome Back</h2>
                <p class="form-instruction">Sign in to your account</p>

                <form id="loginForm" action="javascript:void(0);">
                    @csrf
                    
                    <div class="input-group">
                        <label for="username">USERNAMES</label>
                        <div class="input-wrapper">
                            <i class="fa-regular fa-user field-icon"></i>
                            <input type="text" id="username" name="username" placeholder="Enter username" required autocomplete="off">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="password">PASSWORD</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock field-icon"></i>
                            <input type="password" id="password" name="password" placeholder="Enter password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        Login <i class="fa-solid fa-arrow-right"></i>
                    </button>

                    <div class="signup-text">
                        Don't have an account? <a href="/register">Sign up here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/js/login.js"></script>

    <script>
        document.getElementById('loginForm').addEventListener('submit', (e) => {
            // Stop page from resetting instantly on form submit check
            e.preventDefault();
            
            // Redirect right into your root dashboard path rule mapping module
            window.location.href = "/dashboard";
        });
    </script>
</body>
</html>