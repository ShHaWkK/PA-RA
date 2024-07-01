import React from 'react';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import styled from 'styled-components';
import logo from '../assets/images/logo.png'; 

const Navbar = styled.nav`
  background: #282c34;
  padding: 10px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
`;

const NavList = styled.ul`
  list-style: none;
  display: flex;
  padding: 0;
  margin: 0;
`;

const NavItem = styled.li`
  margin: 0 15px;
`;

const NavLink = styled(Link)`
  color: #61dafb;
  text-decoration: none;
  font-size: 1.2em;
  &:hover {
    color: white;
  }
`;

const Logo = styled.img`
  height: 40px;
  margin-right: 15px;
`;

const LanguageSwitcher = styled.div`
  position: relative;
  display: inline-block;
`;

const LanguageDropdown = styled.select`
  background: #282c34;
  color: #61dafb;
  border: none;
  padding: 5px 10px;
  font-size: 1.2em;
  cursor: pointer;
  &:hover {
    color: white;
  }
`;

const Header = () => {
  const { t, i18n } = useTranslation();

  const changeLanguage = (e) => {
    i18n.changeLanguage(e.target.value);
  };

  return (
    <header>
      <Navbar>
        <Link to="/">
          <Logo src={logo} alt="NO MORE WASTE Logo" />
        </Link>
        <NavList>
          <NavItem>
            <NavLink to="/">{t('home')}</NavLink>
          </NavItem>
          <NavItem>
            <NavLink to="/signin">{t('sign_in')}</NavLink>
          </NavItem>
          <NavItem>
            <NavLink to="/signup">{t('sign_up')}</NavLink>
          </NavItem>
        </NavList>
        <LanguageSwitcher>
          <LanguageDropdown onChange={changeLanguage} defaultValue={i18n.language}>
            <option value="en">EN</option>
            <option value="fr">FR</option>
            {/* Ajouter plus d'options ici pour d'autres langues */}
            <option value="es">ES</option>
            <option value="de">DE</option>
            <option value="it">IT</option>
            <option value="pt">PT</option>
            <option value="jp">JP</option>
          </LanguageDropdown>
        </LanguageSwitcher>
      </Navbar>
    </header>
  );
};

export default Header;