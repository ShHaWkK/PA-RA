import {populateVehicleTable} from "../modules/tables/VehicleTable.js";
import {populateDriverTable} from "../modules/tables/VolunteerTable.js";
import {populateWarehouseTable} from "../modules/tables/WarehouseTable.js";
import {createRoute} from "../api/Distributions.js";
import {getAllProducts} from "../api/Products.js";

function initializeTables() {
    populateVehicleTable();
    populateDriverTable();
    populateWarehouseTable(); // Peupler la table des entrepôts pour le sélecteur
}

function setupEventListeners() {
    document.getElementById('addDestinationButton').addEventListener('click', openAddDestinationModal);
    document.getElementById('createDelivery').addEventListener('click', handleRouteSubmission);
    document.getElementById('closeAddDestinationModal').addEventListener('click', closeAddDestinationModal);
    document.getElementById('addDeliveryButton').addEventListener('click', addProductToDeliverySection);
    document.getElementById('addDestinationsButton').addEventListener('click', addDestinationToPage);

    // Fenêtre modale de vue des disponibilités
    var availabilityModal = document.getElementById("volunteerAvailabilitiesModal");
    var availabilitySpan = document.getElementById("closeVolunteerAvailabilitiesButton");

    availabilitySpan.onclick = function() {
        availabilityModal.style.display = "none";
    }

    // Fenêtre modale de vue des compétences
    var skillModal = document.getElementById("volunteerSkillModal");
    var skillSpan = document.getElementById("closeVolunteerSkillButton");

    skillSpan.onclick = function() {
        skillModal.style.display = "none";
    }
}

function openAddDestinationModal() {
    document.getElementById('addDestinationModal').style.display = 'block';
    populateProductSelector('productSelect_0'); // Peupler le premier sélecteur de produit au chargement
}

function closeAddDestinationModal() {
    document.getElementById('addDestinationModal').style.display = 'none';
}

async function addProductToDeliverySection() {
    const deliveryContainer = document.getElementById('deliveryContainer');
    const productCount = deliveryContainer.childElementCount;

    const newDeliveryDiv = document.createElement('div');
    newDeliveryDiv.className = 'delivery-item';
    newDeliveryDiv.innerHTML = `
        <h3> Produit ${productCount}</h3>
        <div class="form-group">
            <label for="productSelect_${productCount}">Produit :</label>
            <select name="product[]" id="productSelect_${productCount}" required></select>
        </div>
        <div class="form-group">
            <label for="quantity_${productCount}">Quantité :</label>
            <input type="number" name="quantity[]" id="quantity_${productCount}" min="1" required>
        </div>
        <div class="form-group">
            <label for="comment_${productCount}">Quantité :</label>
            <input type="text" name="comment[]" id="comment_${productCount}">
        </div>
        <div class="form-group">
            <label for="status_${productCount}">Statut :</label>
            <select name="status[]" id="status_${productCount}" required>
                <option value="pending">En attente</option>
                <option value="delivered">Livré</option>
            </select>
        </div>
    `;
    deliveryContainer.appendChild(newDeliveryDiv);

    // Peupler le nouveau sélecteur de produits avec les produits disponibles
    await populateProductSelector(`productSelect_${productCount}`);
}

