import {populateVolunteerTable} from "/assets/js/modules/tables/VolunteerTable.js";
import {handleAprovals, deleteUser} from "/assets/js/api/Users.js";
import {populateSkillTable , addVolunteerSubmitEvent} from "/assets/js/pages/VolunteerSignUp.js";

var global_status = "";

document.getElementById('volunteer-status-select').addEventListener('change', async function() {
    global_status = this.value;
    console.log("status constructor",global_status);
    await populateVolunteerTable(global_status);
});

//----------------fonction pour l'approbation des utilisateurs-----------------------
async function handleStatusChange(status){
    const checkedCheckboxes = document.querySelectorAll('#volunteerTable input[type="checkbox"]:checked');
    console.log("click");
    const userIds = Array.from(checkedCheckboxes).map(checkbox => checkbox.value);
    var uri=`${apiEndpoint}/users/approval/1}`;

    console.log("uri",uri);
    console.log(JSON.stringify({ status: status }));

    for (const userId of userIds) {
        await handleAprovals(userId,status);
    }
    console.log("status",global_status);
    await populateVolunteerTable(global_status);
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
async function deleteUsers() {
    const checkedCheckboxes = document.querySelectorAll('#volunteerTable input[type="checkbox"]:checked');
    const userIds = Array.from(checkedCheckboxes).map(checkbox => checkbox.value);

    if (userIds.length === 0) {
        alert("Veuillez sélectionner au moins un utilisateur à supprimer.");
        return;
    }

    // const confirmDelete = confirm("Êtes-vous sûr de vouloir supprimer les utilisateurs sélectionnés ?");
    // if (!confirmDelete) {
    //     return; // Annuler l'action si l'utilisateur ne confirme pas
    // }

    try {
        for (const userId of userIds) {
            await deleteUser(userId);
        }
        // Rafraîchir le tableau des bénévoles après la suppression
        await populateVolunteerTable(); // Vous devez implémenter cette fonction pour rafraîchir le tableau
        alert("Les utilisateurs sélectionnés ont été supprimés avec succès.");
    } catch (error) {
        console.error("Erreur lors de la suppression des utilisateurs :", error.message);
        alert("Une erreur est survenue lors de la suppression des utilisateurs.");
    }
}

// Appel de la fonction au chargement de la page ou lorsque nécessaire
document.addEventListener('DOMContentLoaded',
    function (){
        populateVolunteerTable("");
        handleApproval();
        handleReject();
        handlePending();
        console.log("Admin");
        // addVolunteerSubmitEvent();

        //------------------- Définition des fenêtres modales: --------------------------
        // Fenêtre modale d'ajout d'un bénévole
        var addModal = document.getElementById("addVolunteerModal");
        var addBtn = document.getElementById("addVolunteerButton");
        var addSpan = document.getElementById("closeAdd");

        //On peuple le tableau des compétences dans le tableau de skills:
        // populateSkillTable();

        addBtn.onclick = function() {
            addModal.style.display = "block";
        }

        addSpan.onclick = function() {
            addModal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == addModal) {
                addModal.style.display = "none";
            }
        }

        // Fenêtre modale de suppression d'un bénévole
        var deleteModal = document.getElementById("deleteVolunteerModal");
        var deleteBtn = document.getElementById("deleteVolunteerButton");
        var deleteSpan = document.getElementById("closeDelete");

        deleteBtn.onclick = function() {
            deleteModal.style.display = "block";
        }

        deleteSpan.onclick = function() {
            deleteModal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == deleteModal) {
                deleteModal.style.display = "none";
            }
        }

        // Fenêtre modale de modification d'un bénévole
        var modifyModal = document.getElementById("modifyUserModal");
        var modifyBtn = document.getElementsByClassName("modify-user-button");
        var modifySpan = document.getElementById("closeModify");

        modifySpan.onclick = function() {
            modifyModal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modifyModal) {
                modifyModal.style.display = "none";
            }
        }
    }
);