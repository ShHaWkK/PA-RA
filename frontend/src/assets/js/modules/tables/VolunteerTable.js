import { getAllUsers } from '/assets/js/api/Users.js';
import { setupSearch } from '/assets/js/modules/SearchBar.js';
import {populateModifyUserForm,populateSkillsInModal,populateAvailabilitiesInModal} from "/assets/js/modules/modals/AdminUserModals.js";
import {formatDateToFrench} from "../FormatDate.js";

export var selectedUserId;

export async function populateVolunteerTable(status) {

    // On affiche le Loader
    document.getElementById('loadingBodyGeneral').classList.remove('hidden');

    const users = await getAllUsers('volunteer', status);

    if (!users || users.length === 0) {
        console.log('No volunteers found');
        document.querySelector('.volunteer-table').innerHTML = '';
        document.getElementById('loadingBodyGeneral').classList.add('hidden');
        return;
    }

    // On enlève le Loader
    document.getElementById('loadingBodyGeneral').classList.add('hidden');

    const table = document.createElement('table');
    table.classList.add('volunteer-table'); // Ajout de la classe volunteer-table pour le style
    table.id = 'volunteerTable';

    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');

    const headers = ['', 'First Name', 'Last Name', 'Email', 'Phone Number', 'Status', 'Availabilities', 'Skills', 'Modify', 'Created At', 'Updated At'];
    headers.forEach(headerText => {
        const th = document.createElement('th');
        th.textContent = headerText;
        headerRow.appendChild(th);
    });

    thead.appendChild(headerRow);
    table.appendChild(thead);

    const tbody = document.createElement('tbody');

    users.forEach(user => {
        const row = document.createElement('tr');
        row.dataset.userId = user.id; // Ajout de l'id utilisateur en tant que dataset

        // Ajout de la checkbox dans la première cellule
        const checkboxCell = document.createElement('td');
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.value = user.id; // Utilisation de l'id de l'utilisateur comme value de la checkbox
        checkboxCell.appendChild(checkbox);
        row.appendChild(checkboxCell);

        // Ajout des autres cellules avec les données de l'utilisateur
        const cells = [
            user.first_name,
            user.last_name,
            user.email,
            user.phone_number,
            user.status
        ];

        cells.forEach(cellText => {
            const td = document.createElement('td');
            td.textContent = cellText;
            row.appendChild(td);
        });

        // Ajout des boutons "Voir" pour "Availabilities" et "Skills"
        const availabilitiesButtonCell = document.createElement('td');
        const availabilitiesButton = document.createElement('button');
        availabilitiesButton.textContent = 'Voir';
        availabilitiesButton.onclick = () => viewAvailabilities(user.id);
        availabilitiesButtonCell.appendChild(availabilitiesButton);
        row.appendChild(availabilitiesButtonCell);

        const skillsButtonCell = document.createElement('td');
        const skillsButton = document.createElement('button');
        skillsButton.textContent = 'Voir';
        skillsButton.onclick = () => viewSkills(user.id);
        skillsButtonCell.appendChild(skillsButton);
        row.appendChild(skillsButtonCell);

        // Ajout du bouton "Modifier"
        const modifyButtonCell = document.createElement('td');
        const modifyButton = document.createElement('button');
        modifyButton.textContent = 'Modifier';
        modifyButton.id = 'modifyUserButton_' + user.id;
        modifyButton.className = 'modify-user-button';
        modifyButton.value = user.id;
        modifyButton.onclick = () => openModifyUserModal(user.id);
        modifyButtonCell.appendChild(modifyButton);
        row.appendChild(modifyButtonCell);

        // Ajout des dates dans les bonnes colonnes avec formatage
        const createdAtCell = document.createElement('td');
        createdAtCell.textContent = formatDateToFrench(user.created_at);
        row.appendChild(createdAtCell);

        const updatedAtCell = document.createElement('td');
        updatedAtCell.textContent = formatDateToFrench(user.updated_at);
        row.appendChild(updatedAtCell);

        tbody.appendChild(row);
    });

    table.appendChild(tbody);

    // Sélectionner le conteneur back-office-content et y attacher le tableau
    const backOfficeContent = document.querySelector('.volunteer-table');
    if (backOfficeContent) {
        backOfficeContent.innerHTML = ''; // Effacer le contenu existant si nécessaire
        backOfficeContent.appendChild(table);

        setupSearch(users,'volunteerTable'); // Configuration de la recherche
    } else {
        console.error('Back office content container not found.');
    }
}

