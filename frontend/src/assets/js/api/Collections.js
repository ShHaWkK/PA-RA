async function createCollection(collectionData) {
    try {
        const response = await fetch(apiEndpoint + '/collections', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(collectionData)
        });
        if (!response.ok) {
            throw new Error('Failed to create collection');
        }
        return await response.json();
    } catch (error) {
        console.error('Error creating collection:', error.message);
        throw error;
    }
}

async function getCollectionByID(collection_id) {
    try {
        const response = await fetch(apiEndpoint + '/collections/' + collection_id, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get collection');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting collection:', error.message);
        throw error;
    }
}

async function updateCollection(collection_id, collectionData) {
    try {
        const response = await fetch(apiEndpoint + '/collections/' + collection_id, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(collectionData)
        });
        if (!response.ok) {
            throw new Error('Failed to update collection');
        }
        return await response.json();
    } catch (error) {
        console.error('Error updating collection:', error.message);
        throw error;
    }
}

async function deleteCollection(collection_id) {
    try {
        const response = await fetch(apiEndpoint + '/collections/' + collection_id, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to delete collection');
        }
        return { message: 'Collection deleted successfully' };
    } catch (error) {
        console.error('Error deleting collection:', error.message);
        throw error;
    }
}

async function getAllCollections() {
    try {
        const response = await fetch(apiEndpoint + '/collections', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get all collections');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting all collections:', error.message);
        throw error;
    }
}

    async function getCollectionsByDate(date) {
        try {
            // Construire l'URL avec le paramètre de date
            const url = new URL(apiEndpoint + '/collections');
            url.searchParams.append('date', date);

            console.log("url",url);

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to get collections by date');
            }

            return await response.json();
        } catch (error) {
            console.error('Error getting collections by date:', error.message);
            throw error;
        }
    }

async function getProductsFromCollection(collectionId){
    try {
        const response = await fetch(apiEndpoint + '/collections/' + collectionId +'/products', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get all products from the collection');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting all products from collection:', error.message);
        throw error;
    }
}

async function getCollectionsByDateAndCompletion(date = null, completed) {
    try {
        // Construire l'URL avec les paramètres requis
        let url = apiEndpoint + '/collections?completed=' + completed;

        // Ajouter la date au paramètre si elle est fournie
        if (date) {
            url += '&date=' + encodeURIComponent(date);
        }

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to get collections by date and completion');
        }

        return await response.json();
    } catch (error) {
        console.error('Error getting collections by date and completion:', error.message);
        throw error;
    }
}

async function removeProductsFromCollection(collectionId, productIds) {
    const url = apiEndpoint + `/collections/${collectionId}/remove`;
    const response = await fetch( url, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            products: productIds.map(id => ({ notification_id: id }))
        })
    });

    console.log(JSON.stringify({
        products: productIds.map(id => ({ notification_id: id }))
    }));

    if (!response.ok) {
        throw new Error(`Erreur HTTP: ${response.status}`);
    }

    return response.json();
}

export { createCollection, getCollectionByID, updateCollection, deleteCollection, getAllCollections, getProductsFromCollection, getCollectionsByDate, getCollectionsByDateAndCompletion, removeProductsFromCollection};