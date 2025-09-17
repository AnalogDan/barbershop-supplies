document.addEventListener('DOMContentLoaded', () => {
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const icon = togglePassword.querySelector('i');

    if (togglePassword && password){
        togglePassword.addEventListener('click', function(){
            console.log("👁️ Eye icon clicked");
            const type = password.getAttribute('type') == 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        })
    }else{
        console.log("Toggle or password element not found");
    }
})