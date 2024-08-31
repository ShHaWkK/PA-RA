// Fichier user.js - contenant les fonctions pour interagir avec l'API User

// Fonction pour enregistrer un nouveau bénévole avec un fichier PDF
async function registerVolunteer(userData, file) {
    const formData = new FormData();

    // Ajoute les données utilisateur au formulaire
    formData.append('json_data', JSON.stringify(userData));

    // Ajoute le fichier PDF si présent
    if (file) {
        formData.append('file_data', file);
    }

    // Envoie la requête multipart
    return await fetch(apiEndpoint + '/users/registerVolunteer', {
        method: 'POST',
        body: formData,
    });
}

// Fonction pour enregistrer un nouveau commerçant avec un fichier PDF
async function registerMerchant(formData) {
    return await fetch(apiEndpoint + '/users/registerMerchant', {
        method: 'POST',
        body: formData, // Ne définissez pas l'en-tête Content-Type
        credentials: 'include' // Inclure les cookies et les en-têtes d'authentification
    });
}

// Fonction pour modifier un utilisateur
async function modifyUser(userId,userData) {
    return await fetch(apiEndpoint + '/users/'+userId, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(userData)
    })
}

// Fonction pour approuver un utilisateur par un administrateur
async function handleAprovals(userId, status) {
    var successMessage,errorMessage;

    console.log("uri",`${apiEndpoint}/users/approval/${userId}`);
    console.log("status",status);

    const response = await fetch(`${apiEndpoint}/users/approval/${userId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: status })
        })
            .then(response => {
                switch (status){
                    case 'approved':
                        successMessage = (`User ${userId} approved successfully.`);
                        errorMessage = (`Error approving user ${userId}:` + response.statusText);
                        break;
                    case 'pending':
                         successMessage = (`User ${userId} put on hold successfully.`);
                         errorMessage = (`Error approving user ${userId}:` + response.statusText);
                        break;
                    case 'rejected':
                         successMessage = (`User ${userId} rejected successfully.`);
                         errorMessage = (`Error approving user ${userId}:` + response.statusText);
                        break;}
                if (response.ok) {
                    console.log(successMessage);
                    alert(successMessage);
                } else {
                    console.error(errorMessage);
                    alert(errorMessage);
                }
            })

    return response;
}

// Fonction pour récupérer un utilisateur par son ID
async function getUser(userId) {
    const response = await fetch(apiEndpoint+`/users/${userId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    });
    if (!response.ok) {
        console.error('Failed to get user');
    }
    return await response.json();
}

// Fonction pour récupérer tous les utilisateurs
async function getAllUsers(role, status) {
    // Construction de l'URL avec les query parameters
    const queryParams = new URLSearchParams();
    if (role) {
        console.log("role",role);
        queryParams.append('role', role);
    }
    if (status) {
        console.log("status",status);
        queryParams.append('status', status);
    }

    console.log(`${apiEndpoint}/users?${queryParams.toString()}`);

    const response = await fetch(`${apiEndpoint}/users?${queryParams.toString()}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    });

    if (!response.ok) {
        console.error('Failed to get all users');
        return null;
    }

    return await response.json();
}

async function deleteUser(userId){
    const response = await fetch(apiEndpoint+`/users/${userId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        }
    });

    if (!response.ok) {
        console.error('Failed to delete user');
        return null;
    }

    return await response.json();
}

async function getUserSkills(userId){
    const response = await fetch(apiEndpoint+`/users/getSkills/${userId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    });

    if (!response.ok) {
        console.error('Failed to get user skills');
        return null;
    }

    return await response.json();
}


async function getUserAvailabilities(userId){
    const response = await fetch(apiEndpoint+`/users/getAvailabilities/${userId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    });

    if (!response.ok) {
        console.error('Failed to get user Availabilities');
        return null;
    }

    return await response.json();
}

async function getUserCompanies(userId) {
        const response = await fetch(apiEndpoint+`/users/getUserCompanies/${userId}`, {
        method: 'GET',
            headers: {
            'Content-Type': 'application/json'
        }
        });

        if (response.ok) {
            return await response.json();
        } else {
            console.error('Failed to fetch companies:', response.statusText);
            return null;
        }
}

async function getUserFile(userId) {
    try {
        const url = `${apiEndpoint}/users/getUserFile/${userId}`; // Remplacez avec votre endpoint approprié

        const response = await fetch(url, {
            method: 'GET'
        });

        if (!response.ok) {
            switch (response.status) {
                case 404:
                    const errorData = await response.json();
                    if (errorData.error.includes('not found')) {
                        throw new Error(`Not Found: ${errorData.error}`);
                    }
                    break;
                case 400:
                    throw new Error(`This user doesn't have a file`);
                case 500:
                    throw new Error('Internal Server Error: Error occurred while retrieving the file.');
                default:
                    throw new Error(`HTTP Error: ${response.status}`);
            }
        }

        // Obtenez le fichier en tant que Blob
        const fileBlob = await response.blob();
        const fileName = response.headers.get('Content-Disposition')?.split('filename=')[1]?.replace(/"/g, '') || 'file.pdf';

        // Créez un lien pour télécharger le fichier
        const fileURL = URL.createObjectURL(fileBlob);
        const link = document.createElement('a');
        link.href = fileURL;
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Libérer l'URL après le téléchargement
        URL.revokeObjectURL(fileURL);

    } catch (error) {
        console.error('Error during file retrieval:', error.message);
        alert('An error occurred while retrieving the file. Please try again.');
    }
}

export { registerVolunteer, registerMerchant, handleAprovals, getUser, getAllUsers, deleteUser, getUserSkills, getUserAvailabilities, getUserCompanies, modifyUser, getUserFile};