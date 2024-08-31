import { populateCollectionTable } from "/assets/js/modules/tables/CollectionTable.js";
import { deleteCollection } from "../api/Collections.js";

let allDates = true;

function populateCollectionDate() {
    // Récupérer la date actuelle
    const today = new Date();

    // Formater la date en 'YYYY-MM-DD'
    const formattedDate = today.toISOString().split('T')[0];
    // Mettre la date du jour comme valeur par défaut de l'input
    document.getElementById('collectionDate').value = formattedDate;
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
        alert("Collecte supprimée avec succès");
        // Rafraîchir le tableau après la suppression
        populateCollectionTable();
    } catch (error) {
        console.error("Error deleting the collection:", error.message);
        alert("Une erreur est survenue lors de la suppression de la collecte.");
    }
}

// Fonction pour gérer la mise à jour du tableau
function handleTableUpdate() {
    // Récupérer les valeurs des éléments de formulaire
    const selectedDate = allDates ? null : document.getElementById('collectionDate').value;
    const selectedCompletion = document.getElementById('completionSelector').value;

    console.log('Selected Date:', selectedDate);
    console.log('Selected Completion:', selectedCompletion);

    // Appeler la fonction pour mettre à jour le tableau avec les filtres
    populateCollectionTable(selectedDate, selectedCompletion);
}

document.addEventListener('DOMContentLoaded', function() {
    // populateCollectionDate();

    // Événement de changement sur la date
    document.getElementById('collectionDate').addEventListener('change', function() {
        allDates = false;
        handleTableUpdate();
    });

    // Événement pour afficher toutes les dates
    document.getElementById('allCollectionDates').addEventListener('click', function() {
        allDates = true;
        populateCollectionTable();
    });

    // Événement de changement sur le sélecteur de complétion
    document.getElementById('completionSelector').addEventListener('change', handleTableUpdate);

    // Ajouter l'event listener au bouton de suppression
    document.getElementById('deleteCollectionButton').addEventListener('click', deleteCollections);

    // Appeler initialement pour peupler le tableau
    populateCollectionTable();
});