// Fonction pour peupler le tableau des chauffeurs pour les collectes
export async function populateDriverTable() {

    // On affiche le Loader
    document.getElementById('loadingBodyDriver').classList.remove('hidden');

    const users = await getAllUsers('volunteer', 'approved');

    if (!users || users.length === 0) {
        console.log('No volunteers found');
        document.querySelector('.volunteer-table').innerHTML = '';
        document.getElementById('loadingBodyDriver').classList.add('hidden');
        return;
    }

    // On enlève le Loader
    document.getElementById('loadingBodyDriver').classList.add('hidden');

    const table = document.createElement('table');
    table.classList.add('volunteer-table'); // Ajout de la classe volunteer-table pour le style
    table.id = 'volunteerTable';

    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');

    const headers = ['', 'First Name', 'Last Name', 'Email', 'Phone Number', 'Status', 'Availabilities', 'Skills', 'Created At', 'Updated At'];
    headers.forEach(headerText => {
        const th = document.createElement('th');
        th.textContent = headerText;
        headerRow.appendChild(th);
    });

    thead.appendChild(headerRow);
    table.appendChild(thead);

    const tbody = document.createElement('tbody');

    users.forEach(user => {
        const row = document.createElement('tr');
        row.dataset.userId = user.id; // Ajout de l'id utilisateur en tant que dataset

        // Ajout du radio-button dans la première cellule
        const radioCell = document.createElement('td');
        const radioButton = document.createElement('input');
        radioButton.type = 'radio';
        radioButton.name = 'selectedVolunteer'; // Assurez-vous que tous les radio-buttons ont le même nom pour être exclusifs
        radioButton.value = user.id; // Utilisation de l'id de l'utilisateur comme value du radio-button
        radioCell.appendChild(radioButton);
        row.appendChild(radioCell);

        // Ajout des autres cellules avec les données de l'utilisateur
        const cells = [
            user.first_name,
            user.last_name,
            user.email,
            user.phone_number,
            user.status
        ];

        cells.forEach(cellText => {
            const td = document.createElement('td');
            td.textContent = cellText;
            row.appendChild(td);
        });

        // Ajout des boutons "Voir" pour "Availabilities" et "Skills"
        const availabilitiesButtonCell = document.createElement('td');
        const availabilitiesButton = document.createElement('button');
        availabilitiesButton.textContent = 'Voir';
        availabilitiesButton.onclick = () => viewAvailabilities(user.id);
        availabilitiesButtonCell.appendChild(availabilitiesButton);
        row.appendChild(availabilitiesButtonCell);

        const skillsButtonCell = document.createElement('td');
        const skillsButton = document.createElement('button');
        skillsButton.textContent = 'Voir';
        skillsButton.onclick = () => viewSkills(user.id);
        skillsButtonCell.appendChild(skillsButton);
        row.appendChild(skillsButtonCell);

        // Ajout des dates dans les bonnes colonnes avec formatage
        const createdAtCell = document.createElement('td');
        createdAtCell.textContent = formatDateToFrench(user.created_at);
        row.appendChild(createdAtCell);

        const updatedAtCell = document.createElement('td');
        updatedAtCell.textContent = formatDateToFrench(user.updated_at);
        row.appendChild(updatedAtCell);

        tbody.appendChild(row);
    });

    table.appendChild(tbody);

    // Sélectionner le conteneur back-office-content et y attacher le tableau
    const backOfficeContent = document.querySelector('.volunteer-table');
    if (backOfficeContent) {
        backOfficeContent.innerHTML = ''; // Effacer le contenu existant si nécessaire
        backOfficeContent.appendChild(table);

        setupSearch(users,'volunteerTable'); // Configuration de la recherche
    } else {
        console.error('Back office content container not found.');
    }
}

// Fonctions pour les boutons "Voir" et "Modifier"
function viewAvailabilities(userId) {
    selectedUserId = userId;
    populateAvailabilitiesInModal(selectedUserId);
    var availabilitiesModal = document.getElementById("volunteerAvailabilitiesModal");
    console.log("click on availabilities");
    availabilitiesModal.style.display = "block";
}

function viewSkills(userId) {
    selectedUserId = userId;
    populateSkillsInModal(selectedUserId);
    var skillModal = document.getElementById("volunteerSkillModal");
    console.log("click on skills");
    skillModal.style.display = "block";
}

function openModifyUserModal(userId) {
    selectedUserId = userId;
    var modifyModal = document.getElementById("modifyUserModal");
    populateModifyUserForm(userId);
    console.log("click on modify");
    modifyModal.style.display = "block";
}