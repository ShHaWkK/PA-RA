// Path: /assets/js/modules/tables/ServiceTable.js

import { deleteService } from '/assets/js/api/Service.js';

async function populateServiceTable(serviceId) {
    if (!serviceId) {
        console.error('Service ID is undefined or not provided');
        return;
    }

    const serviceTableContainer = document.querySelector('.service-table');

    if (!serviceTableContainer) {
        console.error('Service table container not found');
        return;
    }

    serviceTableContainer.innerHTML = '';

    try {
        const response = await fetch(`${apiEndpoint}/services/${serviceId}`);
        
        if (!response.ok) {
            throw new Error(`Failed to fetch service: ${response.status} ${response.statusText}`);
        }
        
        const service = await response.json();

        // Populate the table with service data
        const table = document.createElement('table');
        table.classList.add('service-table');

        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');

        const headers = ['Service Name', 'Description', 'Category', 'Status', 'Capacity', 'Actions'];
        headers.forEach(headerText => {
            const th = document.createElement('th');
            th.textContent = headerText;
            headerRow.appendChild(th);
        });

        thead.appendChild(headerRow);
        table.appendChild(thead);

        const tbody = document.createElement('tbody');

        const row = document.createElement('tr');
        const cells = [
            service.name,
            service.description,
            service.category,
            service.status,
            service.capacity  // Add the capacity here
        ];

        cells.forEach(cellText => {
            const td = document.createElement('td');
            td.textContent = cellText;
            row.appendChild(td);
        });

        const actionTd = document.createElement('td');
        const editButton = document.createElement('button');
        editButton.textContent = 'Edit';
        editButton.addEventListener('click', () => editService(service.id));
        actionTd.appendChild(editButton);

        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Delete';
        deleteButton.addEventListener('click', () => deleteService(service.id));
        actionTd.appendChild(deleteButton);

        row.appendChild(actionTd);

        tbody.appendChild(row);

        table.appendChild(tbody);
        serviceTableContainer.appendChild(table);
    } catch (error) {
        console.error('An error occurred:', error);        
        serviceTableContainer.innerHTML = `<p>${error.message}</p>`;
    }
}

function editService(serviceId) {
    console.log("Editing service with ID:", serviceId);
    document.getElementById('serviceDetailModal').style.display = 'block';

    // Fetch service details using the serviceId
    fetch(`${apiEndpoint}/services/${serviceId}`)
        .then(response => response.json())
        .then(service => {
            // Populate the modal with service details
            document.getElementById('modalBodyServiceDetail').innerHTML = `
                <h2>${service.name}</h2>
                <p>Description: ${service.description}</p>
                <p>Category: ${service.category}</p>
                <p>Status: ${service.status}</p>
                <p>Capacity: ${service.capacity}</p>
            `;
        })
        .catch(error => {
            console.error('Error fetching service details:', error.message);
            document.getElementById('modalBodyServiceDetail').innerHTML = `<p>Error loading service details</p>`;
        });
}


// Export the functions to make them available for import in other files
export { populateServiceTable, editService };
