import {
    addDestinationToRoute,
    getDeliveriesByDestination, getDestinationById,
    getRouteById,
    getRouteDestinations, updateDestinationAndDeliveries,
    updateRoute
} from "../../api/Distributions.js";
import {formatDateToFrench} from "../FormatDate.js";
import {getAllVehicles} from "../../api/Vehicle.js";
import {getAllUsers} from "../../api/Users.js";
import {populateRouteTable, selectedRouteId} from "../tables/RouteTable.js";
import {getAllProducts} from "../../api/Products.js";
import {populateWarehouseSelector} from "../../pages/AdminStockPage.js"

export let selectedDestinationId;

export async function populateDestinationsModal(routeID) {
    console.log("routeID",routeID);
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

        // Fonction pour mettre à jour selectedRouteId
        function updateSelectedDestinationId() {
            const selectedRadio = document.querySelector('input[name="destinationSelection"]:checked');
            if (selectedRadio) {
                selectedDestinationId = selectedRadio.value;
                console.log('Modals : Selected Route ID:', selectedRouteId);
                console.log('Modals : Selected Destination ID:', selectedDestinationId);
            }
        }

        // Ajouter un écouteur d'événement à chaque bouton radio
        const radioButtons = document.querySelectorAll('input[name="destinationSelection"]');
        radioButtons.forEach(radio => {
            radio.addEventListener('change', updateSelectedDestinationId);
        });


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

async function populateEditRouteForm() {
    const selectedRadio = document.querySelector('input[name="RouteSelection"]:checked');
    const selectedRouteId = selectedRadio ? selectedRadio.value : null;

    // Afficher la modale de modification
    const editRouteModal = document.getElementById("editRouteModal");
    editRouteModal.style.display = "block";

    // Masquer le formulaire et afficher le loader
    document.getElementById('editRouteForm').classList.add('hidden');
    document.getElementById('loadingEditRoute').classList.remove('hidden');

    try {
        // Récupérer les données de la route
        const routeData = await getRouteById(selectedRouteId);

        // Peupler les sélecteurs de véhicules et de chauffeurs
        await populateVehicleSelector();
        await populateVolunteerSelector();

        // Pré-remplir les champs du formulaire avec les données existantes de la route
        document.getElementById('routeNameInput').value = routeData.route.name || '';
        document.getElementById('vehicleSelect').value = routeData.route.vehicle.id || '';
        document.getElementById('driverSelect').value = routeData.route.driver.id || '';
        document.getElementById('statusSelect').value = routeData.route.status || '';

        // Masquer le loader et afficher le formulaire
        document.getElementById('loadingEditRoute').classList.add('hidden');
        document.getElementById('editRouteForm').classList.remove('hidden');
    } catch (error) {
        console.error('Erreur lors du pré-remplissage du formulaire de modification de la route:', error);
        document.getElementById('loadingEditRoute').classList.add('hidden');
        document.getElementById('editRouteForm').classList.remove('hidden');
    }
}

async function populateVehicleSelector() {
    try {

        console.log("we atre in populateVehicle");

        const vehicleSelect = document.getElementById('vehicleSelect');
        vehicleSelect.innerHTML = ''; // Vider les options existantes

        const vehicles = await getAllVehicles(); // Récupérer tous les véhicules depuis l'API

        vehicles.forEach(vehicle => {
            const option = document.createElement('option');
            option.value = vehicle.id;
            option.textContent = vehicle.licensePlate; // Afficher la plaque d'immatriculation du véhicule
            vehicleSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Erreur lors du peuplement du sélecteur de véhicules:', error);
    }
}

async function populateVolunteerSelector() {
    try {
        const volunteerSelect = document.getElementById('driverSelect');
        volunteerSelect.innerHTML = ''; // Vider les options existantes

        const volunteers = await getAllUsers('volunteer', 'approved'); // Récupérer les bénévoles depuis l'API

        volunteers.forEach(volunteer => {
            const option = document.createElement('option');
            option.value = volunteer.id;
            option.textContent = `${volunteer.first_name} ${volunteer.last_name}`; // Nom complet du bénévole
            volunteerSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Erreur lors du peuplement du sélecteur de bénévoles:', error);
    }
}

async function editRoute(routeID) {
    // Afficher le loader et masquer le formulaire
    document.getElementById('loadingEditRoute').classList.remove('hidden');
    document.getElementById('editRouteForm').classList.add('hidden');

    // Récupérer les valeurs du formulaire
    const name = document.getElementById('routeNameInput').value;
    const vehicleId = parseInt(document.getElementById('vehicleSelect').value);
    const driverId = parseInt(document.getElementById('driverSelect').value);
    const status = document.getElementById('statusSelect').value;

    // Créer un objet contenant les données de la route
    const routeData = {
        name,
        vehicle_id: vehicleId,
        driver_id: driverId,
        status
    };

    try {
        // Appeler la fonction pour mettre à jour la route avec les nouvelles données
        const result = await updateRoute(routeID, routeData);

        // Vérifier le résultat et agir en conséquence
        if (result && result.message) {
            alert("Route modifiée avec succès");
            document.getElementById('editRouteModal').style.display = 'none';
            await populateRouteTable();
        }
    } catch (error) {
        console.error('Erreur lors de la modification de la route:', error.message);
        alert('Erreur lors de la modification de la route. Veuillez réessayer.');
    } finally {
        // Cacher le loader
        document.getElementById('loadingEditRoute').classList.add('hidden');
    }
}

export async function populateProductSelector(selectorId) {
    try {
        // Récupérer tous les produits via une API ou une fonction dédiée
        const products = await getAllProducts();

        // Vider le sélecteur de produits spécifié
        const productSelector = document.getElementById(selectorId);
        productSelector.innerHTML = '';

        // Remplir le sélecteur de produits avec les données récupérées
        products.forEach(product => {
            const option = document.createElement('option');
            option.value = product.id;
            option.textContent = product.name;
            productSelector.appendChild(option);
        });

    } catch (error) {
        console.error(`Erreur lors de la récupération des produits pour ${selectorId}:`, error.message);
    }
}

async function populateDeliveryContainer(containerId){
    console.log("populate container : selectedRouteId",selectedRouteId);
    const deliveryContainer = document.getElementById(containerId);
    const deliveryItems = deliveryContainer.getElementsByClassName('delivery-item');
    const newIndex = deliveryItems.length;

    // Créer un nouvel élément de livraison
    const newDeliveryItem = document.createElement('div');
    newDeliveryItem.className = 'delivery-item';
    newDeliveryItem.innerHTML = `
        <h3>Produit ${newIndex}</h3>

        <div class="form-group">
            <label for="productSelect_${newIndex}">Produit :</label>
            <select name="product[]" id="productSelect_${newIndex}" required>
                <!-- Options à remplir dynamiquement -->
            </select>
        </div>
        <div class="form-group">
            <label for="quantity_${newIndex}">Quantité :</label>
            <input type="number" name="quantity[]" id="quantity_${newIndex}" min="1" required>
        </div>
        <div class="form-group">
            <label for="status_${newIndex}">Statut :</label>
            <select name="status[]" id="status_${newIndex}" required>
                <option value="pending">En attente</option>
                <option value="delivered">Livré</option>
            </select>
        </div>
    `;
    // Ajouter le nouvel élément au container
    deliveryContainer.appendChild(newDeliveryItem);

    // Appeler populateProductSelector avec l'ID du nouveau sélecteur créé
    populateProductSelector(`productSelect_${newIndex}`);
}

async function handleDestinationFormSubmission(event) {
    event.preventDefault();
    // Afficher le loader et masquer le formulaire
    document.getElementById('loadingBodyAddDestination').classList.remove('hidden');
    document.getElementById('addDestinationForm').classList.add('hidden');

    try {
        // Récupération des données du formulaire principal
        const address = document.getElementById('address').value;
        const recipientType = document.getElementById('recipientType').value;
        const warehouseId = document.getElementById('warehouseSelect').value;
        const comment = document.getElementById('comment').value;
        const deliveryDate = new Date().toISOString().split('T')[0]; // Par défaut à la date actuelle

        // Récupération des données des livraisons
        const deliveries = [];
        const deliveryItems = document.querySelectorAll('.delivery-item');

        deliveryItems.forEach((item, index) => {
            const productId = item.querySelector(`#productSelect_${index}`).value;
            const quantity = item.querySelector(`#quantity_${index}`).value;
            const status = item.querySelector(`#status_${index}`).value;

            deliveries.push({
                product_id: parseInt(productId),
                quantity: parseInt(quantity),
                status: status
            });
        });

        // Création du JSON body
        const jsonBody = {
            address: address,
            recipient_type: recipientType,
            warehouse_id: parseInt(warehouseId),
            comment: comment,
            delivery_date: deliveryDate,
            deliveries: deliveries
        };

        console.log('JSON body:', JSON.stringify(jsonBody, null, 2));
        console.log("selectedRouteId",selectedRouteId);

        const result = await addDestinationToRoute(selectedRouteId, jsonBody);

        if (result && result.message) {
            alert("destination ajoutée avec succès");
            document.getElementById('addDestinationModal').style.display = 'none';
            await populateDestinationsModal(selectedRouteId);
        }

        console.log('Form submitted successfully:');

        document.getElementById('loadingBodyAddDestination').classList.add('hidden');
        document.getElementById('addDestinationForm').classList.remove('hidden');

    } catch (error) {
        console.error('Error submitting form:', error.message);
        document.getElementById('loadingBodyAddDestination').classList.add('hidden');
        document.getElementById('addDestinationForm').classList.remove('hidden');
        alert('Une erreur s\'est produite lors de l\'envoi du formulaire.');
    }
}

// Fenêtre de modification de destination
async function populateEditDestinationModalData() {
    const formElement = document.getElementById('editDestinationForm');
    const loadingIndicator = document.getElementById('loadingEditDestination');
    const productContainer = document.getElementById('productContainer');

    // Show loading indicator and hide form
    formElement.classList.add('hidden');
    loadingIndicator.classList.remove('hidden');

    try {
        await populateWarehouseSelector("destination-edit-warehouseSelect");

        // Récupérer les données de la destination
        const destinationData = await getDestinationById(selectedDestinationId);
        if (!destinationData || !destinationData.destination) {
            throw new Error('Destination data not found');
        }

        // Pré-remplir les champs du formulaire avec les données existantes de la destination
        const { address, recipient_type, comment, warehouse_id } = destinationData.destination;
        document.getElementById('destination-edit-address').value = address ?? '';
        document.getElementById('destination-edit-recipientType').value = recipient_type ?? '';
        document.getElementById('destination-edit-comment').value = comment ?? '';
        document.getElementById('destination-edit-warehouseSelect').value = warehouse_id ?? '';

        // Récupérer les livraisons associées à la destination
        const deliveriesData = await getDeliveriesByDestination(selectedDestinationId);
        if (deliveriesData.message || deliveriesData.error) {
            console.warn(deliveriesData.message || deliveriesData.error);
            productContainer.innerHTML = '<p>Aucune livraison trouvée.</p>';
        } else {
            // Clear previous products and populate new ones
            productContainer.innerHTML = '';
            deliveriesData.forEach((delivery, index) => {
                addProductToForm(delivery, index);
            });
        }
    } catch (error) {
        console.error('Error populating destination data:', error.message);
        alert('Une erreur est survenue lors du chargement des données de la destination. Veuillez réessayer plus tard.');
    } finally {
        // Hide loading indicator and show form
        loadingIndicator.classList.add('hidden');
        formElement.classList.remove('hidden');
    }
}

// Add product fields to the form
async function addProductToForm(delivery, index) {
    console.log("addProductToForm");

    const productItem = document.createElement('div');
    productItem.className = 'product-item';
    productItem.innerHTML = `
            <h4>Produit ${index}</h4>
            <div class="form-group">
                <label for="productSelectEdit_${index}">Produit :</label>
                <select name="product[]" id="productSelectEdit_${index}" required></select>
            </div>
            <div class="form-group">
                <label for="quantity_${index}">Quantité :</label>
                <input type="number" name="quantity[]" id="quantityEdit_${index}" min="1" required>
            </div>
            <div class="form-group">
                <label for="status_${index}">Statut :</label>
                <select name="status[]" id="status_${index}" required>
                    <option value="pending" ${delivery.status === 'pending' ? 'selected' : ''}>En attente</option>
                    <option value="pending" ${delivery.status === 'in_route' ? 'selected' : ''}>En route</option>
                    <option value="completed" ${delivery.status === 'delivered' ? 'selected' : ''}>Complétée</option>
                </select>
            </div>
        `;

    // Ajout de l'élément au DOM avant manipulation
    productContainer.appendChild(productItem);

    console.log("delivery product_id", delivery.product_id);
    console.log("delivery product", delivery.product.name);

    // peupler le selector de produit dans la modale
    await populateProductSelector(`productSelectEdit_${index}`);

    // Affecter la valeur du produit correspondant au selector
    document.getElementById(`productSelectEdit_${index}`).value = delivery.product_id ?? '';

    document.getElementById(`quantityEdit_${index}`).value = delivery.quantity ?? '';
}

async function handleEditDestinationFormSubmission(event) {
    event.preventDefault();

    // Afficher le loader et masquer le formulaire
    document.getElementById('loadingEditDestination').classList.remove('hidden');
    document.getElementById('editDestinationForm').classList.add('hidden');

    try {
        // Récupération des données du formulaire principal
        const address = document.getElementById('destination-edit-address').value;
        const recipientType = document.getElementById('destination-edit-recipientType').value;
        const warehouseId = document.getElementById('destination-edit-warehouseSelect').value;
        const comment = document.getElementById('destination-edit-comment').value;
        const deliveryDate = new Date().toISOString().split('T')[0]; // Par défaut à la date actuelle

        // Récupération des données des livraisons
        const deliveries = [];
        const productItems = document.querySelectorAll('#productContainer .product-item');

        productItems.forEach((item, index) => {
            const productId = item.querySelector(`#productSelectEdit_${index}`).value;
            const quantity = item.querySelector(`#quantityEdit_${index}`).value;
            const status = item.querySelector(`#status_${index}`).value;

            deliveries.push({
                id: index + 1, // Remplacez par l'ID réel si nécessaire
                product_id: parseInt(productId),
                quantity: parseInt(quantity),
                status: status,
                comment: '' // Ajoutez un champ de commentaire si nécessaire
            });
        });

        // Création du JSON body
        const jsonBody = {
            address: address,
            recipient_type: recipientType,
            warehouse_id: parseInt(warehouseId),
            comment: comment,
            delivery_date: deliveryDate,
            deliveries: deliveries
        };

        console.log('JSON body for update:', JSON.stringify(jsonBody, null, 2));
        console.log("Destination ID:", selectedDestinationId);

        const result = await updateDestinationAndDeliveries(selectedDestinationId, jsonBody);

        if (result && result.message) {
            alert("Destination mise à jour avec succès");
            document.getElementById('editDestinationModal').style.display = 'none';
            await populateDestinationsModal(selectedRouteId); // Mettre à jour la liste des destinations
        }

        console.log('Form updated successfully:');

        // Réafficher le formulaire et masquer le loader
        document.getElementById('loadingEditDestination').classList.add('hidden');
        document.getElementById('editDestinationForm').classList.remove('hidden');

    } catch (error) {
        console.error('Error updating destination:', error.message);
        document.getElementById('loadingEditDestination').classList.add('hidden');
        document.getElementById('editDestinationForm').classList.remove('hidden');
        alert('Une erreur s\'est produite lors de la mise à jour de la destination.');
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

    // Fenêtre de modification de route
    const editRouteModal = document.getElementById("editRouteModal");
    const editRouteSpan = document.getElementById("closeEditRouteModal");
    const editRouteForm = document.getElementById("editRouteForm");
    const editRouteButton = document.getElementById("modifyRouteButton");

    editRouteButton.onclick = function () {
        // Récupérer l'ID de la route depuis le checkbox sélectionné
        const selectedCheckbox = document.querySelector('input[name="RouteSelection"]:checked');
        const selectedRouteID = selectedCheckbox ? selectedCheckbox.value : null;

        if (!selectedRouteID) {
            alert("Veuillez sélectionner une route.");
            return;
        }

        document.getElementById("editRouteModal").style.display = "block"
        populateEditRouteForm(selectedRouteID);
    }

    editRouteSpan.onclick = function () {
        populateEditRouteForm();
    };


    editRouteSpan.onclick = function () {
        editRouteModal.style.display = "none";
    };

    editRouteForm.onsubmit = async function (event) {
        event.preventDefault();

        // Récupérer l'ID de la route depuis le checkbox sélectionné
        const selectedCheckbox = document.querySelector('input[name="RouteSelection"]:checked');
        const selectedRouteID = selectedCheckbox ? selectedCheckbox.value : null;

        if (!selectedRouteID) {
            alert("Veuillez sélectionner une route.");
            return;
        }

        await editRoute(selectedRouteID);
    };

    // Fenêtre d'ajout de destination
    const addDestinationModal = document.getElementById("addDestinationModal");
    const closeDestinationSpan = document.getElementById("closeAddDestinationModal");
    const addDestinationForm = document.getElementById("addDestinationForm");
    const addDestinationButton = document.getElementById("addDestinationInModalButton");
    const addDeliveryButton = document.getElementById("addDeliveryButton");


    addDestinationButton.onclick = function () {
        addDestinationModal.style.display = "block"
    }

    closeDestinationSpan.onclick = function () {
        addDestinationModal.style.display = "none";
    };

    //Section dynamique d'ajout des deliveries dans la fenêtre modale des destinations
    addDeliveryButton.addEventListener('click', function () {
        populateDeliveryContainer('deliveryContainer');
    });

    await populateProductSelector('productSelect_0');

    addDestinationForm.addEventListener('submit', handleDestinationFormSubmission);

    // Fenêtre de modification de la destination
    const editDestinationModal = document.getElementById('editDestinationModal');
    const closeEditDestinationModal = document.getElementById('closeEditDestinationModal');
    const productContainer = document.getElementById('productContainer');
    const editDestinationForm = document.getElementById('editDestinationForm');
    const editDestinationButton = document.getElementById("modifyDestinationInModalButton");
    let productIndex = 0;

    editDestinationButton.addEventListener('click',function (){
        if (!selectedDestinationId) {
            alert('Destination ID is required.');
            return;
        }

        populateEditDestinationModalData(selectedDestinationId);
        editDestinationModal.style.display = 'block';
    })

    closeEditDestinationModal.onclick = () => {
        editDestinationModal.style.display = 'none';
    };

    editDestinationForm.addEventListener('submit',handleEditDestinationFormSubmission);

});