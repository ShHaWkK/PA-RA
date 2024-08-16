import {getAllUsers, getUser} from "../../api/Users.js";
import {getAllVehicles, getVehicleByID} from "../../api/Vehicle.js";
import {
    getCollectionByID,
    getProductsFromCollection,
    removeProductsFromCollection,
    updateCollection
} from "../../api/Collections.js"
import {formatDateToFrench} from "../FormatDate.js";
import {populateCollectionTable} from "../tables/CollectionTable.js";

let selectedCollectionId;

export async function populateVolunteerDetailsInModal(volunteerID) {
    console.log("we are here", volunteerID);

    const modalBody = document.getElementById('modalBodyVolunteerDetails');

    // Vider le contenu précédent du corps de la modale
    modalBody.innerHTML = '';
    console.log("Selected volunteer ID", volunteerID);

    document.getElementById('loadingVolunteerDetails').classList.remove('hidden');

    try {
        // Obtenir les détails du volontaire
        const volunteer = await getUser(volunteerID);
        console.log(volunteer);

        if (volunteer) {
            // Créer des éléments label et les champs associés
            const firstNameLabel = document.createElement('h3');
            firstNameLabel.textContent = 'First Name:';
            const volunteerFirstName = document.createElement('p');
            volunteerFirstName.textContent = volunteer.first_name;

            const lastNameLabel = document.createElement('h3');
            lastNameLabel.textContent = 'Last Name:';
            const volunteerLastName = document.createElement('p');
            volunteerLastName.textContent = volunteer.last_name;

            const phoneLabel = document.createElement('h3');
            phoneLabel.textContent = 'Phone Number:';
            const volunteerPhoneNumber = document.createElement('p');
            volunteerPhoneNumber.textContent = volunteer.phone_number;

            const emailLabel = document.createElement('h3');
            emailLabel.textContent = 'Email:';
            const volunteerEmail = document.createElement('p');
            volunteerEmail.textContent = volunteer.email;

            // Ajouter les labels et les champs associés au corps de la modale
            modalBody.appendChild(firstNameLabel);
            modalBody.appendChild(volunteerFirstName);

            modalBody.appendChild(lastNameLabel);
            modalBody.appendChild(volunteerLastName);

            modalBody.appendChild(phoneLabel);
            modalBody.appendChild(volunteerPhoneNumber);

            modalBody.appendChild(emailLabel);
            modalBody.appendChild(volunteerEmail);
        } else {
            // Afficher le message "No volunteer details found"
            const noVolunteerMessage = document.createElement('p');
            noVolunteerMessage.textContent = 'No volunteer details found.';
            modalBody.appendChild(noVolunteerMessage);
        }
    } catch (error) {
        console.error('Error fetching volunteer details:', error);
        const errorMessage = document.createElement('p');
        errorMessage.textContent = 'Error fetching volunteer details.';
        modalBody.appendChild(errorMessage);
    } finally {
        document.getElementById('loadingVolunteerDetails').classList.add('hidden');
    }
}

