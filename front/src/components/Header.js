import React from 'react';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import styled from 'styled-components';

const Navbar = styled.nav`
  background: #282c34;
  padding: 10px;
`;

const NavList = styled.ul`
  list-style: none;
  display: flex;
  justify-content: space-around;
  padding: 0;
  margin: 0;
`;

const NavItem = styled.li`
  margin: 0 10px;
`;

const NavLink = styled(Link)`
  color: #61dafb;
  text-decoration: none;
  font-size: 1.2em;
  &:hover {
    color: white;
  }
`;

const LanguageSwitcher = styled.div`
  display: flex;
  justify-content: flex-end;
  padding: 10px;
`;

const LanguageButton = styled.button`
  background: none;
  border: none;
  color: #61dafb;
  margin: 0 5px;
  cursor: pointer;
  &:hover {
    color: white;
  }
`;

const Header = () => {
  const { t, i18n } = useTranslation();

  const changeLanguage = (lng) => {
    i18n.changeLanguage(lng);
  };

  return (
    <header>
      <LanguageSwitcher>
        <LanguageButton onClick={() => changeLanguage('en')}>EN</LanguageButton>
        <LanguageButton onClick={() => changeLanguage('fr')}>FR</LanguageButton>
      </LanguageSwitcher>
      <Navbar>
        <NavList>
          <NavItem>
            <NavLink to="/">{t('home')}</NavLink>
          </NavItem>
          <NavItem>
            <NavLink to="/signin">{t('sign_in')}</NavLink>
          </NavItem>
          {/* Ajouter d'autres liens ici */}
        </NavList>
      </Navbar>
    </header>
  );
};

export default Header;
