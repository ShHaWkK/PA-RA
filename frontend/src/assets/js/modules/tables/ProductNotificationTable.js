import { formatDateToFrench } from "../FormatDate.js";
import { getAllProductNotifications } from "../../api/ProductNotification.js";
import { populateCompaniesInModal } from "../modals/NewCollectionModal.js";
import { populateProductDetailsInModal } from "../modals/StockModals.js";

export async function populateProductNotificationTable(queryParameters) {
    const loader = document.getElementById('loadingBodyNotification');
    const tableContainer = document.querySelector('.product-notification-table');

    try {
        // Vérifiez si le loader existe et l'affichez
        if (loader) {
            loader.classList.remove('hidden');
            console.log('Loader shown.');
        } else {
            console.error('Loader element not found.');
        }

        // Vérifiez si le conteneur de la table existe
        if (tableContainer) {
            tableContainer.classList.add('hidden');
        }else {
            console.error('Product notification table container not found.');
            return;
        }

        // Récupérer les notifications
        const notifications = await getAllProductNotifications(queryParameters);
        console.log("Retrieved notifications:", notifications);

        // Vérifiez si des notifications sont fournies
        if (!notifications || notifications.length === 0) {
            console.log('No product notifications found');
            tableContainer.innerHTML = '<p>No product collections set for this date.</p>'; // Afficher un message si aucune notification
            if (loader) {
                loader.classList.add('hidden');
                console.log('Loader hidden.');
            }
            return;
        }

        // Créer la structure de la table
        const table = document.createElement('table');
        table.classList.add('product-notification-table'); // Ajouter une classe pour le style

        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');

        // Définir les en-têtes de la table
        const headers = ['', 'Company', 'Product', 'Quantity', 'Address', 'Wished Collection Date', 'Notified At'];
        headers.forEach(headerText => {
            const th = document.createElement('th');
            th.textContent = headerText;
            headerRow.appendChild(th);
        });

        thead.appendChild(headerRow);
        table.appendChild(thead);

        // Créer le tbody
        const tbody = document.createElement('tbody');

        // Ajouter les lignes au tbody
        notifications.forEach(notification => {
            const row = document.createElement('tr');
            row.dataset.notificationId = notification.id;

            // Créer une cellule pour la checkbox
            const checkboxCell = document.createElement('td');
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.id = `notification-${notification.id}`;
            checkbox.value = notification.id;
            checkboxCell.appendChild(checkbox);

            // Créer les cellules de données pour chaque champ
            const companyCell = document.createElement('td');
            const companyLink = document.createElement('a');
            companyLink.href = "#";
            companyLink.textContent = notification.company.name;
            companyLink.setAttribute('data-id', notification.company.id);
            companyLink.addEventListener('click', (e) => {
                e.preventDefault();
                populateCompaniesInModal(notification.company.id);
                document.getElementById('userCompaniesModal').style.display = 'block';
            });
            companyCell.appendChild(companyLink);

            const productCell = document.createElement('td');
            const productLink = document.createElement('a');
            productLink.href = "#";
            productLink.textContent = notification.product.name;
            productLink.setAttribute('data-id', notification.product.id);
            productLink.addEventListener('click', (e) => {
                e.preventDefault();
                populateProductDetailsInModal(notification.product.id);
                document.getElementById('productDetailModal').style.display = 'block';
            });
            productCell.appendChild(productLink);

            const quantityCell = document.createElement('td');
            quantityCell.textContent = notification.notifiedQuantity;

            const addressCell = document.createElement('td');
            addressCell.textContent = notification.address;

            const wishedCollectionDateCell = document.createElement('td');
            wishedCollectionDateCell.textContent = formatDateToFrench(new Date(notification.wishedCollectionDate.timestamp * 1000));

            const notifiedAtCell = document.createElement('td');
            notifiedAtCell.textContent = formatDateToFrench(new Date(notification.notifiedAt.timestamp * 1000));

            // Ajouter toutes les cellules à la ligne
            row.appendChild(checkboxCell);
            row.appendChild(companyCell);
            row.appendChild(productCell);
            row.appendChild(quantityCell);
            row.appendChild(addressCell);
            row.appendChild(wishedCollectionDateCell);
            row.appendChild(notifiedAtCell);

            tbody.appendChild(row);
        });

        // Attacher le tbody à la table
        table.appendChild(tbody);

        // Réinitialiser le contenu du conteneur
        tableContainer.innerHTML = '';
        // Attacher la table complète au conteneur
        tableContainer.appendChild(table);

    } catch (error) {
        console.error('Error in populateProductNotificationTable:', error.message);
    } finally {
        // Retirer le loader
        if (loader) {
            loader.classList.add('hidden');
            console.log('Loader hidden.');
        }

        if (tableContainer) {
            tableContainer.classList.remove('hidden');
        }
    }
}