export async function populateVehicleDetailsInModal(vehicleID) {
    const modalBody = document.getElementById('modalBodyVehicleDetails');

    // Vider le contenu précédent du corps de la modale
    modalBody.innerHTML = '';
    console.log("Selected vehicle ID", vehicleID);

    document.getElementById('loadingVehicleDetails').classList.remove('hidden');

    try {
        // Obtenir les détails du volontaire
        const vehicle = await getVehicleByID(vehicleID);
        console.log(vehicle);

        if (vehicle) {
            // Créer des éléments label et les champs associés
            const vehicleBrandLabel = document.createElement('h3');
            vehicleBrandLabel.textContent = 'Brand:';
            const vehicleBrand = document.createElement('p');
            vehicleBrand.textContent = vehicle.brand;

            const vehicleModelLabel = document.createElement('h3');
            vehicleModelLabel.textContent = 'Model:';
            const vehicleModel = document.createElement('p');
            vehicleModel.textContent = vehicle.model;

            const licensePlateLabel = document.createElement('h3');
            licensePlateLabel.textContent = 'License plate:';
            const licensePlate = document.createElement('p');
            licensePlate.textContent = vehicle.licensePlate;

            const statusLabel = document.createElement('h3');
            statusLabel.textContent = 'Status:';
            const vehicleStatus = document.createElement('p');
            vehicleStatus.textContent = vehicle.status;

            const currentLocationLabel = document.createElement('h3');
            currentLocationLabel.textContent = 'Current Location:';
            const currentLocation = document.createElement('p');
            currentLocation.textContent = vehicle.currentLocation;

            // Ajouter les labels et les champs associés au corps de la modale
            modalBody.appendChild(vehicleBrandLabel);
            modalBody.appendChild(vehicleBrand);

            modalBody.appendChild(vehicleModelLabel);
            modalBody.appendChild(vehicleModel);

            modalBody.appendChild(licensePlateLabel);
            modalBody.appendChild(licensePlate);

            modalBody.appendChild(statusLabel);
            modalBody.appendChild(vehicleStatus);

            modalBody.appendChild(currentLocationLabel);
            modalBody.appendChild(currentLocation);
        } else {
            // Afficher le message "No vehicle details found"
            const noVehicleMessage = document.createElement('p');
            noVehicleMessage.textContent = 'No vehicle details found.';
            modalBody.appendChild(noVehicleMessage);
        }
    } catch (error) {
        console.error('Error fetching vehicle details:', error);
        const errorMessage = document.createElement('p');
        errorMessage.textContent = 'Error fetching vehicle details.';
        modalBody.appendChild(errorMessage);
    } finally {
        document.getElementById('loadingVehicleDetails').classList.add('hidden');
    }
}

export async function populateCollectedProductsModal(collectionID) {
    selectedCollectionId = collectionID;

    // Afficher le loader
    document.getElementById('loadingCollectedProductsDetails').classList.remove('hidden');

    const modalBody = document.getElementById('modalBodyCollectedProductsDetails');
    // Vider le contenu précédent du corps de la modale
    modalBody.innerHTML = '';

    try {
        // Récupérer les produits de la collection
        const products = await getProductsFromCollection(collectionID);

        console.log(products);

        // Sélectionner le conteneur de la fenêtre modale
        const modalContent = document.getElementById('modalBodyCollectedProductsDetails');
        if (!modalContent) {
            console.error('Products modal content container not found.');
            return;
        }

        // Effacer le contenu existant du modal
        modalContent.innerHTML = '';

        if (!products || products.length === 0) {
            modalContent.textContent = 'No products found for this collection.';
            return;
        }

        // Créer une liste pour afficher les produits
        const productList = document.createElement('ul');
        productList.classList.add('product-list'); // Ajout d'une classe pour le style, si nécessaire

        // Parcourir les produits et les ajouter à la liste
        products.forEach(element => {
            const productItem = document.createElement('li');
            productItem.classList.add('product-item'); // Ajout d'une classe pour le style, si nécessaire

            // Créer un radio-button
            const radioInput = document.createElement('input');
            radioInput.type = 'radio';
            radioInput.name = 'productSelection'; // Tous les radio-buttons partagent le même nom pour permettre une sélection unique
            radioInput.value = element.product.id; // Attribuer l'ID du produit comme valeur du radio-button

            // Contenu du produit
            const productInfo = document.createElement('span');
            productInfo.innerHTML = `
                <strong>Product Name:</strong> ${element.product.name} <br>
                <strong>Barcode:</strong> ${element.product.barcode} <br>
                <strong>Expiration Date:</strong> ${formatDateToFrench(new Date(element.product.expiration_date).getTime())} <br>
                <strong>Volume:</strong> ${element.product.volume} L <br>
                <strong>Quantity Collected:</strong> ${element.quantity_collected} <br>
                <strong>Scanned:</strong> ${element.product.scanned ? 'Yes' : 'No'}
            `;

            // Ajouter le radio-button et les informations du produit à l'élément de la liste
            productItem.appendChild(radioInput);
            productItem.appendChild(productInfo);

            // Ajouter l'élément à la liste
            productList.appendChild(productItem);
        });

        // Ajouter la liste des produits au modal
        modalContent.appendChild(productList);

        // Afficher la fenêtre modale
        document.getElementById('collectedProductsDetailsModal').style.display = 'block';

    } catch (error) {
        console.error('Error populating products modal:', error.message);

        const modalContent = document.getElementById('modalBodyCollectedProductsDetails');
        if (modalContent) {
            modalContent.textContent = 'No products affected to this collection.';
        }

    } finally {
        // Cacher le loader
        document.getElementById('loadingCollectedProductsDetails').classList.add('hidden');
    }
}

