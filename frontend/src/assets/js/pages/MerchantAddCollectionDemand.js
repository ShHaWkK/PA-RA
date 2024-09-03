import {populateProductSelector} from "../modules/modals/DistributionModals.js";
import {createProductNotification} from "../api/ProductNotification.js";
import {getCookie} from "../api/Api.js";

document.addEventListener('DOMContentLoaded',function (){
    populateProductSelector('productSelect');

    document.getElementById('addNotificationForm').addEventListener('submit', async (event) => {
        event.preventDefault(); // Empêcher le rechargement de la page

        // Récupérer les valeurs du formulaire
        const companyId = parseInt(getCookie('company_id'), 10); // Convertir en entier
        const productId = parseInt(document.getElementById('productSelect').value, 10); // Convertir en entier
        const quantity = parseInt(document.getElementById('quantity').value, 10); // Convertir en entier
        const date = document.getElementById('date').value;
        const address = document.getElementById('address').value;

        // Préparer les données de notification
        const notificationData = {
            company_id: companyId,
            product_id: productId,
            notified_quantity: quantity,
            wished_collection_date: date,
            address: address
        };

        try {
            // Appeler la fonction API pour créer la notificationc
            console.log("notificationData",notificationData);
            const result = await createProductNotification(notificationData);
            alert('Notification created successfully!');

            // Optionnel : Rediriger l'utilisateur après succès
            window.location.href = "/Merchant/Collections";
        } catch (error) {
            alert('Failed to create notification: ' + error.message);
        }
    });
});