document.addEventListener('DOMContentLoaded', function() {
    const signupForm = document.getElementById('signupForm');
    if (!signupForm) {
        console.error("Signup form not found");
        return;
    }

    signupForm.addEventListener('submit', function (event) {
        event.preventDefault();
        clearErrors();
        
        let isValid = verif();
        
        if (isValid) {
            const emailInput = document.getElementById('email');
            const emailValue = emailInput.value.trim();
            
            fetch('signup.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'check_email=1&email=' + encodeURIComponent(emailValue)
            })
            .then(response => response.json())
            .then(data => {
                if (!data.result) {
                    setError(emailInput, "Cet email est déjà utilisé.");
                } else {

                    signupForm.submit();
                }
            })
            .catch(error => {
                console.error("Erreur:", error);
            });
        }
    });
});

function verif() {
    clearErrors();
    let isValid = true;

    const prenomInput = document.getElementById("prenom");
    const nomInput = document.getElementById("nom");
    const ageInput = document.getElementById("age");
    const emailInput = document.getElementById("email");
    const telInput = document.getElementById("tel");
    const mdpInput = document.getElementById("mdp");
    const c_mdpInput = document.getElementById("c_mdp");
    const patientRadio = document.getElementById("patient");
    const medecinRadio = document.getElementById("medecin");
    const termsCheckbox = document.getElementById("terms");

    if (prenomInput.value.trim() === "" || prenomInput.value.trim().length <= 2 || !alpha(prenomInput.value)) {
        setError(prenomInput, "Le prénom doit comporter plus de 2 caractères (lettres uniquement).");
        isValid = false;
    }
    if (nomInput.value.trim() === "" || nomInput.value.trim().length <= 2 || !alpha(nomInput.value)) {
        setError(nomInput, "Le nom doit comporter plus de 2 caractères (lettres uniquement).");
        isValid = false;
    }
    if (ageInput.value.trim() === "" || isNaN(ageInput.value) || Number(ageInput.value) <= 0 || !verif_age(ageInput.value)) {
        setError(ageInput, "Veuillez entrer un âge valide.");
        isValid = false;
    }
    if (emailInput.value.trim() === "" || !verif_email(emailInput.value)) {
        setError(emailInput, "Veuillez entrer un email valide.");
        isValid = false;
    }
    if (telInput.value.trim() === "" || isNaN(telInput.value) || telInput.value.length < 8 || !verif_tel(telInput.value)) {
        setError(telInput, "Veuillez entrer un numéro de téléphone valide.");
        isValid = false;
    }
    if (mdpInput.value.trim() === "" || mdpInput.value.length < 8) {
        setError(mdpInput, "Le mot de passe doit comporter au moins 8 caractères.");
        isValid = false;
    }
    if (c_mdpInput.value.trim() === "" || c_mdpInput.value !== mdpInput.value) {
        setError(c_mdpInput, "La confirmation ne correspond pas au mot de passe.");
        isValid = false;
    }
    if (!patientRadio.checked && !medecinRadio.checked) {
        const errorRadio = document.getElementById("error_userType");
        errorRadio.innerText = "Veuillez sélectionner un type d'utilisateur.";
        errorRadio.parentElement.classList.add("error");
        isValid = false;
    }
    if (!termsCheckbox.checked) {
        const errorTerms = document.getElementById("error_terms");
        errorTerms.innerText = "Veuillez accepter les conditions d'utilisation.";
        errorTerms.parentElement.classList.add("error");
        isValid = false;
    }
    
    return isValid;
}

function setError(inputElement, message) {
    const container = inputElement.parentElement;
    container.classList.add("error");
    const errorDiv = document.getElementById("error_" + inputElement.id);
    if (errorDiv) {
        errorDiv.innerText = message;
    }
}

function clearErrors() {
    const errorMessages = document.querySelectorAll(".error-message");
    errorMessages.forEach(msg => msg.innerText = "");
    const errorContainers = document.querySelectorAll(".input-container.error");
    errorContainers.forEach(container => container.classList.remove("error"));
}

function alpha(ch) {
    return /^[a-zA-Z\-'\s]+$/.test(ch);
}

function verif_age(age) {
    return /^-?\d+$/.test(age) && Number.isInteger(Number(age));
}

function verif_tel(tel) {
    if (tel.length < 8) return false;
    return !isNaN(tel);
}

function verif_email(email) {
    return /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email);
}
