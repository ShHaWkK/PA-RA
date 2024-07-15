import { getAllUsers } from '/assets/js/api/Users.js';

export async function populateVolunteerTable() {
    const users = await getAllUsers('volunteer', null);

    if (!users || users.length === 0) {
        console.log('No volunteers found');
        return;
    }

    const table = document.createElement('table');
    table.border = '1';

    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');

    const headers = ['ID', 'First Name', 'Last Name', 'Email', 'Phone Number', 'Role', 'Status', 'Created At', 'Updated At'];
    headers.forEach(headerText => {
        const th = document.createElement('th');
        th.appendChild(document.createTextNode(headerText));
        headerRow.appendChild(th);
    });

    thead.appendChild(headerRow);
    table.appendChild(thead);

    const tbody = document.createElement('tbody');

    users.forEach(user => {
        const row = document.createElement('tr');

        const cells = [
            user.id,
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
            td.appendChild(document.createTextNode(cellText));
            row.appendChild(td);
        });

        tbody.appendChild(row);
    });

    table.appendChild(tbody);
    document.body.appendChild(table);
}