import {deleteRoute} from "../api/Distributions.js";
import {populateRouteTable} from "../modules/tables/RouteTable.js";

function populateDistributionDate(){
    // Récupérer la date actuelle
    const today = new Date();

    document.getElementById('distributionDate').value = today.toISOString().split('T')[0];
}

async function deleteRoutes() {
    const selectedRadio = document.querySelector('input[name="DistributionSelection"]:checked');
    const selectedDistributionId = selectedRadio ? selectedRadio.value : null;

    if (!selectedDistributionId) {
        alert("Veuillez sélectionner au moins une collecte à supprimer.");
        return;
    }

    // Afficher la boîte de confirmation
    const isConfirmed = confirm("Etes-vous sûr de vouloir supprimer cette collecte ? Cette action est définitive.");

    if (!isConfirmed) {
        return;
    }

    try {
        await deleteRoute(selectedDistributionId);
        alert("Collecte supprimée avec succés");
        populateRouteTable();
    } catch (error) {
        console.error("Error deleting the Distribution:", error.message);
        alert("An error occurred while deleting the Distribution.");
    }
}

// Fonction pour gérer la mise à jour du tableau
function handleTableUpdate() {
    // Récupérer les valeurs des éléments de formulaire
    const selectedDate = document.getElementById('DistributionDate').value;
    const selectedCompletion = document.getElementById('completionSelector').value;

    console.log('Selected Date:', selectedDate);
    console.log('Selected Completion:', selectedCompletion);

    // Appeler la fonction pour mettre à jour le tableau
    if (selectedCompletion !== null) {
        populateRouteTable(selectedDate, selectedCompletion);
    }
    else{
        populateRouteTable(selectedDate);
    }
}

document.addEventListener('DOMContentLoaded',
    function (){
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