async function removeProducts() {
    const selectedProductCheckboxes = document.querySelectorAll('input[name="productSelection"]:checked');
    const selectedProductIds = Array.from(selectedProductCheckboxes).map(checkbox => parseInt(checkbox.value, 10)); // Conversion en entier

    if (!selectedCollectionId) {
        alert("Veuillez sélectionner une collection.");
        return;
    }

    if (selectedProductIds.length === 0) {
        alert("Veuillez sélectionner au moins un produit à supprimer.");
        return;
    }

    // Afficher la boîte de confirmation
    const isConfirmed = confirm("Êtes-vous sûr de vouloir supprimer ces produits de la collection ? Cette action est définitive.");

    if (!isConfirmed) {
        return;
    }

    try {
        const response = await removeProductsFromCollection(selectedCollectionId, selectedProductIds);
        if (response.error) {
            alert(`Erreur: ${response.error}`);
        } else {
            alert("Produits retirés de la collection avec succès.");
            await populateCollectedProductsModal();
        }
    } catch (error) {
        console.error("Erreur lors de la suppression des produits:", error.message);
        alert("Une erreur est survenue lors de la suppression des produits.");
    }
}

// Fenêtre de modification de produit:
async function populateModifyCollectionForm() {
    const selectedRadio = document.querySelector('input[name="collectionSelection"]:checked');
    const selectedCollectionId = selectedRadio ? selectedRadio.value : null;

    try {
        if (!selectedCollectionId) {
            alert("Veuillez sélectionner une collection.");
            return;
        }

        document.getElementById("editCollectionModal").style.display = "block";

        // Masquer le formulaire et afficher le loader
        document.getElementById('editCollectionForm').classList.add('hidden');
        document.getElementById('loadingModifyCollection').classList.remove('hidden');

        // Récupérer les données de la collecte
        const collectionData = await getCollectionByID(selectedCollectionId);

        // Peupler les sélecteurs (volunteers et vehicles)
        await populateVolunteerSelector();
        await populateVehicleSelector();

        // Pré-remplir les champs du formulaire avec les données existantes de la collecte
        document.getElementById('volunteerSelect').value = collectionData.volunteer_id || '';
        document.getElementById('vehicleSelect').value = collectionData.vehicle_id || '';
        document.getElementById('completionCheckbox').checked = collectionData.is_completed;

        // Masquer le loader et afficher le formulaire
        document.getElementById('loadingModifyCollection').classList.add('hidden');
        document.getElementById('editCollectionForm').classList.remove('hidden');
    } catch (error) {
        console.error('Erreur lors du pré-remplissage du formulaire de modification de la collecte:', error);
    }
}

