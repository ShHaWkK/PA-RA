import {populateWarehouseTable} from "../modules/tables/WarehouseTable.js";
import {createWarehouse, deleteWarehouse, getWarehouse, updateWarehouse} from "../api/Warehouse.js";

async function deleteWarehouses() {
    const selectedRadio = document.querySelector('input[name="selectedWarehouse"]:checked');
    const selectedWarehouseId = selectedRadio ? selectedRadio.value : null;

    if (!selectedWarehouseId) {
        alert("Veuillez sélectionner au moins une entrepot à supprimer.");
        return;
    }

    // Afficher la boîte de confirmation
    const isConfirmed = confirm("Etes-vous sûr de vouloir supprimer cette entrepot ? Cette action est définitive.");

    if (!isConfirmed) {
        return;
    }

    try {
        await deleteWarehouse(selectedWarehouseId);
        alert("Entrepot supprimée avec succès");

        await populateWarehouseTable();
    } catch (error) {
        console.error("Error deleting the warehouse:", error.message);
        alert("Une erreur est survenue lors de la suppression de l'entrepot.");
    }
}

async function populateEditWarehouseModalData(warehouseId) {
    const formElement = document.getElementById('editWarehouseForm');
    const loadingIndicator = document.getElementById('loadingEditWarehouse');

    // Show loading indicator and hide form
    formElement.classList.add('hidden');
    loadingIndicator.classList.remove('hidden');

    try {
        // Fetch warehouse data by ID
        const warehouseData = await getWarehouse(warehouseId);
        if (!warehouseData) {
            throw new Error('Warehouse data not found');
        }

        // Populate the form fields with the warehouse data
        document.getElementById('warehouseId').value = warehouseData.id ?? '';
        document.getElementById('editNameInput').value = warehouseData.name ?? '';
        document.getElementById('editAddressInput').value = warehouseData.address ?? '';
        document.getElementById('editContactInfoInput').value = warehouseData.contact_info ?? '';
        document.getElementById('editCapacityInput').value = warehouseData.capacity ?? '';
        document.getElementById('editCityInput').value = warehouseData.city ?? '';
        document.getElementById('editCountryInput').value = warehouseData.country ?? '';

    } catch (error) {
        console.error('Error populating warehouse data:', error.message);
        alert('Une erreur est survenue lors du chargement des données de l\'entrepôt. Veuillez réessayer plus tard.');
    } finally {
        // Hide loading indicator and show form
        loadingIndicator.classList.add('hidden');
        formElement.classList.remove('hidden');
    }
}

async function editWarehouse(warehouseId) {
    const formElement = document.getElementById('editWarehouseForm');
    const loadingIndicator = document.getElementById('loadingEditWarehouse');
    const modalElement = document.getElementById('editWarehouseModal');

    // Afficher le loader et masquer le formulaire
    loadingIndicator.classList.remove('hidden');
    formElement.classList.add('hidden');

    // Récupérer les valeurs du formulaire
    const name = document.getElementById('editNameInput').value.trim();
    const address = document.getElementById('editAddressInput').value.trim();
    const contactInfo = document.getElementById('editContactInfoInput').value.trim();
    const capacity = parseInt(document.getElementById('editCapacityInput').value, 10);
    const city = document.getElementById('editCityInput').value.trim();
    const country = document.getElementById('editCountryInput').value.trim();

    // Valider les données avant l'envoi
    if (!name || !address) {
        alert('Le nom et l\'adresse de l\'entrepôt sont requis.');
        loadingIndicator.classList.add('hidden');
        formElement.classList.remove('hidden');
        return;
    }

    const warehouseData = {
        name,
        address,
        contact_info: contactInfo,
        capacity: isNaN(capacity) ? null : capacity, // Utiliser null si capacity n'est pas un nombre valide
        city,
        country
    };

    try {
        // Appeler la fonction pour mettre à jour l'entrepôt avec les nouvelles données
        const result = await updateWarehouse(warehouseId, warehouseData);

        alert("Entrepôt modifié avec succès");
        modalElement.style.display = 'none';
        await populateWarehouseTable(); // Met à jour la liste des entrepôts

    } catch (error) {
        console.error('Erreur lors de la modification de l\'entrepôt:', error);
        alert('Erreur lors de la modification de l\'entrepôt. Veuillez réessayer.');
    } finally {
        // Cacher le loader
        loadingIndicator.classList.add('hidden');
        formElement.classList.remove('hidden');
    }
}

