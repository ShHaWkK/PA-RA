import { formatDateToFrench } from "../FormatDate.js";
import { getAllProductNotifications } from "../../api/ProductNotification.js";

export async function populateProductNotificationTable(queryParameters) {
    try {
        // Afficher le loader
        document.getElementById('loadingBodyGeneral').classList.remove('hidden');

        // Sélectionner la table et le tbody
        const productNotificationTable = document.querySelector('.product-notification-table table');
        if (!productNotificationTable) {
            console.error('Product notification table not found.');
            return;
        }

        let tbody = productNotificationTable.querySelector('tbody');
        if (!tbody) {
            tbody = document.createElement('tbody');
            productNotificationTable.appendChild(tbody);
        }

        // Effacer le contenu existant du tbody
        tbody.innerHTML = '';

        // Récupérer les notifications
        const notifications = await getAllProductNotifications(queryParameters);
        console.log("notifications", notifications);

        // Vérifier si des notifications sont fournies
        if (!notifications || notifications.length === 0) {
            console.log('No product notifications found');
            document.getElementById('loadingBodyGeneral').classList.add('hidden');
            return;
        }

        // Ajouter les lignes au tbody
        notifications.forEach(notification => {
            const row = document.createElement('tr');
            row.dataset.notificationId = notification.id; // Ajout de l'id de la notification en tant que dataset

            // Créer une cellule pour la checkbox
            const checkboxCell = document.createElement('td');
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.id = `notification-${notification.id}`; // Utiliser l'id de la notification
            checkbox.value = notification.id; // Optionnel, si vous avez besoin de la valeur lors de la soumission
            checkboxCell.appendChild(checkbox);

            // Créer les cellules de données pour chaque champ
            const companyCell = document.createElement('td');
            companyCell.textContent = notification.company.name;

            const productCell = document.createElement('td');
            productCell.textContent = notification.product.name;

            const quantityCell = document.createElement('td');
            quantityCell.textContent = notification.notifiedQuantity;

            const addressCell = document.createElement('td');
            addressCell.textContent = notification.address;

            const wishedCollectionDateCell = document.createElement('td');
            wishedCollectionDateCell.textContent = formatDateToFrench(new Date(notification.wishedCollectionDate.timestamp * 1000));

            const notifiedAtCell = document.createElement('td');
            notifiedAtCell.textContent = formatDateToFrench(new Date(notification.notifiedAt.timestamp * 1000));

            // Ajouter toutes les cellules à la ligne
            row.appendChild(checkboxCell); // Ajouter la cellule de la checkbox en premier
            row.appendChild(companyCell);
            row.appendChild(productCell);
            row.appendChild(quantityCell);
            row.appendChild(addressCell);
            row.appendChild(wishedCollectionDateCell);
            row.appendChild(notifiedAtCell);

            tbody.appendChild(row);
        });

    } catch (error) {
        console.error('Error in populateProductNotificationTable:', error.message);
    } finally {
        // Retirer le loader
        document.getElementById('loadingBodyGeneral').classList.add('hidden');
    }
}