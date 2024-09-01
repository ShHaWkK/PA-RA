
async function createService(serviceData) {
    try {
        const response = await fetch(apiEndpoint + '/services', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(serviceData)
        });
        if (!response.ok) {
            throw new Error('Failed to create service');
        }
        return await response.json();
    } catch (error) {
        console.error('Error creating service:', error.message);
        throw error;
    }
}

async function getServiceByID(serviceId) {
    try {
        const response = await fetch(apiEndpoint + '/services/' + serviceId, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        // Check if the response is not ok (status not in the range 200-299)
        if (!response.ok) {
            const errorData = await response.json();

            // Handle specific status codes
            if (response.status === 400) {
                console.error('Error 400: Bad Request -', errorData.message);
                alert("Bad Request: " + errorData.message);
            } else if (response.status === 404) {
                console.error('Error 404: Not Found -', errorData.message);
                alert("Service not found: " + errorData.message);
            } else {
                console.error(`Error ${response.status}: ${response.statusText}`);
                alert('An error occurred while fetching the service');
            }

            throw new Error(`Failed to get service: ${response.status} ${response.statusText}`);
        }

        return await response.json();
    } catch (error) {
        console.error('Error getting service:', error.message);
        throw error;
    }
}

async function updateService(service_id, serviceData) {
    try {
        const response = await fetch(apiEndpoint + '/services/id/' + service_id, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(serviceData)
        });
        if (!response.ok) {
            throw new Error('Failed to update service');
        }
        return await response.json();
    } catch (error) {
        console.error('Error updating service:', error.message);
        throw error;
    }
}

async function deleteService(service_id) {
    try {
        const response = await fetch(apiEndpoint + '/services/id/' + service_id, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to delete service');
        }
        return { message: 'Service deleted successfully' };
    } catch (error) {
        console.error('Error deleting service:', error.message);
        throw error;
    }
}

async function getAllServices() {
    try {
        const response = await fetch(apiEndpoint + '/services', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get all services');
        }

        const result = await response.json();

        console.log("result",result);

        return result;
    } catch (error) {
        console.error('Error getting all services:', error.message);
        throw error;
    }
}

async function getServiceCapacity(service_id) {
    try {
        const response = await fetch(apiEndpoint + '/services/' + service_id + '/capacity', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get service capacity');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting service capacity:', error.message);
        throw error;
    }
}

async function getServicesByDate(date, filter = 'all') {
    try {
        // Formatage de la date au format 'YYYY-MM-DD'
        const formattedDate = new Date(date).toISOString().split('T')[0];

        // Construction de l'URL avec les paramètres de requête
        const queryParams = new URLSearchParams({ date: formattedDate, filter });
        const url = `${apiEndpoint}/services/get/by_date?${queryParams.toString()}`;

        // Appel de l'API
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        // Vérification de la réponse
        if (!response.ok) {
            throw new Error('Failed to get services by date');
        }

        // Analyse de la réponse JSON
        const result = await response.json();

        console.log("result", result);
        return result;
    } catch (error) {
        console.error('Error getting services by date:', error.message);
        throw error;
    }
}


export { createService, getServiceByID, updateService, deleteService, getAllServices, getServiceCapacity, getServicesByDate};