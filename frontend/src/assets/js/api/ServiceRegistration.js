async function createServiceRegistration(registrationData) {
    try {
        const response = await fetch(apiEndpoint + '/service_registrations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(registrationData)
        });
        if (!response.ok) {
            throw new Error('Failed to create service registration');
        }
        return await response.json();
    } catch (error) {
        console.error('Error creating service registration:', error.message);
        throw error;
    }
}

async function getServiceRegistrationByID(registration_id) {
    try {
        const response = await fetch(apiEndpoint + '/service_registrations/' + registration_id, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get service registration');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting service registration:', error.message);
        throw error;
    }
}

async function updateServiceRegistration(registration_id, registrationData) {
    try {
        const response = await fetch(apiEndpoint + '/service_registrations/id/' + registration_id, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(registrationData)
        });
        if (!response.ok) {
            throw new Error('Failed to update service registration');
        }
        return await response.json();
    } catch (error) {
        console.error('Error updating service registration:', error.message);
        throw error;
    }
}

async function deleteServiceRegistration(registration_id) {
    try {
        const response = await fetch(apiEndpoint + '/service_registrations/' + registration_id, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to delete service registration');
        }
        return { message: 'Service registration deleted successfully' };
    } catch (error) {
        console.error('Error deleting service registration:', error.message);
        throw error;
    }
}

async function getAllServiceRegistrations() {
    try {
        const response = await fetch(apiEndpoint + '/service_registrations', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get all service registrations');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting all service registrations:', error.message);
        throw error;
    }
}

async function getRegistrationByServiceAndUserId(userId, serviceId) {
    try {
        const url = `${apiEndpoint}/service_registrations/${userId}/${serviceId}`;

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`Failed to get registration: ${response.statusText}`);
        }

        return await response.json();
    } catch (error) {
        // Log et lancer une erreur pour gestion ultérieure
        console.error('Error getting registration data:', error.message);
        throw error;
    }
}

async function registerUserToService(registrationData) {
    try {
        const response = await fetch(apiEndpoint + '/service_registrations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(registrationData)
        });
        if (!response.ok) {
            throw new Error('Failed to register user to service');
        }
        return await response.json();
    } catch (error) {
        console.error('Error registering user to service:', error.message);
        throw error;
    }
}

export { createServiceRegistration, getServiceRegistrationByID, updateServiceRegistration, getRegistrationByServiceAndUserId, deleteServiceRegistration, getAllServiceRegistrations, registerUserToService };
