async function createCompany(companyData) {
    try {
        const response = await fetch(apiEndpoint + '/companies', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(companyData)
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.error || 'Failed to create company');
        }

        return await response.json();
    } catch (error) {
        console.error('Error creating company:', error.message);
        throw error;
    }
}

async function getCompany(id) {
    try {
        const response = await fetch(apiEndpoint + `/companies/${id}`, {
            method: 'GET'
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.error || 'Failed to retrieve company');
        }

        return await response.json();
    } catch (error) {
        console.error('Error retrieving company:', error.message);
        throw error;
    }
}

async function getAllCompanies() {
    try {
        const response = await fetch(apiEndpoint + '/companies', {
            method: 'GET'
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.error || 'Failed to retrieve companies');
        }

        return await response.json();
    } catch (error) {
        console.error('Error retrieving all companies:', error.message);
        throw error;
    }
}

async function getEmployeesFromCompany(companyId) {
    const url =apiEndpoint + `/companies/${companyId}/employees`;
    console.log("url",url);

    const response = await fetch(url, {
        method: 'GET'
    });

    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Failed to retrieve employees');
    }

    return await response.json();
}

async function updateCompany(id, companyData) {
    try {
        const response = await fetch(apiEndpoint + `/companies/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(companyData)
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.error || 'Failed to update company');
        }

        return await response.json();
    } catch (error) {
        console.error('Error updating company:', error.message);
        throw error;
    }
}

async function deleteCompany(id) {
    try {
        const response = await fetch(apiEndpoint + `/companies/${id}`, {
            method: 'DELETE'
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.error || 'Failed to delete company');
        }

        return await response.json();
    } catch (error) {
        console.error('Error deleting company:', error.message);
        throw error;
    }
}

export { createCompany, getCompany, getAllCompanies, deleteCompany, updateCompany, getEmployeesFromCompany}