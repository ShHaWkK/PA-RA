import {deleteServiceRegistration, getServiceRegistrationByID} from "../api/ServiceRegistration.js";

function getServiceRegistrationIdFromURL() {
    const params = new URLSearchParams(window.location.search);
    console.log(params.get('serviceRegistrationId'));
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

}

async function initializePage() {
    const registrationId = getServiceRegistrationIdFromURL();

    if (!registrationId) {
        console.error('Service registration ID not found in the URL');
        return;
    }

    try {
        const loader = document.getElementById("loadingServiceDetails");
        loader.classList.remove('hidden');
        const data = await getServiceRegistrationByID(registrationId);
        displayServiceRegistrationDetails(data);
        loader.classList.add('hidden');
    } catch (error) {
        console.error('Error initializing the page:', error);
    }

    document.getElementById('unsubscribeButton').addEventListener('click', async function() {
        // Affiche une alerte de confirmation
        const confirmation = confirm("Êtes-vous sûr de vouloir vous désinscrire ?");

        if (confirmation) {
            try {
                const registrationId = getServiceRegistrationIdFromURL();

                if (!registrationId) {
                    throw new Error("L'ID de l'inscription n'est pas fourni dans l'URL");
                }

                const result = await deleteServiceRegistration(registrationId);
                alert(result.message);
                window.location.href = "/Volunteer/Services";
            } catch (error) {
                alert('Erreur lors de la désinscription : ' + error.message);
            }
        }
    });
}
document.addEventListener('DOMContentLoaded',function (){
    initializePage();
});