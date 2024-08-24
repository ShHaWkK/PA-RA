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

async function getRouteDestinations(routeId) {
    try {
        // Construire l'URL de la requête
        const response = await fetch(`${apiEndpoint}/routes/${routeId}/route-destinations`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        console.log("Response status:", response.status);

        // Vérifier si la réponse est correcte
        if (!response.ok) {
            if (response.status === 404) {
                return { message: `Route with ID ${routeId} not found` };
            }
            throw new Error('Failed to get route destinations');
        }

        const jsonResponse = await response.json();
        console.log("Response body JSON:", jsonResponse);

        // Vérifier si les destinations existent
        if (!jsonResponse || jsonResponse.length === 0) {
            return { error: 'No destinations found for this route' };
        }

        return jsonResponse;

    } catch (error) {
        console.error('Error getting route destinations:', error.message);
        throw error; // Propager l'erreur pour permettre une gestion ultérieure
    }
}

async function getDeliveriesByDestination(destinationId) {
    try {
        // Construire l'URL de la requête
        const response = await fetch(`${apiEndpoint}/routes/${destinationId}/destination-deliveries`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        console.log("Response status:", response.status);

        // Vérifier si la réponse est correcte
        if (!response.ok) {
            if (response.status === 404) {
                return { message: `Destination with ID ${destinationId} not found` };
            }
            throw new Error('Failed to get deliveries for the destination');
        }

        const jsonResponse = await response.json();
        console.log("Response body JSON:", jsonResponse);

        // Vérifier si les livraisons existent
        if (!jsonResponse || jsonResponse.length === 0) {
            return { error: 'No deliveries found for this destination' };
        }

        return jsonResponse;

    } catch (error) {
        console.error('Error getting deliveries for destination:', error.message);
        throw error; // Propager l'erreur pour permettre une gestion ultérieure
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
        const response = await fetch(`${apiEndpoint}/routes/${id}/route`, {
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

async function updateRoute(routeId, routeData) {
    try {
        const response = await fetch(`${apiEndpoint}/routes/${routeId}/route`, {
            method: 'PUT', // ou 'PATCH' selon votre API
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(routeData)
        });

        if (!response.ok) {
            const error = await response.json();
            console.error('Erreur lors de la mise à jour de la route:', error);
            return;
        }

        return await response.json();
    } catch (error) {
        console.error('Exception lors de la mise à jour de la route:', error);
    }
}

async function updateDestination(destinationId, destinationData) {
    try {
        const response = await fetch(`${apiEndpoint}/destinations/${destinationId}/destination`, {
            method: 'PUT', // ou 'PATCH' selon votre API
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(destinationData)
        });

        if (!response.ok) {
            const error = await response.json();
            console.error('Erreur lors de la mise à jour de la destination:', error);
            return;
        }

        return await response.json();
    } catch (error) {
        console.error('Exception lors de la mise à jour de la destination:', error);
    }
}

async function updateDelivery(deliveryId, deliveryData) {
    try {
        const response = await fetch(`${apiEndpoint}/deliveries/${deliveryId}/delivery`, {
            method: 'PUT', // ou 'PATCH' selon votre API
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(deliveryData)
        });

        if (!response.ok) {
            const error = await response.json();
            console.error('Erreur lors de la mise à jour de la livraison:', error);
            return;
        }

        return await response.json();
    } catch (error) {
        console.error('Exception lors de la mise à jour de la livraison:', error);
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
    getRouteDestinations,
    getDeliveriesByDestination,
    updateRoute,
    updateDelivery,
    updateDestination
};