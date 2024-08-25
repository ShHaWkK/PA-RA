import {deleteRoute, removeDestinationFromRoute} from "../api/Distributions.js";
import { populateRouteTable, selectedRouteId } from "../modules/tables/RouteTable.js";
import {populateDestinationsModal} from "../modules/modals/DistributionModals.js";

function populateDistributionDate() {
    // Récupérer la date actuelle
    const today = new Date();
    console.log("today", today);

    // Formater la date en 'YYYY-MM-DD' et la définir comme valeur par défaut
    document.getElementById('distributionDate').value = today.toISOString().split('T')[0];
}

async function deleteRoutes() {
    const selectedRadio = document.querySelector('input[name="RouteSelection"]:checked');
    const selectedDistributionId = selectedRadio ? selectedRadio.value : null;

    if (!selectedDistributionId) {
        alert("Veuillez sélectionner au moins une route à supprimer.");
        return;
    }

    selectedRouteId = selectedDistributionId;
    console.log("selectedRouteId",selectedRouteId);

    // Afficher la boîte de confirmation
    const isConfirmed = confirm("Etes-vous sûr de vouloir supprimer cette route ? Cette action est définitive.");

    if (!isConfirmed) {
        return;
    }

    try {
        await deleteRoute(selectedDistributionId);
        alert("Route supprimée avec succès");
        // Rafraîchir le tableau après la suppression
        await populateRouteTable();
    } catch (error) {
        console.error("Error deleting the Distribution:", error.message);
        alert("Une erreur est survenue lors de la suppression de la route.");
    }
}


async function deleteDestinations() {
    const selectedDestination = document.querySelector('input[name="destinationSelection"]:checked');
    const selectedDestinationId = selectedDestination ? selectedDestination.value : null;

    if (!selectedDestinationId) {
        alert("Veuillez sélectionner au moins une destination à supprimer.");
        return;
    }

    // Afficher la boîte de confirmation
    const isConfirmed = confirm("Etes-vous sûr de vouloir supprimer cette route ? Cette action est définitive.");

    if (!isConfirmed) {
        return;
    }

    try {
        console.log("selectedDestinationsID",selectedDestinationId);
        await removeDestinationFromRoute(selectedDestinationId);
        alert("Destination supprimée avec succès");
        // Rafraîchir le tableau après la suppression
        console.log("selectedRouteID",selectedRouteId);
        await populateDestinationsModal(selectedRouteId);
    } catch (error) {
        console.error("Error deleting the Distribution:", error.message);
        alert("Une erreur est survenue lors de la suppression de la route.");
    }
}

// Fonction pour gérer la mise à jour du tableau
function handleTableUpdate() {
    // Récupérer les valeurs des éléments de formulaire
    const selectedDate = document.getElementById('distributionDate').value;
    const selectedCompletion = document.getElementById('completionSelector').value;

    console.log('Selected Date:', selectedDate);
    console.log('Selected Completion:', selectedCompletion);

    // Créer l'objet des query parameters
    const queryParameters = {};

    // Ajouter les paramètres uniquement s'ils sont définis et non vides
    if (selectedDate) {
        queryParameters.start_date = selectedDate;
    }

    if (selectedCompletion) {
        queryParameters.status = selectedCompletion;
    }

    // Sérialiser les queryParameters en une chaîne de requête
    const queryString = new URLSearchParams(queryParameters).toString();
    console.log(`Serialized Query Parameters: ${queryString}`);

    // Appeler la fonction pour mettre à jour le tableau avec les query parameters
    populateRouteTable(queryString);
}

document.addEventListener('DOMContentLoaded', function() {
    // populateDistributionDate();

    // Attacher les gestionnaires d'événements
    document.getElementById('distributionDate').addEventListener('change', handleTableUpdate);
    document.getElementById('completionSelector').addEventListener('change', handleTableUpdate);

    // Événement pour afficher toutes les dates
    document.getElementById('allDistributionDates').addEventListener('click', function() {
        // Réinitialiser les filtres et appeler la fonction pour peupler le tableau
        document.getElementById('distributionDate').value = '';
        document.getElementById('completionSelector').value = '';
        populateRouteTable();
    });

    // Ajouter l'event listener aux boutons de suppression
    document.getElementById('deleteRouteButton').addEventListener('click', deleteRoutes);

    document.getElementById('deleteDestinationInModalButton').addEventListener('click', deleteDestinations);

    // Appeler initialement pour peupler le tableau
    populateRouteTable();
});