import { getAllWarehouses } from '/assets/js/api/Warehouse.js';
import {populateStockTable} from '/assets/js/modules/tables/StockTable.js';

async function populateWarehouseSelector() {
    try {
        const warehouses = await getAllWarehouses();
        const warehouseSelect = document.getElementById('warehouseSelect');

        // Clear any existing options in the select element
        warehouseSelect.innerHTML = '';

        // Add a default "Choose a warehouse" option
        const defaultOption = document.createElement('option');
        defaultOption.text = 'Choose a warehouse';
        defaultOption.value = '';
        warehouseSelect.add(defaultOption);

        // Populate the select element with warehouses
        warehouses.forEach(warehouse => {
            const option = document.createElement('option');
            option.text = warehouse.name;
            option.value = warehouse.id;
            warehouseSelect.add(option);
        });
    } catch (error) {
        console.error('Error populating warehouse selector:', error.message);
    }
}

// Fonction pour gérer le changement dans le menu déroulant
function handleWarehouseChange(event) {
    const selectedWarehouseId = event.target.value;
    populateStockTable(selectedWarehouseId);
}

document.addEventListener('DOMContentLoaded',
    function (){
        populateWarehouseSelector();
        const warehouseSelect = document.getElementById('warehouseSelect');
        warehouseSelect.addEventListener('change', handleWarehouseChange);
        populateStockTable();
    })