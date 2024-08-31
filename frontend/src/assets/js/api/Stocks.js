async function createStock(stockData) {
    try {
        const response = await fetch(apiEndpoint + '/stocks', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(stockData),
        });

        // Check if the response is not ok (status not in the range 200-299)
        if (!response.ok) {
            // You can extract the status code and any message from the response here
            const errorData = await response.json();

            // Handle specific status codes
            if (response.status === 400) {
                console.error('Error 400: Bad Request -', errorData.message);
                alert("This warehouse doesn't have the capacity to contain this stock");
            } else {
                console.error(`Error ${response.status}: ${response.statusText}`);
                alert('An error occurred while adding the stock');
            }

            // Throw an error to be caught by the catch block
            throw new Error(`Failed to create stock: ${response.status} ${response.statusText}`);
        }

        // If the request was successful, return the JSON data
        return await response.json();
    } catch (error) {
        console.error('Error creating stock:', error.message);
        throw error;
    }
}

async function getStock(stockId) {
    try {
        const response = await fetch(apiEndpoint + '/stocks/' + stockId, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get stock');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting stock:', error.message);
        throw error;
    }
}

async function updateStock(stockId, stockData) {
    try {
        const response = await fetch(apiEndpoint + '/stocks/' + stockId, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(stockData)
        });
        if (!response.ok) {
            throw new Error('Failed to update stock');
        }
        return await response.json();
    } catch (error) {
        console.error('Error updating stock:', error.message);
        throw error;
    }
}

async function deleteStock(stockId) {
    try {
        const response = await fetch(apiEndpoint + '/stocks/' + stockId, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to delete stock');
        }
        return { message: 'Stock deleted successfully' };
    } catch (error) {
        console.error('Error deleting stock:', error.message);
        throw error;
    }
}

async function getAllStocks() {
    try {
        const response = await fetch(apiEndpoint + '/stocks', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get stocks');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting stocks:', error.message);
        throw error;
    }
}

async function getStockByWarehouse(warehouseId) {
    try {
        const response = await fetch(apiEndpoint + '/stocks/getStocksByWarehouse/' + warehouseId, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.status === 400) {
            const errorData = await response.json();
            console.error('Bad Request:', errorData.message);
            const error = new Error('Bad Request');
            error.status = 400;
            throw error;
        }

        if (response.status === 404) {
            const errorData = await response.json();
            console.error('Not Found:', errorData.message);
            const error = new Error('Not Found');
            error.status = 404;
            throw error;
        }

        if (!response.ok) {
            throw new Error('Failed to get stocks');
        }

        return await response.json();
    } catch (error) {
        console.error('Error getting stocks:', error.message);
        throw error;
    }
}


export { createStock, getStock, updateStock, deleteStock, getAllStocks, getStockByWarehouse };