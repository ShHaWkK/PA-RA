async function createVehicle(vehicleData) {
    try {
        const response = await fetch(apiEndpoint + '/vehicles', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(vehicleData)
        });
        if (!response.ok) {
            throw new Error('Failed to create vehicle');
        }
        return await response.json();
    } catch (error) {
        console.error('Error creating vehicle:', error.message);
        throw error;
    }
}

async function getVehicleByID(vehicle_id) {
    try {
        const response = await fetch(apiEndpoint + '/vehicles/' + vehicle_id, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get vehicle');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting vehicle:', error.message);
        throw error;
    }
}

async function updateVehicle(vehicle_id, vehicleData) {
    try {
        const response = await fetch(apiEndpoint + '/vehicles/' + vehicle_id, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(vehicleData)
        });
        if (!response.ok) {
            throw new Error('Failed to update vehicle');
        }
        return await response.json();
    } catch (error) {
        console.error('Error updating vehicle:', error.message);
        throw error;
    }
}

async function deleteVehicle(vehicle_id) {
    try {
        const response = await fetch(apiEndpoint + '/vehicles/' + vehicle_id, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to delete vehicle');
        }
        return { message: 'Vehicle deleted successfully' };
    } catch (error) {
        console.error('Error deleting vehicle:', error.message);
        throw error;
    }
}

async function getAllVehicles() {
    try {
        const response = await fetch(apiEndpoint + '/vehicles', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get all vehicles');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting all vehicles:', error.message);
        throw error;
    }
}

export { createVehicle, getVehicleByID, updateVehicle, deleteVehicle, getAllVehicles };
