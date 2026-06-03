document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');

    // Handle submit behavior adjustments
    loginForm.addEventListener('submit', (e) => {
        const button = loginForm.querySelector('.btn-login');
        
        // Swaps login text with a loading indicator on click
        button.innerHTML = 'LOADING <i class="fa-solid fa-spinner fa-spin"></i>';
        button.style.pointerEvents = 'none';
        button.style.opacity = '0.8';
    });
});