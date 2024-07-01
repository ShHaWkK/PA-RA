import React, { useState } from 'react';
// import './signin.css';
import { validate } from 'react-email-validator';
import { useNavigate } from 'react-router-dom';

const SignIn = () => {

    const navigate = useNavigate();

    const [signin_email, setEmail] = useState('');
    const [signin_password, setPassword] = useState('');
  
    const handleEmailChange = (e) => {
      setEmail(e.target.value);
    };

    const handlePasswordChange = (e) => {
      setPassword(e.target.value);
    };
  
    return (
        <div className="image-background">
        <div className='center-container'>
          <div>
                <div class="wave"></div>
                <div class="wave"></div>
                <div class="wave"></div>
          </div>
          <div className="form-container">
            <h2>Se connecter :</h2>
            <form >
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
              <button type="submit">Se connecter</button>
              Vous n'avez pas de compte ? <a href="/signup">S'inscrire</a>
            </form>
          </div>
        </div>
        </div>
      );
  }

export default SignIn;