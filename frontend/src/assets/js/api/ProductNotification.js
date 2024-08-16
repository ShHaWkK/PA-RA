async function createProductNotification(notificationData) {
    try {
        const response = await fetch(apiEndpoint + '/product_notifications', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(notificationData)
        });

        if (!response.ok) {
            throw new Error('Failed to create product notification');
        }

        return await response.json();
    } catch (error) {
        console.error('Error creating product notification:', error.message);
        throw error;
    }
}

async function getProductNotification(id) {
    try {
        const response = await fetch(apiEndpoint + '/product_notifications/' + id, {
            method: 'GET'
        });

        if (!response.ok) {
            throw new Error('Failed to get product notification');
        }

        return await response.json();
    } catch (error) {
        console.error('Error fetching product notification:', error.message);
        throw error;
    }
}

async function updateProductNotification(id, updateData) {
    try {
        const response = await fetch(apiEndpoint + '/product_notifications/' + id, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(updateData)
        });

        if (!response.ok) {
            throw new Error('Failed to update product notification');
        }

        return await response.json();
    } catch (error) {
        console.error('Error updating product notification:', error.message);
        throw error;
    }
}

async function deleteProductNotification(id) {
    try {
        const response = await fetch(apiEndpoint + '/product_notifications/' + id, {
            method: 'DELETE'
        });

        if (!response.ok) {
            throw new Error('Failed to delete product notification');
        }

        return await response.json();
    } catch (error) {
        console.error('Error deleting product notification:', error.message);
        throw error;
    }
}

async function getAllProductNotifications(queryParams) {
    try {
        const queryString = new URLSearchParams(queryParams).toString();
        const response = await fetch(apiEndpoint + '/product_notifications?' + queryString, {
            method: 'GET'
        });

        console.log(response);

        if (!response.ok) {
            throw new Error('Failed to get product notifications');
        }

        return await response.json();
    } catch (error) {
        console.error('Error fetching all product notifications:', error.message);
        throw error;
    }
}

export { createProductNotification, getProductNotification, updateProductNotification, deleteProductNotification, getAllProductNotifications}