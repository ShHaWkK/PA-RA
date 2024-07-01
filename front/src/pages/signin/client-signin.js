import React, { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';
import '../../styles/global.css';

const SignIn = () => {
  const { t } = useTranslation();
  const navigate = useNavigate();

  const [signin_email, setEmail] = useState('');
  const [signin_password, setPassword] = useState('');

  const handleEmailChange = (e) => {
    setEmail(e.target.value);
  };

  const handlePasswordChange = (e) => {
    setPassword(e.target.value);
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    // Ajoutez ici la logique de connexion
    navigate('/dashboard'); // Redirigez vers le tableau de bord après la connexion
  };

  return (
    <div className="image-background">
      <div className="center-container">
        <div className="form-container">
          <h2>{t('sign_in')}</h2>
          <form onSubmit={handleSubmit}>
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
              <label>{t('password')}</label>
              <input
                type="password"
                value={signin_password}
                onChange={handlePasswordChange}
                required
              />
            </div>
            <button type="submit">{t('sign_in')}</button>
            <p>{t('no_account')} <a href="/signup">{t('sign_up')}</a></p>
          </form>
        </div>
      </div>
    </div>
  );
};

export default SignIn;
