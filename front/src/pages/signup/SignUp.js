import React, { useState } from 'react';
import { useTranslation } from 'react-i18next';
import '../../styles/SignUp.css'; 

const SignUp = () => {
  const { t } = useTranslation();
  const [signup_email, setEmail] = useState('');
  const [signup_password, setPassword] = useState('');
  const [confirm_password, setConfirmPassword] = useState('');

  const handleEmailChange = (e) => {
    setEmail(e.target.value);
  };

  const handlePasswordChange = (e) => {
    setPassword(e.target.value);
  };

  const handleConfirmPasswordChange = (e) => {
    setConfirmPassword(e.target.value);
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    // Ajoutez ici la logique d'inscription
  };

  return (
    <div className="signup-container">
      <h2>{t('sign_up')}</h2>
      <form onSubmit={handleSubmit}>
        <div className="form-group">
          <label>Email :</label>
          <input
            type="email"
            value={signup_email}
            onChange={handleEmailChange}
            required
          />
        </div>
        <div className="form-group">
          <label>{t('password')}</label>
          <input
            type="password"
            value={signup_password}
            onChange={handlePasswordChange}
            required
          />
        </div>
        <div className="form-group">
          <label>{t('confirm_password')}</label>
          <input
            type="password"
            value={confirm_password}
            onChange={handleConfirmPasswordChange}
            required
          />
        </div>
        <button type="submit">{t('sign_up')}</button>
      </form>
    </div>
  );
};

export default SignUp;
