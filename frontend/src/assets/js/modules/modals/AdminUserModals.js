import {modifyUser, getUser, deleteUser, getUserAvailabilities, getUserSkills, getUserCompanies} from "/assets/js/api/Users.js";
import {selectedUserId,populateVolunteerTable} from "/assets/js/modules/tables/VolunteerTable.js";
import {selectedUserIdMerchant,populateMerchantTable} from "/assets/js/modules/tables/MerchantTable.js";
import {addVolunteerSubmitEvent, populateSkillTable} from "/assets/js/pages/VolunteerSignUp.js";
import {addMerchantSubmitEvent} from "/assets/js/pages/MerchantSignUp.js";

// Fonction pour pré-remplir le formulaire avec les données de l'utilisateur
async function populateModifyUserForm(userId) {
    try {
        console.log("we are in populate modify");
        document.getElementById('modificationForm').classList.add('hidden');
        document.getElementById('loadingModification').classList.remove('hidden');

        const userData = await getUser(userId);
        document.getElementById('first_name_modify').value = userData.first_name;
        document.getElementById('last_name_modify').value = userData.last_name;
        document.getElementById('email_modify').value = userData.email;
        document.getElementById('phone_number_modify').value = userData.phone_number;
        document.getElementById('password_modify').value = userData.password;

        document.getElementById('loadingModification').classList.add('hidden');
        document.getElementById('modificationForm').classList.remove('hidden');
    } catch (error) {
        console.error('Erreur lors du pré-remplissage du formulaire:', error);
    }
}

