import {populateVolunteerTable} from "/assets/js/modules/tables/VolunteerTable.js";
import {handleAprovals, deleteUser} from "/assets/js/api/Users.js";

var global_status = "";

document.getElementById('volunteer-status-select').addEventListener('change', async function() {
    global_status = this.value;
    console.log("status constructor",global_status);
    await populateVolunteerTable(global_status);
});

//----------------fonction pour l'approbation des utilisateurs-----------------------
async function handleStatusChange(status){
    const checkedCheckboxes = document.querySelectorAll('#volunteerTable input[type="checkbox"]:checked');
    const userIds = Array.from(checkedCheckboxes).map(checkbox => checkbox.value);

    if (userIds.length === 0) {
        alert("Select at least one user to do this action.");
    }
    else {
        var uri = `${apiEndpoint}/users/approval/1}`;

        console.log("uri", uri);
        console.log(JSON.stringify({status: status}));

        for (const userId of userIds) {
            await handleAprovals(userId, status);
        }
        console.log("status", global_status);
        await populateVolunteerTable(global_status);
    }
}

function handleApproval(){
    document.getElementById('approveBtn').addEventListener('click', function() {
        handleStatusChange('approved');
    });
}

function handleReject(){
    document.getElementById('rejectButton').addEventListener('click', function() {
        handleStatusChange('rejected');
    });
}

function handlePending(){
    document.getElementById('putOnHoldButton').addEventListener('click', function() {
        handleStatusChange('pending');
    });
}

// Fonction pour supprimer les utilisateurs sélectionnés
// Fonction pour supprimer les utilisateurs sélectionnés
async function deleteUsers() {
    const checkedCheckboxes = document.querySelectorAll('#volunteerTable input[type="checkbox"]:checked');
    const userIds = Array.from(checkedCheckboxes).map(checkbox => checkbox.value);

    if (userIds.length === 0) {
        alert("Veuillez sélectionner au moins un utilisateur à supprimer.");
        return;
    }

    try {
        for (const userId of userIds) {
            await deleteUser(userId);
        }
        // Rafraîchir le tableau des bénévoles après la suppression
        await populateVolunteerTable();
        alert("The selected users have been successfully deleted.\n");
    } catch (error) {
        console.error("Error deleting users:", error.message);
        alert("An error occurred while deleting users.");
    }
}

// Appel de la fonction au chargement de la page ou lorsque nécessaire
document.addEventListener('DOMContentLoaded',
    function (){
        populateVolunteerTable("");
        handleApproval();
        handleReject();
        handlePending();
    }
);