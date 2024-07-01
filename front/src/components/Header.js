import React from 'react';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import logo from '../assets/images/logo.png'; 
import '../styles/Header.css'; 

const Header = () => {
  const { t, i18n } = useTranslation();

  const changeLanguage = (e) => {
    i18n.changeLanguage(e.target.value);
  };

  return (
    <header>
      <nav className="navbar">
        <Link to="/">
          <img src={logo} alt="NO MORE WASTE Logo" className="logo" />
        </Link>
        <ul className="nav-list">
          <li className="nav-item">
            <Link to="/" className="nav-link">{t('home')}</Link>
          </li>
          <li className="nav-item">
            <Link to="/signin" className="nav-link">{t('sign_in')}</Link>
          </li>
          <li className="nav-item">
            <Link to="/signup" className="nav-link">{t('sign_up')}</Link>
          </li>
        </ul>
        <div className="language-switcher">
          <select onChange={changeLanguage} defaultValue={i18n.language} className="language-dropdown">
            <option value="en">EN</option>
            <option value="fr">FR</option>
            {/* Ajouter plus d'options ici pour d'autres langues */}
            <option value="es">ES</option>
            <option value="de">DE</option>
            <option value="it">IT</option>
            <option value="pt">PT</option>
            <option value="jp">JP</option>
          </select>
        </div>
      </nav>
    </header>
  );
};

export default Header;
