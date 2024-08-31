import {getServiceRegistrationByID} from "../api/ServiceRegistration.js";

function getServiceRegistrationIdFromURL() {
    const params = new URLSearchParams(window.location.search);
    return params.get('serviceRegistrationId');
}

function displayServiceRegistrationDetails(data) {
    // Updating the service name in the header
    const serviceNameHeader = document.getElementById('service-name-header');
    serviceNameHeader.textContent = data.service.name;

    // Populating the service information
    const serviceInfoDiv = document.getElementById('service-info');
    serviceInfoDiv.innerHTML = `
        <p><strong>Description:</strong> ${data.service.description}</p>
        <p><strong>Schedule:</strong> ${new Date(data.service.schedule).toLocaleString()}</p>
        <p><strong>Location:</strong> ${data.service.location}</p>
        <p><strong>Capacity:</strong> ${data.service.capacity}</p>
        <p><strong>Status:</strong> ${data.service.status}</p>
    `;

    const registrationInfoDiv = document.getElementById('registration-info');
    registrationInfoDiv.innerHTML = `
        <p><strong>Registered User ID:</strong> ${data.userId}</p>
        <p><strong>Registration Date:</strong> ${new Date(data.registrationDate).toLocaleString()}</p>
    `;

    const loader = document.getElementById('loadingServiceDetails');
    loader.style.display = 'none';
}

async function initializePage() {
    const registrationId = getServiceRegistrationIdFromURL();

    if (!registrationId) {
        console.error('Service registration ID not found in the URL');
        return;
    }

    try {
        const data = await getServiceRegistrationByID(registrationId);
        displayServiceRegistrationDetails(data);
    } catch (error) {
        console.error('Error initializing the page:', error);
        // Handle the error appropriately, possibly showing an error message on the page
    }
}

document.addEventListener('DOMContentLoaded',function (){
    initializePage();
});