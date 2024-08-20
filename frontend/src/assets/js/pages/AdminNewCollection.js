import {populateProductNotificationTable} from "../modules/tables/ProductNotificationTable.js";
import {populateDriverTable} from "../modules/tables/VolunteerTable.js";
import {getAllVehicles} from "../api/Vehicle.js";
import {populateVehicleTable} from "../modules/tables/VehicleTable.js";
import {populateWarehouseTable} from "../modules/tables/WarehouseTable.js";
import {assignProductsToCollection, createCollection} from "../api/Collections.js";

function populateCollectionDate(){
    // Récupérer la date actuelle
    const today = new Date();

    // Formater la date en 'YYYY-MM-DD'
    // Mettre la date du jour comme valeur par défaut de l'input
    document.getElementById('collectionDate').value = today.toISOString().split('T')[0];
}

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
        alert(error.message);
    }
}

async function handleCollectionSubmission(message) {

    // Récupérer les valeurs cochées
    const selectedWarehouse = document.querySelector('input[name="selectedWarehouse"]:checked');
    const selectedVehicle = document.querySelector('input[name="selectedVehicle"]:checked');
    const selectedVolunteer = document.querySelector('input[name="selectedVolunteer"]:checked');
    const selectedProducts = Array.from(document.querySelectorAll('input[type="checkbox"]:checked'))
        .map(checkbox => ({ notification_id: parseInt(checkbox.value,10) }));

    // Vérification de la sélection des éléments requis
    if (!selectedWarehouse) {
        alert("Please select a warehouse.");
        return; // Arrêter l'exécution si l'entrepôt n'est pas sélectionné
    }
    if (!selectedVehicle) {
        alert("Please select a vehicle.");
        return; // Arrêter l'exécution si le véhicule n'est pas sélectionné
    }
    if (!selectedVolunteer) {
        alert("Please select a volunteer.");
        return; // Arrêter l'exécution si le bénévole n'est pas sélectionné
    }
    if (selectedProducts.length === 0) {
        alert("Please select at least one product.");
        return; // Arrêter l'exécution si aucun produit n'est sélectionné
    }

    const loader = document.getElementById("loadingBodyGeneral");
    const content = document.getElementsByClassName("back-office-content");

    if(content) {
        console.log("content exist");
        content[0].classList.add('hidden');
    }
        if (loader){
        loader.classList.remove('hidden');
        console.log("loader exist");
        }

        // Créer les données de la collecte
        const collectionData = {
            volunteer_id: parseInt(selectedVolunteer.value, 10),
            vehicle_id: parseInt(selectedVehicle.value, 10),
            collection_date: new Date().toISOString()
        };

        // Créer la collecte
        const collection = await createCollection(collectionData);
        if (collection){
            console.log("collection created");
        }

        // Assigner les produits à la collecte
        const assigned_products = await assignProductsToCollection(parseInt(collection.id,10), selectedProducts);
        if (assigned_products){
            console.log("products assigned successfully");
        }

        // Indiquer le succès de l'opération
        alert('Collection and product assignments successful.');

        content[0].classList.remove('hidden');
        loader.classList.add('hidden');

        window.location.href = '/Admin/Collections';
}

async function handleNotificationTableUpdate() {
    // Récupérer les valeurs des éléments de formulaire
    const selectedDate = document.getElementById('collectionDate').value;

    console.log('Selected Date:', selectedDate);

    // Appeler la fonction pour mettre à jour le tableau
    await populateProductNotificationTable(selectedDate);
}

function initializeTables() {
    const queryParams = { is_assigned: false };

    // Peupler les tables de notification, chauffeur, véhicule, et entrepôt
    populateProductNotificationTable(queryParams);
    populateDriverTable();
    populateVehicleTable();
    populateWarehouseTable();
}

function setupEventListeners() {
    const collectionDateInput = document.getElementById('collectionDate');
    const allCollectionDatesButton = document.getElementById('allCollectionDates');

    // Écouteur pour la modification de la date de collecte
    collectionDateInput.addEventListener('change', handleDateChange);

    // Écouteur pour le bouton "Toutes les dates"
    allCollectionDatesButton.addEventListener('click', handleAllDatesClick);
}

async function handleDateChange() {
    const collectionDate = document.getElementById('collectionDate').value;

    if (!collectionDate) {
        alert('Veuillez sélectionner une date.');
        return;
    }

    console.log("date", collectionDate);

    const queryParams = {
        date: collectionDate,
        is_assigned: false
    };

    await populateProductNotificationTable(queryParams);
}

async function handleAllDatesClick() {
    const queryParams = { is_assigned: false };
    await populateProductNotificationTable(queryParams);
}

document.addEventListener('DOMContentLoaded', function () {
    // Configuration initiale
    initializeTables();

    // Ajouter les écouteurs d'événements
    setupEventListeners();

    document.getElementById('createCollection').addEventListener('click',handleCollectionSubmission);
});