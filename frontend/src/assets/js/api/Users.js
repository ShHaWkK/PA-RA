// Fichier user.js - contenant les fonctions pour interagir avec l'API User

// Fonction pour enregistrer un nouveau bénévole
async function registerVolunteer(userData) {
        const response = await fetch(apiEndpoint+'/users/registerVolunteer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(userData)
        });
        if (!response.ok) {
            throw new Error('Failed to register volunteer');
        }
        return await response.json();
}

// Fonction pour enregistrer un nouveau commerçant
async function registerMerchant(userData) {
        const response = await fetch(apiEndpoint+'/users/registerMerchant', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(userData)
        });
        if (!response.ok) {
            console.error('Failed to register merchant');
        } else {
            console.log('Merchant registered successfully');
        }
        return await response.json();
}

// Fonction pour approuver un utilisateur par un administrateur
async function approveUser(userData, adminUserId) {
        const response = await fetch(apiEndpoint+`/users/approveUser/${adminUserId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(userData)
        });
        if (!response.ok) {
            console.error('Failed to approve user');
        } else {
            console.log('User approved successfully');
        }
        return await response.json();
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
async function getAllUsers() {
        const response = await fetch(apiEndpoint+'/users', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            console.error('Failed to get all users');
        }
        return await response.json();
}