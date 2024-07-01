import React from 'react';
import styled from 'styled-components';

const FooterContainer = styled.footer`
  background-color: #282c34;
  color: white;
  padding: 10px;
  text-align: center;
  position: absolute;
  bottom: 0;
  width: 100%;
`;

const Footer = () => {
  return (
    <FooterContainer>
      <p>&copy; 2024 NO MORE WASTE. All rights reserved.</p>
    </FooterContainer>
  );
};

export default Footer;
