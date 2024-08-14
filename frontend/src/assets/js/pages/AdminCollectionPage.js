import {populateCollectionTable} from "/assets/js/modules/tables/CollectionTable.js";

function populateCollectionDate(){
    // Récupérer la date actuelle
    const today = new Date();

    // Formater la date en 'YYYY-MM-DD'
    // Mettre la date du jour comme valeur par défaut de l'input
    document.getElementById('collectionDate').value = today.toISOString().split('T')[0];
}

document.addEventListener('DOMContentLoaded',
    function (){
        populateCollectionTable();
        populateCollectionDate();
    });