import React, { useState } from 'react';
import { validate } from 'react-email-validator';
import { useNavigate } from 'react-router-dom';
import Cookies from 'js-cookie';
import axios from 'axios';
import querystring from 'querystring';
import '../../styles/SignIn.css'; // Importez le fichier CSS

const SignIn = () => {
  // --------------------- Déclaration des variables ---------------------
  const navigate = useNavigate(); // eslint-disable-line no-unused-vars

  const [signin_email, setEmail] = useState('');
  const [signin_password, setPassword] = useState('');

  const handleEmailChange = (e) => {
    setEmail(e.target.value);
  };

  const handlePasswordChange = (e) => {
    setPassword(e.target.value);
  };

  // --------------------- Envoie du formulaire ---------------------
  const apiURL = process.env.REACT_APP_API_URL;

  // Créer une instance Axios avec l'URL de base
  const instance = axios.create({ // eslint-disable-line no-unused-vars
    baseURL: apiURL,
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    }
  });

  const handleSigninSubmission = async (e) => {
    e.preventDefault();
    
    const valid = await validate(signin_email); 
    if (!valid) {
      alert('Please enter a valid email address.');
      return;
    }
  
    const formData = querystring.stringify({
      username: signin_email,
      password: signin_password
    });
  
    // Afficher l'URI de la requête dans la console
    // const requestURI = `${apiURL}/auth/token?${formData}`;
    // console.log('Request URI:', requestURI);
  
    instance.post('/auth/token', formData)
      .then(response => {
        // Traitement pour une création réussie
        Cookies.set('email', signin_email, { expires: 1 });
        alert('Signed in successfully!');
        navigate('/'); // Rediriger après la connexion réussie
      })
      .catch(error => {
        if (error.response) {
          if (error.response.status === 404) {
            alert('This email is not registered');
          } else {
            alert('An error occurred: ' + error.response.statusText);
          }
        } else {
          alert('Network error: ' + error.message);
        }
      });    
  };

  // --------------------- Affichage de la page ---------------------
  return (
    <div className="image-background">
      <div className='center-container'>
        <div className="form-container">
          <h2>Se connecter :</h2>
          <form onSubmit={handleSigninSubmission}>
            <div className="form-group">
              <label>Email :</label>
              <input
                type="email"
                value={signin_email}
                onChange={handleEmailChange}
                required
              />
            </div>
            <div className="form-group">
              <label>Mot de passe</label>
              <input
                type="password"
                value={signin_password}
                onChange={handlePasswordChange}
                required
              />
            </div>
            <button type="submit" className="button">Se connecter</button>
            <p>Vous n'avez pas de compte ? <a href="/signup">S'inscrire</a></p>
          </form>
        </div>
      </div>
    </div>
  );
}

export default SignIn;
