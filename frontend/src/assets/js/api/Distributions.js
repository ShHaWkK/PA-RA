async function createRoute(routeData) {
    try {
        const response = await fetch(`${apiEndpoint}/routes`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(routeData),
        });
        if (!response.ok) {
            throw new Error('Failed to create route');
        }
        return await response.json();
    } catch (error) {
        console.error('Error creating route:', error.message);
        throw error;
    }
}

async function addDeliveryToDestination(destinationId, deliveryData) {
    try {
        const response = await fetch(`${apiEndpoint}/destinations/${destinationId}/add-delivery`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(deliveryData),
        });
        if (!response.ok) {
            throw new Error('Failed to add delivery to destination');
        }
        return await response.json();
    } catch (error) {
        console.error('Error adding delivery to destination:', error.message);
        throw error;
    }
}

async function removeDeliveryFromDestination(deliveryId) {
    try {
        const response = await fetch(`${apiEndpoint}/deliveries/${deliveryId}/remove-delivery`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
            },
        });
        if (!response.ok) {
            throw new Error('Failed to remove delivery from destination');
        }
        return await response.json();
    } catch (error) {
        console.error('Error removing delivery from destination:', error.message);
        throw error;
    }
}

async function addDestinationToRoute(routeId, destinationData) {
    try {
        const response = await fetch(`${apiEndpoint}/routes/${routeId}/add-destination`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(destinationData),
        });
        if (!response.ok) {
            throw new Error('Failed to add destination to route');
        }
        return await response.json();
    } catch (error) {
        console.error('Error adding destination to route:', error.message);
        throw error;
    }
}

async function removeDestinationFromRoute(destinationId) {
    try {
        const response = await fetch(`${apiEndpoint}/destinations/${destinationId}/remove-destination`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
            },
        });
        if (!response.ok) {
            throw new Error('Failed to remove destination from route');
        }
        return await response.json();
    } catch (error) {
        console.error('Error removing destination from route:', error.message);
        throw error;
    }
}

async function getAllRoutes(queryParams = {}) {
    try {
        console.log(`Request URL: ${apiEndpoint}/routes?${queryParams}`);
        const queryString = new URLSearchParams(queryParams).toString();
        const response = await fetch(`${apiEndpoint}/routes?${queryString}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        console.log("Response status:", response.status);

        if (!response.ok) {
            throw new Error('Failed to get all routes');
        }
        const jsonResponse = await response.json();
        console.log("Response body JSON:", jsonResponse);

        return await jsonResponse;
    } catch (error) {
        console.error('Error getting all routes:', error.message);
        throw error;
    }
}

async function getRouteById(routeId) {
    try {
        const response = await fetch(`${apiEndpoint}/routes/${routeId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        });
        if (!response.ok) {
            throw new Error('Failed to get route');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting route:', error.message);
        throw error;
    }
}

async function getDestinationById(destinationId) {
    try {
        const response = await fetch(`${apiEndpoint}/destinations/${destinationId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        });
        if (!response.ok) {
            throw new Error('Failed to get destination');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting destination:', error.message);
        throw error;
    }
}

async function getDeliveryById(deliveryId) {
    try {
        const response = await fetch(`${apiEndpoint}/deliveries/${deliveryId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        });
        if (!response.ok) {
            throw new Error('Failed to get delivery');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting delivery:', error.message);
        throw error;
    }
}

async function deleteRoute(id) {
    try {
        const response = await fetch(`http://localhost/routes/${id}/route`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        if (!response.ok) {
            // Si la réponse n'est pas OK, vérifier le code de réponse
            if (response.status === 404) {
                console.error('Route not found');
            } else {
                console.error('Failed to delete the route');
            }
            return;
        }

        const result = await response.json();

        if (result.error) {
            console.error(result.error);
            alert(`Erreur: ${result.error}`);
        } else {
            console.log(result.message);
            // Ici, vous pouvez également ajouter du code pour mettre à jour l'interface utilisateur, par exemple, rafraîchir la liste des routes
        }
    } catch (error) {
        console.error('Error in deleteRoute:', error.message);
        alert('Une erreur est survenue lors de la suppression de la route');
    }
}


export {
    createRoute,
    addDeliveryToDestination,
    removeDeliveryFromDestination,
    addDestinationToRoute,
    removeDestinationFromRoute,
    getAllRoutes,
    getRouteById,
    getDestinationById,
    getDeliveryById,
    deleteRoute,
};