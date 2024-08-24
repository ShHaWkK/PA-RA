import {getDeliveriesByDestination, getRouteDestinations} from "../../api/Distributions.js";
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
        modalContent.innerHTML= `<h2> Destinations </h2>`

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
                <strong>Warehouse:</strong> ${element.warehouse_name ? element.warehouse_name : 'None'}<br>
                <strong>Products:</strong> 
            `;

            // Créer un bouton "Voir" pour les produits
            const viewProductsButton = document.createElement('button');
            viewProductsButton.textContent = 'Voir Produits';
            viewProductsButton.classList.add('view-products-button');
            viewProductsButton.setAttribute('data-destination-id', element.id);
            viewProductsButton.addEventListener('click', (e) => {
                e.preventDefault();
                populateDeliveriesModal(element.id);
                document.getElementById('deliveriesDetailsModal').style.display = 'block';
            });

            // Ajouter le radio-button, les informations de la destination, et le bouton "Voir" à l'élément de la liste
            destinationItem.appendChild(radioInput);
            destinationItem.appendChild(destinationInfo);
            destinationItem.appendChild(viewProductsButton);

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

export async function populateDeliveriesModal(destinationID) {
    // Afficher le loader
    document.getElementById('loadingDeliveriesDetails').classList.remove('hidden');

    const modalBody = document.getElementById('modalBodyDeliveriesDetails');
    // Vider le contenu précédent du corps de la modale
    modalBody.innerHTML = '';

    try {
        // Récupérer les livraisons de la destination
        const deliveries = await getDeliveriesByDestination(destinationID);

        console.log(deliveries);

        // Sélectionner le conteneur de la fenêtre modale
        const modalContent = document.getElementById('modalBodyDeliveriesDetails');
        if (!modalContent) {
            console.error('Deliveries modal content container not found.');
            return;
        }

        // Effacer le contenu existant du modal
        modalContent.innerHTML = '';

        if (!deliveries || deliveries.length === 0) {
            modalContent.textContent = 'No deliveries found for this destination.';
            return;
        }

        // Créer une liste pour afficher les livraisons
        const deliveryList = document.createElement('ul');
        deliveryList.classList.add('delivery-list'); // Ajout d'une classe pour le style, si nécessaire

        modalContent.innerHTML= `<h2> Produits livrés à la destination </h2>`

        // Parcourir les livraisons et les ajouter à la liste
        deliveries.forEach(delivery => {
            const deliveryItem = document.createElement('li');
            deliveryItem.classList.add('delivery-item'); // Ajout d'une classe pour le style, si nécessaire

            // Contenu de la livraison
            const deliveryInfo = document.createElement('span');
            deliveryInfo.innerHTML = `
                <strong>Product:</strong> ${delivery.product ? delivery.product.name : 'Unknown'} <br>
                <strong>Barcode:</strong> ${delivery.product ? delivery.product.barcode : 'N/A'} <br>
                <strong>Quantity:</strong> ${delivery.quantity} <br>
                <strong>Status:</strong> ${delivery.status} <br>
                <strong>Comment:</strong> ${delivery.comment ? delivery.comment : 'None'} <br>
                <strong>Created At:</strong> ${formatDateToFrench(new Date(delivery.created_at).getTime())} <br>
                <strong>Updated At:</strong> ${formatDateToFrench(new Date(delivery.updated_at).getTime())}
            `;

            // Ajouter les informations de la livraison à l'élément de la liste
            deliveryItem.appendChild(deliveryInfo);

            // Ajouter l'élément à la liste
            deliveryList.appendChild(deliveryItem);
        });

        // Ajouter la liste des livraisons au modal
        modalContent.appendChild(deliveryList);

        // Afficher la fenêtre modale
        document.getElementById('deliveriesDetailsModal').style.display = 'block';

    } catch (error) {
        console.error('Error populating deliveries modal:', error.message);

        const modalContent = document.getElementById('modalBodyDeliveriesDetails');
        if (modalContent) {
            modalContent.textContent = 'No deliveries found for this destination.';
        }

    } finally {
        // Cacher le loader
        document.getElementById('loadingDeliveriesDetails').classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', async function() {
    const destinationsModal = document.getElementById("destinationsDetailsModal");
    const destinationsSpan = document.getElementById("closeRouteDestinationsButton");

    destinationsSpan.onclick = function () {
        destinationsModal.style.display = "none";
    }

    const deliveriesModal = document.getElementById("deliveriesDetailsModal");
    const deliveriesSpan = document.getElementById("closeRouteDeliveriesButton");

    deliveriesSpan.onclick = function () {
        deliveriesModal.style.display = "none";
    }
});