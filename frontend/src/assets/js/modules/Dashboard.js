import {deleteCookie} from "../api/Api.js";

document.addEventListener('DOMContentLoaded', () => {
    const deconnectionLink = document.getElementById('deconnection-link');

    deconnectionLink.addEventListener('click', async function () {
        // Afficher une alerte pour confirmer la déconnexion
        const confirmation = confirm("Êtes-vous sûr de vouloir vous déconnecter ?");

        if (confirmation) {
            await deleteCookie('user_id');
            await deleteCookie('company_id');
            await deleteCookie('user_role');
            await deleteCookie('jwt');

            window.location.href = '/Login';
        }
    });
});