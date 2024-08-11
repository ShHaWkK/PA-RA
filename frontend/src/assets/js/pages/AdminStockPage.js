import { getAllWarehouses, getWarehouseCapacity} from '/assets/js/api/Warehouse.js';
import {populateStockTable} from '/assets/js/modules/tables/StockTable.js';
import {deleteStock} from "/assets/js/api/Stocks.js";

export var selectedWarehouseId;
export var selectedWarehouseAvailableVolume;

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
async function handleWarehouseChange(event) {
    selectedWarehouseId = event.target.value;
    console.log("selectedWarehouseVolume", selectedWarehouseAvailableVolume);
    // On retire l'affichage de la barre de progression
    const progressBar = document.getElementById('progress-bar');
    progressBar.style.display = 'none';
    populateStockTable(selectedWarehouseId);
    console.log("selectedWarehouseVolume", selectedWarehouseAvailableVolume);
}

// Gestion de la barre de progression de la capacité d'un entrepôt:
function updateProgressBar(occupied, total) {
    const percentage = (occupied / total) * 100;
    const progressLabel = document.getElementById('progress-label');

    const progressBar = document.getElementById('progress-bar');
    progressBar.style.display = 'none';

    console.log('progressBar',progressBar.style.display);

    progressBar.style.width = percentage + '%';
    progressBar.textContent = percentage.toFixed(2) + '%';
    progressLabel.textContent = `${occupied}/${total} m³`;

    // Change color based on the percentage
    if (percentage < 50) {
        progressBar.style.backgroundColor = '#76c7c0'; // Green
    } else if (percentage < 75) {
        progressBar.style.backgroundColor = '#ffa500'; // Orange
    } else {
        progressBar.style.backgroundColor = '#ff0000'; // Red
    }

    progressBar.style.display = 'block';
    console.log('progressBar',progressBar.style.display);

}

export async function populateProgressBar(warehouseId) {
    try {
        const capacityData = await getWarehouseCapacity(warehouseId);
        const { total_capacity, occupied_capacity } = capacityData;
        selectedWarehouseAvailableVolume = capacityData.available_capacity;
        document.getElementById("available-volume").value = selectedWarehouseAvailableVolume;
        updateProgressBar(occupied_capacity, total_capacity);
    } catch (error) {
        console.error('Error populating progress bar:', error.message);
    }
}

function setupDeleteStockButton(deleteButtonId, getStockIdFunction) {
    const deleteButton = document.getElementById(deleteButtonId);

    deleteButton.addEventListener('click', async function() {
        const stockId = getStockIdFunction(); // Obtenez l'ID du stock via la fonction passée en paramètre

        if (!stockId) {
            alert('Aucun stock sélectionné.');
            return;
        }

        const confirmation = confirm('Voulez-vous supprimer le stock ?');
        if (confirmation) {
            try {
                const result = await deleteStock(stockId);
                alert(result.message); // Affiche un message de succès après la suppression
            } catch (error) {
                alert('Erreur lors de la suppression du stock : ' + error.message);
            }
        }
    });
}

function deleteSelectedStock() {
    const selectedRadio = document.querySelector('input[name="selectedStock"]:checked');

    if (selectedRadio) {
        const stockId = selectedRadio.value; // Récupérer l'ID du stock sélectionné

        // Demander une confirmation à l'utilisateur
        const confirmation = confirm("Voulez-vous vraiment supprimer ce stock ?");
        if (confirmation) {
            // Appeler la fonction deleteStock avec l'ID du stock sélectionné
            deleteStock(stockId)
                .then(response => {
                    alert(response.message); // Afficher un message de succès
                    populateStockTable(selectedWarehouseId); // Actualiser la table (assurez-vous que selectedWarehouseId est bien défini)
                })
                .catch(error => {
                    alert("Erreur lors de la suppression du stock : " + error.message);
                });
        }
    } else {
        alert("Veuillez sélectionner un stock à supprimer.");
    }
}

document.addEventListener('DOMContentLoaded',
    function (){
        populateWarehouseSelector();
        const warehouseSelect = document.getElementById('warehouseSelect');
        warehouseSelect.addEventListener('change', handleWarehouseChange);

        const stockDelete = document.getElementById('deleteStockButton');
        stockDelete.addEventListener('click', deleteSelectedStock);

        populateStockTable();
    });