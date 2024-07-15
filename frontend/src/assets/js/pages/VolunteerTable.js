import { getAllUsers } from '/assets/js/api/Users.js';

export async function populateVolunteerTable() {
    const users = await getAllUsers('volunteer', null);

    if (!users || users.length === 0) {
        console.log('No volunteers found');
        return;
    }

    const table = document.createElement('table');
    table.classList.add('volunteer-table'); // Ajout de la classe volunteer-table pour le style

    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');

    const headers = ['', 'First Name', 'Last Name', 'Email', 'Phone Number', 'Role', 'Status', 'Created At', 'Updated At'];
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
            user.role,
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
    } else {
        console.error('Back office content container not found.');
    }
}