async function createWarehouse(warehouseData) {
    try {
        const response = await fetch(apiEndpoint + '/warehouses', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(warehouseData)
        });
        if (!response.ok) {
            throw new Error('Failed to create warehouse');
        }
        return await response.json();
    } catch (error) {
        console.error('Error creating warehouse:', error.message);
        throw error;
    }
}

async function getWarehouse(warehouseId) {
    try {
        const response = await fetch(apiEndpoint + '/warehouses/' + warehouseId, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get warehouse');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting warehouse:', error.message);
        throw error;
    }
}

async function updateWarehouse(warehouseId, warehouseData) {
    try {
        const response = await fetch(apiEndpoint + '/warehouses/' + warehouseId, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(warehouseData)
        });
        if (!response.ok) {
            throw new Error('Failed to update warehouse');
        }
        return await response.json();
    } catch (error) {
        console.error('Error updating warehouse:', error.message);
        throw error;
    }
}

async function deleteWarehouse(warehouseId) {
    try {
        const response = await fetch(apiEndpoint + '/warehouses/' + warehouseId, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to delete warehouse');
        }
        return { message: 'Warehouse deleted successfully' };
    } catch (error) {
        console.error('Error deleting warehouse:', error.message);
        throw error;
    }
}

async function getAllWarehouses() {
    try {
        const response = await fetch(apiEndpoint + '/warehouses', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get warehouses');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting warehouses:', error.message);
        throw error;
    }
}

export { createWarehouse, deleteWarehouse, getAllWarehouses, getWarehouse, updateWarehouse };
