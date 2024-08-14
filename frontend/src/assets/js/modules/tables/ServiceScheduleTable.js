// path : frontend/src/assets/js/modules/tables/ServiceScheduleTable.js
import { getAllServiceSchedules } from "/assets/js/api/ServiceSchedule.js";
import { populateServiceScheduleDetailsInModal } from "/assets/js/modules/modals/ServiceScheduleModals.js";

export async function populateServiceScheduleTable() {
    const scheduleTable = document.querySelector('.service-schedule-table');

    if (!scheduleTable) {
        console.error('Service Schedule table container not found');
        return;
    }

    scheduleTable.innerHTML = '';

    const table = document.createElement('table');
    table.classList.add('service-schedule-table');
    table.id = 'serviceScheduleTable';

    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');

    const headers = ['Name', 'Start Time', 'End Time', 'Location'];
    headers.forEach(headerText => {
        const th = document.createElement('th');
        th.textContent = headerText;
        headerRow.appendChild(th);
    });

    thead.appendChild(headerRow);
    table.appendChild(thead);

    scheduleTable.appendChild(table);

    try {
        const schedules = await getAllServiceSchedules();

        if (!schedules || schedules.length === 0) {
            console.log('No service schedules found');
            return;
        }

        const tbody = document.createElement('tbody');

        schedules.forEach(schedule => {
            const row = document.createElement('tr');
            row.dataset.scheduleId = schedule.id;

            const cells = [
                schedule.name,
                new Date(schedule.start_time).toLocaleString(),
                new Date(schedule.end_time).toLocaleString(),
                schedule.location
            ];

            cells.forEach(cellText => {
                const td = document.createElement('td');
                td.textContent = cellText;
                row.appendChild(td);
            });

            row.addEventListener('click', function() {
                populateServiceScheduleDetailsInModal(schedule.id);
                document.getElementById('serviceScheduleDetailModal').style.display = 'block';
            });

            tbody.appendChild(row);
        });

        table.appendChild(tbody);
    } catch (error) {
        console.error('Error loading service schedules:', error.message);
        scheduleTable.innerHTML = '<p>Error loading service schedules</p>';
    }
}
