import {getServicesByDate} from "../api/Service.js";
import {getCookie} from "../api/Api.js";
import {registerUserToService} from "../api/ServiceRegistration.js";

document.addEventListener('DOMContentLoaded', function () {
    // Fonction pour définir la date d'aujourd'hui
    function setDefaultDate() {
        const today = new Date();
        document.getElementById('serviceDate').value = today.toISOString().split('T')[0];
    }

    // Définir la date d'aujourd'hui comme valeur par défaut
    setDefaultDate();

    // Fonction pour peupler le tableau des services
    async function populateServicesTable() {
        try {
            const tableBody = document.querySelector('#servicesTable tbody');

            tableBody.classList.add('hidden');



            const loader = document.getElementById('loadingBodyGeneral');
            loader.classList.remove('hidden');

            // Obtenir la date sélectionnée et le statut du service
            const selectedDate = document.getElementById('serviceDate').value;
            const filter = document.getElementById('serviceDateStatus').value;

            // Appeler l'API pour obtenir les services filtrés par date et statut
            let services = await getServicesByDate(selectedDate, filter);

            const servicesTableBody = document.querySelector('#servicesTable tbody');
            servicesTableBody.innerHTML = '';

            if (!services || services.length === 0) {
                servicesTableBody.innerHTML = '<tr><td colspan="5">No services available for the selected criteria</td></tr>';
                loader.classList.add('hidden');
                return;
            }

            services.forEach(service => {
                const row = document.createElement('tr');

                const nameCell = document.createElement('td');
                nameCell.textContent = service.name;
                row.appendChild(nameCell);

                const dateCell = document.createElement('td');
                dateCell.textContent = service.schedule;
                row.appendChild(dateCell);

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

                if (filter === 'upcoming' && service.status === 'open' && service.capacity !== 0){
                        actionCell.appendChild(registerButton);
                }
                else{
                        actionCell.textContent = 'Registrations Closed';
                }

                row.appendChild(actionCell);
                servicesTableBody.appendChild(row);
            });

            loader.classList.add('hidden');
            tableBody.classList.remove('hidden');
        } catch (error) {
            console.error('Error populating services table:', error.message);
            document.getElementById('loadingBodyGeneral').classList.add('hidden');
        }
    }

    populateServicesTable();

    document.getElementById('serviceDate').addEventListener('change', populateServicesTable);
    document.getElementById('serviceDateStatus').addEventListener('change', populateServicesTable);
});