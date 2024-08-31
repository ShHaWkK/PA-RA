import {getAllWarehouses} from "../../api/Warehouse.js";
import {formatDateToFrench} from "../FormatDate.js";

export async function populateWarehouseTable(queryParameters) {
    const loader = document.getElementById('loadingBodyWarehouse');
    // Sélectionner le conteneur où la table sera insérée
    const tableContainer = document.querySelector('.warehouse-table');

    try {
        if (!tableContainer) {
            console.error('Warehouse table container not found.');
            return;
        }

        tableContainer.classList.add('hidden');
        loader.classList.remove('hidden');

        // Récupérer les entrepôts
        const warehouses = await getAllWarehouses(queryParameters);
        console.log("warehouses", warehouses);

        // Vérifier si des entrepôts sont fournis
        if (!warehouses || warehouses.length === 0) {
            console.log('No warehouses found');
            document.getElementById('loadingBodyWarehouse').classList.add('hidden');
            return;
        }

        // Créer la structure de la table
        const table = document.createElement('table');
        table.classList.add('warehouse-table'); // Ajouter une classe pour le style

        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');

        // Définir les en-têtes de la table
        const headers = ['Select', 'Name', 'Address', 'Contact Info', 'Capacity', 'City', 'Country', 'Stocks Count', 'Created At', 'Updated At'];
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
        warehouses.forEach(warehouse => {
            const row = document.createElement('tr');
            row.dataset.warehouseId = warehouse.id;

            // Ajout du radio-button dans la première cellule
            const radioCell = document.createElement('td');
            const radioButton = document.createElement('input');
            radioButton.type = 'radio';
            radioButton.name = 'selectedWarehouse';
            radioButton.value = warehouse.id;
            radioCell.appendChild(radioButton);
            row.appendChild(radioCell);

            // Créer les cellules de données pour chaque champ
            const nameCell = document.createElement('td');
            nameCell.textContent = warehouse.name;
            nameCell.setAttribute('data-id', warehouse.id);
            nameCell.addEventListener('click', (e) => {
                e.preventDefault();
                // populateWarehouseDetailsInModal(warehouse.id);
                document.getElementById('warehouseDetailModal').style.display = 'block';
            });

            const addressCell = document.createElement('td');
            addressCell.textContent = warehouse.address;

            const contactInfoCell = document.createElement('td');
            contactInfoCell.textContent = warehouse.contact_info;

            const capacityCell = document.createElement('td');
            capacityCell.textContent = warehouse.capacity;

            const cityCell = document.createElement('td');
            cityCell.textContent = warehouse.city;

            const countryCell = document.createElement('td');
            countryCell.textContent = warehouse.country;

            const stocksCountCell = document.createElement('td');
            stocksCountCell.textContent = warehouse.stocks_count;

            const createdAtCell = document.createElement('td');
            createdAtCell.textContent = formatDateToFrench(new Date(warehouse.created_at));

            const updatedAtCell = document.createElement('td');
            updatedAtCell.textContent = formatDateToFrench(new Date(warehouse.updated_at));

            // Ajouter toutes les cellules à la ligne
            row.appendChild(radioCell);
            row.appendChild(nameCell);
            row.appendChild(addressCell);
            row.appendChild(contactInfoCell);
            row.appendChild(capacityCell);
            row.appendChild(cityCell);
            row.appendChild(countryCell);
            row.appendChild(stocksCountCell);
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
        console.error('Error in populateWarehouseTable:', error.message);
    } finally {
        // Retirer le loader
        loader.classList.add('hidden');
        tableContainer.classList.remove('hidden');
    }
}