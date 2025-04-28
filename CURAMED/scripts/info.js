function verif() {
    clearErrors();

    const currentPage = window.location.pathname.split('/').pop().toLowerCase();
    let isValid = true;

    if (currentPage === 'information_patient.html') {

        const tailleInput = document.getElementById("taille");
        const poidsInput = document.getElementById("poids");
        const maladiesChroniquesInput = document.getElementById("maladies_chroniques");
        const groupSanguinInput = document.getElementById("group_sanguin");
        const infoSupplementairesInput = document.getElementById("info_supplementaires");

        if (tailleInput.value.trim() === "" || isNaN(tailleInput.value) || Number(tailleInput.value) <= 0) {
            setError(tailleInput, "Veuillez entrer une taille valide en cm.");
            isValid = false;
        }

        if (poidsInput.value.trim() === "" || isNaN(poidsInput.value) || Number(poidsInput.value) <= 0) {
            setError(poidsInput, "Veuillez entrer un poids valide en kg.");
            isValid = false;
        }

        if (maladiesChroniquesInput.value.trim() === "") {
            setError(maladiesChroniquesInput, "Veuillez entrer les maladies chroniques.");
            isValid = false;
        }

        if (!groupSanguinInput.value) {
            setError(groupSanguinInput, "Veuillez sélectionner un groupe sanguin.");
            isValid = false;
        }

        if (infoSupplementairesInput.value.trim() === "") {
            setError(infoSupplementairesInput, "Veuillez entrer des informations supplémentaires.");
            isValid = false;
        }

        if (!isValid) {
            const firstErrorInput = document.querySelector('.input-container.error input, .input-container.error select, .input-container.error textarea');
            if (firstErrorInput) {
                firstErrorInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstErrorInput.focus();
            }
        } else {
            this.submit();
        }
    } else {


        const specialiteInput = document.getElementById('specialite');
        if (!specialiteInput.value) {
            setError(specialiteInput, "Veuillez sélectionner une spécialité.");
            isValid = false;
        }

        const adresseInput = document.getElementById('adresse');
        if (!adresseInput.value.trim()) {
            setError(adresseInput, "Veuillez entrer l'adresse du cabinet.");
            isValid = false;
        }

        const experienceInput = document.getElementById('experience');
        if (!experienceInput.value) {
            setError(experienceInput, "Veui llez donner votre expérience.");
            isValid = false;
        }

        const villeInput = document.getElementById('ville');
        if (!villeInput.value) {
            setError(villeInput, "Veuillez sélectionner une Ville.");
            isValid = false;
        }

        if (!isValid) {
            const firstErrorInput = document.querySelector('.input-container.error input, .input-container.error select, .input-container.error textarea');
            if (firstErrorInput) {
                firstErrorInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstErrorInput.focus();
            }
        } else {
            this.submit();
        }
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
    errorMessages.forEach(function (message) {
        message.innerText = "";
    });

    const errorContainers = document.querySelectorAll(".input-container.error");
    errorContainers.forEach(function (container) {
        container.classList.remove("error");
    });
}

document.getElementById('signup-form').addEventListener('submit', function (event) {
    event.preventDefault();


    if (verif()) {
        const formData = new FormData(this);

        fetch('signup.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {

                window.location.href = 'verification.php';
            } else {

                if (data.message) {
                    const emailInput = document.getElementById('email');
                    setError(emailInput, data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
});
