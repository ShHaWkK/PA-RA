import {formatDateToFrench} from "../FormatDate.js";
import {getAllVehicles} from "../../api/Vehicle.js";

export async function populateVehicleTable(queryParameters) {
    try {
        // Afficher le loader
        document.getElementById('loadingBodyVehicle').classList.remove('hidden');

        // Sélectionner le conteneur où la table sera insérée
        const tableContainer = document.querySelector('.vehicle-table');
        if (!tableContainer) {
            console.error('Vehicle table container not found.');
            return;
        }

        // Récupérer les véhicules (fonction à implémenter pour obtenir les véhicules en fonction des paramètres)
        const vehicles = await getAllVehicles(queryParameters);
        console.log("vehicles", vehicles);

        // Vérifier si des véhicules sont fournis
        if (!vehicles || vehicles.length === 0) {
            console.log('No vehicles found');
            document.getElementById('loadingBodyVehicle').classList.add('hidden');
            return;
        }

        // Créer la structure de la table
        const table = document.createElement('table');
        table.classList.add('vehicle-table'); // Ajouter une classe pour le style

        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');

        // Définir les en-têtes de la table
        const headers = ['Select', 'Brand', 'Model', 'License Plate', 'Status', 'Current Location', 'Created At', 'Updated At'];
        headers.forEach(headerText => {
            const th = document.createElement('th');
            th.textContent = headerText;
            headerRow.appendChild(th);
        });

        thead.appendChild(headerRow);
        table.appendChild(thead);

        // Créer le tbody
        const tbody = document.createElement('tbody');

        // Ajouter les lignes au tbody
        vehicles.forEach(vehicle => {
            const row = document.createElement('tr');
            row.dataset.vehicleId = vehicle.id;

            // Ajout du radio-button dans la première cellule
            const radioCell = document.createElement('td');
            const radioButton = document.createElement('input');
            radioButton.type = 'radio';
            radioButton.name = 'selectedVehicle';
            radioButton.value = vehicle.id;
            radioCell.appendChild(radioButton);
            row.appendChild(radioCell);

            // Créer les cellules de données pour chaque champ
            const brandCell = document.createElement('td');
            brandCell.textContent = vehicle.brand;

            const modelCell = document.createElement('td');
            modelCell.textContent = vehicle.model;

            const licensePlateCell = document.createElement('td');
            licensePlateCell.textContent = vehicle.licensePlate;

            const statusCell = document.createElement('td');
            statusCell.textContent = vehicle.status;

            const currentLocationCell = document.createElement('td');
            currentLocationCell.textContent = vehicle.currentLocation;

            const createdAtCell = document.createElement('td');
            createdAtCell.textContent = formatDateToFrench(new Date(vehicle.createdAt.timestamp * 1000));

            const updatedAtCell = document.createElement('td');
            updatedAtCell.textContent = formatDateToFrench(new Date(vehicle.updatedAt.timestamp * 1000));

            // Ajouter toutes les cellules à la ligne
            row.appendChild(radioCell);
            row.appendChild(brandCell);
            row.appendChild(modelCell);
            row.appendChild(licensePlateCell);
            row.appendChild(statusCell);
            row.appendChild(currentLocationCell);
            row.appendChild(createdAtCell);
            row.appendChild(updatedAtCell);

            tbody.appendChild(row);
        });

        // Attacher le tbody à la table
        table.appendChild(tbody);

        // Effacer le contenu existant du conteneur
        tableContainer.innerHTML = '';

        // Attacher la table complète au conteneur
        tableContainer.appendChild(table);

    } catch (error) {
        console.error('Error in populateVehicleTable:', error.message);
    } finally {
        // Retirer le loader
        document.getElementById('loadingBodyVehicle').classList.add('hidden');
    }
}
