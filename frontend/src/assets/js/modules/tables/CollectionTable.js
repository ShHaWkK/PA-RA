import { getAllCollections, getCollectionsByDate } from '/assets/js/api/Collections.js';
import { populateVolunteerDetailsInModal, populateVehicleDetailsInModal, populateCollectedProductsModal } from "../modals/CollectionModals.js";
import { formatDateToFrench } from "../FormatDate.js";

export async function populateCollectionTable(date) {
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
    const headers = ['', 'Affected driver', 'Affected vehicle', 'Collected Products', 'Collection Date', 'Created At', 'Updated At'];
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
        let collections;

        // Vérifier si une date valide est fournie (non vide et non null)
        if (date && date.trim() !== "") {
            console.log("date",date);
            console.log("collectionByDate");
            collections = await getCollectionsByDate(date);
        } else {
            console.log("date",date);
            console.log("collectionByDate");
            collections = await getAllCollections();
        }

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

            // Créer la cellule du bouton radio
            const radioCell = document.createElement('td');
            const radioInput = document.createElement('input');
            radioInput.type = 'radio';
            radioInput.name = 'collectionSelection'; // Assurez-vous que toutes les options radio partagent le même nom
            radioInput.value = collection.id; // Attribuez l'ID de la collection comme valeur du bouton radio
            radioCell.appendChild(radioInput);

            // Créer les cellules de données pour le chauffeur
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

            // Créer la cellule avec le bouton 'voir' pour 'Collected Products'
            const productsCell = document.createElement('td');
            const viewProductsButton = document.createElement('button');
            viewProductsButton.textContent = 'Voir';
            viewProductsButton.value = collection.id;
            viewProductsButton.addEventListener('click', (e) => {
                e.preventDefault();
                populateCollectedProductsModal(collection.id);
                document.getElementById('collectedProductsDetailsModal').style.display = 'block';
            });
            productsCell.appendChild(viewProductsButton);

            // Créer les autres cellules de données
            const collectionDateCell = document.createElement('td');
            collectionDateCell.textContent = formatDateToFrench(new Date(collection.collection_date).getTime());

            const createdAtCell = document.createElement('td');
            createdAtCell.textContent = collection.created_at ? formatDateToFrench(new Date(collection.created_at).getTime()) : 'N/A';

            const updatedAtCell = document.createElement('td');
            updatedAtCell.textContent = collection.updated_at ? formatDateToFrench(new Date(collection.updated_at).getTime()) : 'N/A';

            // Ajouter toutes les cellules à la ligne
            row.appendChild(radioCell);
            row.appendChild(volunteerCell);
            row.appendChild(vehicleCell);
            row.appendChild(productsCell);
            row.appendChild(collectionDateCell);
            row.appendChild(createdAtCell);
            row.appendChild(updatedAtCell);

            tbody.appendChild(row);
        });

        table.appendChild(tbody);

    } catch (error) {
        console.error('Error in populateCollectionTable:', error.message);
        // Retirer le loader en cas d'erreur
        document.getElementById('loadingBodyGeneral').classList.add('hidden');
    }
}
