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

async function getCollections(queryParameters = {}) {
    try {
        // Construire l'URL avec les paramètres requis
        const url = new URL(apiEndpoint + '/collections');

        // Ajouter les query parameters à l'URL
        Object.keys(queryParameters).forEach(key => {
            if (queryParameters[key] !== undefined && queryParameters[key] !== null) {
                url.searchParams.append(key, queryParameters[key]);
            }
        });

        // Effectuer la requête GET
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        // Vérifier si la réponse est correcte
        if (!response.ok) {
            throw new Error(`Failed to get collections with parameters: ${JSON.stringify(queryParameters)}`);
        }

        // Retourner les données JSON
        return await response.json();
    } catch (error) {
        console.error('Error in getCollections:', error.message);
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

async function assignProductsToCollection(collectionId, products) {
    const url = apiEndpoint + `/collections/${collectionId}/products`;
    const response = await fetch(url, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ products })
    });

    console.log(JSON.stringify({ products }));

    if (!response.ok) {
        if (response.status === 400) {
            throw new Error('Bad Request: No products provided or missing required fields.');
        } else if (response.status === 404) {
            throw new Error('Not Found: Collection or ProductNotification not found.');
        } else if (response.status === 409) {
            throw new Error('Conflict: Product already assigned to this collection.');
        } else {
            throw new Error(`HTTP Error: ${response.status}`);
        }
    }

    return response.json();
}

async function modifyProductsInCollection(collectionId, products) {
    const url = `${apiEndpoint}/collections/${collectionId}/update`;
    const response = await fetch(url, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ products }) // Utiliser directement le tableau de produits
    });

    console.log(JSON.stringify({ products }));

    if (!response.ok) {
        switch (response.status) {
            case 400:
                throw new Error('Bad Request: No products provided, missing required fields, or invalid data.');
            case 404:
                const errorData = await response.json();
                if (errorData.error === 'Collection not found') {
                    throw new Error('Not Found: Collection not found.');
                } else if (errorData.error === 'ProductNotification not found') {
                    throw new Error('Not Found: ProductNotification not found.');
                } else if (errorData.error === 'Product not assigned to this collection') {
                    throw new Error('Not Found: Product not assigned to this collection.');
                }
                break;
            case 409:
                throw new Error('Conflict: Attempt to modify an unassigned product.');
            default:
                throw new Error(`HTTP Error: ${response.status}`);
        }
    }

    return response.json();
}

async function exportCollectionToExcel(collectionId) {
    const url = `${apiEndpoint}/collections/${collectionId}/export`;

    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    });

    if (!response.ok) {
        switch (response.status) {
            case 400:
                throw new Error('Bad Request: The request was invalid.');
            case 404:
                throw new Error('Not Found: Collection not found or other resources.');
            default:
                throw new Error(`HTTP Error: ${response.status}`);
        }
    }

    return response.json();
}

async function getCollectionExcel(collectionId) {
    const url = `${apiEndpoint}/collections/${collectionId}/get_excel`;

    const response = await fetch(url, {
        method: 'GET'
    });

    if (!response.ok) {
        switch (response.status) {
            case 404:
                const errorData = await response.json();
                if (errorData.error.includes('not found')) {
                    throw new Error('Not Found: Collection or file not found.');
                }
                break;
            case 500:
                throw new Error('Internal Server Error: Error occurred while retrieving the file.');
            default:
                throw new Error(`HTTP Error: ${response.status}`);
        }
    }

    const fileBlob = await response.blob();
    const fileURL = URL.createObjectURL(fileBlob);
    const link = document.createElement('a');
    link.href = fileURL;
    link.download = `collection_${collectionId}.xlsx`; // Nom du fichier à télécharger
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}


export { createCollection, getCollectionByID, updateCollection, deleteCollection, getAllCollections, getProductsFromCollection, removeProductsFromCollection, assignProductsToCollection, getCollections, modifyProductsInCollection, exportCollectionToExcel, getCollectionExcel};