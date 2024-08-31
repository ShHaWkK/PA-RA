import { getDeliveriesByDestination, getRouteById, updateDelivery, updateDestinationAndDeliveries } from "../api/Distributions.js";

document.addEventListener('DOMContentLoaded', async function () {
    const routeId = new URLSearchParams(window.location.search).get('routeId');
    if (routeId) {
        await displayRouteDetails(routeId);
    }

    async function displayRouteDetails(routeId) {
        try {

            const loader= document.getElementById('loadingBodyCollectedProducts');
            const destinationsContainer = document.getElementById('destinations-container');

            loader.classList.remove('hidden');
            destinationsContainer.classList.add('hidden');

            const route = await getRouteById(routeId);
            document.getElementById('route-name-header').textContent = route.route.name;
            const routeInfoDiv = document.getElementById('route-info');

            routeInfoDiv.innerHTML = `
                <p>Driver: ${route.route.driver.first_name} ${route.route.driver.last_name}</p>
                <p>Vehicle: ${route.route.vehicle.brand} ${route.route.vehicle.model} (${route.route.vehicle.license_plate})</p>
                <p>Start Time: ${route.route.start_time}</p>
                <p>Status: ${route.route.status}</p>
            `;

            for (const destination of route.route.destinations) {
                const destinationSection = document.createElement('div');
                destinationSection.classList.add('destination-section');

                destinationSection.innerHTML = `
                    <h3>Destination: ${destination.address} <button class="edit-button-destination" data-id="${destination.id}">Modifier</button></h3>
                    <p>Warehouse: ${destination.warehouse_name}</p>
                    <p>Status: <span class="destination-status">${destination.status}</span></p>
                    <p>Comment: ${destination.comment}</p>
                    <div class="delivery-sections" id="delivery-sections-${destination.id}"></div>
                `;

                destinationsContainer.appendChild(destinationSection);

                // Fetch and display deliveries for this destination
                const deliveries = await getDeliveriesByDestination(destination.id);
                const deliverySectionsDiv = document.getElementById(`delivery-sections-${destination.id}`);

                if (deliveries.error) {
                    deliverySectionsDiv.innerHTML = `<p>${deliveries.error}</p>`;
                } else {
                    deliveries.forEach(delivery => {
                        const deliverySection = document.createElement('div');
                        deliverySection.classList.add('delivery-section');
                        deliverySection.innerHTML = `
                            <p>Product: ${delivery.product.name} (Quantity: ${delivery.quantity}) <button class="edit-button-delivery" data-id="${delivery.id}">Modifier</button></p>
                            <p>Status: <span class="delivery-status">${delivery.status}</span></p>
                            <p>Comment: ${delivery.comment}</p>
                        `;
                        deliverySectionsDiv.appendChild(deliverySection);
                    });
                }
            }

            // Attach event listeners for "Modifier" buttons
            attachEditButtonListeners();

            loader.classList.add('hidden');
            destinationsContainer.classList.remove('hidden');

        } catch (error) {
            console.error('Error displaying route details:', error.message);
        }
    }

    function attachEditButtonListeners() {
        const statusOptions = ['pending', 'in_route', 'delivered'];

        document.querySelectorAll('.edit-button-destination').forEach(button => {
            button.addEventListener('click', async function () {
                const destinationSection = this.closest('.destination-section');
                const statusElement = destinationSection.querySelector('.destination-status');
                const destinationId = this.dataset.id;

                // Toggle select field for status
                if (this.textContent === 'Modifier') {
                    const statusSelect = document.createElement('select');
                    statusOptions.forEach(option => {
                        const optionElement = document.createElement('option');
                        optionElement.value = option;
                        optionElement.textContent = option;
                        if (option === statusElement.textContent) {
                            optionElement.selected = true;
                        }
                        statusSelect.appendChild(optionElement);
                    });
                    statusElement.replaceWith(statusSelect);

                    this.textContent = 'Sauvegarder';
                } else {
                    const statusSelect = destinationSection.querySelector('select');
                    const newStatus = statusSelect.value;

                    const newStatusSpan = document.createElement('span');
                    newStatusSpan.classList.add('destination-status');
                    newStatusSpan.textContent = newStatus;
                    statusSelect.replaceWith(newStatusSpan);

                    try {
                        await updateDestinationAndDeliveries(destinationId, { status: newStatus });
                        console.log('Destination status updated successfully.');
                    } catch (error) {
                        console.error('Failed to update destination status.', error);
                    }

                    this.textContent = 'Modifier';
                }
            });
        });

        document.querySelectorAll('.edit-button-delivery').forEach(button => {
            button.addEventListener('click', async function () {
                const deliverySection = this.closest('.delivery-section');
                const statusElement = deliverySection.querySelector('.delivery-status');
                const deliveryId = this.dataset.id;

                // Toggle select field for status
                if (this.textContent === 'Modifier') {
                    const statusSelect = document.createElement('select');
                    statusOptions.forEach(option => {
                        const optionElement = document.createElement('option');
                        optionElement.value = option;
                        optionElement.textContent = option;
                        if (option === statusElement.textContent) {
                            optionElement.selected = true;
                        }
                        statusSelect.appendChild(optionElement);
                    });
                    statusElement.replaceWith(statusSelect);

                    this.textContent = 'Sauvegarder';
                } else {
                    const statusSelect = deliverySection.querySelector('select');
                    const newStatus = statusSelect.value;

                    const newStatusSpan = document.createElement('span');
                    newStatusSpan.classList.add('delivery-status');
                    newStatusSpan.textContent = newStatus;
                    statusSelect.replaceWith(newStatusSpan);

                    try {
                        await updateDelivery(deliveryId, { status: newStatus });
                        console.log('Delivery status updated successfully.');
                    } catch (error) {
                        console.error('Failed to update delivery status.', error);
                    }

                    this.textContent = 'Modifier';
                }
            });
        });
    }
});