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

    <div id="toastMessage" class="toast-notification">
        <span class="toast-icon-wrapper"><i class="fa-solid fa-circle-exclamation"></i></span>
        <span id="toastText">Notification message goes here</span>
    </div>

    <div class="signup-container">
        <p class="brand-header">BEAN N' BITES</p>
        <h1>SIGN UP</h1>

        <form id="signupForm">
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
                        <input type="password" id="password" name="password" placeholder="Password (6-15 chars)" minlength="6" maxlength="15" required>
                        <i class="fa-regular fa-eye toggle-password-icon" onclick="togglePasswordVisibility('password', this)"></i>
                    </div>
                </div>

                <div class="input-group split-input">
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock field-icon"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" minlength="6" maxlength="15" required>
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

        // Custom Helper Function to show beautiful temporary Toast Alerts on screen
        function showToast(message) {
            const toast = document.getElementById('toastMessage');
            const toastText = document.getElementById('toastText');
            
            toastText.textContent = message;
            toast.classList.add('show');
            
            // Auto hide toast banner out of screen frame after 4 seconds
            setTimeout(() => {
                toast.classList.remove('show');
            }, 4000);
        }

        // Google Authentication Warning Message Trigger
        document.getElementById('googleAuthBtn').addEventListener('click', () => {
            showToast("This feature is under the process");
        });

        // AJAX Database Submission Engine
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = document.getElementById('email').value;
            const usernameInput = document.getElementById('username').value;
            const passwordInput = document.getElementById('password').value;
            const confirmPasswordInput = document.getElementById('password_confirmation').value;

            // 1. Double check email format has an "@" symbol manually just in case
            if (!emailInput.includes('@')) {
                showToast("Please enter a valid email address containing '@'.");
                return;
            }

            // 2. Frontend restriction check for password limits range configuration rules
            if (passwordInput.length < 6 || passwordInput.length > 15) {
                showToast("Password must be between 6 to 15 characters long.");
                return;
            }

            // 3. Confirm match verification rule mapping
            if (passwordInput !== confirmPasswordInput) {
                showToast("Passwords do not match.");
                return;
            }
            
            const formData = new FormData(this);

            // Automatically format and capitalize the first letter of the username on submission
            const formattedUsername = usernameInput.charAt(0).toUpperCase() + usernameInput.slice(1);
            formData.set('username', formattedUsername);

            // Fetch request pointing straight to your Laravel register route handler
            fetch('/register', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(res => {
                if (res.status === 200 && res.body.success) {
                    alert(res.body.message); // Native alert for critical redirection changes
                    window.location.href = "/";
                } else {
                    if (res.body.errors) {
                        let errorMessages = Object.values(res.body.errors).flat().join('\n');
                        showToast(errorMessages);
                    } else {
                        showToast(res.body.message || "Registration processing failed.");
                    }
                }
            })
            .catch(error => {
                console.error("Database Registration Error:", error);
                showToast("Could not connect to registration database server.");
            });
        });
    </script>
</body>
</html>