async function populateVolunteerSelector() {
    try {
        const volunteerSelect = document.getElementById('volunteerSelect');
        volunteerSelect.innerHTML = ''; // Vider les options existantes

        const volunteers = await getAllUsers('volunteer','approved');

        console.log("volunteers",volunteers);

        volunteers.forEach(volunteer => {
            const option = document.createElement('option');
            option.value = volunteer.id;
            option.textContent = `${volunteer.first_name} ${volunteer.last_name}`;
            volunteerSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Erreur lors du peuplement du sélecteur de chauffeurs:', error);
    }
}

async function populateVehicleSelector() {
    try {
        const vehicleSelect = document.getElementById('vehicleSelect');
        vehicleSelect.innerHTML = ''; // Vider les options existantes

        const vehicles = await getAllVehicles(); // Fonction pour récupérer les véhicules depuis l'API

        console.log("vehicles",vehicles);

        vehicles.forEach(vehicle => {
            const option = document.createElement('option');
            option.value = vehicle.id;
            option.textContent = vehicle.licensePlate;
            vehicleSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Erreur lors du peuplement du sélecteur de véhicules:', error);
    }
}

async function modifyCollection(){
    const selectedRadio = document.querySelector('input[name="collectionSelection"]:checked');
    const selectedCollectionId = selectedRadio ? selectedRadio.value : null;
    // Afficher le loader
    document.getElementById('loadingModifyCollection').classList.remove('hidden');
    document.getElementById('editCollectionForm').classList.add('hidden');


    // Récupérer les valeurs des champs du formulaire
    const collectionId = selectedCollectionId;
    const volunteerId = document.getElementById('volunteerSelect').value;
    const vehicleId = document.getElementById('vehicleSelect').value;
    const isCompleted = document.getElementById('completionCheckbox').checked;

    // Créer un objet contenant les données de la collecte
    const collectionData = {
        volunteer_id: volunteerId,
        vehicle_id: vehicleId,
        is_completed: isCompleted
    };

    try {
        // Appeler la fonction pour mettre à jour la collecte avec les nouvelles données
        const result = await updateCollection(collectionId, collectionData);

        // Vérifier le résultat et agir en conséquence
        if (result && result.message) {
            alert(result.message);
            document.getElementById('editCollectionModal').style.display = 'none';
            await populateCollectionTable();
        }
    } catch (error) {
        console.error('Erreur lors de la modification de la collecte:', error.message);
        alert('Erreur lors de la modification de la collecte. Veuillez réessayer.');
    } finally {
        document.getElementById('loadingModifyCollection').classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', async function() {
    // Fenêtre modale de détails des volontaires
    const volunteerDetailsModal = document.getElementById("volunteerDetailsModal");
    const volunteerDetailsSpan = document.getElementById("closeVolunteerDetailsButton");

    volunteerDetailsSpan.onclick = function() {
        volunteerDetailsModal.style.display = "none";
    }

    // Fenêtre modale de détails des véhicules
    const vehicleDetailsModal = document.getElementById("vehicleDetailsModal");
    const vehicleDetailsSpan = document.getElementById("closeVehicleDetailsButton");

    vehicleDetailsSpan.onclick = function() {
        vehicleDetailsModal.style.display = "none";
    }

    // Fenêtre modale de détails des produits
    const productDetailsModal = document.getElementById("collectedProductsDetailsModal");
    const productDetailsSpan = document.getElementById("closeCollectedProductsDetailsButton");

    productDetailsSpan.onclick = function() {
        productDetailsModal.style.display = "none";
    }

    // Fenêtre modale de modification des collectes
    const editCollectionModal = document.getElementById("editCollectionModal");
    const editCollectionForm = document.getElementById('editCollectionForm');
    const modifyCollectionButton = document.getElementById("modifyCollectionButton");
    const closeEditModal = document.getElementById("closeEditModal");

    modifyCollectionButton.addEventListener('click',function (){
        populateModifyCollectionForm(selectedCollectionId);
    })

    closeEditModal.onclick = function() {
        editCollectionModal.style.display = "none";
    }

    editCollectionForm.addEventListener('submit', function(event) {
        event.preventDefault();
        modifyCollection();
    });

    // Ajouter l'event listener au bouton de suppression de produit
    document.getElementById('deleteProductInModalButton').addEventListener('click',function (){
        removeProducts();
    })
});
