import { getAllWarehouses, getWarehouseCapacity} from '/assets/js/api/Warehouse.js';
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
    populateProgressBar(selectedWarehouseId);
}

// Gestion de la barre de progression de la capacité d'un entrepôt:
function updateProgressBar(percentage) {
    const progressBar = document.getElementById('progress-bar');
    progressBar.style.width = percentage + '%';
    progressBar.textContent = percentage + '%';

    // Change color based on the percentage
    if (percentage < 50) {
        progressBar.style.backgroundColor = '#76c7c0'; // Green
    } else if (percentage < 75) {
        progressBar.style.backgroundColor = '#ffa500'; // Orange
    } else {
        progressBar.style.backgroundColor = '#ff0000'; // Red
    }
}

async function populateProgressBar(warehouseId) {
    try {
        const capacityData = await getWarehouseCapacity(warehouseId);
        const { total_capacity, occupied_capacity } = capacityData;
        const percentage = (occupied_capacity / total_capacity) * 100;
        updateProgressBar(percentage.toFixed(2));
    } catch (error) {
        console.error('Error populating progress bar:', error.message);
    }
}

document.addEventListener('DOMContentLoaded',
    function (){
        populateWarehouseSelector();
        const warehouseSelect = document.getElementById('warehouseSelect');
        warehouseSelect.addEventListener('change', handleWarehouseChange);
        populateStockTable();
    })