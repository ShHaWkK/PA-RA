import { getAllUsers } from '/assets/js/api/Users.js';
import { setupSearch } from '/assets/js/modules/SearchBar.js';

export async function populateVolunteerTable(status) {
    console.log("status in populateVolunteerTable",status);

    const users = await getAllUsers('volunteer', status);

    console.log("users",users);

    if (!users || users.length === 0) {
        console.log('No volunteers found');
        document.querySelector('.volunteer-table').innerHTML = '';
        return;
    }

    const table = document.createElement('table');
    table.classList.add('volunteer-table'); // Ajout de la classe volunteer-table pour le style
    table.id = 'volunteerTable';

    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');

    const headers = ['', 'First Name', 'Last Name', 'Email', 'Phone Number', 'Status', 'Created At', 'Updated At'];
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
            user.status,
            user.created_at,
            user.updated_at
        ];

        cells.forEach(cellText => {
            const td = document.createElement('td');
            td.textContent = cellText;
            row.appendChild(td);
        });

        tbody.appendChild(row);
    });

    table.appendChild(tbody);

    // Sélectionner le conteneur back-office-content et y attacher le tableau
    const backOfficeContent = document.querySelector('.volunteer-table');
    if (backOfficeContent) {
        backOfficeContent.innerHTML = ''; // Effacer le contenu existant si nécessaire
        backOfficeContent.appendChild(table);

        setupSearch(users); // Configuration de la recherche
    } else {
        console.error('Back office content container not found.');
    }
}