async function addWarehouse() {
    const formElement = document.getElementById('addWarehouseForm');
    const loadingIndicator = document.getElementById('loadingAddWarehouse');
    const modalElement = document.getElementById('addWarehouseModal');

    // Afficher le loader et masquer le formulaire
    loadingIndicator.classList.remove('hidden');
    formElement.classList.add('hidden');

    // Récupérer les valeurs du formulaire
    const name = document.getElementById('addNameInput').value.trim();
    const address = document.getElementById('addAddressInput').value.trim();
    const contactInfo = document.getElementById('addContactInfoInput').value.trim();
    const capacity = parseInt(document.getElementById('addCapacityInput').value, 10);
    const city = document.getElementById('addCityInput').value.trim();
    const country = document.getElementById('addCountryInput').value.trim();

    // Valider les données avant l'envoi
    if (!name || !address) {
        alert('Le nom et l\'adresse de l\'entrepôt sont requis.');
        loadingIndicator.classList.add('hidden');
        formElement.classList.remove('hidden');
        return;
    }

    const warehouseData = {
        name,
        address,
        contact_info: contactInfo,
        capacity: isNaN(capacity) ? null : capacity, // Utiliser null si capacity n'est pas un nombre valide
        city,
        country
    };

    try {
        // Appeler la fonction pour ajouter un nouvel entrepôt avec les données fournies
        const result = await createWarehouse(warehouseData);

        alert("Entrepôt ajouté avec succès");
        modalElement.style.display = 'none';
        await populateWarehouseTable(); // Met à jour la liste des entrepôts

    } catch (error) {
        console.error('Erreur lors de l\'ajout de l\'entrepôt :', error);
        alert('Erreur lors de l\'ajout de l\'entrepôt. Veuillez réessayer.');
    } finally {
        // Cacher le loader et afficher le formulaire
        loadingIndicator.classList.add('hidden');
        formElement.classList.remove('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    populateWarehouseTable();
    document.getElementById('deleteWarehouseButton').addEventListener('click', deleteWarehouses);

    // Fenêtre de modification de warehouse
    const editWarehouseModal = document.getElementById("editWarehouseModal");
    const editWarehouseSpan = document.getElementById("closeEditWarehouseModal");
    const editWarehouseForm = document.getElementById("editWarehouseForm");
    const editWarehouseButton = document.getElementById("modifyWarehouseButton");

    editWarehouseButton.onclick = function () {
        // Récupérer l'ID de la warehouse depuis le checkbox sélectionné
        const selectedCheckbox = document.querySelector('input[name="selectedWarehouse"]:checked');
        const selectedWarehouseID = selectedCheckbox ? selectedCheckbox.value : null;

        if (!selectedWarehouseID) {
            alert("Veuillez sélectionner un entrepot.");
            return;
        }

        document.getElementById("editWarehouseModal").style.display = "block"
        populateEditWarehouseModalData(selectedWarehouseID);
    }

    editWarehouseSpan.onclick = function () {
        populateEditWarehouseModalData();
    };

    editWarehouseSpan.onclick = function () {
        editWarehouseModal.style.display = "none";
    };

    editWarehouseForm.onsubmit = async function (event) {
        event.preventDefault();

        // Récupérer l'ID de la warehouse depuis le checkbox sélectionné
        const selectedCheckbox = document.querySelector('input[name="selectedWarehouse"]:checked');
        const selectedWarehouseID = selectedCheckbox ? selectedCheckbox.value : null;

        if (!selectedWarehouseID) {
            alert("Veuillez sélectionner un entepot.");
            return;
        }

        await editWarehouse(selectedWarehouseID);
    };

    // Fenêtre d'ajout de l'entrepôt
    const addWarehouseModal = document.getElementById("addWarehouseModal");
    const addWarehouseSpan = document.getElementById("closeAddWarehouseModal");
    const addWarehouseForm = document.getElementById("addWarehouseForm");
    const addWarehouseButton = document.getElementById("addWarehouseButton");

    addWarehouseButton.onclick = function () {
        addWarehouseModal.style.display = "block";
    };

    addWarehouseSpan.onclick = function () {
        addWarehouseModal.style.display = "none";
    };

    addWarehouseForm.onsubmit = async function (event) {
        event.preventDefault();
        await addWarehouse();
    };


})