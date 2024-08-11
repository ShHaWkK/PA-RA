import { getAllCollections } from '/assets/js/api/Collections.js'; // Assurez-vous que le chemin vers votre fichier Collections.js est correct

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

    const headers = ['ID', 'Company ID', 'Product ID', 'Vehicle ID', 'Collection Date', 'Created At', 'Updated At'];
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

        console.log("collections", collections);

        collections.forEach(collection => {
            const row = document.createElement('tr');
            row.dataset.collectionId = collection.id; // Ajout de l'id de la collection en tant que dataset

            const cells = [
                collection.id,
                collection.companyId,
                collection.productId,
                collection.vehicleId,
                formatDateToFrench(collection.collectionDate.timestamp * 1000), // Convertir le timestamp en millisecondes
                formatDateToFrench(collection.createdAt.timestamp * 1000), // Convertir le timestamp en millisecondes
                formatDateToFrench(collection.updatedAt.timestamp * 1000) // Convertir le timestamp en millisecondes
            ];

            cells.forEach(cellText => {
                const td = document.createElement('td');
                td.textContent = cellText;
                row.appendChild(td);
            });

            tbody.appendChild(row);
        });

        table.appendChild(tbody);

        setupSearch(collections, 'collectionTable');
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

// Fonction d'initialisation de recherche (doit être implémentée)
function setupSearch(items, tableId) {
    // Implémentez la logique de recherche ici
}