export async function populateMerchantProductNotificationTable(queryParameters) {
    const loader = document.getElementById('loadingBodyNotification');
    const tableContainer = document.querySelector('.product-notification-table');

    try {
        // Vérifiez si le loader existe et l'affichez
        if (loader) {
            loader.classList.remove('hidden');
            console.log('Loader shown.');
        } else {
            console.error('Loader element not found.');
        }

        // Vérifiez si le conteneur de la table existe
        if (tableContainer) {
            tableContainer.classList.add('hidden');
        } else {
            console.error('Product notification table container not found.');
            return;
        }

        // Récupérer les notifications
        const notifications = await getAllProductNotifications(queryParameters);
        console.log("Retrieved notifications:", notifications);

        // Vérifiez si des notifications sont fournies
        if (!notifications || notifications.length === 0) {
            console.log('No product notifications found');
            tableContainer.innerHTML = '<p>No product collections set for this date.</p>'; // Afficher un message si aucune notification
            if (loader) {
                loader.classList.add('hidden');
                console.log('Loader hidden.');
            }
            return;
        }

        // Créer la structure de la table
        const table = document.createElement('table');
        table.classList.add('product-notification-table'); // Ajouter une classe pour le style

        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');

        // Définir les en-têtes de la table, sans la colonne "Company" et ajout des colonnes "Is Collected" et "Is Assigned"
        const headers = ['', 'Product', 'Quantity', 'Address', 'Wished Collection Date', 'Notified At', 'Is Collected', 'Is Assigned'];
        headers.forEach(headerText => {
            const th = document.createElement('th');
            th.textContent = headerText;
            headerRow.appendChild(th);
        });

        thead.appendChild(headerRow);
        table.appendChild(thead);

        // Créer le tbody
        const tbody = document.createElement('tbody');

        // Ajouter les lignes au tbody
        notifications.forEach(notification => {
            const row = document.createElement('tr');
            row.dataset.notificationId = notification.id;

            // Créer une cellule pour la checkbox
            const radioCell = document.createElement('td');
            const radioButton = document.createElement('input');
            radioButton.type = 'radio';
            radioButton.name = 'NotificationSelection';
            radioButton.value = notification.id;
            radioCell.appendChild(radioButton);
            row.appendChild(radioCell);

            // Créer les cellules de données pour chaque champ
            const productCell = document.createElement('td');
            const productLink = document.createElement('a');
            productLink.href = "#";
            productLink.textContent = notification.product.name;
            productLink.setAttribute('data-id', notification.product.id);
            productLink.addEventListener('click', (e) => {
                e.preventDefault();
                populateProductDetailsInModal(notification.product.id);
                document.getElementById('productDetailModal').style.display = 'block';
            });
            productCell.appendChild(productLink);

            const quantityCell = document.createElement('td');
            quantityCell.textContent = notification.notifiedQuantity;

            const addressCell = document.createElement('td');
            addressCell.textContent = notification.address;

            const wishedCollectionDateCell = document.createElement('td');
            wishedCollectionDateCell.textContent = formatDateToFrench(new Date(notification.wishedCollectionDate.timestamp * 1000));

            const notifiedAtCell = document.createElement('td');
            notifiedAtCell.textContent = formatDateToFrench(new Date(notification.notifiedAt.timestamp * 1000));

            // Ajouter les cellules pour 'Is Collected' et 'Is Assigned'
            const isCollectedCell = document.createElement('td');
            isCollectedCell.textContent = notification.is_collected ? 'Yes' : 'No';

            const isAssignedCell = document.createElement('td');
            isAssignedCell.textContent = notification.is_assigned ? 'Yes' : 'No';

            // Ajouter toutes les cellules à la ligne
            row.appendChild(radioCell);
            row.appendChild(productCell);
            row.appendChild(quantityCell);
            row.appendChild(addressCell);
            row.appendChild(wishedCollectionDateCell);
            row.appendChild(notifiedAtCell);
            row.appendChild(isCollectedCell);
            row.appendChild(isAssignedCell);

            tbody.appendChild(row);
        });

        // Attacher le tbody à la table
        table.appendChild(tbody);

        // Réinitialiser le contenu du conteneur
        tableContainer.innerHTML = '';
        // Attacher la table complète au conteneur
        tableContainer.appendChild(table);

    } catch (error) {
        console.error('Error in populateProductNotificationTable:', error.message);
    } finally {
        // Retirer le loader
        if (loader) {
            loader.classList.add('hidden');
            console.log('Loader hidden.');
        }

        if (tableContainer) {
            tableContainer.classList.remove('hidden');
        }
    }
}