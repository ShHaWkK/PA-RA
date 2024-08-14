import { createServiceSchedule, updateServiceSchedule, getServiceScheduleByID, deleteServiceSchedule } from "/assets/js/api/ServiceSchedule.js";

export async function populateServiceScheduleDetailsInModal(scheduleId) {
    const modalBody = document.getElementById('modalBodyServiceScheduleDetail');
    modalBody.innerHTML = '';

    try {
        const schedule = await getServiceScheduleByID(scheduleId);

        if (schedule) {
            const scheduleName = document.createElement('h3');
            scheduleName.textContent = schedule.name;

            const scheduleStartTime = document.createElement('p');
            scheduleStartTime.textContent = `Start Time: ${new Date(schedule.start_time).toLocaleString()}`;

            const scheduleEndTime = document.createElement('p');
            scheduleEndTime.textContent = `End Time: ${new Date(schedule.end_time).toLocaleString()}`;

            const scheduleLocation = document.createElement('p');
            scheduleLocation.textContent = `Location: ${schedule.location}`;

            modalBody.appendChild(scheduleName);
            modalBody.appendChild(scheduleStartTime);
            modalBody.appendChild(scheduleEndTime);
            modalBody.appendChild(scheduleLocation);
        } else {
            const noDetailsMessage = document.createElement('p');
            noDetailsMessage.textContent = 'No service schedule details found.';
            modalBody.appendChild(noDetailsMessage);
        }
    } catch (error) {
        console.error('Error fetching service schedule details:', error.message);
        const errorMessage = document.createElement('p');
        errorMessage.textContent = 'Error fetching service schedule details.';
        modalBody.appendChild(errorMessage);
    }
}

function addServiceScheduleSubmitEvent() {
    const form = document.getElementById('addServiceScheduleForm');

    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        const formData = new FormData(event.target);
        const scheduleData = Object.fromEntries(formData.entries());

        try {
            const result = await createServiceSchedule(scheduleData);
            alert('Service Schedule created successfully');
            form.reset();
            document.getElementById('addServiceScheduleModal').style.display = 'none';
        } catch (error) {
            console.error('Error creating service schedule:', error.message);
            document.getElementById('addServiceScheduleModal').style.display = 'none';
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const addScheduleButton = document.getElementById("addServiceScheduleButton");
    const scheduleModal = document.getElementById("addServiceScheduleModal");
    const scheduleSpan = document.getElementById("closeServiceScheduleAdd");

    addScheduleButton.onclick = function() {
        addServiceScheduleSubmitEvent();
        scheduleModal.style.display = "block";
    }

    scheduleSpan.onclick = function() {
        scheduleModal.style.display = "none";
    }
});
