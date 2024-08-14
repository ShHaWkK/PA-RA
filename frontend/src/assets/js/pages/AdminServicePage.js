// Path: /assets/js/pages/AdminServicePage.js

import { populateServiceTable } from '/assets/js/modules/tables/ServiceTable.js';
import { getAllServices, getServiceCapacity, deleteService } from '/assets/js/api/Service.js';

export var selectedServiceId;
export var selectedServiceAvailableCapacity;

async function populateServiceSelector() {
    try {
        const services = await getAllServices();
        const serviceSelect = document.getElementById('serviceSelect');

        serviceSelect.innerHTML = '';

        const defaultOption = document.createElement('option');
        defaultOption.text = 'Choose a service';
        defaultOption.value = '';
        serviceSelect.add(defaultOption);

        services.forEach(service => {
            const option = document.createElement('option');
            option.text = service.name;
            option.value = service.id;
            serviceSelect.add(option);
        });
    } catch (error) {
        console.error('Error populating service selector:', error.message);
    }
}

async function handleServiceChange(event) {
    const serviceId = event.target.value;
    if (serviceId) {
        console.log("Service ID selected:", serviceId);
        selectedServiceId = serviceId;
        await populateServiceTable(serviceId);
        await populateProgressBar(serviceId);
    } else {
        console.error("No valid service ID selected.");
    }
}

async function populateProgressBar(serviceId) {
    const progressBarElement = document.getElementById('progress-bar');
    const availableCapacityElement = document.getElementById("available-capacity");

    if (!progressBarElement || !availableCapacityElement) {
        console.error('Error populating progress bar: Required DOM elements are missing');
        return;
    }

    try {
        const capacityData = await getServiceCapacity(serviceId);
        if (capacityData && capacityData.total_capacity !== undefined && capacityData.occupied_capacity !== undefined) {
            const { total_capacity, occupied_capacity } = capacityData;
            selectedServiceAvailableCapacity = capacityData.available_capacity;
            availableCapacityElement.value = selectedServiceAvailableCapacity;
            updateProgressBar(occupied_capacity, total_capacity);
        } else {
            console.error('Capacity data not available or incomplete');
            progressBarElement.style.display = 'none'; // Hide the progress bar if data is missing
        }
    } catch (error) {
        console.error('Error populating progress bar:', error.message);
        progressBarElement.style.display = 'none'; // Hide the progress bar in case of error
    }
}

function updateProgressBar(occupied, total) {
    const progressLabel = document.getElementById('progress-label');
    const progressBar = document.getElementById('progress-bar');

    if (!progressLabel || !progressBar) {
        console.error('Progress bar or label element not found');
        return;
    }

    // Check if the total capacity is valid
    if (total > 0) {
        const percentage = (occupied / total) * 100;
        progressBar.style.width = percentage + '%';
        progressBar.textContent = percentage.toFixed(2) + '%';
        progressLabel.textContent = `${occupied}/${total} units`;

        if (percentage < 50) {
            progressBar.style.backgroundColor = '#76c7c0';
        } else if (percentage < 75) {
            progressBar.style.backgroundColor = '#ffa500';
        } else {
            progressBar.style.backgroundColor = '#ff0000';
        }

        progressBar.style.display = 'block';
    } else {
        console.error('Invalid total capacity');
        progressBar.style.display = 'none'; // Hide the progress bar if total capacity is invalid
        progressLabel.textContent = `Invalid capacity data`;
    }
}

function deleteSelectedService() {
    const serviceSelect = document.getElementById('serviceSelect');
    const serviceId = serviceSelect.value;

    if (serviceId) {
        const confirmation = confirm("Do you really want to delete this service?");
        if (confirmation) {
            deleteService(serviceId)
                .then(response => {
                    alert(response.message);
                    populateServiceSelector();  // Reload services after deletion
                })
                .catch(error => {
                    alert("Error deleting service: " + error.message);
                });
        }
    } else {
        alert("Please select a service to delete.");
    }
}

document.addEventListener('DOMContentLoaded', function () {
    populateServiceSelector();
    const serviceSelect = document.getElementById('serviceSelect');
    serviceSelect.addEventListener('change', handleServiceChange);

    const serviceDelete = document.getElementById('deleteServiceButton');
    serviceDelete.addEventListener('click', deleteSelectedService);
});
