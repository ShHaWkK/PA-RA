import { getStockByWarehouse } from '/assets/js/api/Stocks.js'; // Assurez-vous que le chemin vers votre fichier Stock.js est correct

export var selectedWarehouseId;

export async function populateStockTable(warehouseId) {
    console.log("warehouse id", warehouseId);

    // Afficher le loader
    if (warehouseId == undefined || warehouseId == null || warehouseId == 'Choose a warehouse') {
        // Retirer le loader
        document.getElementById('loadingBodyGeneral').classList.add('hidden');

        const message = document.createElement('h2');
        message.innerHTML = 'Please select a warehouse';

        const backOfficeContent = document.querySelector('.stock-table');
        if (backOfficeContent) {
            backOfficeContent.innerHTML = ''; // Effacer le contenu existant si nécessaire
            backOfficeContent.appendChild(message);
        } else {
            console.error('Back office content container not found.');
        }
        return;
    }

    document.getElementById('loadingBodyGeneral').classList.remove('hidden');

    // Sélectionner le conteneur back-office-content
    const backOfficeContent = document.querySelector('.stock-table');
    if (!backOfficeContent) {
        console.error('Back office content container not found.');
        return;
    }

    // Effacer le contenu existant du tableau avant de faire l'appel API
    backOfficeContent.innerHTML = '';

    // Créer le tableau et son header
    const table = document.createElement('table');
    table.classList.add('stock-table'); // Ajout de la classe stock-table pour le style
    table.id = 'stockTable';

    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');

    const headers = ['ID', 'Product ID', 'Quantity', 'Entry Date', 'Exit Date', 'Availability', 'Created At', 'Updated At'];
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
        const stocks = await getStockByWarehouse(warehouseId);

        if (!stocks || stocks.length === 0) {
            console.log('No stocks found');
            document.getElementById('loadingBodyGeneral').classList.add('hidden');
            return;
        }

        // Enlever le loader
        document.getElementById('loadingBodyGeneral').classList.add('hidden');

        const tbody = document.createElement('tbody');

        stocks.forEach(stock => {
            const row = document.createElement('tr');
            row.dataset.stockId = stock.id; // Ajout de l'id du stock en tant que dataset

            const cells = [
                stock.id,
                stock.productId,
                stock.quantity,
                formatDateToFrench(stock.entryDate.timestamp * 1000), // Convertir le timestamp en date
                stock.exitDate ? formatDateToFrench(stock.exitDate.timestamp * 1000) : 'N/A', // Vérifier si exitDate est null
                stock.availability,
                formatDateToFrench(stock.createdAt.timestamp * 1000),
                formatDateToFrench(stock.updatedAt.timestamp * 1000)
            ];

            cells.forEach(cellText => {
                const td = document.createElement('td');
                td.textContent = cellText;
                row.appendChild(td);
            });

            tbody.appendChild(row);
        });

        table.appendChild(tbody);

        setupSearch(stocks, 'stockTable');
    } catch (error) {
        console.error('Error in populateStockTable:', error.message);
        // Retirer le loader en cas d'erreur 400 ou 404
        document.getElementById('loadingBodyGeneral').classList.add('hidden');
        if (error.status === 400 || error.status === 404) {
            console.log('Stocks not found or bad request');
        }
    }
}


// Fonction de formatage de date (exemple simple)
function formatDateToFrench(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleDateString('fr-FR') + ' ' + date.toLocaleTimeString('fr-FR');
}

// Fonction d'initialisation de recherche (doit être implémentée)
function setupSearch(items, tableId) {
    // Implémentez la logique de recherche ici
}