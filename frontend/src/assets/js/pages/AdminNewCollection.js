import {populateProductNotificationTable} from "../modules/tables/ProductNotificationTable.js";
import {populateDriverTable} from "../modules/tables/VolunteerTable.js";
import {getAllVehicles} from "../api/Vehicle.js";
import {populateVehicleTable} from "../modules/tables/VehicleTable.js";
import {populateWarehouseTable} from "../modules/tables/WarehouseTable.js";


async function populateVehicleSelector() {
    try {
        const vehicles = await getAllVehicles();
        const vehicleSelect = document.getElementById('vehicleSelect');

        // Clear any existing options in the select element
        vehicleSelect.innerHTML = '';

        // Add a default "Choose a vehicle" option
        const defaultOption = document.createElement('option');
        defaultOption.text = 'Choose a vehicle';
        defaultOption.value = '';
        vehicleSelect.add(defaultOption);

        // Populate the select element with vehicles
        vehicles.forEach(vehicle => {
            const option = document.createElement('option');
            option.text = vehicle.name;
            option.value = vehicle.id;
            vehicleSelect.add(option);
        });
    } catch (error) {
        console.error('Error populating vehicle selector:', error.message);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Récupérer la date de collecte sélectionnée
    const collectionDate = document.getElementById('collectionDate').value;

    const queryParams = {
        is_assigned: false
    };

    // Appeler la fonction pour peupler la table avec les notifications
    populateProductNotificationTable(queryParams);

    // Appeler la fonction pour peupler la table de sélection du chauffeur
    populateDriverTable();

    // Écouteur pour le bouton "Toutes les dates"
    document.getElementById('allCollectionDates').addEventListener('click', async () => {
        // Créer les query parameters
        const queryParams = {
            is_assigned: false
        };

        // Appeler la fonction pour peupler la table avec les notifications mises à jour
        await populateProductNotificationTable(queryParams);
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
        await populateProductNotificationTable(queryParams);
    });

    // On remplit les tableaux
    populateVehicleTable();

    populateWarehouseTable();

});