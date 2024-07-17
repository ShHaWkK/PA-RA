import {modifyUser, getUser} from "/assets/js/api/Users.js";
import {selectedUserId} from "/assets/js/modules/tables/VolunteerTable.js";

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

export {populateModifyUserForm}