export function validateDateInput(selector) {
    // Sélectionner l'élément par son sélecteur
    const inputElement = document.querySelector(selector);

    if (!inputElement) {
        console.error(`Element with selector "${selector}" not found.`);
        return;
    }

    // Ajouter un écouteur d'événements pour le changement de valeur
    inputElement.addEventListener('change', function() {
        // Obtenir la date sélectionnée et la date actuelle
        const selectedDate = new Date(inputElement.value);
        const today = new Date();

        // Réinitialiser la date d'aujourd'hui pour comparaison (sans heure)
        today.setHours(0, 0, 0, 0);

        // Vérifier si la date sélectionnée est antérieure à aujourd'hui
        if (selectedDate < today) {
            // Afficher une alerte
            alert('La date sélectionnée est antérieure à la date d\'aujourd\'hui.');

            // Réinitialiser le champ à la date d'aujourd'hui
            inputElement.value = today.toISOString().slice(0, 16);
        }
    });
}