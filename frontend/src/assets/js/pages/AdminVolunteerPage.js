import {populateVolunteerTable} from "/assets/js/modules/tables/VolunteerTable.js";
import {handleAprovals} from "/assets/js/api/Users.js";

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

// Appel de la fonction au chargement de la page ou lorsque nécessaire
document.addEventListener('DOMContentLoaded',
    function (){
        populateVolunteerTable("");
        handleApproval();
        handleReject();
        handlePending();

        //------------------- Définition des fenêtres modales: --------------------------
        var modal = document.getElementById("addVolunteerModal");
        var btn = document.getElementById("addVolunteerButton");
        var span = document.getElementsByClassName("close")[0];

        btn.onclick = function() {
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        document.getElementById('registrationForm').onsubmit = function(event) {
            event.preventDefault();
            alert('Form submitted!');
            modal.style.display = "none";
        }
    }
);