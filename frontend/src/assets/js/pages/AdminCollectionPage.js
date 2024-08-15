import {populateCollectionTable} from "/assets/js/modules/tables/CollectionTable.js";
import {deleteCollection} from "../api/Collections.js";

function populateCollectionDate(){
    // Récupérer la date actuelle
    const today = new Date();

    // Formater la date en 'YYYY-MM-DD'
    // Mettre la date du jour comme valeur par défaut de l'input
    document.getElementById('collectionDate').value = today.toISOString().split('T')[0];
}

async function deleteCollections() {
    const selectedRadio = document.querySelector('input[name="collectionSelection"]:checked');
    const selectedCollectionId = selectedRadio ? selectedRadio.value : null;

    if (!selectedCollectionId) {
        alert("Veuillez sélectionner au moins une collecte à supprimer.");
        return;
    }

    // Afficher la boîte de confirmation
    const isConfirmed = confirm("Etes-vous sûr de vouloir supprimer cette collecte ? Cette action est définitive.");

    if (!isConfirmed) {
        return;
    }

    try {
        await deleteCollection(selectedCollectionId);
        alert("Collection supprimée avec succés");

    } catch (error) {
        console.error("Error deleting the collection:", error.message);
        alert("An error occurred while deleting the collection.");
    }
}

// Fonction pour gérer la mise à jour du tableau
function handleTableUpdate() {
    // Récupérer les valeurs des éléments de formulaire
    const selectedDate = document.getElementById('collectionDate').value;
    const selectedCompletion = document.getElementById('completionSelector').value;

    console.log('Selected Date:', selectedDate);
    console.log('Selected Completion:', selectedCompletion);

    // Appeler la fonction pour mettre à jour le tableau
    if (selectedCompletion !== null) {
        populateCollectionTable(selectedDate, selectedCompletion);
    }
    else{
        populateCollectionTable(selectedDate);
    }

}

document.addEventListener('DOMContentLoaded',
    function (){
        populateCollectionDate();
        document.getElementById('collectionDate').addEventListener('change', handleTableUpdate);

        document.getElementById('allCollectionDates').addEventListener('click', function () {
            // Appeler handleTableUpdate avec des paramètres par défaut
            populateCollectionTable();
        });

        document.getElementById('completionSelector').addEventListener('change', handleTableUpdate);

        // Ajouter l'event listener au bouton
        document.getElementById('deleteCollectionButton').addEventListener('click',function (){
            deleteCollections();
        })

        populateCollectionTable();
    });