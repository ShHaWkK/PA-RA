import {populateCollectionTable} from "/assets/js/modules/tables/CollectionTable.js";

console.log("bonjour");

function populateCollectionDate(){
    // Récupérer la date actuelle
    const today = new Date();

    // Formater la date en 'YYYY-MM-DD'
    // Mettre la date du jour comme valeur par défaut de l'input
    document.getElementById('collectionDate').value = today.toISOString().split('T')[0];
}

document.addEventListener('DOMContentLoaded',
    function (){
        populateCollectionDate();
        document.getElementById('collectionDate').addEventListener('change', function() {
            // Récupérer l'élément input
            const dateInput = document.getElementById('collectionDate');
            const selectedDate = dateInput.value;

            console.log('Selected Date:', selectedDate);
            populateCollectionTable(selectedDate);
        });
        
        // Afficher toutes les collectes peu importe la date
        document.getElementById('allCollectionDates').addEventListener('click', function (){
            populateCollectionTable();
        })
        populateCollectionTable();
    });