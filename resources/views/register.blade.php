<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bean N' Bites - Sign Up</title>
    <link rel="stylesheet" href="/css/register.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="signup-container">
        <p class="brand-header">BEAN N' BITES</p>
        <h1>SIGN UP</h1>

        <form id="signupForm" action="#" method="POST">
            @csrf
            
            <div class="input-group">
                <div class="input-wrapper">
                    <i class="fa-regular fa-user field-icon"></i>
                    <input type="text" id="name" name="name" placeholder="Name" required autocomplete="off">
                </div>
            </div>

            <div class="input-group">
                <div class="input-wrapper">
                    <i class="fa-solid fa-at field-icon"></i>
                    <input type="text" id="username" name="username" placeholder="Username" required autocomplete="off">
                </div>
            </div>

            <div class="input-group">
                <div class="input-wrapper">
                    <i class="fa-regular fa-envelope field-icon"></i>
                    <input type="email" id="email" name="email" placeholder="Email Address" required autocomplete="off">
                </div>
            </div>

            <div class="password-row">
                <div class="input-group split-input">
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock field-icon"></i>
                        <input type="password" id="password" name="password" placeholder="Password" required>
                        <i class="fa-regular fa-eye toggle-password-icon" onclick="togglePasswordVisibility('password', this)"></i>
                    </div>
                </div>

                <div class="input-group split-input">
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock field-icon"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
                        <i class="fa-regular fa-eye toggle-password-icon" onclick="togglePasswordVisibility('password_confirmation', this)"></i>
                    </div>
                </div>
            </div>

            <div class="terms-container">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">I Agree with Terms and service and privacy policy</label>
            </div>

            <button type="submit" class="btn-signup">
                SIGN UP <i class="fa-solid fa-arrow-right"></i>
            </button>
        </form>

        <div class="divider">
            <span>or</span>
        </div>

        <button type="button" id="googleAuthBtn" class="btn-google">
            <img src="/background_assets/GoogleLogo.webp" alt="Google Logo" class="google-icon">
            Sign up with google
        </button>

        <div class="login-footer">
            Already have an account? <a href="/">Login here</a>
        </div>
    </div>

    <script>
        // Password Visibility Toggle Logic
        function togglePasswordVisibility(fieldId, eyeIcon) {
            const passwordField = document.getElementById(fieldId);
            if (passwordField.type === "password") {
                passwordField.type = "text";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            }
        }

        // Google Authentication Warning Message Trigger
        document.getElementById('googleAuthBtn').addEventListener('click', () => {
            alert("This feature is under the process");
        });

        // Redirect to login screen after clicking "SIGN UP"
        document.getElementById('signupForm').addEventListener('submit', (e) => {
            e.preventDefault(); // Temporarily prevent full page reload error since action points nowhere
            
            // Optional registration toast / alert before shipping them back
            alert("Account registered successfully!");
            
        // Change it to route them right into your dashboard layout on signup click instead:
        window.location.href = "/dashboard";
        });
    </script>
</body>
</html>