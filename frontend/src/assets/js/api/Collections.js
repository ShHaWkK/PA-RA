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

export { createCollection, getCollectionByID, updateCollection, deleteCollection, getAllCollections };