import {login} from "../api/Login.js";

document.getElementById('loginForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    try {
        const response = await login(email, password);
        console.log('Login successful:', response);
        alert('Login successful');


        // Ajoutez ici le code pour rediriger l'utilisateur ou afficher un message de succès.
    } catch (error) {
        console.error('Login failed:', error.message);
        alert('Login failed');

        // Ajoutez ici le code pour afficher un message d'erreur à l'utilisateur.
        alert('Erreur de connexion: ' + error.message);
    }
});
