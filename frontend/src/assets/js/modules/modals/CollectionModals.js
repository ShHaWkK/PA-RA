import {getUser} from "../../api/Users.js";
import {getVehicleByID} from "../../api/Vehicle.js";
import {getProductsFromCollection} from "../../api/Collections.js"
import {formatDateToFrench} from "../FormatDate.js";

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

export async function populateCollectedProductsModal(CollectionID) {
    // Afficher le loader
    document.getElementById('loadingCollectedProductsDetails').classList.remove('hidden');

    const modalBody = document.getElementById('modalBodyCollectedProductsDetails');
    // Vider le contenu précédent du corps de la modale
    modalBody.innerHTML = '';

    try {
        // Récupérer les produits de la collection
        const products = await getProductsFromCollection(CollectionID);

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

            // Contenu du produit
            const productInfo = `
                <strong>Product Name:</strong> ${element.product.name} <br>
                <strong>Barcode:</strong> ${element.product.barcode} <br>
                <strong>Expiration Date:</strong> ${formatDateToFrench(new Date(element.product.expiration_date).getTime())} <br>
                <strong>Volume:</strong> ${element.product.volume} L <br>
                <strong>Quantity Collected:</strong> ${element.quantity_collected} <br>
                <strong>Scanned:</strong> ${element.product.scanned ? 'Yes' : 'No'}
                <br>
            `;
            productItem.innerHTML = productInfo;

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
});
