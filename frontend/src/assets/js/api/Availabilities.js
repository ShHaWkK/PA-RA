async function createAvailability(data) {
    console.log("body", JSON.stringify(data));
    try {
        const response = await fetch(`${apiEndpoint}/availabilities`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        if (!response.ok) {
            throw new Error('Failed to create availability');
        }

        console.log("response",response);

        return await response.json();
    } catch (error) {
        console.error('Error creating availability:', error.message);
        throw error;
    }
}

async function getAvailability(id) {
    try {
        const response = await fetch(`${apiEndpoint}/availabilities/${id}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get availability');
        }

        console.log("response",response);

        return await response.json();
    } catch (error) {
        console.error('Error getting availability:', error.message);
        throw error;
    }
}

async function getAllAvailabilities() {
    try {
        const response = await fetch(`${apiEndpoint}/availabilities`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get all availabilities');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting all availabilities:', error.message);
        throw error;
    }
}

async function updateAvailability(id, data) {
    try {
        const response = await fetch(`${apiEndpoint}/availabilities/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        if (!response.ok) {
            throw new Error('Failed to update availability');
        }
        return await response.json();
    } catch (error) {
        console.error('Error updating availability:', error.message);
        throw error;
    }
}

async function deleteAvailability(id) {
    try {
        const response = await fetch(`${apiEndpoint}/availabilities/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to delete availability');
        }
        return { message: 'Availability deleted successfully' };
    } catch (error) {
        console.error('Error deleting availability:', error.message);
        throw error;
    }
}

export { createAvailability, getAvailability, getAllAvailabilities, updateAvailability, deleteAvailability };
