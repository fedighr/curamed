function verif() {
    clearErrors(); // Clear previous errors

    let isValid = true;

    const tailleInput = document.getElementById("taille");
    const poidsInput = document.getElementById("poids");
    const maladiesChroniquesInput = document.getElementById("maladies_chroniques");
    const groupSanguinInput = document.getElementById("group_sanguin");
    const infoSupplementairesInput = document.getElementById("info_supplementaires");

    // Validate Taille
    if (tailleInput.value.trim() === "" || isNaN(tailleInput.value) || Number(tailleInput.value) <= 0) {
        setError(tailleInput, "Veuillez entrer une taille valide en cm.");
        isValid = false;
    }

    // Validate Poids
    if (poidsInput.value.trim() === "" || isNaN(poidsInput.value) || Number(poidsInput.value) <= 0) {
        setError(poidsInput, "Veuillez entrer un poids valide en kg.");
        isValid = false;
    }

    // Validate Maladies Chroniques
    if (maladiesChroniquesInput.value.trim() === "") {
        setError(maladiesChroniquesInput, "Veuillez entrer les maladies chroniques.");
        isValid = false;
    }

    // Validate Groupe Sanguin
    if (!groupSanguinInput.value) {
        setError(groupSanguinInput, "Veuillez sélectionner un groupe sanguin.");
        isValid = false;
    }

    // Validate Informations Supplémentaires
    if (infoSupplementairesInput.value.trim() === "") {
        setError(infoSupplementairesInput, "Veuillez entrer des informations supplémentaires.");
        isValid = false;
    }

    if (!isValid) {
        const firstErrorInput = document.querySelector('.input-container.error input, .input-container.error select');
        if (firstErrorInput) {
            firstErrorInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstErrorInput.focus();
        }
    } else {
        this.submit();
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
