document.getElementById('loginForm').addEventListener('submit', function(event) {
    let isValid = true;

   
    document.getElementById('emailError').textContent = '';
    document.getElementById('passwordError').textContent = '';
    document.getElementById('loginError').textContent = ''; 

    const emailInput = document.getElementById('email');
    const emailContainer = emailInput.closest('.input-container');
    const email = emailInput.value;
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    
    // Validate Email
    if (!email.match(emailPattern)) {
        document.getElementById('emailError').textContent = 'Veuillez entrer un e-mail valide.';
        emailContainer.classList.add('error');  
        isValid = false;
    } else {
        emailContainer.classList.remove('error'); 
    }

    const passwordInput = document.getElementById('password');
    const passwordContainer = passwordInput.closest('.input-container');
    const password = passwordInput.value;
    
    
    if (password.length < 6) {
        document.getElementById('passwordError').textContent = 'Le mot de passe doit comporter au moins 6 caractères.';
        passwordContainer.classList.add('error'); 
        isValid = false;
    } else {
        passwordContainer.classList.remove('error'); 
    }

    if (isValid) {
        event.preventDefault(); 

        
        fetch('signin.php', {
            method: 'POST',
            body: new URLSearchParams({
                'email': email,
                'mdp': password
            })
        })
        .then(response => response.json())  
        .then(data => {
            if (data.success) {
                
                window.location.href = 'index.php'; 
            } else {
                
                document.getElementById('loginError').textContent = data.message || 'Email ou mot de passe incorrect.';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('loginError').textContent = 'Une erreur est survenue. Veuillez réessayer.';
        });
    } else {
        event.preventDefault(); 
    }
});
