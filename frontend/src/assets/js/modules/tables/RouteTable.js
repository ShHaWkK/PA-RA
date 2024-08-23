import {getAllRoutes} from "../../api/Distributions.js";
import {
    populateVehicleDetailsInModal,
    populateVolunteerDetailsInModal
} from "../modals/CollectionModals.js";
import {formatDateToFrench, extractDateTime, parseDate} from "../FormatDate.js";

export async function populateRouteTable(queryParameters) {
    try {
        document.getElementById('loadingBodyGeneral').classList.remove('hidden');

        // Appel de la fonction pour obtenir toutes les routes avec les paramètres de requête
        const responseJson = await getAllRoutes(queryParameters);

        // Assurez-vous d'accéder au tableau des routes
        const routes = responseJson.routes;

        if (!routes || routes.length === 0) {
            console.log('No routes found');
            return;
        }

        const backOfficeContent = document.querySelector('.distribution-table table');
        if (!backOfficeContent) {
            console.error('Distribution table container not found.');
            return;
        }

        // Effacer le contenu existant du tableau
        backOfficeContent.innerHTML = '';

        // Créer le tableau et son header
        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');

        const headers = ['', 'Name', 'Driver', 'Vehicle', 'Date', 'Start Time','End Time', 'Status', 'Destinations', 'Created At', 'Updated At'];
        headers.forEach(headerText => {
            const th = document.createElement('th');
            th.textContent = headerText;
            headerRow.appendChild(th);
        });

        thead.appendChild(headerRow);
        backOfficeContent.appendChild(thead);

        const tbody = document.createElement('tbody');

        routes.forEach(route => {
            console.log(route);

            const row = document.createElement('tr');

            // Créer la cellule du bouton radio
            const radioCell = document.createElement('td');
            const radioInput = document.createElement('input');
            radioInput.type = 'radio';
            radioInput.name = 'RouteSelection'; // Assurez-vous que toutes les options radio partagent le même nom
            radioInput.value = route.id; // Attribuez l'ID de la route comme valeur du bouton radio
            radioCell.appendChild(radioInput);
            row.appendChild(radioCell);

            const nameCell = document.createElement('td');
            nameCell.textContent = route.name;
            row.appendChild(nameCell);

            // Créer les cellules de données pour le chauffeur
            const driverCell = document.createElement('td');
            const driverLink = document.createElement('a');
            driverLink.href = "#";
            driverLink.textContent = route.driver.first_name + ' ' + route.driver.last_name; // Définir le texte du lien
            driverLink.setAttribute('data-id', route.driver.id); // Ajouter l'ID en tant qu'attribut data-id
            driverLink.addEventListener('click', (e) => {
                e.preventDefault();
                populateVolunteerDetailsInModal(route.driver.id);
                document.getElementById('volunteerDetailsModal').style.display = 'block';
            });
            driverCell.appendChild(driverLink); // Ajouter le lien à la cellule
            row.appendChild(driverCell);

            // Créer les cellules de données pour le véhicule
            const vehicleCell = document.createElement('td');
            const vehicleLink = document.createElement('a');
            vehicleLink.href = "#";
            vehicleLink.textContent = route.vehicle.license_plate; // Définir le texte du lien
            vehicleLink.setAttribute('data-id', route.vehicle.id); // Ajouter l'ID en tant qu'attribut data-id
            vehicleLink.addEventListener('click', (e) => {
                e.preventDefault();
                populateVehicleDetailsInModal(route.vehicle.id);
                document.getElementById('vehicleDetailsModal').style.display = 'block';
            });
            vehicleCell.appendChild(vehicleLink); // Ajouter le lien à la cellule
            row.appendChild(vehicleCell);


            // Ajout des dates et des heures de départ et des retour
            const { dateOnly: startDateOnly, timeOnly: startTimeOnly } = extractDateTime(route.start_time);
            console.log("startDateOnly",startDateOnly);
            const endTimeOnly = route.end_time ? extractDateTime(route.end_time).timeOnly : 'En cours';

            const routeDateCell = document.createElement('td');
            routeDateCell.textContent = startDateOnly;
            row.appendChild(routeDateCell);

            const startTimeCell = document.createElement('td');
            startTimeCell.textContent = startTimeOnly;
            row.appendChild(startTimeCell);

            const endTimeCell = document.createElement('td');
            endTimeCell.textContent = endTimeOnly;
            row.appendChild(endTimeCell);

            const statusCell = document.createElement('td');
            statusCell.textContent = route.status
            row.appendChild(statusCell);

            const destinationsCell = document.createElement('td');
            const viewDestinationsButton = document.createElement('button');
            viewDestinationsButton.textContent = 'Voir';
            viewDestinationsButton.value = route.id;
            viewDestinationsButton.addEventListener('click', (e) => {
                e.preventDefault();
                populateCollectedDestinationsModal(collection.id);
                document.getElementById('collectedDestinationsDetailsModal').style.display = 'block';
            });
            destinationsCell.appendChild(viewDestinationsButton);
            row.appendChild(destinationsCell);

            const createdAtCell = document.createElement('td');
            createdAtCell.textContent = route.created_at ? formatDateToFrench(parseDate(route.created_at)) : 'N/A';
            row.appendChild(createdAtCell);

            const updatedAtCell = document.createElement('td');
            updatedAtCell.textContent = route.updated_at ? formatDateToFrench(parseDate(route.updated_at)) : 'N/A';
            row.appendChild(updatedAtCell);

            tbody.appendChild(row);
        });

        backOfficeContent.appendChild(tbody);
        document.getElementById('loadingBodyGeneral').classList.add('hidden');

    } catch (error) {
        console.error('Error in populateRouteTable:', error.message);
        document.getElementById('loadingBodyGeneral').classList.add('hidden');
    }
}
