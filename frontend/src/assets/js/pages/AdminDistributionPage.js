import {deleteRoute} from "../api/Distributions.js";
import {populateRouteTable} from "../modules/tables/RouteTable.js";

function populateDistributionDate(){
    // Récupérer la date actuelle
    const today = new Date();
    console.log("today",today);

    document.getElementById('distributionDate').value = today.toISOString().split('T')[0];
}

async function deleteRoutes() {
    const selectedRadio = document.querySelector('input[name="RouteSelection"]:checked');
    const selectedDistributionId = selectedRadio ? selectedRadio.value : null;

    if (!selectedDistributionId) {
        alert("Veuillez sélectionner au moins une route à supprimer.");
        return;
    }

    // Afficher la boîte de confirmation
    const isConfirmed = confirm("Etes-vous sûr de vouloir supprimer cette collecte ? Cette action est définitive.");

    if (!isConfirmed) {
        return;
    }

    try {
        await deleteRoute(selectedDistributionId);
        alert("Route supprimée avec succés");
        window.location.reload();
    } catch (error) {
        console.error("Error deleting the Distribution:", error.message);
        alert("An error occurred while deleting the Distribution.");
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

document.addEventListener('DOMContentLoaded',
    function (){
        populateDistributionDate();
        document.getElementById('distributionDate').addEventListener('change', handleTableUpdate);

        document.getElementById('allDistributionDates').addEventListener('click', function () {
            // Appeler handleTableUpdate avec des paramètres par défaut
            populateRouteTable();
        });

        document.getElementById('completionSelector').addEventListener('change', handleTableUpdate);

        // Ajouter l'event listener au bouton
        document.getElementById('deleteRouteButton').addEventListener('click',function (){
            deleteRoutes();
        })

        populateRouteTable();
    });