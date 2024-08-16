import {populateProductNotificationTable} from "../modules/tables/ProductNotificationTable.js";

document.addEventListener('DOMContentLoaded', function () {
    // Récupérer la date de collecte sélectionnée
    const collectionDate = document.getElementById('collectionDate').value;

    const queryParams = {
        is_assigned: false
    };

    // Appeler la fonction pour peupler la table avec les notifications
    populateProductNotificationTable(queryParams);


    // Écouteur pour le bouton "Toutes les dates"
    document.getElementById('allCollectionDates').addEventListener('click', async () => {
        // Créer les query parameters
        const queryParams = {
            is_assigned: false
        };

        // Appeler la fonction pour peupler la table avec les notifications mises à jour
        populateProductNotificationTable(queryParams);
    });

    document.getElementById('collectionDate').addEventListener('change', async () => {
        // Récupérer la date de collecte sélectionnée
        const collectionDate = document.getElementById('collectionDate').value;

        // Vérifier si une date a été sélectionnée
        if (!collectionDate) {
            alert('Veuillez sélectionner une date.');
            return;
        }

        console.log("date",collectionDate);

        // Créer les query parameters
        const queryParams = {
            date : collectionDate,
            is_assigned: false
        };

        // Appeler la fonction pour peupler la table avec les notifications mises à jour
        populateProductNotificationTable(queryParams);
    });


});