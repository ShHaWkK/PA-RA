import { getAllServices } from "../api/Service.js";
import {registerUserToService} from "../api/ServiceRegistration.js";
import {getCookie} from "../api/Api.js";

// Fonction pour afficher les services dans le tableau
async function populateServicesTable() {
    try {
        const loader = document.getElementById('loadingBodyGeneral');
        loader.classList.remove('hidden');

        // Récupérer tous les services
        let services = await getAllServices();

        const servicesTableBody = document.querySelector('#servicesTable tbody');
        servicesTableBody.innerHTML = '';

        // Filtrer les services pour ne garder que ceux qui sont ouverts
        let openServices = services.filter(service => service.status === 'open');

        // Obtenir la date sélectionnée par l'utilisateur
        const selectedDate = document.getElementById('serviceDate').value;

        if (selectedDate) {
            // Filtrer les services par date
            openServices = openServices.filter(service => {
                // Comparer les timestamps (en millisecondes) après les avoir convertis en date locale
                const serviceDate = new Date(service.schedule.timestamp * 1000).toISOString().split('T')[0];
                return serviceDate === selectedDate;
            });
        }

        if (!openServices || openServices.length === 0) {
            servicesTableBody.innerHTML = '<tr><td colspan="4">No services available for the selected date</td></tr>';
            loader.classList.add('hidden');
            return;
        }

        openServices.forEach(service => {
            const row = document.createElement('tr');

            const nameCell = document.createElement('td');
            nameCell.textContent = service.name;
            row.appendChild(nameCell);

            const descriptionCell = document.createElement('td');
            descriptionCell.textContent = service.description;
            row.appendChild(descriptionCell);

            const capacityCell = document.createElement('td');
            capacityCell.textContent = `${service.capacity} slots available`;
            row.appendChild(capacityCell);

            const actionCell = document.createElement('td');
            const registerButton = document.createElement('button');
            registerButton.textContent = 'Register';
            registerButton.addEventListener('click', async () => {
                try {
                    const userId = getCookie('user_id'); // Fonction pour obtenir l'ID utilisateur depuis les cookies
                    if (!userId) {
                        alert('You must be logged in to register');
                        return;
                    }

                    const registrationData = {
                        user_id: userId,
                        service_id: service.id
                    };

                    await registerUserToService(registrationData);
                    alert('Successfully registered to the service!');
                } catch (error) {
                    alert('Failed to register to the service: ' + error.message);
                }
            });
            actionCell.appendChild(registerButton);
            row.appendChild(actionCell);

            servicesTableBody.appendChild(row);
        });

        loader.classList.add('hidden');
    } catch (error) {
        console.error('Error populating services table:', error.message);
        document.getElementById('loadingBodyGeneral').classList.add('hidden');
    }
}


document.addEventListener('DOMContentLoaded',function (){
    populateServicesTable();

    document.getElementById('serviceDate').addEventListener('change', populateServicesTable);

    document.getElementById('allServiceDates').addEventListener('click', () => {
        document.getElementById('serviceDate').value = '';  // Réinitialiser la valeur du sélecteur de date
        populateServicesTable(); 
    });
});