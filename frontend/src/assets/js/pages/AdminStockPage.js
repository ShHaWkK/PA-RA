import { getAllWarehouses, getWarehouseCapacity} from '/assets/js/api/Warehouse.js';
import {deleteStock} from "/assets/js/api/Stocks.js";
import {getStock} from "/assets/js/api/Stocks.js";
import {populateStockTable} from "/assets/js/modules/tables/StockTable.js";
import { populateProductSelector, getSelectedStockId,applyWithdraw , addStockSubmitEvent} from "/assets/js/modules/modals/StockModals.js";


export var selectedWarehouseId;
export var selectedWarehouseAvailableVolume;

async function populateWarehouseSelector(selectorId) {
    try {
        const warehouses = await getAllWarehouses();
        let warehouseSelect;
        
        if(!selectorId) {
            warehouseSelect = document.getElementById('warehouseSelect');
        }else{
            warehouseSelect = document.getElementById(selectorId);
        }

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

document.addEventListener('DOMContentLoaded', async function() {
    // Fenêtre modale d'ajout de stock
    const addStockButton = document.getElementById("addStockButton");
    const stockModal = document.getElementById("addStockModal");
    const stockSpan = document.getElementById("closeStockAdd");

    addStockButton.onclick = async function() {
        if (selectedWarehouseId == undefined || selectedWarehouseId == 'Choose a warehouse') {
            alert("Please select a warehouse");
        } else {
            populateProductSelector();
            document.getElementById('warehouse_id').value = selectedWarehouseId;
            addStockSubmitEvent();
            stockModal.style.display = "block";
        }
    }

    stockSpan.onclick = function() {
        stockModal.style.display = "none";
    }

    // Fenêtre modale de vue des détails du produit
    const productModal = document.getElementById("productDetailModal");
    const productSpan = document.getElementById("closeProductButton");

    productSpan.onclick = function() {
        productModal.style.display = "none";
    }

    // Fenêtre modale de retrait de stock
    const withdrawStockButton = document.getElementById("withdrawStockButton");
    const withdrawModal = document.getElementById("withdrawStockModal");
    const closeWithdrawModal = document.getElementById("closeWithdrawModal");

    withdrawStockButton.onclick = async function() {
        const selectedRadio = document.querySelector('input[name="selectedStock"]:checked');

        if (!selectedRadio) {
            alert("Veuillez sélectionner un stock à retirer.");
        } else {
            var selectedStockId = selectedRadio.value;
            // Récupérer les détails du stock sélectionné
            // Afficher la fenêtre modale
            withdrawModal.style.display = "block";

            const stock = await getStock(selectedStockId);

            if (stock) {
                // Utiliser le volume par unité à partir de la variable globale
                const volumePerUnit = productsVolume[stock.product_id] || 0;

                // Calculer le volume actuel du stock
                const currentVolume = stock.quantity * volumePerUnit;

                // Récupérer la capacité de l'entrepôt
                const warehouseCapacity = await getWarehouseCapacity(stock.warehouse_id);

                // Remplir les champs de la modale avec les informations pertinentes
                document.getElementById('currentStockVolume').value = currentVolume;
                document.getElementById('warehouseVolume').value = warehouseCapacity?.total_capacity || 'Capacité inconnue';

                // Réinitialiser les autres champs
                document.getElementById('withdrawQuantity').value = '';
                document.getElementById('withdrawVolume').value = '';
                document.getElementById('postWithdrawStockVolume').value = '';

                // Écouter les changements dans la quantité pour mettre à jour les autres champs
                document.getElementById('withdrawQuantity').addEventListener('input', function() {
                    const withdrawQuantity = parseInt(this.value, 10);

                    // Calculer le volume à retirer
                    const withdrawVolume = withdrawQuantity * volumePerUnit;
                    const postWithdrawVolume = currentVolume - withdrawVolume;

                    document.getElementById('withdrawVolume').value = withdrawVolume;
                    document.getElementById('postWithdrawStockVolume').value = postWithdrawVolume < 0 ? 0 : postWithdrawVolume;
                });
            } else {
                alert("Stock non trouvé.");
            }
        }
    };

    closeWithdrawModal.onclick = function() {
        withdrawModal.style.display = "none";
    };

    // Ajoute un écouteur d'événement au bouton de confirmation dans la fenêtre de retrait
    const confirmWithdrawButton = document.getElementById('confirmWithdrawButton');
    confirmWithdrawButton.onclick = async function() {
        const withdrawQuantity = parseInt(document.getElementById('withdrawQuantity').value, 10);
        if (isNaN(withdrawQuantity) || withdrawQuantity <= 0) {
            alert("Veuillez entrer une quantité valide à retirer.");
            return;
        }

        const selectedStockId = getSelectedStockId();

        if (selectedStockId) {
            await applyWithdraw(selectedStockId, withdrawQuantity);
            withdrawModal.style.display = 'none'; // Fermer la modale après confirmation
        }
    };
});