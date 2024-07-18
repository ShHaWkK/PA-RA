import {modifyUser, getUser} from "/assets/js/api/Users.js";
import {selectedUserId} from "/assets/js/modules/tables/VolunteerTable.js";
import {getUserAvailabilities, getUserSkills} from "../../api/Users.js";

// Fonction pour pré-remplir le formulaire avec les données de l'utilisateur
async function populateModifyUserForm(userId) {
    try {
        console.log("populateForm");
        const userData = await getUser(userId);
        document.getElementById('first_name_modify').value = userData.first_name;
        document.getElementById('last_name_modify').value = userData.last_name;
        document.getElementById('email_modify').value = userData.email;
        document.getElementById('phone_number_modify').value = userData.phone_number;
        document.getElementById('password_modify').value = userData.password;
    } catch (error) {
        console.error('Erreur lors du pré-remplissage du formulaire:', error);
    }
}

// Ajouter un écouteur d'événement au formulaire pour la soumission
document.getElementById('modificationForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const userId = selectedUserId;
    const formData = {
        first_name: document.getElementById('first_name_modify').value,
        last_name: document.getElementById('last_name_modify').value,
        email: document.getElementById('email_modify').value,
        phone_number: document.getElementById('phone_number_modify').value,
        password: document.getElementById('password_modify').value
    };

    try {
        const result = await modifyUser(userId, formData);
        console.log('Modification succeeded:', result);
        alert('Modification succeeded:');
    } catch (error) {
        console.error('Erreur lors de la modification de l\'utilisateur:', error);
        alert('Modification failed');
    }
});

// Fonctions pour les boutons "Voir" et "Modifier"
function viewAvailabilities(userId) {
    selectedUserId = userId;
    var availabilitiesModal = document.getElementById("volunteerSkillModal");
    console.log("click on availabilities");
    modifyModal.style.display = "block";
}

function viewSkills(userId) {
    selectedUserId = userId;
    var modifyModal = document.getElementById("volunteerSkillModal");
    console.log("click on skills");
    modifyModal.style.display = "block";
}

function openModifyUserModal(userId) {
    selectedUserId = userId;
    var modifyModal = document.getElementById("modifyUserModal");
    populateModifyUserForm(userId);
    console.log("click on modify");
    modifyModal.style.display = "block";
}

// Fonction pour afficher les compétences dans la fenêtre modale
async function populateSkillsInModal(selectedUserId) {
    const modalBody = document.getElementById('modalBodySkill');

    // Vider le contenu précédent du corps de la modale
    modalBody.innerHTML = '';
    console.log("selected User ID",selectedUserId);
    var skills = await getUserSkills(selectedUserId);
    console.log(skills);

    // Ajouter chaque compétence au corps de la modale
    skills.forEach(skill => {
        const skillElement = document.createElement('div');
        skillElement.classList.add('skill');

        const skillName = document.createElement('h3');
        skillName.textContent = skill.name;

        const skillDescription = document.createElement('p');
        skillDescription.textContent = skill.description;

        skillElement.appendChild(skillName);
        skillElement.appendChild(skillDescription);

        modalBody.appendChild(skillElement);
    });
}

function populateAvailabilitiesInModal(selectedUserId) {
    const modalBody = document.getElementById('modalBodyAvailabilities');

    // Vider le contenu précédent du corps de la modale
    modalBody.innerHTML = '';

   var availabilities = getUserAvailabilities(selectedUserId);

    // Vérifier si availabilities est un tableau
    if (Array.isArray(availabilities) && availabilities.length > 0) {
        // Ajouter chaque disponibilité au corps de la modale
        availabilities.forEach(availability => {
            const availabilityElement = document.createElement('div');
            availabilityElement.classList.add('availability');

            const day = document.createElement('h3');
            day.textContent = `Day: ${availability.day}`;

            const timeRange = document.createElement('p');
            timeRange.textContent = `From: ${availability.startTime} - To: ${availability.endTime}`;

            availabilityElement.appendChild(day);
            availabilityElement.appendChild(timeRange);

            modalBody.appendChild(availabilityElement);
        });
    } else {
        const noAvailabilitiesMessage = document.createElement('p');
        noAvailabilitiesMessage.textContent = 'No availabilities found for this user.';
        modalBody.appendChild(noAvailabilitiesMessage);
    }
}

document.addEventListener('DOMContentLoaded',
    function (){
        //------------------- Définition des fenêtres modales: --------------------------
        // Fenêtre modale d'ajout d'un bénévole
        var addModal = document.getElementById("addVolunteerModal");
        var addBtn = document.getElementById("addVolunteerButton");
        var addSpan = document.getElementById("closeAdd");

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
        var modifySpan = document.getElementById("closeModify");

        modifySpan.onclick = function() {
            modifyModal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modifyModal) {
                modifyModal.style.display = "none";
            }
        }

        // Fenêtre modale de vue des compétences
        var skillModal = document.getElementById("volunteerSkillModal");
        var skillSpan = document.getElementById("closeVolunteerSkillButton");

        skillSpan.onclick = function() {
            skillModal.style.display = "none";
        }

        // Fenêtre modale de vue des disponibilités
        var availabilityModal = document.getElementById("volunteerAvailabilitiesModal");
        var availabilitySpan = document.getElementById("closeVolunteerAvailabilitiesButton");

        availabilitySpan.onclick = function() {
            availabilityModal.style.display = "none";
        }

    });


export {populateModifyUserForm, populateSkillsInModal, populateAvailabilitiesInModal}