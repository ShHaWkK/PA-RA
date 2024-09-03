import {
    populateMerchantProductNotificationTable,} from "../modules/tables/ProductNotificationTable.js";
import {getCookie} from "../api/Api.js";
import {deleteProductNotification} from "../api/ProductNotification.js";
import {populateProductSelector} from "../modules/modals/DistributionModals.js";

async function deleteThisProductNotification() {
    // Récupérer la notification sélectionnée
    const selectedRadio = document.querySelector('input[name="NotificationSelection"]:checked');

    if (!selectedRadio) {
        alert("Please select a notification to delete.");
        return;
    }

    const notificationId = selectedRadio.value;
    const row = selectedRadio.closest('tr');

    // Récupérer les informations sur la notification (Is Collected et Is Assigned)
    const isCollected = row.querySelector('td:nth-child(7)').textContent.trim().toLowerCase() === 'yes';
    const isAssigned = row.querySelector('td:nth-child(8)').textContent.trim().toLowerCase() === 'yes';

    // Vérifier si la notification est assignée ou collectée
    if (isCollected || isAssigned) {
        alert("This notification cannot be deleted because it is either assigned or collected.");
        return;
    }

    try {
        // Appeler la fonction API pour supprimer la notification
        const result = await deleteProductNotification(notificationId);
        alert(result.message);

        window.location.reload();

    } catch (error) {
        alert("Failed to delete notification: " + error.message);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    let queryParameters = {};
    queryParameters['company_id'] = getCookie('company_id');

    populateMerchantProductNotificationTable(queryParameters);

    const statusSelect = document.getElementById('collectionDateStatus');
    const dateInput = document.getElementById('collectionDate');

    // Vérifiez si les éléments existent
    if (statusSelect && dateInput) {
        // Écouteur pour le changement de la date
        dateInput.addEventListener('change', async () => {
            // Récupérer la date sélectionnée
            const selectedDate = dateInput.value;
            let queryParameters = {};
            queryParameters['company_id'] = getCookie('company_id');

            // Ajouter la date sélectionnée aux queryParameters
            if (selectedDate) {
                queryParameters['date'] = selectedDate;
            }

            // Ajouter le statut sélectionné aux queryParameters
            const selectedStatus = statusSelect.value;
            if (selectedStatus && selectedStatus !== 'all') {
                if (selectedStatus === 'upcoming') {
                    queryParameters['is_collected'] = false;
                } else if (selectedStatus === 'completed') {
                    queryParameters['is_collected'] = true;
                } else if (selectedStatus === 'assigned') {
                    queryParameters['is_assigned'] = true;
                }
            }

            try {
                // Appeler la fonction populateProductNotificationTable avec les queryParameters mis à jour
                await populateMerchantProductNotificationTable(queryParameters);
            } catch (error) {
                console.error('Error fetching notifications:', error.message);
            }
        });

        // Écouteur pour le changement de statut
        statusSelect.addEventListener('change', async () => {
            // Récupérer le statut sélectionné
            const selectedStatus = statusSelect.value;
            let queryParameters = {};

            // Ajouter la date sélectionnée aux queryParameters
            const selectedDate = dateInput.value;
            if (selectedDate) {
                queryParameters['date'] = selectedDate;
            }

            // Ajouter le statut sélectionné aux queryParameters
            if (selectedStatus && selectedStatus !== 'all') {
                if (selectedStatus === 'upcoming') {
                    queryParameters['is_collected'] = false;
                } else if (selectedStatus === 'completed') {
                    queryParameters['is_collected'] = true;
                } else if (selectedStatus === 'assigned') {
                    queryParameters['is_assigned'] = true;
                }
            }

            try {
                // Appeler la fonction populateProductNotificationTable avec les queryParameters mis à jour
                await populateMerchantProductNotificationTable(queryParameters);
            } catch (error) {
                console.error('Error fetching notifications:', error.message);
            }
        });

    } else {
        console.error('Status select or date input element not found.');
    }

    const closeProductModal  = document.getElementById('closeProductButton');
    const productModal= document.getElementById('productDetailModal');

    closeProductModal.addEventListener('click',function (){
        productModal.style.display = "none";
    });

    document.getElementById('deleteNotificationButton').addEventListener('click', deleteThisProductNotification);
    document.getElementById('addNotificationButton').addEventListener('click',function (){
        window.location.href = "/Merchant/Collections/AddDemand";
    });


});