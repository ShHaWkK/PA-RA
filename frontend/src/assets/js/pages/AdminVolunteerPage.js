import {populateVolunteerTable} from "/assets/js/modules/tables/VolunteerTable.js";

document.getElementById('volunteer-status-select').addEventListener('change', async function() {
    const status = this.value;
    console.log("status constructor",status);
    await populateVolunteerTable(status);
});


// Appel de la fonction au chargement de la page ou lorsque nécessaire
document.addEventListener('DOMContentLoaded', populateVolunteerTable(""));