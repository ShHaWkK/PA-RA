import {getRouteDestinations} from "../../api/Distributions.js";
import {formatDateToFrench} from "../FormatDate.js";

export async function populateDestinationsModal(routeID) {
    // Afficher le loader
    document.getElementById('loadingDestinationsDetails').classList.remove('hidden');

    const modalBody = document.getElementById('modalBodyDestinationsDetails');
    // Vider le contenu précédent du corps de la modale
    modalBody.innerHTML = '';

    try {
        // Récupérer les destinations de la route
        const destinations = await getRouteDestinations(routeID);

        console.log(destinations);

        // Sélectionner le conteneur de la fenêtre modale
        const modalContent = document.getElementById('modalBodyDestinationsDetails');
        if (!modalContent) {
            console.error('Destinations modal content container not found.');
            return;
        }

        // Effacer le contenu existant du modal
        modalContent.innerHTML = '';

        if (!destinations || destinations.length === 0) {
            modalContent.textContent = 'No destinations found for this route.';
            return;
        }

        // Créer une liste pour afficher les destinations
        const destinationList = document.createElement('ul');
        destinationList.classList.add('destination-list'); // Ajout d'une classe pour le style, si nécessaire

        // Parcourir les destinations et les ajouter à la liste
        destinations.forEach(element => {
            const destinationItem = document.createElement('li');
            destinationItem.classList.add('destination-item'); // Ajout d'une classe pour le style, si nécessaire

            // Créer un radio-button
            const radioInput = document.createElement('input');
            radioInput.type = 'radio';
            radioInput.name = 'destinationSelection'; // Tous les radio-buttons partagent le même nom pour permettre une sélection unique
            radioInput.value = element.id; // Attribuer l'ID de la destination comme valeur du radio-button

            // Contenu de la destination
            const destinationInfo = document.createElement('span');
            destinationInfo.innerHTML = `
                <strong>Address:</strong> ${element.address} <br>
                <strong>Recipient Type:</strong> ${element.recipient_type} <br>
                <strong>Delivery Date:</strong> ${formatDateToFrench(new Date(element.delivery_date).getTime())} <br>
                <strong>Status:</strong> ${element.status} <br>
                <strong>Comment:</strong> ${element.comment ? element.comment : 'None'} <br>
                <strong>Warehouse:</strong> ${element.warehouse_name ? element.warehouse_name : 'None'}
            `;

            // Ajouter le radio-button et les informations de la destination à l'élément de la liste
            destinationItem.appendChild(radioInput);
            destinationItem.appendChild(destinationInfo);

            // Ajouter l'élément à la liste
            destinationList.appendChild(destinationItem);
        });

        // Ajouter la liste des destinations au modal
        modalContent.appendChild(destinationList);

        // Afficher la fenêtre modale
        document.getElementById('destinationsDetailsModal').style.display = 'block';

    } catch (error) {
        console.error('Error populating destinations modal:', error.message);

        const modalContent = document.getElementById('modalBodyDestinationsDetails');
        if (modalContent) {
            modalContent.textContent = 'No destinations found for this route.';
        }

    } finally {
        // Cacher le loader
        document.getElementById('loadingDestinationsDetails').classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', async function() {
    const destinationsModal = document.getElementById("volunteerDetailsModal");
    const destinationsSpan = document.getElementById("closeVolunteerDetailsButton");

    destinationsSpan.onclick = function () {
        destinationsModal.style.display = "none";
    }
});