// Fonction pour supprimer les utilisateurs sélectionnés
async function deleteUsers(type) {
    if (type == 'merchant'){
        const checkedCheckboxes = document.querySelectorAll('#merchantTable input[type="checkbox"]:checked');
    }else {
        const checkedCheckboxes = document.querySelectorAll('#volunteerTable input[type="checkbox"]:checked');
    }

    const userIds = Array.from(checkedCheckboxes).map(checkbox => checkbox.value);

    if (userIds.length === 0) {
        alert("Select at least one user to delete.");
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

// Fonction pour afficher les compétences dans la fenêtre modale
async function populateSkillsInModal(selectedUserId) {
    const modalBody = document.getElementById('modalBodySkill');

    // Vider le contenu précédent du corps de la modale
    modalBody.innerHTML = '';
    console.log("selected User ID", selectedUserId);

    document.getElementById('loadingSkills').classList.remove('hidden');

    // Obtenir les compétences de l'utilisasteur
    var skills = await getUserSkills(selectedUserId);
    console.log(skills);

    // Vérifier si skills est un tableau
    if (Array.isArray(skills) && skills.length > 0) {
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


    } else {
        // Afficher le message "No skills found"
        const noSkillsMessage = document.createElement('p');
        noSkillsMessage.textContent = 'No skills found for this user.';
        modalBody.appendChild(noSkillsMessage);
    }

    document.getElementById('loadingSkills').classList.add('hidden');
}

async function populateCompaniesInModal(selectedUserId) {
    const modalBody = document.getElementById('modalBodyCompany');

    // Vider le contenu précédent du corps de la modale
    modalBody.innerHTML = '';
    console.log("selected User ID", selectedUserId);

    document.getElementById('loadingCompanies').classList.remove('hidden');

    // Obtenir les entreprises de l'utilisateur
    var companies = await getUserCompanies(selectedUserId);
    console.log(companies);

    // Vérifier si companies est un tableau
    if (Array.isArray(companies) && companies.length > 0) {
        // Ajouter chaque entreprise au corps de la modale
        companies.forEach(company => {
            const companyElement = document.createElement('div');
            companyElement.classList.add('company');

            const companyName = document.createElement('h3');
            companyName.textContent = company.name;

            const companyAddress = document.createElement('p');
            companyAddress.textContent = 'Address: ' + company.address;

            const companyContact = document.createElement('p');
            companyContact.textContent = 'Contact Info: ' + company.contact_info;

            const companySiret = document.createElement('p');
            companySiret.textContent = 'SIRET: ' + company.siret;

            companyElement.appendChild(companyName);
            companyElement.appendChild(companyAddress);
            companyElement.appendChild(companyContact);
            companyElement.appendChild(companySiret);

            modalBody.appendChild(companyElement);
        });

    } else {
        // Afficher le message "No companies found"
        const noCompaniesMessage = document.createElement('p');
        noCompaniesMessage.textContent = 'No companies found for this user.';
        modalBody.appendChild(noCompaniesMessage);
    }
    document.getElementById('loadingCompanies').classList.add('hidden');
}

async function populateAvailabilitiesInModal(selectedUserId) {
    const modalBody = document.getElementById('modalBodyAvailabilities');

    // Vider le contenu précédent du corps de la modale
    modalBody.innerHTML = '';

    // On affiche le loader
    document.getElementById('loadingAvailabilities').classList.remove('hidden');

    var availabilities = await getUserAvailabilities(selectedUserId);
    console.log(availabilities);

    // Vérifier si availabilities est un tableau
    if (Array.isArray(availabilities) && availabilities.length > 0) {
        // Ajouter chaque disponibilité au corps de la modale
        availabilities.forEach(availability => {
            const availabilityElement = document.createElement('div');
            availabilityElement.classList.add('availability');

            const day = document.createElement('h3');
            day.textContent = `Day: ${availability.day_of_week}`;

            const timeRange = document.createElement('p');
            timeRange.textContent = `From: ${availability.start_time} - To: ${availability.end_time}`;

            availabilityElement.appendChild(day);
            availabilityElement.appendChild(timeRange);

            modalBody.appendChild(availabilityElement);
        });

    } else {
        const noAvailabilitiesMessage = document.createElement('p');
        noAvailabilitiesMessage.textContent = 'No availabilities found for this user.';
        modalBody.appendChild(noAvailabilitiesMessage);
    }
    // On retire le loader
    document.getElementById('loadingAvailabilities').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded',
    function (){
        //------------------- Définition des fenêtres modales: --------------------------
        // Fenêtre modale d'ajout d'un bénévole
        var addBtn = document.getElementById("addVolunteerButton");
        if(addBtn){
            var addModal = document.getElementById("addVolunteerModal");
            var addSpan = document.getElementById("closeAdd");

            addBtn.onclick = function() {
                document.getElementById('registrationForm').classList.remove('hidden');
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

            addVolunteerSubmitEvent();
            populateSkillTable();

            // Fenêtre modale de suppression d'un bénévole
            var deleteModal = document.getElementById("deleteVolunteerModal");
            var deleteBtn = document.getElementById("deleteVolunteerButton");
            var deleteSpan = document.getElementById("closeDelete");
            var confirmDelete = document.getElementById("confirmDelete");

            deleteBtn.onclick = function() {
                const checkedCheckboxes = document.querySelectorAll('#volunteerTable input[type="checkbox"]:checked');
                const userIds = Array.from(checkedCheckboxes).map(checkbox => checkbox.value);

                if (userIds.length === 0) {
                    alert("Select at least one user to delete.");
                    return;
                }
                else {
                    deleteModal.style.display = "block";
                }
            }

            deleteSpan.onclick = function() {
                deleteModal.style.display = "none";
            }

            confirmDelete.onclick = function (){
                deleteUsers('volunteer');
            }

            window.onclick = function(event) {
                if (event.target == deleteModal) {
                    deleteModal.style.display = "none";
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
        }

        // Fenêtre modale d'ajout d'un commerçant
        var addMerchantBtn = document.getElementById("addMerchantButton");
        if(addMerchantBtn){
            var addMerchantModal = document.getElementById("addMerchantModal");
            var addMerchantSpan = document.getElementById("closeMerchantAdd");

            addMerchantBtn.onclick = function() {
                document.getElementById('merchantForm').classList.remove('hidden');
                addMerchantModal.style.display = "block";
            }

            addMerchantSpan.onclick = function() {
                addMerchantModal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == addModal) {
                    addMerchantModal.style.display = "none";
                }
            }

            addMerchantSubmitEvent();

            // Fenêtre modale de suppression d'un bénévole
            var deleteModal = document.getElementById("deleteVolunteerModal");
            var deleteBtn = document.getElementById("deleteVolunteerButton");
            var deleteSpan = document.getElementById("closeDelete");
            var confirmDelete = document.getElementById("confirmDelete");

            deleteBtn.onclick = function() {
                const checkedCheckboxes = document.querySelectorAll('#merchantTable input[type="checkbox"]:checked');
                const userIds = Array.from(checkedCheckboxes).map(checkbox => checkbox.value);

                if (userIds.length === 0) {
                    alert("Select at least one user to delete.");
                    return;
                }
                else {
                    deleteModal.style.display = "block";
                }
            }

            deleteSpan.onclick = function() {
                deleteModal.style.display = "none";
            }

            confirmDelete.onclick = function (){
                deleteUsers('merchant');
            }

            window.onclick = function(event) {
                if (event.target == deleteModal) {
                    deleteModal.style.display = "none";
                }
            }

            // Fenêtre modale de vue des entreprises du commerçant
            var companyModal = document.getElementById("userCompaniesModal");
            var companySpan = document.getElementById("closeUserCompaniesButton");

            companySpan.onclick = function() {
                companyModal.style.display = "none";
            }
        }

        // Fenêtre modale de modification d'un utilisateur
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

        // Ajouter un écouteur d'événement au formulaire pour la soumission
        document.getElementById('modificationForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            let userId;

            if (selectedUserIdMerchant === undefined ){
                userId = selectedUserId;
            }
            else {
                userId = selectedUserIdMerchant;
            }
            const formData = {
                first_name: document.getElementById('first_name_modify').value,
                last_name: document.getElementById('last_name_modify').value,
                email: document.getElementById('email_modify').value,
                phone_number: document.getElementById('phone_number_modify').value,
                password: document.getElementById('password_modify').value
            };

            console.log("formData",formData);

            try {
                const result = await modifyUser(userId, formData);
                console.log('Modification succeeded:', result);
                alert('Modification succeeded:');
                populateVolunteerTable("");
                populateMerchantTable("");

                document.getElementById("modifyUserModal").style.display = "none";

            } catch (error) {
                console.error('Erreur lors de la modification de l\'utilisateur:', error);
                alert('Modification failed');
            }
        });
    });


export {populateModifyUserForm, populateSkillsInModal, populateAvailabilitiesInModal, populateCompaniesInModal}