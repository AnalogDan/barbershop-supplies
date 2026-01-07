document.addEventListener('DOMContentLoaded', () => {
    const toggles = document.querySelectorAll('.password-toggle');

    if (!toggles.length) return;

    toggles.forEach(toggle => {
        const targetId = toggle.dataset.target;
        const passwordInput = document.getElementById(targetId);
        const icon = toggle.querySelector('i');

        if (!passwordInput || !icon) return;

        toggle.addEventListener('click', () => {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';

            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    });
});