async function populateProductSelector(selectorId) {
    try {
        const products = await getAllProducts();
        const productSelector = document.getElementById(selectorId);
        productSelector.innerHTML = '';

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

function addDestinationToPage(event) {
    event.preventDefault(); // Empêcher la soumission du formulaire

    const address = document.getElementById('address').value;
    const recipientType = document.getElementById('recipientType').value;
    const warehouse = document.getElementById('warehouseSelect').value;
    const comment = document.getElementById('comment').value;

    const destinationId = document.querySelectorAll('.destination-item').length;

    const destinationDiv = document.createElement('div');
    destinationDiv.className = 'destination-item';
    destinationDiv.dataset.destinationId = destinationId;
    destinationDiv.innerHTML = `
        <h3>Destination ${destinationId + 1}</h3>
        <p>Adresse : ${address}</p>
        <p>Type de destinataire : ${recipientType}</p>
        <p>Entrepôt : ${warehouse}</p>
        <p>Commentaire : ${comment}</p>
        <div class="delivery-items" id="deliveryItems_${destinationId}"></div>
    `;

    document.querySelectorAll('#deliveryContainer .delivery-item').forEach((item, index) => {
        const productId = item.querySelector(`select[name="product[]"]`).value;
        const quantity = item.querySelector(`input[name="quantity[]"]`).value;
        const status = item.querySelector(`select[name="status[]"]`).value;
        const comment = item.querySelector(`input[name="comment[]"]`).value;

        const deliveryDiv = document.createElement('div');
        deliveryDiv.className = 'delivery-item-summary';
        deliveryDiv.innerHTML = `
            <p>Produit : ${productId}</p>
            <p>Quantité : ${quantity}</p>
            <p>Statut : ${status}</p>
            <p>Commentaire : ${comment}</p>
        `;

        destinationDiv.querySelector(`#deliveryItems_${destinationId}`).appendChild(deliveryDiv);
    });

    document.getElementById('destinationContainer').appendChild(destinationDiv);

    // Réinitialiser la modale et la fermer
    document.getElementById('addDestinationForm').reset();
    closeAddDestinationModal();
}

async function handleRouteSubmission() {
    const selectedVehicle = document.querySelector('input[name="selectedVehicle"]:checked');
    const selectedDriver = document.querySelector('input[name="selectedVolunteer"]:checked');

    if (!selectedVehicle || !selectedDriver) {
        alert("Veuillez sélectionner un véhicule et un chauffeur.");
        return;
    }

    const routeName = document.getElementById('routeName').value;
    const startDate = document.getElementById('startDate').value;
    const destinations = gatherDestinations();

    const routeData = {
        name: routeName,
        vehicle_id: parseInt(selectedVehicle.value, 10),
        driver_id: parseInt(selectedDriver.value, 10),
        date: startDate,
        destinations: destinations
    };

    // Masquer le formulaire et afficher le loader général
    const formContainer = document.getElementById('createRouteForm');
    const loader = document.getElementById('loadingBodyGeneral');

    if (formContainer) {
        formContainer.classList.add('hidden');
    }

    if (loader) {
        loader.classList.remove('hidden');
    }

    try {
        const route = await createRoute(routeData);
        if (route) {
            alert("Route et livraisons créées avec succès.");
            window.location.href = '/Admin/Distributions';
        }
    } catch (error) {
        console.error("Erreur lors de la création de la route :", error);
        alert("Erreur lors de la création de la route.");
    } finally {

        if (formContainer) {
            formContainer.classList.remove('hidden');
        }

        if (loader) {
            loader.classList.add('hidden');
        }
    }
}

function gatherDestinations() {
    const destinations = [];
    document.querySelectorAll('.destination-item').forEach(destinationItem => {
        const address = destinationItem.querySelector('p:nth-of-type(1)').textContent.split(': ')[1];
        const recipientType = destinationItem.querySelector('p:nth-of-type(2)').textContent.split(': ')[1];
        const warehouse = destinationItem.querySelector('p:nth-of-type(3)').textContent.split(': ')[1];
        const comment = destinationItem.querySelector('p:nth-of-type(4)').textContent.split(': ')[1];
        const deliveries = gatherDeliveries(destinationItem);

        destinations.push({
            address: address,
            recipient_type: recipientType,
            warehouse: warehouse,
            comment: comment,
            deliveries: deliveries
        });
    });

    return destinations;
}

function gatherDeliveries(destinationItem) {
    const deliveries = [];
    destinationItem.querySelectorAll('.delivery-item-summary').forEach(item => {
        const productId = item.querySelector('p:nth-of-type(1)').textContent.split(': ')[1];
        const quantity = item.querySelector('p:nth-of-type(2)').textContent.split(': ')[1];
        const status = item.querySelector('p:nth-of-type(3)').textContent.split(': ')[1];
        const comment = item.querySelector('p:nth-of-type(4)').textContent.split(': ')[1];

        deliveries.push({
            product_id: parseInt(productId, 10),
            quantity: parseInt(quantity, 10),
            status: status,
            comment: comment || ""
        });
    });

    return deliveries;
}

document.addEventListener('DOMContentLoaded', function () {
    initializeTables();
    setupEventListeners();
});