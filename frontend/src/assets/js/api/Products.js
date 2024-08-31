async function createProduct(productData) {
    try {
        const response = await fetch(apiEndpoint + '/products', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(productData)
        });
        if (!response.ok) {
            throw new Error('Failed to create product');
        }
        return await response.json();
    } catch (error) {
        console.error('Error creating product:', error.message);
        throw error;
    }
}

async function getProductByID(product_id) {
    try {
        const response = await fetch(apiEndpoint + '/products/id/' + product_id, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get product');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting product:', error.message);
        throw error;
    }
}

async function getProductByBarcode(barcode) {
    try {
        const response = await fetch(apiEndpoint + '/products/' + barcode, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get product');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting product:', error.message);
        throw error;
    }
}

async function updateProduct(barcode, productData) {
    try {
        const response = await fetch(apiEndpoint + '/products/' + barcode, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(productData)
        });
        if (!response.ok) {
            throw new Error('Failed to update product');
        }
        return await response.json();
    } catch (error) {
        console.error('Error updating product:', error.message);
        throw error;
    }
}

async function deleteProduct(barcode) {
    try {
        const response = await fetch(apiEndpoint + '/products/' + barcode, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to delete product');
        }
        return { message: 'Product deleted successfully' };
    } catch (error) {
        console.error('Error deleting product:', error.message);
        throw error;
    }
}

async function getAllProducts() {
    try {
        const response = await fetch(apiEndpoint + '/products', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Failed to get all products');
        }
        return await response.json();
    } catch (error) {
        console.error('Error getting all products:', error.message);
        throw error;
    }
}

export { createProduct, getProductByID, getProductByBarcode, updateProduct, deleteProduct, getAllProducts };