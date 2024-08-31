async function createServiceSchedule(scheduleData) {
    try {
        const response = await fetch(apiEndpoint + '/service_schedules', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(scheduleData)
        });
        if (!response.ok) {
            throw new Error('Failed to create service schedule');
        }
        return await response.json();
    } catch (error) {
        console.error('Error creating service schedule:', error.message);
        throw error;
    }
}

async function getServiceScheduleByID(schedule_id) {
    try {
        const response = await fetch(apiEndpoint + '/service_schedules/id/' + schedule_id, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get service schedule');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting service schedule:', error.message);
        throw error;
    }
}

async function updateServiceSchedule(schedule_id, scheduleData) {
    try {
        const response = await fetch(apiEndpoint + '/service_schedules/id/' + schedule_id, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(scheduleData)
        });
        if (!response.ok) {
            throw new Error('Failed to update service schedule');
        }
        return await response.json();
    } catch (error) {
        console.error('Error updating service schedule:', error.message);
        throw error;
    }
}

async function deleteServiceSchedule(schedule_id) {
    try {
        const response = await fetch(apiEndpoint + '/service_schedules/id/' + schedule_id, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to delete service schedule');
        }
        return { message: 'Service schedule deleted successfully' };
    } catch (error) {
        console.error('Error deleting service schedule:', error.message);
        throw error;
    }
}

async function getAllServiceSchedules() {
    try {
        const response = await fetch(apiEndpoint + '/service_schedules', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get all service schedules');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting all service schedules:', error.message);
        throw error;
    }
}

export { createServiceSchedule, getServiceScheduleByID, updateServiceSchedule, deleteServiceSchedule, getAllServiceSchedules };
