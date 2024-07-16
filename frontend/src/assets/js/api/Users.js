// Fichier user.js - contenant les fonctions pour interagir avec l'API User

// Fonction pour enregistrer un nouveau bénévole
async function registerVolunteer(userData) {
    return await fetch(apiEndpoint + '/users/registerVolunteer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(userData)
    })
}

// Fonction pour enregistrer un nouveau commerçant
async function registerMerchant(userData) {
    return await fetch(apiEndpoint + '/users/registerMerchant', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(userData)
    });
}

// Fonction pour approuver un utilisateur par un administrateur
async function handleAprovals(userId, status) {
        const response = await fetch(`${apiEndpoint}/users/approval/${userId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: status })
        })
            .then(response => {
                if (response.ok) {
                    switch (status){
                        case 'approved':
                            console.log(`User ${userId} approved successfully.`);
                            alert(`User ${userId} approved successfully.`);
                        break;
                        case 'pending':
                            console.log(`User ${userId} put on hold successfully.`);
                            alert(`User ${userId} put on hold successfully.`);
                        break;
                        case 'rejected':
                            console.log(`User ${userId} rejected successfully.`);
                            alert(`User ${userId} rejected successfully.`);
                            break;}
                } else {
                    console.error(`Error approving user ${userId}:`, response.statusText);
                    alert(`Error approving user ${userId}:`);
                }
            })
            .catch(error => {
                console.error(`Error approving user ${userId}:`, error);
            });

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

export { registerVolunteer, registerMerchant, handleAprovals, getUser, getAllUsers };