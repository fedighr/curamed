document.getElementById('loginForm').addEventListener('submit', function(event) {
    let isValid = true;
    
    document.getElementById('emailError').textContent = '';
    document.getElementById('passwordError').textContent = '';

    const email = document.getElementById('email').value;
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!email.match(emailPattern)) {
        document.getElementById('emailError').textContent = 'Veuillez entrer un e-mail valide.';
        isValid = false;
    }

    const password = document.getElementById('password').value;
    if (password.length < 6) {
        document.getElementById('passwordError').textContent = 'Le mot de passe doit comporter au moins 6 caractères.';
        isValid = false;
    }
    
    if (!isValid) {
        event.preventDefault();
    }
});
