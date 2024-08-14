import { getAllCollections } from '/assets/js/api/Collections.js';
import {populateVolunteerDetailsInModal, populateVehicleDetailsInModal} from "../modals/CollectionModals.js";

export async function populateCollectionTable() {
    // Afficher le loader
    document.getElementById('loadingBodyGeneral').classList.remove('hidden');

    // Sélectionner le conteneur back-office-content
    const backOfficeContent = document.querySelector('.collection-table');
    if (!backOfficeContent) {
        console.error('Back office content container not found.');
        return;
    }

    // Effacer le contenu existant du tableau avant de faire l'appel API
    backOfficeContent.innerHTML = '';

    // Créer le tableau et son header
    const table = document.createElement('table');
    table.classList.add('collection-table'); // Ajout de la classe collection-table pour le style
    table.id = 'collectionTable';

    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');

    // Modifier les en-têtes de colonnes (sans ID)
    const headers = ['Affected driver','Affected vehicle', 'Collection Date', 'Created At', 'Updated At'];
    headers.forEach(headerText => {
        const th = document.createElement('th');
        th.textContent = headerText;
        headerRow.appendChild(th);
    });

    thead.appendChild(headerRow);
    table.appendChild(thead);

    // Ajouter le tableau vide avec le header au DOM
    backOfficeContent.appendChild(table);

    try {
        const collections = await getAllCollections();

        if (!collections || collections.length === 0) {
            console.log('No collections found');
            document.getElementById('loadingBodyGeneral').classList.add('hidden');
            return;
        }

        // Enlever le loader
        document.getElementById('loadingBodyGeneral').classList.add('hidden');

        const tbody = document.createElement('tbody');

        collections.forEach(collection => {
            const row = document.createElement('tr');
            row.dataset.collectionId = collection.id; // Ajout de l'id de la collection en tant que dataset

            // Créer les cellules de données
            const volunteerCell = document.createElement('td');
            const volunteerLink = document.createElement('a');
            volunteerLink.href = "#";
            volunteerLink.textContent = collection.volunteer_name; // Ajouter le texte du nom au lien
            volunteerLink.setAttribute('data-id', collection.volunteer_id); // Ajouter l'ID en tant qu'attribut data-id
            volunteerLink.addEventListener('click', (e) => {
                e.preventDefault();
                populateVolunteerDetailsInModal(collection.volunteer_id);
                document.getElementById('volunteerDetailsModal').style.display = 'block';
            });
            volunteerCell.appendChild(volunteerLink); // Ajouter le lien à la cellule

            // Créer les cellules de données pour le véhicule
            const vehicleCell = document.createElement('td');
            const vehicleLink = document.createElement('a');
            vehicleLink.href = "#";
            vehicleLink.textContent = collection.vehicle_license_plate; // Ajouter le texte du nom au lien
            vehicleLink.setAttribute('data-id', collection.vehicle_id); // Ajouter l'ID en tant qu'attribut data-id
            vehicleLink.addEventListener('click', (e) => {
                e.preventDefault();
                populateVehicleDetailsInModal(collection.vehicle_id);
                document.getElementById('vehicleDetailsModal').style.display = 'block';
            });
            vehicleCell.appendChild(vehicleLink); // Ajouter le lien à la cellule

            const cells = [
                volunteerCell,
                vehicleCell,
                formatDateToFrench(new Date(collection.collection_date).getTime()), // Convertir la date en timestamp
                collection.created_at ? formatDateToFrench(new Date(collection.created_at).getTime()) : 'N/A',
                collection.updated_at ? formatDateToFrench(new Date(collection.updated_at).getTime()) : 'N/A'
            ];

            cells.forEach(cell => {
                const td = typeof cell === 'object' ? cell : document.createElement('td');
                if (typeof cell !== 'object') {
                    td.textContent = cell;
                }
                row.appendChild(td);
            });

            tbody.appendChild(row);
        });

        table.appendChild(tbody);

    } catch (error) {
        console.error('Error in populateCollectionTable:', error.message);
        // Retirer le loader en cas d'erreur
        document.getElementById('loadingBodyGeneral').classList.add('hidden');
    }
}

// Fonction de formatage de date
function formatDateToFrench(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleDateString('fr-FR') + ' ' + date.toLocaleTimeString('fr-FR');
}