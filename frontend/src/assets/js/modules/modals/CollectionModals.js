import {getUser} from "../../api/Users.js";

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
    console.log("we are here", vehicleID);

    const modalBody = document.getElementById('modalBodyVehicleDetails');

    // Vider le contenu précédent du corps de la modale
    modalBody.innerHTML = '';
    console.log("Selected vehicle ID", vehicleID);

    document.getElementById('loadingVehicleDetails').classList.remove('hidden');

    try {
        // Obtenir les détails du volontaire
        const vehicle = await getUser(vehicleID);
        console.log(vehicle);

        if (vehicle) {
            // Créer des éléments label et les champs associés
            const firstNameLabel = document.createElement('h3');
            firstNameLabel.textContent = 'First Name:';
            const vehicleFirstName = document.createElement('p');
            vehicleFirstName.textContent = vehicle.first_name;

            const lastNameLabel = document.createElement('h3');
            lastNameLabel.textContent = 'Last Name:';
            const vehicleLastName = document.createElement('p');
            vehicleLastName.textContent = vehicle.last_name;

            const phoneLabel = document.createElement('h3');
            phoneLabel.textContent = 'Phone Number:';
            const vehiclePhoneNumber = document.createElement('p');
            vehiclePhoneNumber.textContent = vehicle.phone_number;

            const emailLabel = document.createElement('h3');
            emailLabel.textContent = 'Email:';
            const vehicleEmail = document.createElement('p');
            vehicleEmail.textContent = vehicle.email;

            // Ajouter les labels et les champs associés au corps de la modale
            modalBody.appendChild(firstNameLabel);
            modalBody.appendChild(vehicleFirstName);

            modalBody.appendChild(lastNameLabel);
            modalBody.appendChild(vehicleLastName);

            modalBody.appendChild(phoneLabel);
            modalBody.appendChild(vehiclePhoneNumber);

            modalBody.appendChild(emailLabel);
            modalBody.appendChild(vehicleEmail);
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
});
